<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthAdmin implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $this->session = \Config\Services::session();
        if ($this->session->has('isLoggedAdmin') && $this->session->isLoggedAdmin == false) {
            return redirect()->to('/mensagem/erro')->with('mensagem', 'Você não tem permissão para acessar a área administrativa.');
        }
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //to do
    }
}
