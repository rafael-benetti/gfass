<?php

namespace App\Models;


class RecoveryCodesModel extends BaseModel
{

    protected $table = 'recovery_codes';

    protected $primaryKey = 'id';

    protected $useSoftDeletes = false;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $useTimestamps = true;

    protected $beforeInsert = ['vinculaIdUsuario', 'geraCodigos'];

    protected $allowedFields = [
        'codigo',
        'usuarios_id',
        'usado'
    ];

    /**
     * Gera os códigos randômicos secretos.
     *
     * @param [type] $data
     * @return void
     */
    protected function geraCodigos($data)
    {
        helper('text');

        $data['data']['codigo'] =  strtoupper(random_string('alpha', 8));

        return $data;
    }

    /**
     * Apaga os recovery codes do usuário logado.
     *
     * @return void
     */
    public function apagaRecoveryCodes()
    {
        return $this->where('usuarios_id', session()->id_usuario)->delete();
    }

    /**
     * Retorna os recovery codes do usuário.
     *
     * @param [type] $id
     * @return void
     */
    public function getByUsuariosId($id)
    {
        return $this->where('usuarios_id', $id)->findAll();
    }

    /**
     * Retorna um registro buscando pelo campo codigo
     *
     * @param [type] $code
     * @return void
     */
    public function getByCode($code)
    {
        return $this->where('codigo', $code)->where('usado', false)->first();
    }
}
