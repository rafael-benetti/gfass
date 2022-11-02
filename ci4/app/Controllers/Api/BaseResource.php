<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BaseResource extends ResourceController
{
    /**
     * Retorna os idsfilhos da família da pessoa logada.
     *
     * @return array
     */
    protected function getIdsDaFamilia(): array
    {
        $idPai = $this->usuarioModel->getIdPai($this->id_usuario);
        $idsFilhos = $this->usuarioModel->getIdsFilhos($idPai);
        array_push($idsFilhos, $idPai);
        return $idsFilhos;
    }
}
