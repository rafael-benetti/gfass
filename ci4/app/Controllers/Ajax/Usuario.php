<?php

namespace App\Controllers\Ajax;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;

class Usuario extends BaseController
{
    public function storeFoto()
    {
        if ($this->request->isAJAX()) {
            $usuarioModel = new UsuarioModel();
            try {
                $file = $this->request->getFile('foto')->store();
                $chave = $this->request->getPost('chave');
                $image = \Config\Services::image();
                $arquivo = WRITEPATH . 'uploads/' . $file;
                $image->withFile($arquivo)->fit(250, 250, 'center');
                if ($image->save()) {
                    $dadosFoto = [
                        'chave' => $chave,
                        'foto' => $file
                    ];
                    if ($usuarioModel->save($dadosFoto)) {
                        $result = [
                            'error' => false,
                            'path' => base_url("usuario/getFoto/{$chave}")
                        ];
                    } else {
                        $result = [
                            'error' => true,
                            'pathFoto' => $file,
                            'message' => 'Não foi possível salvar o arquivo no banco de dados.'
                        ];
                    }
                }
            } catch (\CodeIgniter\Images\Exceptions\ImageException $e) {
                $result = [
                    'error' => true,
                    'message' => $e->getMessage()
                ];
            }
        } else {
            $result = [
                'error' => true,
                'code' => 403,
                'message' => '[ERROR] - Only AJAX requests allowed'
            ];
        }

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
