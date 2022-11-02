<?php

namespace App\Controllers;



use App\Models\CategoriaModel;
use App\Models\LancamentoModel;
use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class Cron extends Controller
{

    protected $lancamentoModel;
    protected $usuarioModel;
    protected $categoriaModel;
    protected $email;

    public function __construct()
    {

        $this->lancamentoModel = new LancamentoModel();
        $this->usuarioModel = new UsuarioModel();
        $this->categoriaModel = new CategoriaModel();

        // $this->email = \Config\Services::email();
    }

    /**
     * Chama os métodos desejados
     *
     * @return void
     */
    public function index()
    {

        $this->notificaLancamentoPorEmail();
        $this->consolidaLancamentos();
    }


    /**
     * Consolida os lançamentos do dia
     *
     * @return void
     */
    public function consolidaLancamentos()
    {
        $lancamentos = $this->lancamentoModel
            ->addConsolidado(2)
            ->addData(date('Y-m-d'))
            ->getAll();
        if (count($lancamentos) > 0) {
            foreach ($lancamentos as $lancamento) {
                $this->lancamentoModel->save([
                    'chave' => $lancamento['chave'],
                    'consolidado' => true
                ]);
            }
        } else {
            echo "Sem lançamentos a consolidar na data de hoje.";
        }
    }

    /**
     * Notifica os usuários sobre o lançamento consolidado caso o usuário tenha solicitado isto.
     *
     * @return void
     */
    public function notificaLancamentoPorEmail()
    {
        $email = \Config\Services::email();
        //Primeiro. recupero todos os usuários que possuem lançamentos não consolidados na data de hoje.
        $getUsuariosComLancamentosANotificar = $this->lancamentoModel
            ->select('usuarios_id')
            ->addGroupBy('usuarios_id')
            ->addData(date('Y-m-d'))
            ->addNotificarPorEmail(1)
            ->getAll();

        if (count($getUsuariosComLancamentosANotificar) > 0) {
            $arraysId = array_column($getUsuariosComLancamentosANotificar, 'usuarios_id');
            $usuarios = $this->usuarioModel->addWhereIn('id', $arraysId)->getAll();
            foreach ($usuarios as $usuario) {
                $getCategorias = $this->categoriaModel
                    ->addOrder([
                        'campo' => 'descricao',
                        'sentido' => 'asc'
                    ])
                    ->getByIdUsuario($usuario['id']);
                if (count($getCategorias) === 0) {
                    continue;
                }

                $result = [];
                foreach ($getCategorias as $categoria) {
                    $getLancamentos = $this->lancamentoModel
                        ->addOrder([
                            'campo' => 'descricao',
                            'sentido' => 'asc'
                        ])
                        ->addData(date('Y-m-d'))
                        ->addNotificarPorEmail(1)
                        ->getByIdCategoria($categoria['id']);

                    if (count($getLancamentos) === 0) {
                        continue;
                    }
                    $result[] = [
                        $categoria['descricao'] => $getLancamentos
                    ];
                }
                $dados = [
                    'usuario' => $usuario['nome'],
                    'categorias' => $result
                ];

                $conteudo = view('_common/emails/cron_notificacao_consolidacao_lancamentos', $dados);
                $email->setTo($usuario['email']);
                $email->setSubject('GFASS - Notificação de Lançamentos Consolidados - ' . date('d/m/Y H:i:s'));
                $email->setMessage($conteudo);
                if ($email->send(false)) {
                    echo 'Email enviado com sucesso para: ' . $usuario['email'] . PHP_EOL;
                } else {
                    log_message('critical', 'ERRO ao enviar a mensagem para: ' . $usuario['email'] . ' - ' . $email->printDebugger('headers'));
                    echo 'Erro ao enviar email: ' . $usuario['email'] . ' - ' . $email->printDebugger('headers');
                }
            }
        } else {
            echo 'Nenhum lançamento para notificar.\n' . PHP_EOL;
        }
    }

    /**
     * Envia o email para o destinatário.
     *
     * @param [type] $email
     * @param [type] $conteudo
     * @return void
     */
    // protected function enviaEmail($email, $conteudo)
    // {

    //     if (empty($email)) {
    //         return false;
    //     }
    //     $this->email->setTo($email);
    //     $this->email->setSubject('GFASS - Notificação de Lançamentos Consolidados - ' . date('d/m/Y H:i:s'));
    //     $this->email->setMessage($conteudo);
    //     return $this->email->send();
    // }
}
