<?php

namespace App\Controllers\Api;

class Usuario extends BaseResource
{
    protected $modelName = 'App\Models\UsuarioModel';
    protected $format    = 'json';

    //post
    //Cria um novo usuário
    public function create()
    {
        return $this->failNotFound('Não implementado');
    }

    public function index()
    {
        return $this->failNotFound('Não implementado');
    }

    /**
     * Mosrta um usuário pela sua chave
     * get/(:segment)
     * @param [type] $chave
     * @return void
     */
    public function show($chave = null)
    {
        $rq = $this->model->getByChave($chave);
        unset($rq['senha']);
        if ($rq) {
            echo json_encode([
                'data' => $rq
            ], JSON_PRETTY_PRINT);
        } else {
            return $this->failNotFound("Usuário não encontrado");
        }
    }
    //get/(:segment)/edit
    //Edita um usuário pelo seu id
    public function edit($id = null)
    {
        return $this->failNotFound('Não implementado');
    }

    //put/(:segment)
    //Atualiza um usuário.
    public function update($id = null)
    {
        return $this->failNotFound('Não implementado');
    }

    //delete/(:segment)
    public function delete($chave = null)
    {
        return $this->failNotFound('Não implementado');
    }
}
