<?php

namespace App\Models;

class TRSModel extends BaseModel
{

    protected $table = 'token_redefinicao_senha';

    protected $primaryKey = 'chave';

    protected $useSoftDeletes = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $useTimestamps = true;

    protected $skipValidation = false;

    protected $beforeInsert = ['geraChave', 'geraTokenRedefinicao'];

    protected $allowedFields = [
        'chave',
        'usuarios_id',
        'ativo',
    ];


    /**
     * Gera um token para o campo token
     *
     * @return void
     */
    protected function geraTokenRedefinicao($data)
    {
        $data['data']['token'] = md5(uniqid(rand(), true));
        return $data;
    }

    /**
     * Verifica a validade de um token
     *
     * @param [type] $token
     * @return void
     */
    public function checkTokenValidity($token = null)
    {
        return $this
            ->select('usuarios_id, TIMESTAMPDIFF(MINUTE, created_at, now()) as tempo')
            ->where('token', $token)
            ->where('ativo', true)
            ->first();
    }

    /**
     * Retorna os dados do token
     *
     * @param [type] $token
     * @return void
     */
    public function getByToken($token)
    {
        return $this->where('token', $token)->where('ativo', true)->first();
    }
}
