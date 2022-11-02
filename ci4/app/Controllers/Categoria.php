<?php

namespace App\Controllers;

use App\Models\CategoriaModel;

class Categoria extends BaseController
{

    protected $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * Carrega a view principal
     *
     * @return void
     */
    public function index()
    {

        $search = $this->request->getGet('search');

        $categorias = $this->categoriaModel
            ->addUserId($this->session->id_usuario)
            ->addSearch($search, 'descricao')
            ->addOrder(
                [
                    'order' => [
                        [
                            'campo' => 'tipo',
                            'sentido' => 'desc'
                        ],
                        [
                            'campo' => 'descricao',
                            'sentido' => 'asc'
                        ]
                    ]
                ]
            )
            ->paginate(10);


        $data = [
            'categorias' => $categorias,
            'totalCategorias' => $this->categoriaModel->addUserId()->getAll(),
            'pager' => $this->categoriaModel->pager,
            'search' => $search
        ];

        echo view('categorias/index', $data);
    }

    /**
     * Chama o formulário de criação
     *
     * @return void
     */
    public function create()
    {
        $data = [
            'titulo' => 'Nova categoria'
        ];
        echo view('categorias/form', $data);
    }

    /**
     * Salva os dados vindos do formulário.
     *
     * @return void
     */
    public function store()
    {
        $post = $this->request->getPost();

        if ($this->categoriaModel->save($post)) {
            return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                'mensagem' => 'Registro salvo com sucesso',
                'link' => [
                    'to' => 'categoria',
                    'texto' => 'Voltar para Categorias'
                ]
            ]);
        } else {
            echo view('categorias/form', [
                'titulo' => !empty($post['chave']) ? 'Editar Categoria' : 'Nova categoria',
                'errors' => $this->categoriaModel->errors()
            ]);
        }
    }

    /**
     * Chama o formulário de edição com os campos populados.
     *
     * @param [type] $chave
     * @return void
     */
    public function edit($chave)
    {

        $categoria = $this->categoriaModel->addUserId($this->session->id_usuario)->getByChave($chave);

        if (!is_null($categoria)) {
            $data = [
                'titulo' => 'Editar Categoria',
                'categoria' => $categoria
            ];
            echo view('categorias/form', $data);
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', [
                'mensagem' => 'Categoria não encontrada',
                'link' => [
                    'to' => 'categoria',
                    'texto' => 'Voltar para Categorias'
                ]
            ]);
        }
    }

    /**
     * Exclui um registro do banco
     *
     * @param [type] $chave
     * @return void
     */
    public function delete($chave = null)
    {
        if ($this->categoriaModel->addUserId($this->session->id_usuario)->delete($chave)) {
            return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                'mensagem' => 'Categoria excluída com sucesso.',
                'link' => [
                    'to' => 'categoria',
                    'texto' => 'Voltar para Categorias'
                ]
            ]);
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', [
                'mensagem' => 'Erro ao excluir a categoria',
                'link' => [
                    'to' => 'categoria',
                    'texto' => 'Voltar para Categorias'
                ]
            ]);
        }
    }
}
