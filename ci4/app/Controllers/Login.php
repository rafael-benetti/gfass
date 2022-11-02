<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\Security;
use App\Models\RecoveryCodesModel;
use App\Models\UsuarioModel;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

class Login extends BaseController
{

    protected $usuarioModel;
    protected $ga;
    protected $recoveryCodesModel;
    protected $validation;


    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->recoveryCodesModel = new RecoveryCodesModel();
        $this->ga = new GoogleAuthenticator();
        $this->validation   = \Config\Services::validation();
    }
    /**
     * Chama a view de login
     *
     * @return void
     */
    public function index()
    {
        echo view('login/index');
    }

    /**
     * Loga o usuário.
     *
     * @return void
     */
    public function signin()
    {
        $post = $this->request->getPost();
        $validationRules = [
            'login_email'  => [
                'label'  => 'E-mail',
                'rules'  => 'required|valid_email'
            ],
            'login_senha' => [
                'label'  => "Senha",
                'rules'  => "required"
            ],
        ];
        $this->validation->setRules($validationRules);

        if ($this->validation->withRequest($this->request)->run()) {
            $dadosUsuario = $this->usuarioModel->getByEmail($post['login_email']);
            if (is_null($dadosUsuario)) {
                return redirect()->to('/login')->with('errorLogin', 'Usuário e/ou senha incorretos.');
            } else {
                if (!$this->usuarioModel->isEmailConfirmado($dadosUsuario['chave'])) {
                    return redirect()->to('/login')->with('errorLogin', 'Seu e-mail ainda não foi confirmado.<br />Por favor, clique no link enviado para o seu e-mail no momento do cadastro.');
                }
                if (!$this->usuarioModel->isAtivo($dadosUsuario['chave'])) {
                    return redirect()->to('/login')->with('errorLogin', 'Seu cadastro consta como desativado.<br />Entre em contato com a pessoa que lhe cadastrou e peça para ativar seu cadastro.');
                }
                if (!password_verify($post['login_senha'], $dadosUsuario['senha'])) {
                    return redirect()->to('/login')->with('errorLogin', 'Usuário e/ou senha incorretos.');
                }

                [$usuario] = explode('@', $dadosUsuario['email']);
                /**
                 * Se o campo secret_google_auth contiver algum valor, então entende-se que a autentição em 2 fatores
                 * está habilitada. Então, eu redireciono o usuário para tela onde ele deverá digitar o código de desbloqueio
                 */
                if (!is_null($dadosUsuario['secret_google_auth'])) {
                    if (!get_cookie(md5($usuario))) {
                        return redirect()->to("/login/getCode/{$dadosUsuario['chave']}");
                    }
                }

                return $this->logaUsuario($dadosUsuario['chave']);
            }
        } else {
            echo view('login/index', [
                'errors' => $this->validation->getErrors()
            ]);
        }
    }

    /**
     * Chama a view para receber o código do google authenticator
     *
     * @param [type] $chave
     * @return void
     */
    public function getCode($chave)
    {
        echo view('login/get_google_auth', [
            'chave' => $chave
        ]);
    }

    /**
     * Recebe o código do google authenticator digitado pelo usuário.
     * Se esta verificação retornar true, indica que o código digitado estava correto
     * e então o usuário poderá ser logado.
     * 
     * Se a verificação do google auth retornar false, procuro o código na tabela recovery codes. Se encontra permito o login.
     *
     * @return void
     */
    public function checkGoogleAuth()
    {

        $post = $this->request->getPost();

        $dadosUsuario = $this->usuarioModel->getByChave($post['chave']);
        if (!is_null($dadosUsuario)) {
            $secret = $dadosUsuario['secret_google_auth'];
            [$usuario]  = explode('@', $dadosUsuario['email']);
            if ($this->ga->checkCode($secret, $post['code'])) {
                if (array_key_exists('salvar', $post)) {
                    $this->response->setCookie(md5($usuario), true);
                }
                return $this->logaUsuario($post['chave']);
            } else {
                //Antes de negar, verifico os códigos de segurança
                $recoveryCodes = $this->recoveryCodesModel->getByUsuariosId($dadosUsuario['id']);
                if (count($recoveryCodes) > 0) {
                    $dadosCode = $this->recoveryCodesModel->getByCode($post['code']);
                    if (!is_null($dadosCode)) {
                        $this->recoveryCodesModel->save([
                            'id' => $dadosCode['id'],
                            'usado' => true
                        ]);
                        if (array_key_exists('salvar', $post)) {
                            $this->response->setCookie(md5($usuario), true);
                        }
                        return $this->logaUsuario($post['chave']);
                    }
                }
            }
            return redirect()->to("/login/getCode/{$post['chave']}")->with('mensagem', 'O código digitado é inválido.<br>Você pode usar o código de recuperação se desejar.');
        } else {
            return redirect()->to("/mensagem/erro/")->with('mensagem', '[ERRO] - Usuário não encontrado.');
        }
    }

    /**
     * Loga o usuário no sistema atribuindo seus valores na sessão.
     *
     * @param [type] $chave
     * @return void
     */
    protected function logaUsuario($chave = null)
    {
        $dadosUsuario = $this->usuarioModel->getByChave($chave);
        $isAdmin = $this->usuarioModel->isAdmin($chave);

        $dadosSession = [
            'isLoggedIn'        => true,
            'isLoggedAdmin'     => $isAdmin,
            'id_usuario'        => $dadosUsuario['id'],
            'perfis_id'         => $dadosUsuario['perfis_id'],
            'isUsuarioPai'      => is_null($dadosUsuario['usuario_pai']) ? true : false,
            'nome_usuario'      => $dadosUsuario['nome'],
            'email_usuario'     => $dadosUsuario['email'],
            'chave'             => $chave

        ];

        $this->session->set($dadosSession);

        Security::updateIDsFilhos();
        Security::updatePermissoes();

        if (!$this->session->has('id_usuario')) {
            return redirect()->to('mensagem/erro')->with('mensagem', 'ERRO - Não foi possível recuperar o id do usuário.');
        }

        //E agora começa a aventura...
        return redirect()->to(base_url())->withCookies();
    }

    /**
     * Desloga o usuário.
     *
     * @return void
     */
    public function signout()
    {
        $this->session->destroy();
        return redirect()->to(base_url());
    }
}
