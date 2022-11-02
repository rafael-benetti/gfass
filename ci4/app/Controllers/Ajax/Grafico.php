<?php

namespace App\Controllers\Ajax;

use App\Controllers\BaseController;
use App\Models\CategoriaModel;
use App\Models\LancamentoModel;

class Grafico extends BaseController
{
    protected $categoriaModel;
    protected $lancamentoModel;

    public function __construct()
    {
        $this->categoriaModel  = new CategoriaModel();
        $this->lancamentoModel = new LancamentoModel();
    }
    public function getPorAno()
    {
        $mes = date('m');
        $ano = date('Y');

        $result = [];
        if ($this->request->isAJAX()) {
            for ($mes = 1; $mes <= 12; $mes++) {
                $lancamentos[] = [
                    'mes' => nomeMes($mes, true),
                    'totalReceitas' => $this->lancamentoModel
                        ->addUserId($this->session->id_usuario)
                        ->addMes($mes)
                        ->addAno($ano)
                        ->addTableCategorias()
                        ->addTipo('r')
                        ->addConsolidado(true)
                        ->getTotais(),
                    'totalDespesas' => $this->lancamentoModel
                        ->addUserId($this->session->id_usuario)
                        ->addMes($mes)
                        ->addAno($ano)
                        ->addTableCategorias()
                        ->addTipo('d')
                        ->addConsolidado(true)
                        ->getTotais()
                ];
            }


            $cols = [];
            $rows = [];

            $cols = [
                [
                    'id' => '',
                    'lable' => 'Mês',
                    'pattern' => '',
                    'type' => 'string'
                ],
                [
                    'id' => '',
                    'label' => 'Total Receitas',
                    'pattern' => '',
                    'type' => 'number'
                ],
                [
                    'id' => '',
                    'label' => 'Total Despesas',
                    'pattern' => '',
                    'type' => 'number'
                ]
            ];

            foreach ($lancamentos as $lancamento) {
                $rows[] = [
                    'c' => [
                        [
                            'v' => $lancamento['mes'],
                            'f' => null
                        ],
                        [
                            'v' => floatVal($lancamento['totalReceitas']),
                            'f' => null
                        ],
                        [
                            'v' => floatVal($lancamento['totalDespesas']),
                            'f' => null
                        ]
                    ]
                ];
            }

            $result = [
                'cols' => $cols,
                'rows' => $rows
            ];
        } else {
            $result = [
                'error'   => true,
                'code'    => 400,
                'message' => '[ERROR] - Only AJAX requests allowed',
            ];
        }
        echo json_encode($result, JSON_PRETTY_PRINT);
    }

    public function getPorCategoria()
    {
        $result = [];
        if ($this->request->isAJAX()) {

            $resultCategorias = [];
            $post = $this->request->getPost();

            $ano = empty($post['ano']) ? date("Y") : $post['ano'];
            $mes = empty($post['mes']) ? date("m") : $post['mes'];

            $categorias = $this->categoriaModel
                ->addConsolidado(true)
                ->addMes($mes)
                ->addAno($ano)
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

            foreach ($categorias as $categoria) {

                $totalPorCategoria = $this->lancamentoModel
                    ->addIdCategoria($categoria['id_categoria'])
                    ->addUserId($this->session->id_usuario)
                    ->addConsolidado(true)
                    ->addMes($mes)
                    ->addAno($ano)
                    ->getTotais();

                $resultCategorias[] = [
                    'descricao' => $categoria['descricao_categoria'],
                    'tipo' => $categoria['tipo'],
                    'totalPorCategoria' => $totalPorCategoria
                ];
            }

            $cols = [];
            $rows = [];

            $cols = [
                [
                    'id' => '',
                    'label' => 'Categoria',
                    'pattern' => '',
                    'type' => 'string'
                ],
                [
                    'id' => '',
                    'label' => 'Valor',
                    'pattern' => '',
                    'type' => 'number'
                ],
                [
                    'type' => 'string',
                    'p' => [
                        'role' => 'style'
                    ]
                ],
                [
                    'type' => 'string',
                    'p' => [
                        'role' => 'annotation'
                    ]
                ]
            ];

            foreach ($resultCategorias as $result) {
                $cellCollor = $result['tipo'] == 'r' ? '#009933' : '#f00';
                $rows[] = [
                    'c' =>  [
                        [
                            'v' => $result['descricao'],
                            'f' => null
                        ],
                        [
                            'v' => floatVal($result['totalPorCategoria']),
                            'f' => null
                        ],
                        [
                            'v' => $cellCollor
                        ],
                        [
                            'v' => 'R$ ' . number_format(floatVal($result['totalPorCategoria']), 2, ',', '.')
                        ]

                    ]
                ];
            }

            $result = [
                'cols' => $cols,
                'rows' => $rows
            ];
        } else {
            $result = [
                'error'   => true,
                'code'    => 400,
                'message' => '[ERROR] - Only AJAX requests allowed',
            ];
        }
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
