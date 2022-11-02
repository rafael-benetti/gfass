<?php

namespace App\Filters;


use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;

use Lcobucci\JWT\Signer\Hmac\Sha256;


class AuthApi implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        //Se existir o header Authorization, indica que o usuário está enviando um token de acesso.
        if ($request->hasHeader('Authorization')) {
            $tokenRecebido = $request->getHeaderLine('Authorization');
            try {
                $signer = new Sha256();
                $token = (new Parser())->parse((string) $tokenRecebido);
                $data = new ValidationData();
                if ($token->verify($signer, API_KEY)) {
                    $token->validate($data);
                } else {
                    echo json_encode([
                        'error' => true,
                        'message' => 'Assinatura do Token incorreta.'
                    ]);
                    die();
                }
            } catch (\Exception $e) {
                echo json_encode([
                    'error' => true,
                    'message' => $e->getMessage()
                ]);
                die();
            }
        } else {
            echo json_encode([
                'error' => true,
                'message' => 'ERRO - Token de autorização não enviado.'
            ]);
            die();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //to do   
    }
}
