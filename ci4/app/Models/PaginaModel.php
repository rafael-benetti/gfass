<?php

namespace App\Models;

class PaginaModel extends BaseModel
{
    protected $table = 'paginas';
    protected $primaryKey = 'chave';

    protected $useSoftDeletes = false;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $useTimestamps = true;

    protected $skipValidation = false;

    protected $beforeInsert = ['geraChave'];

    protected $allowedFields = [
        'nome_amigavel',
        'nome_classe',
        'chave'
    ];

    protected $validationRules = [
        'nome_amigavel' => [
            'label' => 'Nome Amigável',
            'rules' => 'required'
        ],
        'nome_classe' => [
            'label' => 'Nome da Classe',
            'rules' => 'required|check_class_exists',
            'errors' => [
                'check_class_exists' => 'A classe {value} não foi encontrada'
            ]
        ]
    ];
}
