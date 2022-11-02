<?php

namespace App\Controllers;

use App\Models\MetodoModel;
use App\Models\PaginaModel;
use App\Models\PerfilModel;
use App\Models\PermissaoModel;

class Perfil extends BaseController
{
    protected $perfilModel;
    protected $paginaModel;
    protected $permissaoModel;
    protected $metodoModel;

    public function __construct()
    {
        $this->perfilModel = new PerfilModel();
        $this->paginaModel = new PaginaModel();
        $this->permissaoModel = new PermissaoModel();
        $this->metodoModel = new MetodoModel();
    }

    /**
     * Carrega a view principal
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'perfis' => $this->perfilModel->addUserId($this->session->id_usuario)->addOrder(['campo' => 'perfis.descricao', 'sentido' => 'asc'])->getAll()
        ];
        echo view('perfis/index', $data);
    }

    /**
     * Chama a view de cadastro
     *
     * @return void
     */
    public function create()
    {
        $paginas = $this->paginaModel->getAll();
        $result = [];
        foreach ($paginas as $pagina) {
            $result[] = [
                'paginas_id'    => $pagina['id'],
                'nome_amigavel' => $pagina['nome_amigavel'],
                'nome_classe'   => $pagina['nome_classe'],
                'metodos'       => $this->metodoModel->getByPaginasId($pagina['id']),
                'regras'        => '',
                'id_permissao'  => '',
            ];
        }

        echo view('perfis/form', [
            'tiulo' => 'Novo Perfil',
            'paginas' => $result
        ]);
    }

    /**
     * Chama a view de edição com os campos populados.
     *
     * @param [type] $chave
     * @return void
     */
    public function edit($chave = null)
    {
        $perfil = $this->perfilModel->getByChave($chave);
        $result = [];
        if (!is_null($perfil)) {
            $paginas = $this->paginaModel->getAll();
            foreach ($paginas as $pagina) {
                $permissoes = $this->permissaoModel->getByIdPaginaAndIdPerfil($pagina['id'], $perfil['id']);
                if (count($permissoes) > 0) {
                    foreach ($permissoes as $permissao) {
                        $result[] = [
                            'paginas_id' => $pagina['id'],
                            'nome_amigavel' => $pagina['nome_amigavel'],
                            'nome_classe' => $pagina['nome_classe'],
                            'metodos' => $this->metodoModel->getByPaginasId($pagina['id']),
                            'regras' => $permissao['regras'],
                            'id_permissao' => $permissao['id']
                        ];
                    }
                } else {
                    $result[] = [
                        'paginas_id' => $pagina['id'],
                        'nome_amigavel' => $pagina['nome_amigavel'],
                        'nome_classe' => $pagina['nome_classe'],
                        'metodos' => $this->metodoModel->getByPaginasId($pagina['id']),
                        'regras' => '',
                        'id_permissao' => ''
                    ];
                }
            }
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', 'Perfil não encontrado');
        }

        $data = [
            'titulo' => 'Edição de Perfil',
            'paginas' => $result,
            'perfil' => $perfil,
            'chave' => $chave
        ];
        // dd($data);

        echo view('perfis/form', $data);
    }

    public function store()
    {
        $post = $this->request->getPost();
        // dd($post);
        $this->perfilModel->transStart();

        if ($this->perfilModel->save($post)) {
            if (empty($post['chave'])) {
                $idPerfil = $this->perfilModel->getInsertID();
            } else {
                $idPerfil = $this->perfilModel->getByChave($post['chave'])['id'];
            }
            //Insiro as regras para cada página na tabela permissões
            foreach ($post['permissoes'] as $id_pagina => $regras) {
                if (array_key_exists('id_permissao', $regras) && !empty($regras['id_permissao'])) {
                    foreach ($regras['id_permissao'] as $id_permissao => $regrasEdicao) {
                        $data = [
                            'id' => $id_permissao,
                            'paginas_id' => $id_pagina,
                            'perfis_id' => $idPerfil,
                            'regras' => implode(',', $regrasEdicao)
                        ];
                    }
                } else {
                    $data = [
                        'paginas_id' => $id_pagina,
                        'perfis_id' => $idPerfil,
                        'regras' => implode(',', $regras)
                    ];
                }
                $this->permissaoModel->save($data);
            }
            $this->perfilModel->transComplete();
            $mensagem = !empty($chave) ? 'Registro atualizado com sucesso.' : 'Registro cadastrado com sucesso.';
            return redirect()->to('/perfil')->with('mensagem', $mensagem);
        } else {
            $paginas = $this->paginaModel->getAll();
            foreach ($paginas as $pagina) {
                $result[] = [
                    'paginas_id'    => $pagina['id'],
                    'nome_amigavel' => $pagina['nome_amigavel'],
                    'nome_classe'   => $pagina['nome_classe'],
                    'metodos'       => $this->metodoModel->getByPaginasId($pagina['id']),
                    'regras'        => '',
                    'id_permissao'  => '',
                ];
            }
            echo view('perfis/form', [
                'titulo' => !empty($post['chave']) ? 'Editar Perfil' : 'Novo Perfil',
                'paginas' => $result,
                'errors' => $this->perfilModel->errors()
            ]);
        }
    }

    /**
     * Apaga um registro pela sua chave.
     *
     * @param [type] $chave
     * @return void
     */
    public function delete($chave)
    {
        if ($this->perfilModel->addUserId($this->session->id_usuario)->delete($chave)) {
            return redirect()->to('/perfil')->with('mensagem', 'Registro excluído com sucesso.');
        }
    }
}
