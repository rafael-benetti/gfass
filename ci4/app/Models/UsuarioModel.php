<?php

namespace App\Models;


class UsuarioModel extends BaseModel
{
    protected $table = 'usuarios';

    protected $primaryKey = 'chave';

    protected $useSoftDeletes = false;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $useTimestamps = true;

    protected $skipValidation = false;

    protected $beforeInsert = ['geraToken', 'geraChave', 'hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterInsert  = ['updateIdsFilhos'];
    protected $afterUpdate  = ['updateIdsFilhos'];

    protected $allowedFields = [
        'nome',
        'chave',
        'perfis_id',
        'usuario_pai',
        'email',
        'email_confirmado',
        'foto',
        'senha',
        'token_confirmacao_email',
        'token_criado_em',
        'ativo',
        'admin',
        'secret_google_auth'
    ];


    /**
     * Gera um token para o usuário confirmar o email futuramente.
     *
     * @param [type] $data
     * @return void
     */
    protected function geraToken($data)
    {
        $data['data']['token_confirmacao_email'] = md5(uniqid(rand(), true));
        return $data;
    }

    /**
     * Encripta a senha do usuário.
     *
     * @param array $data
     * @return void
     */
    protected function hashPassword(array $data)
    {
        if (!isset($data['data']['senha'])) {
            return $data;
        }
        $data['data']['senha'] = password_hash($data['data']['senha'], PASSWORD_DEFAULT);
        return $data;
    }

    /**
     * Retorna os dados da pessoa logada
     *
     * @param [type] $id_usuario
     * @return void
     */
    public function getByIdUsuario($id_usuario = null)
    {
        $this->select('
            usuarios.chave,
            usuarios.nome,
            usuarios.email,
            usuarios.created_at,            
            usuarios.token_confirmacao_email,
            usuarios.ativo,
            usuarios.id as id_usuario,
            usuarios.chave as chave_usuario,
            perfis.id as perfis_id,
            perfis.usuarios_id,
            perfis.chave as chave_perfil,
            perfis.descricao as descricao_perfil');
        $this->join('perfis', 'perfis.id = usuarios.perfis_id', 'LEFT');
        $this->where('usuarios.id', $id_usuario);
        return $this->first();
    }

    /**
     * Retorna todos os usuários pertentecentes a um usuario pai
     *
     * @param [type] $id_usuario_pai
     * @return void
     */
    public function getByUsuarioPai($id_usuario_pai = null)
    {
        $this->select('
        usuarios.chave, 
        usuarios.nome, 
        usuarios.email, 
        usuarios.created_at, 
        token_confirmacao_email, 
        usuarios.ativo, 
        usuarios.chave as chave_usuario, 
        perfis.chave as chave_perfil, 
        perfis.descricao as descricao_perfil');
        $this->join('perfis', 'perfis.id = usuarios.perfis_id', 'LEFT');
        $this->where('usuario_pai', $id_usuario_pai);
        return $this->findAll();
    }

    /**
     * Retorna o id do usuario_pai de um  membro da família. Faz a busca recursiva.
     *
     * @param integer $id
     * @return integer
     */
    public function getIdPai(int $id): int
    {
        $dados = $this->select('id, usuario_pai')->where('id', $id)->first();
        if (!is_null($dados['usuario_pai'])) {
            return $this->getIdPai($dados['usuario_pai']);
        } else {
            return $id;
        }
    }

    /**
     * Retorna os ids filhos já tratados.
     *
     * @param integer $id
     * @return array
     */
    public function getIdsFilhos(int $id): array
    {
        $arrayStrings = explode(',', rtrim($this->idsFilhos($id), ','));
        $result = array_map('intval', array_filter($arrayStrings, 'is_numeric'));
        sort($result);
        return $result;
    }

    /**
     * Retorna os ids filhos de um usuario_pai recursivamente
     *
     * @param integer $id
     * @param string $string
     * @return string
     */
    protected function idsFilhos(int $id, string $result = ''): string
    {
        $dados = $this->select('id')->where('usuario_pai', $id)->findAll();

        if (count($dados) > 0) {
            foreach ($dados as $dado) {
                $result .= $dado['id'] . ',' . $this->idsFilhos($dado['id']);
            }
        }

        return $result;
    }

    /**
     * Retorna todos os usuários filhos recursivamente de um usuario_pai.
     *
     * @param integer $id
     * @param boolean $fromAdmin =indica se é para acrescentar o endereço Admin
     * @return array
     */
    public function getUsuariosFilhos(int $id = null, $fromAdmin = false): array
    {
        $result = [];
        $dados = $this->select('id, nome, chave')->where('usuario_pai', $id)->findAll();

        if (count($dados) > 0) {
            foreach ($dados as $dado) {
                $request = $this->getUsuariosFilhos($dado['id'], $fromAdmin);
                if (count($request) === 0) {
                    $result[] = $fromAdmin ? anchor("admin/usuario/{$dado['chave']}/edit", $dado['nome']) : anchor("usuario/{$dado['chave']}/edit", $dado['nome']);
                } else {
                    if ($fromAdmin) {
                        $result[anchor("admin/usuario/{$dado['chave']}/edit", $dado['nome'])] = $request;
                    } else {
                        $result[anchor("usuario/{$dado['chave']}/edit", $dado['nome'])] = $request;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Conta os registros da tabela.
     *
     * @return void
     */
    public function countAll()
    {
        return $this->selectCount('*', 'numrows')->first()['numrows'];
    }

    /**
     * Retorna os dados do usuário pelo seu email
     *
     * @param [type] $email
     * @return void
     */
    public function getByEmail($email = null)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Retorna o valor do cmapo email_confirmado
     *
     * @param [type] $chave
     * @return boolean
     */
    public function isEmailConfirmado($chave): bool
    {
        return $this
            ->select('email_confirmado')
            ->where('chave', $chave)
            ->first()['email_confirmado'];
    }

    /**
     * Retorna o valor do campo ativo
     *
     * @param [type] $chave
     * @return boolean
     */
    public function isAtivo($chave): bool
    {
        return $this
            ->select('ativo')
            ->where('chave', $chave)
            ->first()['ativo'];
    }

    /**
     * Verifica se um usuário é admin
     *
     * @param [type] $chave
     * @return boolean
     */
    public function isAdmin($chave): bool
    {
        return $this
            ->select('admin')
            ->where('chave', $chave)
            ->first()['admin'];
    }

    /**
     * Retorna um registro pelo campo token_confirmacao_email
     *
     * @param [type] $token
     * @return void
     */
    public function getByTokenEmail($token)
    {
        return $this->where('token_confirmacao_email', $token)->first();
    }
}
