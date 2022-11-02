<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\OrcamentoModel;

class Orcamento extends BaseController
{

    protected $orcamentoModel;
    protected $categoriaModel;

    public function __construct()
    {
        $this->orcamentoModel = new OrcamentoModel();
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

        $orcamentos = $this->orcamentoModel
            ->addSearch($search, 'orcamentos.descricao')
            ->addUserId($this->session->id_usuario)
            ->addOrder([
                'campo' => 'orcamentos.descricao',
                'sentido' => 'asc'
            ])
            ->getAllWithCategorias();

        $data = [
            'orcamentos' => $orcamentos,
            'search' => $search
        ];

        echo view('orcamentos/index', $data);
    }

    /**
     * Carrega a view do formulário
     *
     * @return void
     */
    public function create()
    {
        $data = [
            'titulo' => 'Novo orçamento',
            'formDropDown' => $this->categoriaModel->addOrder([
                'campo' => 'descricao',
                'sentido' => 'asc'
            ])->addUserId($this->session->id_usuario)->formDropDown([
                'opcaoNova' => true,
                'tipo' => 'd'
            ])
        ];
        echo view('orcamentos/form', $data);
    }

    /**
     * Salva um registro no banco.
     * Se a chave vier junto, atualiza.
     *
     * @return void
     */
    public function store()
    {
        $post = $this->request->getPost();
        if ($this->orcamentoModel->save($post)) {
            return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                'mensagem' => "Orçamento salvo com sucesso",
                'link' => [
                    'to' => 'orcamento',
                    'texto' => 'Voltar para Orçamentos'
                ]
            ]);
        } else {
            $dados = [
                'titulo' => !empty($post['chave']) ? 'Editar Orçamento' : 'Novo Orçamento',
                'errors' => $this->orcamentoModel->errors(),
                'formDropDown' => $this->categoriaModel->addOrder([
                    'campo' => 'descricao',
                    'sentido' => 'asc'
                ])->formDropDown([
                    'tipo' => 'd',
                    'opcaoNova' => true
                ])
            ];
            echo view('orcamentos/form', $dados);
        }
    }

    /**
     * Carrega o form de edição de orçamentos já com os campos populados.
     *
     * @param [type] $chave
     * @return void
     */
    public function edit($chave)
    {
        $orcamento = $this->orcamentoModel->addUserId($this->session->id_usuario)->getByChave($chave);
        if (!is_null($orcamento)) {
            echo view('orcamentos/form', [
                'titulo' => 'Editar Orçamento',
                'orcamento' => $orcamento,
                'formDropDown' => $this->categoriaModel->addOrder([
                    'campo' => 'descricao',
                    'sentido' => 'asc'
                ])->formDropDown([
                    'tipo' => 'd',
                    'opcaoNova' => true
                ])
            ]);
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', [
                'mensagem' => 'Orçamento não encontrado',
                'link' => [
                    'to' => 'orcamento',
                    'texto' => 'Voltar para Orçamentos'
                ]
            ]);
        }
    }

    /**
     * Remove um registro.
     *
     * @param [type] $chave
     * @return void
     */
    public function delete($chave)
    {
        if ($this->orcamentoModel->addUserId($this->session->id_usuario)->delete($chave)) {
            return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                'mensagem' => "Orçamento excluído com sucesso",
                'link' => [
                    'to' => 'orcamento',
                    'texto' => 'Voltar para Orçamentos'
                ]
            ]);
        }
    }








    public function exemplo()
    {
        $usuarioModel = new UsuarioModel();

        $usuarioModel->query("SELECT * FROM TABELA");
    }
}
