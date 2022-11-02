<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\LancamentoModel;
use App\Models\OrcamentoModel;
use App\Models\UsuarioModel;

class Lancamento extends BaseController {

    protected $categoriaModel;
    protected $lancamentoModel;
    protected $orcamentoModel;
    protected $email;

    public function __construct() {
        $this->categoriaModel = new CategoriaModel();
        $this->lancamentoModel = new LancamentoModel();
        $this->orcamentoModel = new OrcamentoModel();
        $this->email = \Config\Services::email();
    }

    /**
     * Carrega a view principal
     *
     * @return void
     */
    public function index($mes = null, $ano = null) {
        $this->lancamentoModel->select('descricao')->selectCount('categorias_id')->groupBy('descricao')->findAll();

        $post = $this->request->getPost();

        $ano = empty($post['ano']) ? (empty($ano) ? date("Y") : $ano) : $post['ano'];
        $mes = empty($post['mes']) ? (empty($mes) ? date("m") : $mes) : $post['mes'];

        $search = $this->request->getGet('search') ?: '';

        if (empty($search)) {
            $this->categoriaModel->addMes($mes)->addAno($ano);
        }

        $categorias = $this->categoriaModel
                ->groupStart()
                ->addSearch($search, 'categorias.descricao', true)
                ->addSearch($search, 'lancamentos.descricao', true)
                ->groupEnd()
                ->addUserId($this->session->id_usuario)
                ->addOrder([
                    'order' => [
                        [
                            'campo' => 'tipo',
                            'sentido' => 'desc'
                        ],
                        [
                            'campo' => 'categorias.descricao',
                            'sentido' => 'asc'
                        ]
                    ]
                ])
                ->getComLancamentos();


        $resultCategorias = [];
        $totalLancamentos = 0;
        //Agora, para cada categoria, eu busco os seus respectivos lançamentos
        foreach ($categorias as $categoria) {
            if (empty($search)) {
                $this->lancamentoModel->addMes($mes)->addAno($ano);
            }
            $lancamentos = $this->lancamentoModel
                    ->groupStart()
                    ->addSearch($search, 'categorias.descricao', true)
                    ->addSearch($search, 'lancamentos.descricao', true)
                    ->groupEnd()
                    ->getByIdCategoria($categoria['id_categoria']);

            $valorOrcamento = $this->orcamentoModel
                    ->addUserId($this->session->id_usuario)
                    ->valorOrcamento($categoria['id_categoria']);



            if (empty($search)) {
                $this->lancamentoModel->addMes($mes)->addAno($ano);
            }
            $totalPorCategoria = $this->lancamentoModel
                    ->addUserId($this->session->id_usuario)
                    ->addConsolidado(1)
                    ->addIdCategoria($categoria['id_categoria'])
                    ->addTableCategorias()
                    ->getTotais();


            $resultCategorias[] = [
                'descricao' => $categoria['descricao_categoria'],
                'lancamentos' => $lancamentos,
                'totalPorCategoria' => $totalPorCategoria,
                'valorOrcamento' => $valorOrcamento,
                'orcamentoDisponivel' => (float) $valorOrcamento - (float) $totalPorCategoria
            ];
            $totalLancamentos += count($lancamentos);
        }

        $receitasTotalGeral = $this->lancamentoModel
                ->addConsolidado(1)
                ->addUserId($this->session->id_usuario)
                ->addTipo('r')
                ->addTableCategorias()
                ->getTotais();

        $despesasTotalGeral = $this->lancamentoModel
                ->addConsolidado(1)
                ->addUserId($this->session->id_usuario)
                ->addTipo('d')
                ->addTableCategorias()
                ->getTotais();
        $saldoTotalGeral = (float) $receitasTotalGeral - (float) $despesasTotalGeral;


        $receitasMesAtual = $despesasMesAtual = $saldoAnterior = 0;
        if (empty($search)) {
            $receitasMesAtual = $this->lancamentoModel
                    ->addUserId($this->session->id_usuario)
                    ->addConsolidado(1)
                    ->addMes($mes)
                    ->addAno($ano)
                    ->addTipo('r')
                    ->addTableCategorias()
                    ->getTotais();

            $despesasMesAtual = $this->lancamentoModel
                    ->addUserId($this->session->id_usuario)
                    ->addConsolidado(1)
                    ->addMes($mes)
                    ->addAno($ano)
                    ->addTipo('d')
                    ->addTableCategorias()
                    ->getTotais();
            $dataReferencia = date('Y-m-t', strtotime("$ano-$mes-01"));
            $saldoAnterior = $this->lancamentoModel->getSaldoAnterior($dataReferencia);
        }


        $dados = [
            'mes' => !is_null($mes) ? $mes : date("m"),
            'ano' => !is_null($ano) ? $ano : date("Y"),
            'comboAnos' => comboAnos([
                'ano_inicial' => $this->lancamentoModel->addUserId($this->session->id_usuario)->getMenorAno()
            ]),
            'categorias' => $resultCategorias,
            'receitasTotalGeral' => $receitasTotalGeral,
            'despesasTotalGeral' => $despesasTotalGeral,
            'saldoTotalGeral' => $saldoTotalGeral,
            'receitasMesAtual' => $receitasMesAtual,
            'despesasMesAtual' => $despesasMesAtual,
            'saldoMesAtual' => (float) $receitasMesAtual - (float) $despesasMesAtual + $saldoAnterior,
            'saldoAnterior' => $saldoAnterior,
            'totalLancamentos' => $totalLancamentos,
            'search' => $search
        ];


        echo view('lancamentos/index', $dados);
    }

    /**
     * Carrega o formulário de novo lançamento
     *
     * @return void
     */
    public function create() {
        
        
         $data = [
            'titulo' => 'Novo Lançamento',
            'dropDownCategorias' => $this->categoriaModel
                    ->addUserId($this->session->id_usuario)
                    ->addOrder([
                        'campo' => 'descricao',
                        'sentido' => 'asc'
                    ])
                    ->formDropDown(
                            [
                                'opcaoNova' => true
                            ]
                    )
        ];

        echo view('lancamentos/form', $data);
    }

    /**
     * Armazena o lançamento
     *
     * @return void
     */
    public function store() {
        $post = $this->request->getPost();
        $dataLancamento = strtotime(toDataEUA($post['data']));
        $hoje = strtotime(date('Y-m-d'));
        /**
         * Se a data de lançamento for futura, então eu marco consolidado como não.
         */
        if ($dataLancamento > $hoje) {
            $post['consolidado'] = 2;
        }

        if($post['chave']!=''){
            
            $this->lancamentoModel->save($post);
        }
     


        if ($id_lancamento = $this->lancamentoModel->salvar($post)) {
            
          //  echo $id_lancamento; exit;
            
            $mensagem = 'Registro salvo com sucesso, comprovante nao enviado';
            
            if ($_FILES["images"]['name']!='') {

         

            $filepath = $_FILES['images']['tmp_name'];
            $fileSize = filesize($filepath);
            $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
            $filetype = finfo_file($fileinfo, $filepath);

            if ($fileSize === 0) {
               $mensagem = 'Registro salvo com sucesso,Arquyivo vazio';
            }

            if ($fileSize > 5145728) { // 3 MB (1 byte * 1024 * 1024 * 3 (for 3 MB))
               $mensagem = 'Registro salvo com sucesso, maior que 5mb';
            }

            $allowedTypes = [
                'image/png' => 'png',
                'image/png' => 'PNG',
                'image/jpeg' => 'jpg',
                'image/jpeg' => 'JPG',
                'image/jpeg' => 'jpeg',
                'image/jpeg' => 'JPEG'
            ];

            if (!in_array($filetype, array_keys($allowedTypes))) {
                $mensagem = 'Registro salvo com sucesso, arquivo nao liberado';
            }
            
            

            $extension = $allowedTypes[$filetype];
            $filename = date('dmYHs').'_anexo.'.$extension;
            $targetDirectory = $_SERVER['DOCUMENT_ROOT'].'/upload/comprovantes'; // __DIR__ is the directory of the current PHP file
            $newFilepath = $targetDirectory . "/" . $filename;

            if (copy($filepath, $newFilepath)) { // Copy the file, returns false if failed
                
               
                    $data_lancamento = [
			'anexo'		=> $filename
		];

		$this->lancamentoModel->update_lancamento($id_lancamento, $data_lancamento);
                    
                    
               }
            
            }



            /*
             *  $mensagem = 'Registro salvo com sucesso, comprovante nao enviado';
             *  $lancamento= new LancamentoModel();
              $lancamento->where('id', $id_lancamento);
              $lancamento->update(array('anexo'=>$config['file_name']));
             */


            return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                        'mensagem' => $mensagem,
                        'link' => [
                            'to' => 'lancamento',
                            'texto' => 'Voltar para Lançamentos'
                        ]
            ]);
        } else {
            echo view('lancamentos/form', [
                'titulo' => !empty($post['chave']) ? 'Editar Lançamento' : 'Novo Lançamento',
                'errors' => $this->lancamentoModel->erros(),
                'dropDownCategorias' => $this->categoriaModel
                        ->addUserId($this->session->id_usuario)
                        ->addOrder([
                            'campo' => 'descricao',
                            'sentido' => 'asc'
                        ])
                        ->formDropDown(
                                [
                                    'opcaoNova' => true
                                ]
                        )
            ]);
        }
    }

    function uploadFiles() {
        helper(['form', 'url']);
        $database = \Config\Database::connect();
        $db = $database->table('lancamentos');
        $msg = 'Please select a valid files';
        if ($this->request->getFileMultiple('images')) {
            foreach ($this->request->getFileMultiple('images') as $file) {
                $file->move(WRITEPATH . 'uploads');
                $data = [
                    'anexo' => $file->getClientName(),
                    'type' => $file->getClientMimeType()
                ];
                $save = $db->insert($data);
                $msg = 'Files have been successfully uploaded';
            }
        }
    }

    public function edit($chave) {
        
        
      

	//$lancamento = 	$this->lancamentoModel->update_lancamento($chave, $post);
        $lancamento = $this->lancamentoModel->addUserId($this->session->id_usuario)->getByChave($chave);
        
        
        
        
        if (!is_null($lancamento)) {
            echo view('lancamentos/form', [
                'titulo' => 'Editar Lançamento',
                'lancamento' => $lancamento,
                'dropDownCategorias' => $this->categoriaModel
                        ->addUserId($this->session->id_usuario)
                        ->addOrder([
                            'campo' => 'descricao',
                            'sentido' => 'asc'
                        ])
                        ->formDropDown(
                                [
                                    'opcaoNova' => true
                                ]
                        )
            ]);
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', [
                        'mensagem' => 'ERRO - Lançamento não encontrado',
                        'link' => [
                            'to' => 'lancamento',
                            'texto' => 'Voltar para Lançamentos',
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
    public function delete($chave = null) {
        if ($this->lancamentoModel->addUserId($this->session->id_usuario)->delete($chave)) {
            return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                        'mensagem' => 'Lançamento excluído com sucesso.',
                        'link' => [
                            'to' => 'lancamento',
                            'texto' => 'Voltar para Lançamentos'
                        ]
            ]);
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', [
                        'mensagem' => 'Erro ao excluir o lançamento',
                        'link' => [
                            'to' => 'lancamento',
                            'texto' => 'Voltar para Lançamentos'
                        ]
            ]);
        }
    }

}
