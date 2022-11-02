<?php

namespace App\Controllers;

use App\Controllers\Admin\BaseController;
use App\Models\TRSModel;
use App\Models\UsuarioModel;


class Cadastro extends BaseController
{

    protected $usuarioModel;
    protected $trsModel;
    protected $validation;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->trsModel     = new TRSModel();
        $this->validation   = \Config\Services::validation();
    }

    /**
     * Caso o controller seja chamado sem o método, redireciona para a página inicial.
     *
     * @return void
     */
    public function index()
    {
        return redirect()->to(base_url());
    }

    public function store()
    {

        $post = $this->request->getPost();

        $validationRules = [
            'nome'          => [
                'label'  => 'nome',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Campo {field} obrigatório',
                ],
            ],
            'email'         => [
                'label'  => "E-mail",
                'rules'  => "required|valid_email|is_unique[usuarios.email]",
                'errors' => [
                    'required'    => 'Campo {field} obrigatório',
                    'valid_email' => 'Este e-mail: {value} parece ter um formato inválido',
                    'is_unique'   => 'O e-mail {value} já está sendo utilizado',
                ],
            ],
            'senha' => [
                'label'  => 'Senha',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Campo {field} obrigatório',
                ],
            ],
            'senha_confirm' => [
                'label'  => 'Repita a Senha',
                'rules'  => 'required|matches[senha]',
                'errors' => [
                    'required' => 'Campo {field} obrigatório',
                ],
            ],
        ];

        $this->validation->setRules($validationRules);

        if ($this->validation->withRequest($this->request)->run()) {
            //Removo o conteúdo do campo email_confirmado por segurança
            unset($post['email_confirmado']);
            $this->usuarioModel->transBegin();
            if ($this->usuarioModel->save($post)) {
                // $dadosUsuario = $this->usuarioModel->getByEmail($post['email']);
                $dadosUsuario = $this->usuarioModel->getById($this->usuarioModel->getInsertID());

                $email = \Config\Services::email();
                $email->setTo($post['email']);
                $email->setSubject('Por favor, confirme seu e-mail.');
                $mensagem = view('_common/emails/novo_usuario_externo_notificao', [
                    'usuario' => $post['nome'],
                    'token_confirmacao' => $dadosUsuario['token_confirmacao_email']
                ]);
                $email->setMessage($mensagem);
                if ($email->send()) {
                    $this->usuarioModel->transCommit();
                    $email->clear();

                    return redirect()->to('/login/index')->with('mensagem', "Cadastro recebido com sucesso.<br />Confirme seu e-mail clicando na mensagem enviada para: {$post['email']}.");
                } else {
                    $this->usuarioModel->transRollback();
                    log_message('error', $email->printDebugger(['headers']));
                    return redirect()->to('/login/index')->with('mensagem', "Erro ao enviar o e-mail. Por favor, entre em contato com o administrador do sistema.");
                }
            } else {
                return redirect()->to('/mensagem/erro')->with('mensagem', "Erro. Não foi possível cadastrá-lo no banco de dados.");
            }
        } else {
            echo view('login/index', [
                'errors' => $this->validation->getErrors()
            ]);
        }
    }

    /**
     * Método de confirmação de email do usuário.
     *
     * @param [type] $token
     * @return void
     */
    public function confirm($token)
    {
        $dadosUsuario = $this->usuarioModel->getByTokenEmail($token);
        if (is_null($dadosUsuario)) {
            return redirect()->to('/mensagem/erro')->with('mensagem', '[ERRO] - Token não encontrado. Entre em contato com o administrador do sistema: rafael@asaisurf.com.br');
        } else {
            if ($dadosUsuario['email_confirmado'] == true) {
                return redirect()->to('/mensagem/sucesso')->with('mensagem', "Seu e-mail já foi confirmado anteriormente. Obrigado.");
            } else {
                $dadosUsuario['email_confirmado'] = true;
                unset($dadosUsuario['senha']);
                if ($this->usuarioModel->save($dadosUsuario)) {
                    $link = base_url();
                    return redirect()->to('/mensagem/sucesso')->with('mensagem', "Seu e-mail foi confirmado com sucesso. Clique <a href={$link}>aqui</a> para acessar o sistema.");
                }
            }
        }
    }
    /**
     * Chama a view para relembrar a senha
     *
     * @return void
     */
    public function esqueciSenha()
    {
        echo view('cadastro/esqueci_senha');
    }

    /**
     * Localizar o usuário no sistema através do email.
     * Se achar, envia uma mensagem ao email com instrução de redefinição de senha.
     * Se não achar, retorna com uma mensagem de erro.
     *
     * @return void
     */
    public function get()
    {
        $email = $this->request->getPost('email');
        $dadosUsuario = $this->usuarioModel->getByEmail($email);

        if (!is_null($dadosUsuario)) {
            $dadosTrs = [
                'usuarios_id' => (int)$dadosUsuario['id']
            ];

            if ($this->trsModel->save($dadosTrs)) {
                $dadosReset = $this->trsModel->getById($this->trsModel->getInsertID());
                $email = \Config\Services::email();
                $email->setTo($dadosUsuario['email']);
                $dataHoraAtual = date('d/m/Y H:i:s');
                $email->setSubject('GFASS - Redefinição de senha - ' . $dataHoraAtual);
                $mensagem = view('_common/emails/reset_senha', [
                    'usuario' => $dadosUsuario['nome'],
                    'token' => $dadosReset['token']
                ]);

                $email->setMessage($mensagem);
                if ($email->send(false)) {
                    $mensagem = "Foi enviada uma mensagem com instruções de redefinição de senha para o e-mail informado acima.";
                } else {
                    $mensagem = "Erro ao enviar o e-mail. Por favor, entre em contato com o administrador do sistema.";
                    log_message('critical', $email->printDebugger(['headers']));
                }
            }
        } else {
            $mensagem = "[ERRO] - Não foi encontrado um usuário com o e-mail: {$email}";
        }

        echo view('cadastro/esqueci_senha', [
            "mensagem" => $mensagem,
        ]);
    }

    /**
     * Verifica se o token informado existe e se está dentro do prazo de 120.
     * Se estiver, mostra uma view de alteração de senha.
     *
     * @param [type] $token
     * @return void
     */
    public function resetSenha($token)
    {
        $dadosToken = $this->trsModel->checkTokenValidity($token);
        $dadosUsuario = $this->usuarioModel->getById($dadosToken['usuarios_id']);
        if (is_null($dadosToken)) {
            $mensagemErro = "[ERRO] - Token não encontrado ou já utilizado.";
        } elseif ($dadosToken['tempo'] > 120) {
            $mensagemErro = "[ERRO] - Token expirado. Por favor, acesse o link Esqueci a Senha novamente e gere um novo pedido.";
        } else {
            return redirect()->to("/cadastro/novaSenha/{$token}/{$dadosUsuario['chave']}");
        }

        echo view('cadastro/esqueci_senha', [
            'mensagem' => $mensagemErro
        ]);
    }
    /**
     * Chama a view de criação de nova senha.
     *
     * @param [type] $token
     * @param [type] $chave
     * @return void
     */
    public function novaSenha($token, $chave)
    {
        echo view('cadastro/nova_senha', [
            'token' => $token,
            'chave' => $chave
        ]);
    }

    /**
     * Faz o update da senha.
     * Aqui são recebidos o token e a chave do usuário.
     * Por questões de segurança eu verifico:
     * se o usuário existe através de sua chave
     * se o token existe e se está ativo
     * se o usuário e token existe, vejo se o token gerado pertence ao usuário sendo alterado
     * caso não, nego o update.
     *
     * @return void
     */
    public function update()
    {

        $validation = \Config\Services::validation();
        $post = $this->request->getPost();

        $validationRules = [
            'senha'         => [
                'label'  => 'Senha',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Campo {field} obrigatório',
                ],
            ],
            'senha_confirm' => [
                'label'  => 'Repita a Senha',
                'rules'  => 'required|matches[senha]',
                'errors' => [
                    'required' => 'Campo {field} obrigatório',
                ],
            ],
        ];
        $validation->setRules($validationRules);

        if ($validation->withRequest($this->request)->run()) {
            //Recupero o id do usuário através da chave
            $dadosUsuario = $this->usuarioModel->getByChave($post['chave']);
            if (!is_null($dadosUsuario)) {
                $idUsuario = $dadosUsuario['id'];
                $dadosToken = $this->trsModel->getByToken($post['token']);
                if (!is_null($dadosToken)) {
                    $idUsuarioToken = $dadosToken['usuarios_id'];
                    if ($idUsuario != $idUsuarioToken) {
                        return redirect()->to('/mensagem/erro')->with('mensagem', "ERRO -  Dados do usuário não coincidem com os dados do token.");
                    }
                } else {
                    return redirect()->to('/mensagem/erro')->with('mensagem', "[ERRO] - Dados do Token não encontrados ou token já utilizado.");
                }
                //Atualizo a senha do usuário
                if ($this->usuarioModel->save($post)) {
                    //Desativo o token
                    $this->trsModel->save([
                        'chave' => $dadosToken['chave'],
                        'ativo' => false
                    ]);
                    return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                        'mensagem' => "Sua nova senha foi cadastrada com sucesso.",
                        'link' => [
                            'to' => base_url(),
                            'texto' => 'Voltar para a página inicial'
                        ]
                    ]);
                }
            } else {
                return redirect()->to('/mensagem/erro')->with('mensagem', "[ERRO] - Usuário não encontrado.");
            }
        } else {
            echo view('cadastro/nova_senha', [
                'errors' => $validation->getErrors(),
                'chave'  => $post['chave'],
                'token'  => $post['token'],
            ]);
        }
    }
}
