<?php

namespace App\Libraries;

use App\Models\UsuarioModel;

class MinhasRegras
{

    /**
     * Verifica se uma classe foi definida
     *
     * @param string $className
     * @return boolean
     */
    public function check_class_exists(string $className): bool
    {
        $path = "App\Controllers\\" . $className;
        return class_exists($path);
    }

    /**
     * Verifica se a senha digitada pelo usuário está correta.
     *
     * @param string $senha
     * @return boolean
     */
    public function check_senha_atual(string $senha): bool
    {
        $usuarioModel = new UsuarioModel();
        $dadosUsuario = $usuarioModel->getByChave(session()->chave);
        return password_verify($senha, $dadosUsuario['senha']);
    }
}
