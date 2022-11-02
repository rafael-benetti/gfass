<?php

namespace App\Models;

class CategoriaModel extends BaseModel
{
    protected $table = 'categorias';
    protected $primaryKey = 'chave';

    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $useTimestamps = true;

    protected $beforeInsert = ['vinculaIdUsuario', 'geraChave'];
    protected $beforeUpdate = ['checaPropriedade'];

    // protected $allowCallbacks = false;

    protected $allowedFields = [
        'usuarios_id',
        'anexo',
        'chave',
        'tipo',
        'descricao'
    ];

    protected $validationRules = [
        'descricao' => [
            'label' => 'Descrição',
            'rules' => 'required'
        ],
        'tipo' => [
            'label' => 'Tipo',
            'rules' => 'required'
        ]
    ];


    /**
     * Injeta a tabela Laçamentos na query.
     *
     * @return object
     */
    public function addTableLancamentos()
    {
        $this->join('lancamentos', 'lancamentos.categorias_id = categorias.id');
        $this->where('lancamentos.deleted_at IS NULL');
        return $this;
    }

    /**
     * Gera uma array de categorias pronta para ser populada na função form_dropdown
     * Se for passado o parâmetro opcaoNova, insere a opção "Nova Categoria..."
     *
     * @param array $params
     * @return void
     */
    public function formDropDown(array $params = null)
    {

        $this->select('id, descricao, tipo');

        if (!is_null($params) && isset($params['tipo'])) {
            $this->where(['tipo' => $params['tipo']]);
        }

        if (!is_null($params) && isset($params['id'])) {
            $this->where(['id' => $params['id']]);
        }

        $categoriasArray = $this->findAll();

        $optionCategorias = array_column($categoriasArray, 'descricao', 'id');

        $optionsSelecione = [
            '' => 'Selecione...'
        ];

        $selectConteudo = $optionsSelecione + $optionCategorias;

        $novaCategoria = [];
        if (!is_null($params) && isset($params['opcaoNova'])) {
            if ((bool) $params['opcaoNova'] === true) {
                $novaCategoria = [
                    '---' => [
                        'n' => 'Nova categoria...'
                    ]
                ];
            }
        }

        return $selectConteudo + $novaCategoria;
    }

    /**
     * Retorna todas as categorias que possuem lançamentos
     *
     * @return void
     */
    public function getComLancamentos()
    {
        $this->select(
            "
            tipo,
            lancamentos.anexo as anexo,
            categorias.usuarios_id,
            categorias.descricao as descricao_categoria,
            categorias.id as id_categoria,
            lancamentos.descricao as descricao_lancamento,
            lancamentos.id as id_lancamento
            "
        );
        $this->join('lancamentos', 'lancamentos.categorias_id = categorias.id');
        $this->groupBy('descricao_categoria');
        return $this->findAll();
    }

    /**
     * Retorna o tipo de uma categoria filtrando pelo seu id
     *
     * @param [type] $id_categoria
     * @return void
     */
    public function getTipoByCategoria($id_categoria = null)
    {
        if (!is_null($id_categoria)) {
            return $this->select('tipo')->where('id', $id_categoria)->first()['tipo'];
        }
    }

    /**
     * Retorna todas as categorias do usuário.
     *
     * @param [type] $id_usuario
     * @return void
     */
    public function getByIdUsuario($id_usuario)
    {
        return $this
            ->select('descricao, id')
            ->where('categorias.usuarios_id', $id_usuario)->findAll();
    }
}
