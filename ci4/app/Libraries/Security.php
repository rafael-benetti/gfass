<?php

namespace App\Libraries;

use App\Controllers\Admin\BaseController;
use App\Models\PaginaModel;
use App\Models\PermissaoModel;
use App\Models\UsuarioModel;

class Security extends BaseController
{
    protected static $usuarioModel;
    protected static $permissaoModel;
    protected static $paginaModel;
    protected static $id_usuario;

    public static function init()
    {
        self::$usuarioModel = new UsuarioModel();
        self::$permissaoModel = new PermissaoModel();
        self::$paginaModel = new PaginaModel();
        self::$id_usuario = session()->id_usuario;
    }

    /**
     * Faz a atualização dos idsFilhos na sessão.
     *
     * @return void
     */
    public static function updateIDsFilhos()
    {
        self::init();

        if (!is_null(self::$id_usuario)) {
            $idUsuarioPai = self::$usuarioModel->getIdPai(self::$id_usuario);
            $idsFilhos = self::$usuarioModel->getIdsFilhos($idUsuarioPai);
            array_push($idsFilhos, $idUsuarioPai);

            $sessionData = [
                'id_usuario_pai' => $idUsuarioPai,
                'ids_filhos' => $idsFilhos
            ];

            session()->set($sessionData);
        }
    }

    /**
     * Atualiza na sessão a chave permissões do usuário logado
     *
     * @return void
     */
    public static function updatePermissoes()
    {
        self::init();

        $permissoes = self::$permissaoModel->getByPerfisId(session()->perfis_id);
        $regras = [];
        foreach ($permissoes as $permissao) {
            $regras += [
                strtolower(self::$paginaModel->getById($permissao['paginas_id'])['nome_classe']) => explode(',', $permissao['regras'])
            ];
        }
        session()->set('regras', $regras);
    }
}
