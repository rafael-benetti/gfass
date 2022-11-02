<?php

namespace App\Controllers\Api;

use App\Models\UsuarioModel;
use Lcobucci\JWT\Parser;

class Grafico extends BaseResource
{
    protected $modelName = 'App\Models\LancamentoModel';
    protected $format    = 'json';

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->request = \Config\Services::request();

        $tokenRecebido = $this->request->getHeaderLine('Authorization');
        $token = (new Parser())->parse((string) $tokenRecebido);
        $this->id_usuario = $token->getClaim('uid');;
    }

    /**
     * Retorna um json com o total de lançamentos de despesas e receitas
     *
     * @return void
     */
    public function index()
    {
        helper('funcoes');
        $mes = date('m');
        $ano = date('Y');
        $dados = [];
        $totalDespesas = $this->model
            ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
            ->addConsolidado(true)
            ->addTableCategorias()
            ->addTipo('d')
            ->addMes($mes)
            ->addAno($ano)
            ->getTotais();

        $totalReceitas = $this->model
            ->addWhereIn('lancamentos.usuarios_id', $this->getIdsDaFamilia())
            ->addConsolidado(true)
            ->addTableCategorias()
            ->addTipo('r')
            ->addMes($mes)
            ->addAno($ano)
            ->getTotais();

        $dados = [
            'total_despesas' => $totalDespesas,
            'total_receitas' => $totalReceitas,
            'mes' => nomeMes($mes),
            'ano' => $ano
        ];

        return json_encode([
            'data' => $dados
        ], JSON_PRETTY_PRINT);
    }
}
