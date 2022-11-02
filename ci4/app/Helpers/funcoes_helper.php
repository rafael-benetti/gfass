<?php

use CodeIgniter\I18n\Time;

/**
 * Retorna o nome do mês a partir do seu número.
 * Se o segundo parâmetro for true, retorna o nome abreviado.
 */

function nomeMes($numero_mes, $abreviado = false)
{
    switch ($numero_mes) {
        case '01':
            $mes = $abreviado ? 'Jan' : 'Janeiro';
            break;
        case '02':
            $mes = $abreviado ? 'Fev' : 'Fevereiro';
            break;
        case '03':
            $mes = $abreviado ? 'Mar' : 'Março';
            break;
        case '04':
            $mes = $abreviado ? 'Abr' : 'Abril';
            break;
        case '05':
            $mes = $abreviado ? 'Mai' : 'Maio';
            break;
        case '06':
            $mes = $abreviado ? 'Jun' : 'Junho';
            break;
        case '07':
            $mes = $abreviado ? 'Jul' : 'Julho';
            break;
        case '08':
            $mes = $abreviado ? 'Ago' : 'Agosto';
            break;
        case '09':
            $mes = $abreviado ? 'Set' : 'Setembro';
            break;
        case '10':
            $mes = $abreviado ? 'Out' : 'Outubro';
            break;
        case '11':
            $mes = $abreviado ? 'Nov' : 'Novembro';
            break;
        case '12':
            $mes = $abreviado ? 'Dez' : 'Dezembro';
            break;
    }

    return strtoupper($mes);
}

/**
 * Converte uma data em formato EUA para formato Brasileiro
 * Se o segundo parâmetro for true, retorna também com a hora
 *
 * @param [type] $data
 * @param boolean $mostrar_hora
 * @return void
 */
function toDataBR($data, $mostrar_hora = false)
{
    return $mostrar_hora ? date('d/m/Y H:i:s', strtotime($data)) :  date('d/m/Y', strtotime($data));
}

/**
 * Converte uma data no formatd d/m/Y para EUA: Y-m-d
 *
 * @param [type] $data
 * @return void
 */
function toDataEUA($data)
{
    return Time::createFromFormat('d/m/Y', $data)->toDateString();
}

/**
 * Retorna uma array de anos para ser usada dentro de um formDropdow
 * O ano inicia-se através do parâmetro informado.
 *
 * @param array $params
 * @return void
 */
function comboAnos(array $params = null)
{
    $ano_inicial = $params['ano_inicial'];
    $ano_final = date("Y");

    $result = [];
    while ($ano_inicial <= $ano_final) {
        $result += [
            $ano_inicial => $ano_inicial
        ];
        $ano_inicial++;
    }

    return $result;
}

/**
 * Verifica se a página e o método informado estão liberados para o usuário logado.
 *
 * @param [type] $pagina
 * @param array $metodo
 * @return void
 */
function checkLinkPermission($pagina = null, $metodo = [])
{
    if (!session()->isLoggedIn) {
        return ' d-none ';
    } else {
        if (!session()->isUsuarioPai) {
            $regras = session()->regras;
            if (!array_key_exists($pagina, $regras)) {
                return ' disabled ';
            } elseif (!in_array($metodo, $regras[$pagina])) {
                return ' disabled ';
            }
        }
    }
}
