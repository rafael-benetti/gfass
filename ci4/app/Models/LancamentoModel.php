<?php

namespace App\Models;

use CodeIgniter\HTTP\RequestInterface;
use DateTime;

class LancamentoModel extends BaseModel

{

    protected $table = 'lancamentos';
    protected $primaryKey = 'chave';

    protected $useSoftDeletes = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $beforeInsert = ['vinculaIdUsuario', 'geraChave', 'corrigeValor', 'converteData'];
    protected $beforeUpdate = ['converteData', 'corrigeValor', 'checaPropriedade'];

    protected $afterInsert = ['notificaOrcamentoUltrapassado'];

    protected $useTimestamps = true;

    protected $allowedFields = [
        'usuarios_id',
        'chave',
        'categorias_id',
        'descricao',
        'anexo',
        'type',
        'valor',
        'data',
        'notificar_por_email',
        'consolidado'
    ];

    protected $validationRules = [
        'descricao' => [
            'label' => 'Descrição',
            'rules' => 'required'
        ],
        'categorias_id' => [
            'label' => "Categoria",
            'rules' => 'required'
        ],
        'valor' => [
            'label' => "Valor",
            'rules' => 'required'
        ],
        'data' => [
            'label' => "Data",
            'rules' => 'required'
        ],
    ];

    /**
     * Método que é chamado sempre quando ocorre um update na tabela Lançamentos.
     *
     * @param [type] $data
     * @return void
     */
    protected function notificaOrcamentoUltrapassado($data)
    {

        //Se existir um usuario setado em $this->idUsuarioApi, pego de lá, se não, pego da sessão.
        $id_usuario = $this->idUsuarioApi ? $this->idUsuarioApi : session()->id_usuario;

        //Verifico se este lançamento foi feito em uma categoria que possui orçamento cadastrado e notifica
        //o usuário caso o valor da categoria tenha superado o valor do orçamento
        $dadosOrcamento = (new OrcamentoModel())->addUserId($id_usuario)->getByIdCategoria($data['data']['categorias_id'], true);

        if (!is_null($dadosOrcamento)) {
            $totalOrcamento = $dadosOrcamento['valor'];
            $totalDespesasMesAtual = (new LancamentoModel())
                ->addUserId($id_usuario)
                ->addConsolidado(1)
                ->addMes(date('m'))
                ->addAno(date('Y'))
                ->addTableCategorias()
                ->addIdCategoria($dadosOrcamento['categorias_id'])
                ->addTipo('d')
                ->getTotais();

            $limiteOrcamento = 80 * $totalOrcamento / 100;
            $dadosUsuario = (new UsuarioModel())->getById($dadosOrcamento['usuarios_id']);
            if ($totalDespesasMesAtual > $limiteOrcamento) {
                $email = \Config\Services::email();
                $dadosView = [
                    'usuario' => $dadosUsuario['nome'],
                    'categoria' => (new CategoriaModel())->getById($dadosOrcamento['categorias_id'])['descricao'],
                    'total_categoria' => $totalDespesasMesAtual,
                    'nome_orcamento' => $dadosOrcamento['descricao'],
                    'total_orcamento' => $totalOrcamento,
                    'limite_orcamento' => $limiteOrcamento
                ];
                $mensagem = view('_common/emails/notifica_orcamento_ultrapassado', $dadosView);
                $email->setTo($dadosUsuario['email']);
                $email->setSubject('GFASS - Notificação de Orçamento Ultrapassado - ' . date('d/m/Y H:i:s'));
                $email->setMessage($mensagem);
                if (!$email->send()) {
                    log_message('critical', "ERRO ao enviar e-mail de notificação de orçamento ultrapassado. {$email->printDebugger('headers')}");
                }
            }
        }

        return $data;
    }
    
    
    public function update_lancamento($id, $data) {
		return $this->db
                        ->table('lancamentos')
                        ->where(["id" => $id])
                        ->set($data)
                        ->update();
	}
    
    public function salvar($dados){
        
  
        
        $user= new LancamentoModel();
$user->insert($dados);
        $id = $user->getInsertID();
        return $id;
        
        
    }

    /**
     * Retorna todas os lançamentos vinculados a uma categoria
     *
     * @param [type] $id_categoria
     * @return void
     */
    public function getByIdCategoria($id_categoria)
    {
        $this->select("        
        lancamentos.id as id_lancamento,
        lancamentos.created_at,
                    lancamentos.anexo as anexo,

        lancamentos.usuarios_id,
        categorias.tipo,
        if (tipo = 'r', 'Receita', 'Despesa') as tipo_formatado,
        lancamentos.descricao,
        lancamentos.data,
        lancamentos.categorias_id,
        notificar_por_email, 
        lancamentos.valor,
        lancamentos.chave,
        consolidado,
        if (consolidado = 1, 'Sim', 'Não') as consolidado_formatado,
        if (notificar_por_email = 1, 'Sim', 'Não') as notificar_formatado
        ");
        $this->where('categorias_id', $id_categoria);
        $this->join('categorias', 'categorias.id = lancamentos.categorias_id and categorias.deleted_at IS NULL');
        return $this->findAll();
    }

    /**
     * Retorna a soma dos lançamentos
     *
     * @return float
     */
    public function getTotais(): float
    {
        $this->selectSum('valor');
        $result = $this->first();

        return !is_null($result['valor']) ? $result['valor'] : 0.00;
    }

    /**
     * Injeta a tabela categorias quando necessário.
     *
     * @return object
     */
    public function addTableCategorias(): object
    {
        $this->join('categorias', 'categorias.id = lancamentos.categorias_id');
        $this->where('categorias.deleted_at IS NULL');
        return $this;
    }

    /**
     * Retorna o ano do lançamento mais antigo
     * Se não encontrar nada, retorna o ano atual.
     *
     * @return void
     */
    public function getMenorAno()
    {
        $result = $this
            ->select('MIN(YEAR(data)) as menor_ano')
            ->first();

        return !is_null($result['menor_ano']) ? $result['menor_ano'] : date('Y');
    }

    /**
     * Calcula o saldo do mês anterior usando o parâmetro como data de refência. 
     * Começando pelo lançamento mais antigo.
     *
     * @param string $data
     * @return float
     */
    public function getSaldoAnterior(string $data = null): float
    {
        $dataReferencia  = new DateTime($data);

        $id_usuario = session()->id_usuario;

        $dataAnterior = $dataReferencia->modify('last day of last month')->format('Y-m-d');
        $dataInicial = $this->addUserId($id_usuario)->getMenorAno() . "-01-01";

        $this->selectSum('valor');
        $this->where("data BETWEEN '{$dataInicial}' AND '{$dataAnterior}'");
        $this->where('tipo', 'd');
        $this->join('categorias', 'categorias.id = lancamentos.categorias_id AND categorias.deleted_at IS NULL');
        $this->where('consolidado', 1);
        $this->addUserId($id_usuario);
        $totalDespesas = (float) $this->first()['valor'];

        $this->selectSum('valor');
        $this->where("data BETWEEN '{$dataInicial}' AND '{$dataAnterior}'");
        $this->where('tipo', 'r');
        $this->join('categorias', 'categorias.id = lancamentos.categorias_id AND categorias.deleted_at IS NULL');
        $this->where('consolidado', 1);
        $this->addUserId($id_usuario);
        $totalReceitas = (float) $this->first()['valor'];

        $saldo = $totalReceitas - $totalDespesas;
        return !is_null($saldo) ? $saldo : 0.00;
    }

    /**
     * Retorna os registros pela sua data.
     *
     * @param [type] $data
     * @return object
     */
    public function addData($data)
    {
        return $this->where('data', $data);
        return $this;
    }
}
