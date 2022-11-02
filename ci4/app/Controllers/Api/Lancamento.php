<?php

namespace App\Controllers\Api;


use App\Models\UsuarioModel;
use Lcobucci\JWT\Parser;

class Lancamento extends BaseResource
{

    protected $request;
    protected $modelName = 'App\Models\LancamentoModel';
    protected $format    = 'json';
    protected $id_usuario;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->request = \Config\Services::request();

        $tokenRecebido = $this->request->getHeaderLine('Authorization');
        $token = (new Parser())->parse((string) $tokenRecebido);
        $this->id_usuario = $token->getClaim('uid');
    }

    //get
    //traz todos os registros
    //Recupero todos os registros que fazem parte da família do usuário solicitante
    public function index()
    {
        $dados = [];

        $categorias = $this->model
            ->select('categorias_id, categorias.descricao as descricao_categoria, categorias.chave as chave_categoria')
            ->join('categorias', 'categorias.id = lancamentos.categorias_id')
            ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
            ->orderBy('categorias.tipo', 'desc')
            ->orderBy('categorias.descricao')
            ->groupBy('categorias_id')
            ->findAll();

        foreach ($categorias as $categoria) {
            $lancamentos = $this->model
                ->select('
                    lancamentos.descricao, 
                    lancamentos.id,
                    lancamentos.chave, 
                    lancamentos.valor,
                    lancamentos.data,
                    lancamentos.categorias_id,
                    lancamentos.consolidado,
                    lancamentos.notificar_por_email,
                    lancamentos.usuarios_id,
                    lancamentos.created_at,
                    tipo
                    ')
                ->orderBy('data', 'desc')
                ->orderBy('descricao')
                ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
                ->join('categorias', 'categorias.id = lancamentos.categorias_id')
                ->where('categorias_id', $categoria['categorias_id'])
                ->findAll();

            $dados[] = [
                'descricao' => $categoria['descricao_categoria'],
                'lancamentos' => $lancamentos
            ];
        }

        return $this->respond(
            json_encode([
                'data' => $dados
            ], JSON_PRETTY_PRINT)
        );
    }

    //get/(:segment)
    //Mostra um registro pela sua chave
    //É verificado também se o registro pertence ao um membro da família
    public function show($chave = null)
    {
        $rq = $this->model->getByChave($chave);
        if ($rq) {
            $idDonoRegistro = $rq['usuarios_id'];
            if ($idDonoRegistro != $this->id_usuario) {
                if (!in_array($idDonoRegistro, $this->getIdsDaFamilia())) {
                    return $this->failForbidden("Este registro não pertence a você e a nenhum membro de sua família.");
                }
            }
            return $this->respond(
                json_encode([
                    'data' => $rq
                ], JSON_PRETTY_PRINT)
            );
        } else {
            return $this->failNotFound('Lançamento não encontrado');
        }
    }

    /**
     * Retorna os lançamentos baseados no mês e anos informados
     *
     * @param [type] $mes
     * @param [type] $ano
     * @return void
     */
    public function getByData($mes = null, $ano = null)
    {
        $dados = [];

        $categorias = $this->model
            ->select('categorias_id, categorias.descricao as descricao_categoria, categorias.chave as chave_categoria')
            ->join('categorias', 'categorias.id = lancamentos.categorias_id')
            ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
            ->orderBy('categorias.tipo', 'desc')
            ->orderBy('categorias.descricao')
            ->groupBy('categorias_id')
            ->findAll();

        foreach ($categorias as $categoria) {
            $lancamentos = $this->model
                ->select('
                    lancamentos.descricao, 
                    lancamentos.id,
                    lancamentos.chave, 
                    lancamentos.valor,
                    lancamentos.data,
                    lancamentos.categorias_id,
                    lancamentos.consolidado,
                    lancamentos.notificar_por_email,
                    lancamentos.usuarios_id,
                    lancamentos.created_at,
                    tipo
                    ')
                ->orderBy('data', 'desc')
                ->orderBy('descricao')
                ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
                ->join('categorias', 'categorias.id = lancamentos.categorias_id')
                ->where('categorias_id', $categoria['categorias_id'])
                ->addMes($mes)
                ->addAno($ano)
                ->findAll();
            //Se não retornar nenhum lançamento naquela categoria, pula para o próximo lançamento
            if (count($lancamentos) == 0) {
                continue;
            }

            $totalPorCategoria = $this->model
                ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
                ->addConsolidado(1)
                ->addIdCategoria($categoria['categorias_id'])
                ->addTableCategorias()
                ->addMes($mes)
                ->addAno($ano)
                ->getTotais();

            $dados[] = [
                'descricao' => $categoria['descricao_categoria'],
                'total_categoria' => $totalPorCategoria,
                'lancamentos' => $lancamentos
            ];
        }

        return $this->respond(
            json_encode([
                'data' => $dados
            ], JSON_PRETTY_PRINT)
        );
    }

    //post
    //Cria um novo registro
    public function create()
    {
        $dados = json_decode($this->request->getBody(), true);

        $this->model->setIdUsuarioApi($this->id_usuario);
        if ($this->model->save($dados)) {
            $idLancamento = $this->model->getInsertID();
            $lancamento = $this->model->getById($idLancamento);
            $result = json_encode([
                'data' => $lancamento
            ], JSON_PRETTY_PRINT);
            return $this->respondCreated($result);
        } else {
            return $this->fail($this->model->errors());
        }
    }

    //put/(:segment)
    //Atualiza um registro.
    public function update($chave = null)
    {
        $dados = json_decode($this->request->getBody(), true);
        if (is_null($chave)) {
            return $this->failNotFound('Chave não fornecida');
        }
        $dados['chave'] = $chave;
        $this->model->setIdsFilhosApi($this->getIdsDaFamilia());
        if ($this->model->save($dados)) {
            $lancamento = $this->model->getByChave($dados['chave']);
            $result = json_encode([
                'data' => $lancamento
            ], JSON_PRETTY_PRINT);
            return $this->respond($result);
        } else {
            return $this->fail($this->model->errors());
        }
    }

    //delete/(:segment)
    public function delete($chave = null)
    {
        return $this->model->delete($chave) ? $this->respondDeleted('Registro excluído.') : $this->fail('Não foi possível excluir o registro.');
    }
}
