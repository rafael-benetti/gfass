<?php

namespace App\Controllers\Api;

use App\Models\UsuarioModel;

use Lcobucci\JWT\Parser;

// Especificações:
// https://jsonapi.org/
class Categoria extends BaseResource
{
    protected $modelName = 'App\Models\CategoriaModel';
    protected $format    = 'json';
    protected $request;
    protected $id_usuario;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->request = \Config\Services::request();

        $tokenRecebido = $this->request->getHeaderLine('Authorization');
        $token = (new Parser())->parse((string) $tokenRecebido);
        $this->id_usuario = $token->getClaim('uid');;
    }

    //get
    //Recupero todos os registros que fazem parte da família do usuário solicitante.
    public function index()
    {
        $categorias = $this->model
            ->orderBy('tipo', 'desc')
            ->orderBy('descricao', 'asc')
            ->addWhereIn('usuarios_id', $this->getIdsDaFamilia())
            ->findAll();
        return json_encode([
            'data' => $categorias
        ], JSON_PRETTY_PRINT);
    }

    //get/(:segment)
    //Mostra um registro pela sua chave
    //É verificado também se o registro pertence a um membro da família.
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
            return $this->respond(json_encode([
                'data' => $rq
            ], JSON_PRETTY_PRINT));
        } else {
            return $this->failNotFound('Registro não encontrado');
        }
    }

    /**
     * post
     * Cria um novo registro.
     */
    public function create()
    {
        $dados = json_decode($this->request->getBody(), true);
        $this->model->setIdUsuarioApi($this->id_usuario);
        if ($this->model->save($dados)) {
            $idCategoria = $this->model->getInsertID();
            $categoria = $this->model->getById($idCategoria);
            $result = json_encode([
                'data' => $categoria
            ], JSON_PRETTY_PRINT);
            return $this->respondCreated($result);
        } else {
            return $this->fail($this->model->errors());
        }
    }

    /**
     * Atualiza um registro
     * put/(:segment)
     *
     * @param [type] $chave
     * @return void
     */
    public function update($chave = null)
    {
        $dados = json_decode($this->request->getBody(), true);
        if (is_null($chave)) {
            return $this->failNotFound('Chave não fornecida');
        }
        $dados['chave'] = $chave;
        $this->model->setIdsFilhosApi($this->getIdsDaFamilia());
        if ($this->model->save($dados)) {
            $categoria = $this->model->getByChave($dados['chave']);
            $result = json_encode([
                'data' => $categoria
            ], JSON_PRETTY_PRINT);
            return $this->respond($result);
        } else {
            return $this->fail($this->model->errors());
        }
    }

    /**
     * Apaga um registro
     * delete/(:segment)
     * @param [type] $chave
     * @return void
     */
    public function delete($chave = null)
    {
        return $this->model->delete($chave) ? $this->respondDeleted('Registro excluído.') : $this->fail('Não foi possível excluir o registro.');
    }
}
