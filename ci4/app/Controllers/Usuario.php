<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\RecoveryCodesModel;
use App\Models\UsuarioModel;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class Usuario extends BaseController
{
    protected $usuarioModel;
    protected $idUsuario;
    protected $perfilModel;
    protected $validation;
    protected $ga;
    protected $qrCodeUrl;
    protected $recoveryCodesModel;


    public function __construct()
    {

        $this->usuarioModel = new UsuarioModel();
        $this->idUsuario = session()->id_usuario;
        $this->perfilModel = new PerfilModel();
        $this->validation = \Config\Services::validation();
        $this->ga = new GoogleAuthenticator();
        $this->recoveryCodesModel = new RecoveryCodesModel();
    }


    /**
     * Carrega a view principal
     *
     * @return void
     */
    public function index()
    {

        $data = [
            'meusDados' => $this->usuarioModel->getByIdUsuario($this->idUsuario),
            'meusUsuarios' => $this->usuarioModel->addOrder([
                'campo' => 'nome',
                'sentido' => 'asc'
            ])->getByUsuarioPai($this->idUsuario)
        ];
        echo view('usuarios/index', $data);
    }


    /**
     * Chama a view do formulário
     *
     * @return void
     */
    public function create()
    {
        $data = [
            'titulo' => 'Novo Usuário',
            'perfisDropDown' => $this->perfilModel->addUserId($this->idUsuario)->formDropDown()
        ];
        echo view('usuarios/form', $data);
    }

    /**
     * Salva o registro no banco.
     *
     * @return void
     */
    public function store()
    {
        $post = $this->request->getPost();

        $validationRules = [
            'nome' => [
                'label' => 'Nome',
                'rules' => 'required'
            ],
            'email' => [
                'label' => 'E-mail',
                'rules' => "required|valid_email|is_unique[usuarios.email,usuarios.chave,{$post['chave']}]", //NÃO PODE TER ESPAÇOS ENTRE AS VÍRGULAS DO IS_UNIQUE,                
                'errors' => [
                    'required' => 'O campo {field} é obrigatório',
                    'valid_email' => 'Este e-mail: {value} parece ter um formato inválido',
                    'is_unique' => 'O e-mail {value} já está sendo utilizado.'
                ]
            ]
        ];

        $validationPerfil = [
            'perfis_id' => [
                'label'  => 'Perfil',
                'rules'  => 'required',
                'errors' => [
                    'required' => 'Campo {field} obrigatório para usuários filhos.',
                ],
            ],
        ];

        $validationSenhaAtual = [
            'senha_atual' => [
                'label' => 'Senha Atual',
                'rules' => 'required|check_senha_atual',
                'errors' => [
                    'check_senha_atual' => 'Senha atual inválida!'
                ]
            ]
        ];

        $validationRulesPassword = [
            'senha'         => [
                'label'  => 'Senha',
                'rules'  => 'required'
            ],
            'senha_confirm' => [
                'label'  => 'Repita a Senha',
                'rules'  => 'required|matches[senha]'
            ],
        ];

        /**
         * Se o campo chave for vazio, indica um usuário novo. Então deve-se exigir a senha.
         */
        if (empty($post['chave'])) {
            $validationRules += $validationRulesPassword;
        }

        /**
         * Se os campos de senha forem vazios, então retiro do post o campo senha para não correr o risco
         * de uma atualização de um campo em branco
         */
        if (empty($post['senha']) && empty($post['senha_confirm'])) {
            unset($post['senha']);
        }

        /**
         * Se houver algo digitado no campo senha ou repita a senha, então, deve-se incluir a validação de senhas
         */
        if (!empty($post['senha']) || !empty($post['senha_confirm'])) {
            $validationRules += $validationRulesPassword;
            if ($post['chave'] == $this->session->chave) {
                $validationRules += $validationSenhaAtual;
            }
        }

        /**
         * Se for um novo cadastro e existir alguém logado, indica que um usuário filho está sendo cadastrdo.
         * Então, deve-se informar o perfil
         */
        if (session()->has('id_usuario') && empty($post['chave'])) {
            $validationRules += $validationPerfil;
        }

        $this->validation->setRules($validationRules);

        if ($this->validation->withRequest($this->request)->run()) {
            /**
             * Se não existir a chave, indica um usuário novo. Então deve-se obrigatoriamente preenche o campo usuarios_pai.
             * E também indica um usuário filho. Então posso deixar o email confirmado como true.
             * 
             * Se existir a chave, indica uma edição.
             * Não permito a edição do próprio email da pessoa logada.
             * Se for um usuario_pai, é permitido alterar o email somente dos usuários cadastrados por ele e por sua família.
             */
            if (empty($post['chave'])) {
                $post['email_confirmado'] = true;
                if ($this->session->has('id_usuario')) {
                    $post['usuario_pai'] = $this->idUsuario;
                } else {
                    return redirect()->to('/mensagem/erro')->with('mensagem', 'ERRO - Não foi possível identificar o usuário logado atualmente.');
                }
            } else {
                //Não permito a edição do próprio email.
                if ($post['chave'] == $this->session->chave) {
                    unset($post['email']);
                    unset($post['perfis_id']);
                } else {
                    /**
                     * Verifico se o usuario sendo alterado faz parte da família do usuário logado. 
                     * Se não fizer parte, não permito a edição.
                     * Para fazer parte da família, o valor id do registro, deve estar presente na array ids_filhos.
                     */
                    $idProprietario = $this->usuarioModel->getByChave($post['chave'])['id'];
                    if (!in_array($idProprietario, $this->session->ids_filhos)) {
                        return redirect()->to('/mensagem/erro')->with('mensagem', 'O usuário sendo editado não foi cadastrado por você.');
                    }
                }
            }
            if ($this->usuarioModel->save($post)) {
                if (empty($post['chave'])) {
                    $idUsuario = $this->usuarioModel->getInsertID();
                    $chaveUsuario = $this->usuarioModel->getById($idUsuario)['chave'];
                    return redirect()->to("/usuario/{$chaveUsuario}/edit")->with('mensagem', 'Registro cadastrado com sucesso.');
                } else {
                    return redirect()->to("/usuario")->with('mensagem', 'Registro atualizado com sucesso.');
                }
            } else {
                return redirect()->to('/mensagem/erro')->with('mensagem', 'ERRO ao salvar os dados no banco de dados.');
            }
        } else {
            echo view('usuarios/form', [
                'titulo' => !empty($post['chave']) ? 'Editar usuário' : 'Novo usuário',
                'errors' => $this->validation->getErrors(),
                'perfisDropDown' => $this->perfilModel->addUserId($this->session->id_usuario)->formDropDown(),
                'nomePerfil' => $this->perfilModel->getById($post['perfis_id'])['descricao'],
                'chave' => $post['chave']
            ]);
        }
    }

    /**
     * Chama a view de edição de usuário.
     *
     * @param [type] $chave
     * @return void
     */
    public function edit($chave = null)
    {
        $dadosUsuario = $this->usuarioModel->getByChave($chave);

        if (!is_null($dadosUsuario)) {
            if (!in_array($dadosUsuario['id'], $this->session->ids_filhos)) {
                return redirect()->to('/mensagem/erro')->with('mensagem', 'Este registro não pertence a nenhum dos membros da família do usuário logado.');
            }
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', 'Registro não localizado');
        }

        echo view('usuarios/form', [
            'titulo' => 'Editar Usuário',
            'usuariosFilhos' => $this->usuarioModel->getUsuariosFilhos($dadosUsuario['id']),
            'perfisDropDown' => $this->perfilModel->addUserId($this->idUsuario)->formDropDown(),
            'nomePerfil' => $this->perfilModel->getById($dadosUsuario['perfis_id'])['descricao'],
            'usuario' => $dadosUsuario,
            'chave' => $chave,
            'recoveryCodes' => $this->recoveryCodesModel->getByUsuariosId($dadosUsuario['id'])
        ]);
    }

    /**
     * Carrega a view para o cadastro do segredo no celular do usuário.
     *
     * @return void
     */
    public function googleAuth()
    {
        //Gera o segredo
        $secret = $this->ga->generateSecret();
        $qrCodeUrl = GoogleQrUrl::generate($this->session->email . '@GFASS', $secret);

        $dados = [
            'titulo' => 'Cadastro de código secreto',
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
            'chave' => $this->session->chave
        ];

        echo view('usuarios/qr_code_google_auth', $dados);
    }

    /**
     * Armazena o código digitado do google authenticator no registro do usuário
     *
     * @return void
     */
    public function storeGoogleAuth()
    {
        $post =  $this->request->getPost();
        $dadosUsuario = $this->usuarioModel->getByChave($post['chave']);
        if (!is_null($dadosUsuario)) {
            if ($this->ga->checkCode($post['secret'], $post['code'])) {
                $dadosGoogle = [
                    'chave' => $post['chave'],
                    'secret_google_auth' => $post['secret']
                ];
                if ($this->usuarioModel->save($dadosGoogle)) {
                    return redirect()->to('/mensagem/sucesso')->with('mensagem', [
                        'mensagem' => 'Segredo salvo com sucesso. No próximo login você precisará do aplicativo Google Authenticator para logar-se no sistema.',
                        'link' => [
                            'texto' => 'Voltar para Edição de Usuários',
                            'to' => "usuario/{$post['chave']}/edit"
                        ]
                    ]);
                }
            } else {
                return redirect()->to('/usuario/googleAuth')->with('mensagem', 'Código Inválido.<br />Escaneie novamente o código acima.');
            }
        } else {
            return redirect()->to('/mensagem/erro')->with('mensagem', 'Usuário não encontrado');
        }
    }

    /**
     * Desativa a autenticação em 2 fatores.
     *
     * @return void
     */
    public function desativaAuth2Fatores()
    {
        $chave = $this->session->chave;
        $request = $this->usuarioModel->save([
            'chave' => $chave,
            'secret_google_auth' => null
        ]);

        //Apagos os recovery codes deste usuário
        $this->recoveryCodesModel->apagaRecoveryCodes();

        [$usuario] = explode('@', $this->session->email);
        setcookie(md5($usuario), "", time() - 3600);

        if ($request) {
            return redirect()->to("/usuario/{$chave}/edit");
        }
    }

    /**
     * Gera os códigos de recuperação.
     *
     * @return void
     */
    public function createBackupCodes()
    {
        //primeiro apago os códigos atuais.
        if ($this->recoveryCodesModel->apagaRecoveryCodes()) {
            for ($i = 1; $i <= 16; $i++) {
                $this->recoveryCodesModel->save(['usado' => false]);
            }
            return redirect()->to("/usuario/{$this->session->chave}/edit")->with('mensagem', 'Códigos de recuperação gerados. Guarde-os em local seguro.');
        }
    }

    /**
     * Retorna a imagem de um usuário.
     *
     * @param [type] $chave
     * @return void
     */
    public function getFoto($chave = null)
    {
        $dadosUsuario = $this->usuarioModel->getByChave($chave);
        if (!is_null($dadosUsuario)) {
            $foto = $dadosUsuario['foto'];
            if (!is_null($foto)) {
                $filename = WRITEPATH . 'uploads/' . $foto;
                if (!file_exists($filename)) {
                    echo json_encode(['error' => true, 'mensagem' => 'Foto não encontrada']);
                    die();
                } else {
                    $imgInfo = getimagesize($filename);
                    $this->response->setHeader('Content-Type', $imgInfo['mime']);
                    echo file_get_contents($filename);
                }
            } else {
                echo json_encode(['error' => true, 'mensagem' => 'Usuário sem foto cadastrada.']);
            }
        } else {
            echo json_encode(['error' => true, 'mensagem' => 'Usuário não encontrado.']);
        }
    }

    /**
     * Apaga um usuário.
     *
     * @param [type] $chave
     * @return void
     */
    public function delete($chave)
    {
        if ($this->usuarioModel->delete($chave)) {
            return redirect()->to('/usuario')->with('mensagem', 'Usuário excluído com sucesso.');
        }
    }
}
