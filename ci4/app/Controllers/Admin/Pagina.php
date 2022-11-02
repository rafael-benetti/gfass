<?php

namespace App\Controllers\Admin;

use App\Models\MetodoModel;
use App\Models\PaginaModel;

class Pagina extends BaseController
{

    protected $paginaModel;
    protected $metodoModel;

    public function __construct()
    {
        $this->paginaModel = new PaginaModel();
        $this->metodoModel = new MetodoModel();
    }

    /**
     * Chama a view principal
     *
     * @return void
     */
    public function index()
    {

        $data = [
            'paginas' => $this->paginaModel
                ->addOrder([
                    'campo' => 'id',
                    'sentido' => 'asc'
                ])
                ->getAll()
        ];

        echo view('admin/paginas/index', $data);
    }


    /**
     * Chama a view de criação de nova página.
     *
     * @return void
     */
    public function create()
    {
        echo view('admin/paginas/form', [
            'titulo' => 'Nova página'
        ]);
    }

    /**
     * Chama view de edição de registro
     *
     * @param [type] $chave
     * @return void
     */
    public function edit(string $chave)
    {
        $pagina = $this->paginaModel->getByChave($chave);
        $metodosCompleto = [];
        if (!is_null($pagina)) {
            //Verifo se a página possui métodos cadastrados.
            $path = "\App\Controllers\\" . $pagina['nome_classe'];
            $className = class_exists($path);
            if ($className) {
                $classMethods = get_class_methods($path);
                $metodosIndesejados = ['__construct', 'initController', 'forceHTTPS', 'cachePage', 'loadHelpers', 'validate'];
                $metodos = array_diff($classMethods, $metodosIndesejados);

                foreach ($metodos as $metodo) {
                    $metodosCompleto[] = [
                        'nome_metodo' => $metodo,
                        'nome_amigavel' => $this->metodoModel->getNomeAmigavel($pagina['id'], $metodo)['nome_amigavel']
                    ];
                }

                echo view('admin/paginas/form', [
                    'titulo' => 'Editar Página',
                    'chave' => $chave,
                    'pagina' => $pagina,
                    'metodos' => $metodosCompleto
                ]);
            }
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', 'Página não encontrada');
        }
    }

    /**
     * Salva no banco as páginas e seus respectivos métodos.
     *
     * @return void
     */
    public function store()
    {
        $post = $this->request->getPost();
        // dd($post);
        if ($this->paginaModel->save($post)) {
            //Se for uma edição
            if (!empty($post['chave'])) {
                $dadosPagina = $this->paginaModel->getByChave($post['chave']);
                if (!is_null($dadosPagina)) {
                    $idPagina = $dadosPagina['id'];
                }
                //Removo todos os métodos antes de inseri-los novamente.
                //Mas somente se estiverem vindo novos métodos do formulário
                if (isset($post['metodos'])) {
                    if ($this->metodoModel->deleteByPaginasId($idPagina)) {
                        foreach ($post['metodos'] as $nome_metodo => $nome_amigavel) {
                            //Se não for informado um nome_amigavel, pula o registro.
                            //Assim, somente os métodos que possuem nome_amgiavel é que vão ser cadastrados.
                            if (empty($nome_amigavel)) {
                                continue;
                            }
                            $dadosMetodos = [
                                'paginas_id' => $idPagina,
                                'nome_metodo' => $nome_metodo,
                                'nome_amigavel' => $nome_amigavel
                            ];
                            $this->metodoModel->insert($dadosMetodos);
                        }
                    } else {
                        return redirect()->to('/mensagem/erro')->with('mensagem', 'Não foi possível excluir os métodos antes de inseri-los novamente.');
                    }
                }
                return redirect()->to("/admin/pagina/{$post['chave']}/edit")->with('mensagem', 'Registro salvo com sucesso.'); //Chama o controller paginas
            } else {
                $idPagina = $this->paginaModel->getInsertID();
                $chavePagina = $this->paginaModel->getById($idPagina)['chave'];
                return redirect()->to("/admin/pagina/{$chavePagina}/edit")->with('mensagem', 'Registro salvo com sucesso');
            }
        } else {
            echo view('admin/paginas/form', [
                'titulo' => !empty($post['chave']) ? 'Editar Página' : 'Nova Página',
                'chave' => $post['chave'],
                'errors' => $this->paginaModel->errors()
            ]);
        }
    }

    /**
     * Apaga um registro pela sua chave
     *
     * @param [type] $chave
     * @return void
     */
    public function delete($chave)
    {
        if ($this->paginaModel->delete($chave)) {
            return redirect()->to("/admin/pagina/")->with('mensagem', 'Registro excluído com sucesso');
        }
    }
}
