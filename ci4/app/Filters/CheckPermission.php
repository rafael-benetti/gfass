<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CheckPermission implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $this->session = \Config\Services::session();

        /**
         * Se um usuário logado não for do tipo usuario_pai, então deve-se garantir que as regras de acesso foram carregadas com sucesso.
         * Caso contrário, deve-se negar o acesso do usuário.         
         */
        if (!session()->regras && !session()->isUsuarioPai) {
            return redirect()->to('mensagem/erro')->with('/mensagem', 'ERRO FATAL - Não foi possível carregar as suas permissões de acesso. Por favor, tente logar-se novamente.');
        }
        if (!$this->session->has('id_usuario')) {
            return redirect()->to('mensagem/erro')->with('/mensagem', 'ERRO FATAL - Não foi possível carregar o ID do usuário logado. Por favor, tente logar-se novamente.');
        }


        $routes = \CodeIgniter\Services::routes();

        //Se a requisição for via ajax, devo tratar isso.
        if ($request->isAjax()) {
            $pagina = !empty($request->uri->getSegment(1)) ? "ajax\\" . $request->uri->getSegment(2) : strtolower($routes->getDefaultController());
        } else {
            $pagina = !empty($request->uri->getSegment(1)) ? $request->uri->getSegment(1) : strtolower($routes->getDefaultController());
        }

        if (!session()->isAdmin) {
            if (!session()->isUsuarioPai) {
                $regras = session()->regras;
                $totalSegments = $request->uri->getTotalSegments();
                $metodoAtual = $request->uri->getSegments();
                if ($totalSegments === 0 || $totalSegments === 1) {
                    array_push($metodoAtual, $routes->getDefaultMethod());
                }
                $mensagem = "O perfil associado ao seu usuário não permite a você acessar este recurso.";
                if (!array_key_exists($pagina, $regras)) {
                    if ($request->isAJAX()) {
                        $erro = [
                            'error' => true,
                            'message' => $mensagem
                        ];
                        echo json_encode($erro, JSON_PRETTY_PRINT);
                        die();
                    } else {
                        return redirect()->to('/mensagem/erro')->with('mensagem', $mensagem);
                    }
                }
                if (!array_intersect($metodoAtual, $regras[$pagina])) {
                    if ($request->isAJAX()) {
                        $erro = [
                            'error' => true,
                            'message' => $mensagem
                        ];
                        echo json_encode($erro, JSON_PRETTY_PRINT);
                        die();
                    } else {
                        return redirect()->to('/mensagem/erro')->with('mensagem', $mensagem);
                    }
                }
            }
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //to do
    }
}
