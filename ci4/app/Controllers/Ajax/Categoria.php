<?php

namespace App\Controllers\Ajax;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;

class Categoria extends BaseController
{

    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * Salva a categoria recebida via ajax no banco.
     *
     * @return void
     */
    public function store()
    {
        $result = [];
        if ($this->request->isAJAX()) {
            $post = $this->request->getPost();
            if ($this->categoriaModel->save($post)) {
                $result = [
                    'error' => false,
                    'code' => 201,
                    'message' => 'created',
                    'id' => $this->categoriaModel->getInsertID()
                ];
            } else {
                $result = [
                    'error' => true,
                    'message' => $this->categoriaModel->errors()
                ];
            }
        } else {
            $result = [
                'error' => true,
                'code' => 400,
                'message' => '[ERRO] - Somente requisições AJAX são permitidas'
            ];
        }
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    /**
     * Retorna todas as categorias baseadas em seu tipo.
     *
     * @return void
     */
    public function get()
    {
        if ($this->request->isAJAX()) {
            $result = [];
            $tipo = $this->request->getGet('tipo');
            if (!is_null($tipo)) {
                $this->categoriaModel->addTipo($tipo);
            }

            $result = $this->categoriaModel
                ->addUserId($this->session->id_usuario)
                ->addOrder([
                    'campo' => 'descricao',
                    'sentido' => 'asc'
                ])
                ->getAll();
        } else {
            $result = [
                'error' => true,
                'code' => 400,
                'message' => '[ERRO] - Somente requisições AJAX são permitidas'
            ];
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
