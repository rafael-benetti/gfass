<?php

namespace App\Controllers\Api;

use App\Libraries\MinhaBiblioteca;
use CodeIgniter\RESTful\ResourceController;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class Login extends ResourceController
{
    protected $modelName = 'App\Models\UsuarioModel';
    protected $format = 'json';

    /** 
     * Recebe o email e senha do usuário
     * Se forem encontrados, devolve o token de acesso.
     */
    public function getToken()
    {

        $post = (array)$this->request->getJSON();

        $dadosUsuario = $this->model->getByEmail($post['email']);

        $erros = [];
        if (is_null($dadosUsuario)) {
            $erros = [
                'error' => true,
                'message' => 'Usuário e/ou senha incorretos'
            ];
            return $this->failUnauthorized($erros['message']);
        } else {
            if (!$this->model->isEmailConfirmado($dadosUsuario['chave'])) {
                $erros = [
                    'error' => true,
                    'message' => 'Seu e-mail ainda não foi confirmado. Por favor, clique no link enviado para o seu e-mail no momento do cadastro para ativá-lo.'
                ];
            }
            if (!$this->model->isAtivo($dadosUsuario['chave'])) {
                $erros = [
                    'error' => true,
                    'message' => 'Seu cadastro consta como desativado. Entre em contato com a pessoa que lhe cadastrou e peça para ativar seu cadastro.'
                ];
            }
            if (!password_verify($post['senha'], $dadosUsuario['senha'])) {
                $erros = [
                    'error' => true,
                    'message' => 'Usuário e/ou senha incorretos'
                ];
            }

            if (count($erros) > 0) {
                return $this->failUnauthorized($erros['message']);
            }

            $signer = new Sha256();
            $time = time();
            $key = API_KEY;
            $token  = (new Builder())->issuedBy('https://sistema.asaisurf.com.br')
                ->issuedAt($time)
                ->canOnlyBeUsedAfter($time)
                ->expiresAt(strtotime('+1 WEEK'))
                ->withClaim('uid', $dadosUsuario['id'])
                ->getToken($signer, new Key($key));

            return $this->respond(json_encode([
                'token' => "$token",
                'usuarios_id' => (int)$dadosUsuario['id'],
                'nome_usuario' => $dadosUsuario['nome']
            ], JSON_PRETTY_PRINT));
        }
    }
}
