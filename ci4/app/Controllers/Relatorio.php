<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\LancamentoModel;
use App\Models\OrcamentoModel;
use Dompdf\Dompdf;

class Relatorio extends BaseController
{
    protected $categoriaModel;
    protected $lancamentoModel;
    protected $orcamentoModel;

    public function __construct()
    {
        $this->categoriaModel = new  CategoriaModel();
        $this->lancamentoModel = new LancamentoModel();
        $this->orcamentoModel = new OrcamentoModel();
    }

    /**
     * Carrega a view principal.
     *
     * @return void
     */
    public function index()
    {
        $data = [
            'totalLancamentos' => 0,
            'dropDownCategorias' => $this->categoriaModel->addOrder([
                'campo' => 'descricao',
                'sentido' => 'asc'
            ])->addUserId($this->session->id_usuario)->formDropDown(),
        ];

        echo view('relatorio/index', $data);
    }

    /**
     * Carrega a view principal
     *
     * @return void
     */
    public function getDados()
    {

        helper('text');
        $getDados = $this->request->getGet();

        $descricao = $getDados['descricao'] ?: null;
        $categorias_id = $getDados['categorias_id'] ?: null;
        $dataInicial = !empty($getDados['dataInicial']) ?  toDataEUA($getDados['dataInicial']) : null;
        $dataFinal = !empty($getDados['dataFinal']) ?  toDataEUA($getDados['dataFinal']) : null;
        $tipo = $getDados['tipo'] ?: null;
        $consolidado = $getDados['consolidado'] ?: null;
        $tipoByCategoria = $this->categoriaModel->getTipoByCategoria($categorias_id);

        $dataInicial = !is_null($dataInicial) ? "'{$dataInicial}'" : null;
        $dataFinal = !is_null($dataFinal) ? "'{$dataFinal}'" : null;


        if (!is_null($descricao)) {
            $this->categoriaModel
                ->groupStart()
                ->addSearch($descricao, 'categorias.descricao', true)
                ->addSearch($descricao, 'lancamentos.descricao', true)
                ->groupEnd();
        }
        $categorias  = $this->categoriaModel
            ->addUserId($this->session->id_usuario)
            ->addIdCategoria($categorias_id)
            ->addConsolidado($consolidado)
            ->addTipo($tipo)
            ->addDatas($dataInicial, $dataFinal)
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
            if (!is_null($descricao)) {
                $this->lancamentoModel
                    ->groupStart()
                    ->addSearch($descricao, 'categorias.descricao', true)
                    ->addSearch($descricao, 'lancamentos.descricao', true)
                    ->groupEnd();
            }
            $lancamentos = $this->lancamentoModel
                ->addTipo($tipo)
                ->addUserId($this->session->id_usuario)
                ->addConsolidado($consolidado)
                ->addDatas($dataInicial, $dataFinal)
                ->getByIdCategoria($categoria['id_categoria']);


            if (!is_null($descricao)) {
                $this->lancamentoModel
                    ->groupStart()
                    ->addSearch($descricao, 'categorias.descricao', true)
                    ->addSearch($descricao, 'lancamentos.descricao', true)
                    ->groupEnd();
            }

            $totalPorCategoria = $this->lancamentoModel
                ->addTipo($tipo)
                ->addUserId($this->session->id_usuario)
                ->addConsolidado($consolidado)
                ->addDatas($dataInicial, $dataFinal)
                ->addTableCategorias()
                ->addIdCategoria($categoria['id_categoria'])
                ->getTotais();


            $resultCategorias[] = [
                'descricao' => $categoria['descricao_categoria'],
                'lancamentos' => $lancamentos,
                'totalPorCategoria' => $totalPorCategoria
            ];
            $totalLancamentos += count($lancamentos);
        }


        $tipo = !is_null($tipoByCategoria) ? $tipoByCategoria : $tipo;

        $totalReceitas = $totalDespesas = 0;

        if ($tipo == 'r' || empty($tipo)) {
            if (!is_null($descricao)) {
                $this->lancamentoModel
                    ->groupStart()
                    ->addSearch($descricao, 'categorias.descricao', true)
                    ->addSearch($descricao, 'lancamentos.descricao', true)
                    ->groupEnd();
            }
            $totalReceitas = $this->lancamentoModel
                ->addUserId($this->session->id_usuario)
                ->addTableCategorias()
                ->addIdCategoria($categorias_id)
                ->addConsolidado($consolidado)
                ->addTipo('r')
                ->addDatas($dataInicial, $dataFinal)
                ->getTotais();
        } else {
            $totalReceitas = 0.00;
        }

        if ($tipo == 'd' || empty($tipo)) {
            if (!is_null($descricao)) {
                $this->lancamentoModel
                    ->groupStart()
                    ->addSearch($descricao, 'categorias.descricao', true)
                    ->addSearch($descricao, 'lancamentos.descricao', true)
                    ->groupEnd();
            }

            $totalDespesas = $this->lancamentoModel
                ->addUserId($this->session->id_usuario)
                ->addTableCategorias()
                ->addIdCategoria($categorias_id)
                ->addConsolidado($consolidado)
                ->addTipo('d')
                ->addDatas($dataInicial, $dataFinal)
                ->getTotais();
        } else {
            $totalDespesas = 0.00;
        }

        $dados = [
            'dropDownCategorias' => $this->categoriaModel->addOrder([
                'campo' => 'descricao',
                'sentido' => 'asc'
            ])->addUserId($this->session->id_usuario)->formDropDown(),
            'categorias' =>  $resultCategorias,
            'totalReceitas' => $totalReceitas,
            'totalDespesas' => $totalDespesas,
            'saldo' => (float) $totalReceitas - (float) $totalDespesas,
            'totalLancamentos' => $totalLancamentos,
            'consolidado' => $consolidado,
            'dataInicial' => $dataInicial ? toDataBR(strip_quotes($dataInicial)) : $dataInicial,
            'dataFinal' => $dataFinal ? toDataBR(strip_quotes($dataFinal)) : $dataFinal,
            'descricao' => $descricao,
            'categorias_id' => $categorias_id,
            'tipo' => $tipo

        ];

        if (isset($getDados['tipo_impressao'])) {
            $view = view('relatorio/output', $dados);
            if ($getDados['tipo_impressao'] === 'pdf') {
                $nomeArquivo = 'relatorio - ' . date('d-m-Y-H-i-s') . '.pdf';
                $dompdf = new Dompdf();
                $dompdf->loadHtml($view);
                $dompdf->render();
                $dompdf->stream($nomeArquivo, ['Attachment' => true]);
            }
            if ($getDados['tipo_impressao'] === 'csv') {
                return $this->geraCSV($dados);
            }
        } else {
            echo view('relatorio/index', $dados);
        }
    }

    /**
     * Gera um arquivo CSV para download.
     *
     * @param [type] $dados
     * @return void
     */
    protected function geraCSV($dados)
    {
        helper('filesystem');
        $nl = PHP_EOL;
        $result = 'DESCRIÇÃO;DATA;TIPO DE LANÇAMENTO;CONSOLIDADO?;VALOR' . $nl;
        foreach ($dados['categorias'] as $categoria) {
            $result .= $categoria['descricao'] . $nl;
            foreach ($categoria['lancamentos'] as $lancamento) {
                $result .=
                    $lancamento['descricao'] . ';'
                    . toDataBR($lancamento['data']) . ';'
                    . $lancamento['tipo_formatado'] . ';'
                    . $lancamento['consolidado_formatado'] . ';'
                    . number_format($lancamento['valor'], 2, ',', '.') .
                    $nl;
            }
            $result .=  ';' . ';' . ';' . 'Subtotal: ' . ';' . number_format($categoria['totalPorCategoria'], 2, ',', '.') . $nl;
        }
        $result .= "Total de Receitas: " . ';' . number_format($dados['totalReceitas'], 2, ',', '.') . $nl;
        $result .= "Total de Despesas: " . ';' . number_format($dados['totalDespesas'], 2, ',', '.') . $nl;
        $result .= "Saldo: " . ';' . number_format($dados['saldo'], 2, ',', '.');
        if (!file_exists(WRITEPATH . '/relatorios')) {
            mkdir(WRITEPATH . '/relatorios');
        }
        $nomeArquivo = 'relatorio - ' . date('d-m-Y-H-i-s') . '.csv';
        $path = WRITEPATH . "/relatorios/{$nomeArquivo}";
        if (!write_file($path, $result)) {
            return redirect()->to('/mensagem/erro')->with('mensagem', 'ERRO - Não foi possível gravar o arquivo.');
        } else {
            return $this->response->download($path, null, true);
        }
    }
}
