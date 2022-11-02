<?php

namespace App\Models;

use App\Libraries\Security;
use CodeIgniter\Model;

class BaseModel extends Model
{

    private $idUsuarioApi;
    private $idsFilhosApi;

    protected $afterFind = [
        'escapeXSS'
    ];

    /**
     * Seta os ids filhos para a propriedade idsFihosapi
     *
     * @param array $idsFilhos
     * @return void
     */
    public function setIdsFilhosApi(array $idsFilhos)
    {
        $this->idsFilhosApi = $idsFilhos;
    }

    /**
     * Injeta o id do usuário quando a requisição vier da api.
     *
     * @param [type] $id
     * @return void
     */
    public function setIdUsuarioApi($id)
    {
        $this->idUsuarioApi = $id;
    }


    /**
     * Gera um token para o usuário confirmar o email futuramente.
     *
     * @param [type] $data
     * @return void
     */
    protected function geraToken($data)
    {
        $data['data']['token_confirmacao_email'] = md5(uniqid(rand(), true));
        return $data;
    }

    /**
     * Vincula o id_usuario do usuário logado no momento no campo usuario_id da tabela.
     *
     * @param [type] $data
     * @return void
     */
    protected function vinculaIdUsuario($data)
    {

        $data['data']['usuarios_id'] = $this->idUsuarioApi ?: session()->id_usuario;

        return $data;
    }

    /**
     * Gera uma chave randômica e vincula ao campo chave da tabela.
     *
     * @param [type] $data
     * @return void
     */
    protected function geraChave($data)
    {
        $data['data']['chave'] = md5(uniqid(rand(), true));
        return $data;
    }

    /**
     * Verifica se o registro sendo excluído ou atualizado pertence ao seu dono ou a algum membro de sua família.
     *
     * @param [type] $data
     * @return void
     */
    protected function checaPropriedade($data)
    {
        $request = \Config\Services::request();

        if ($request->isCLI()) {
            return $data;
        }

        if (!isset($data['data']['chave'])) {
            return $data;
        }

        $ids_filhos = $this->idsFilhosApi ?: session()->ids_filhos;

        $idProprietario = $this->getByChave($data['data']['chave'])['usuarios_id'];
        if ($idProprietario != session()->id_usuario) {
            if (!in_array($idProprietario, $ids_filhos)) {
                if ($this->idsFilhosApi) {
                    echo json_encode([
                        'error' => true,
                        'message' => '[PROIBIDO] - O registro sendo alterado não pertence ao usuário solicitante ou a algum membro de sua família.'
                    ], JSON_PRETTY_PRINT);
                } else {
                    session()->setFlashdata('mensagem', '[PROIBIDO] - O registro sendo alterado não pertence ao usuário logado ou a algum membro de sua família.');
                    header("Location: /mensagem/erro");
                }
                die();
            }
        }

        return $data;
    }

    /**
     * Converte a data para o formato americano
     *
     * @param [type] $data
     * @return void
     */
    protected function converteData($data)
    {

        helper('funcoes');

        if (!isset($data['data']['data'])) {
            return $data;
        }

        $data['data']['data'] = toDataEUA($data['data']['data']);
        return $data;
    }

    /**
     * Faz a conversão do valor padrão brasileiro para americano
     *
     * @param [type] $data
     * @return void
     */
    protected function corrigeValor($data)
    {
        if (!isset($data['data']['valor'])) {
            return $data;
        }

        $data['data']['valor'] = str_replace('.', '', $data['data']['valor']);
        $data['data']['valor'] = str_replace(',', '.', $data['data']['valor']);

        return $data;
    }

    /**
     * Chama o método que atualiza os ids filhos na sessão.
     *
     * @return void
     */
    protected function updateIdsFilhos()
    {
        Security::updateIDsFilhos();
    }


    protected function escapeXSS($data)
    {

        $data = esc($data);

        return $data;
    }

    ##################################################################
    ### MÉTODOS PÚBLICOS ###
    ##################################################################    




    /**
     * Injeta a busca por datas na query
     *
     * @param [type] $dataInicial
     * @param [type] $dataFinal
     * @return object
     */
    public function addDatas($dataInicial = null, $dataFinal = null): object
    {
        if (!is_null($dataInicial) && !is_null($dataFinal)) {
            $this->where("data BETWEEN {$dataInicial} AND {$dataFinal}");
        } elseif (!is_null($dataInicial)) {
            $this->where("data >= {$dataInicial}");
        } elseif (!is_null($dataFinal)) {
            $this->where("data <= {$dataFinal}");
        }

        return $this;
    }

    /**
     * Injeta o campo categorias_id na query
     *
     * @param [type] $id_categoria
     * @return object
     */
    public function addIdCategoria($id_categoria = null): object
    {
        if (!is_null($id_categoria)) {
            $this->where('categorias_id', $id_categoria);
        }

        return $this;
    }


    /**
     * Injeta o campo mês na query
     *
     * @param [type] $mes
     * @return object
     */
    public function addMes($mes = null): object
    {
        if (!is_null($mes)) {
            $this->where("MONTH(data)", $mes);
        }
        return $this;
    }

    /**
     * Injeta o campo ano na query
     *
     * @param [type] $ano
     * @return object
     */
    public function addAno($ano = null): object
    {
        if (!is_null($ano)) {
            $this->where("YEAR(data)", $ano);
        }
        return $this;
    }

    /**
     * Retorna os registros baseados na informação de consolidação.
     * É preciso que a tabela lançamentos exista na query para usar este método.
     * 1 para sim, 2 para não.
     *
     * @param integer $value
     * @return object
     */
    public function addConsolidado(int $value = null): object
    {
        if (!is_null($value)) {
            $this->where('lancamentos.consolidado', $value);
        }
        return $this;
    }

    /**
     * Injeta o campo tipo na query de busca
     *
     * @param [type] $tipo
     * @return object
     */
    public function addTipo($tipo = null): object
    {
        if (!is_null($tipo)) {
            $this->where('tipo', $tipo);
        }
        return $this;
    }

    /**
     * Injeta a busca por chave dentro da query
     *
     * @param string $chave
     * @return mixed
     */
    public function getByChave(string $chave = null)
    {
        if (!is_null($chave)) {
            return $this->find($chave);
        }
    }

    /**
     * Retorna os dados pelo id
     *
     * @param [type] $id
     * @return void
     */
    public function getById($id = null)
    {
        return $this->where('id', $id)->first();
    }

    /**
     * Retorna todos os registros.
     *
     * @return array
     */
    public function getAll(): array
    {
        return $this->findAll();
    }

    /**
     * Injeta o campo order na query
     *
     * @param array $order
     * @return object
     */
    public function addOrder(array $order = null): object
    {
        if (!is_null($order)) {
            if (key_exists('order', $order)) {
                foreach ($order['order'] as $ordem) {
                    $this->orderBy($ordem['campo'], $ordem['sentido']);
                }
            } else {
                $this->orderBy($order['campo'], $order['sentido']);
            }
        }
        return $this;
    }

    /**
     * Injeta o campo id_usuario na query.
     *
     * @param integer $id_usuario
     * @return object
     */
    public function addUserId(int $idPai = null): object
    {
        $idUsuarioPai = !is_null($idPai) ? $idPai : session()->id_usuario_pai;

        if (!is_null($idUsuarioPai)) {
            $this->whereIn("{$this->table}.usuarios_id", session()->ids_filhos);
        }
        return $this;
    }

    /**
     * Injeta a busca por like na query.
     * Se o parâmetro or for true, faz a busca por orLike
     *
     * @param string $search
     * @param string $campo
     * @param [type] $or
     * @return object
     */
    public function addSearch(string $search = null, string $campo = null, $or = null): object
    {
        if (!is_null($campo) && !is_null($search)) {
            if (!is_null($or)) {
                $this->orLike($campo, $search);
            } else {
                $this->like($campo, $search);
            }
        }
        return $this;
    }

    /** 
     * Insere a cláusula groupBy
     */
    public function addGroupBy($campo = null)
    {
        $this->groupBy($campo);
        return $this;
    }

    /**
     * Insere a cláusula que verifica se o usuário deseja ser notificado por emai.
     *
     * @param [type] $valor
     * @return void
     */
    public function addNotificarPorEmail($valor)
    {
        $this->where('notificar_por_email', $valor);
        return $this;
    }

    /**
     * Insere a cláusula whereIn na query.
     *
     * @param [type] $campo
     * @param array $valores
     * @return object
     */
    public function addWhereIn($campo, array $valores)
    {
        $this->whereIn($campo, $valores);
        return $this;
    }
}
