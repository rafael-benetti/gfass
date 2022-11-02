<?php

namespace App\Models;

class OrcamentoModel extends BaseModel
{

    protected $table = 'orcamentos';
    protected $primaryKey = 'chave';
    protected $useSoftDeletes = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $useTimeStamps = true;

    protected $beforeInsert = ['corrigeValor', 'vinculaIdUsuario', 'geraChave'];
    protected $beforeUpdate = ['corrigeValor', 'checaPropriedade'];

    protected $allowedFields = [
        'usuarios_id',
        'categorias_id',
        'chave',
        'valor',
        'descricao',
        'notificar_por_email'
    ];

    protected $validationRules = [
        'descricao' => [
            'label' => 'Descrição',
            'rules' => 'required'
        ],
        'categorias_id' => [
            'label' => "Categoria",
            'rules' => 'required|numeric'
        ],
        'valor' => [
            'label' => "Valor",
            'rules' => 'required'
        ]
    ];

    /**
     * Retorna todos os orçamentos já com as categorias vinculadas
     *
     * @return void
     */
    public function getAllWithCategorias()
    {
        $this->select("
            orcamentos.chave as chave_orcamento,
            orcamentos.descricao as descricao_orcamento,
            categorias.chave chave_categoria,
            categorias.descricao as descricao_categoria,
            valor,
            notificar_por_email
            ");

        $this->join('categorias', 'categorias.id = orcamentos.categorias_id and categorias.deleted_at IS NULL');
        return $this->findAll();
    }

    /**
     * Retorna o valor do orçamento da categoria informada caso possua um orçamento definido.
     *
     * @param [type] $id_categoria
     * @return mixed
     */
    public function valorOrcamento($id_categoria = null)
    {
        $rq = $this
            ->select('valor')
            ->join('categorias', 'categorias.id = orcamentos.categorias_id')
            ->where('categorias.id', $id_categoria)->first();
        return is_null($rq) ? null : $rq['valor'];
    }

    /**
     * Retorna os orçamentos de uma categoria
     * Pode ser informado se é para trazer somente os orçamentos que têm permissão de notificação
     *
     * @param [type] $id
     * @param boolean $notificar_por_email
     * @return void
     */
    public function getByIdCategoria($id, $notificar_por_email = false)
    {
        return $this
            ->where('notificar_por_email', $notificar_por_email)
            ->where('categorias_id', $id)
            ->first();
    }
}
