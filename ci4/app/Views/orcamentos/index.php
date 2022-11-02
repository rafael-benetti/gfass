<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>
<script>
    function confirma() {
        if (!confirm("Deseja excluir o registro?")) {
            return false;
        }

        return true;
    }
</script>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo anchor('', "Home") ?></li>
        <li class="breadcrumb-item active" aria-current="page">Orçamentos</li>
    </ol>
</nav>

<h1>Orçamentos</h1>
<div class="card">
    <div class="card-header">
        Orçamentos - <?php echo count($orcamentos) ?> - registro(s) encontrado(s)
    </div>
    <div class="card-body">
        <div class="row no-gutters d-flex justify-content-center justify-content-sm-between">
            <div class="my-3">
                <?php echo anchor('orcamento/create', 'Novo Orçamento', ['class' => 'btn btn-primary']) ?>
            </div>
            <?php echo form_open('orcamento', ['class' => 'form-inline', 'method' => 'GET']) ?>
            <div class="form-group d-flex justify-content-center my-3">
                <input type="search" name="search" autocomplete="off" placeholder="busca..." class="form-control" value="<?php echo !empty($search) ? $search : '' ?>">
                <input type="submit" class="ml-2 btn btn-primary" value="OK">
            </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-stripped table-hover">
                <thead>
                    <tr class="bg-dark text-white">
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Notificar?</th>
                        <th>Valor</th>
                        <th class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($orcamentos) > 0) : ?>
                        <?php foreach ($orcamentos as $orcamento) : ?>
                            <tr>
                                <td><?php echo $orcamento['descricao_orcamento'] ?></td>
                                <td><?php echo $orcamento['descricao_categoria'] ?></td>
                                <td><?php echo (int) $orcamento['notificar_por_email'] === 1 ? 'Sim' : 'Não' ?></td>
                                <td>R$ <?php echo number_format($orcamento['valor'], 2, ',', '.') ?></td>
                                <td class="text-center">
                                    <?php echo anchor("orcamento/{$orcamento['chave_orcamento']}/edit", 'Editar', ['class' => "btn btn-success btn-sm" . checkLinkPermission('orcamento', 'edit')]) ?>
                                    -
                                    <?php echo anchor("orcamento/{$orcamento['chave_orcamento']}/delete", 'Excluir', ['class' => "btn btn-danger btn-sm" . checkLinkPermission('orcamento', 'delete'), 'onclick' => 'return confirma()']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center">Nenhum orçamento encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>


<?php echo $this->endSection('content') ?>