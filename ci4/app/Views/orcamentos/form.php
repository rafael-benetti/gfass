<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/novaCategoria.js') ?>"></script>
<script>
    $(document).ready(function() {
        $('#valor').mask('000.000.000.000.000,00', {
            reverse: true
        })
    });
</script>

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo anchor('', "Home") ?></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo anchor('orcamento', 'Orçamentos') ?></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $titulo ?></li>
    </ol>
</nav>

<h1>Orçamentos</h1>
<div class="card">
    <div class="card-header">
        <?php echo $titulo ?>
    </div>
    <div class="card-body">
        <?php echo form_open('orcamento/store') ?>

        <div class="form-group col-sm-6">
            <label for="descricao">Descrição</label>
            <input type="text" name="descricao" id="descricao" value="<?php echo !empty($orcamento['descricao']) ? $orcamento['descricao'] : set_value('descricao') ?>" class="form-control" autofocus>
            <?php if (!empty($errors['descricao'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo  $errors['descricao'] ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group col-sm-6">
            <label for="categorias_id">Categoria <span id="spinnerLoading" style="display: none;" class="ml-2 spinner-border spinner-border-sm"></span></label>
            <?php echo form_dropdown('categorias_id', $formDropDown, !empty($orcamento['categorias_id']) ? $orcamento['categorias_id'] : set_value('categorias_id'), ['class' => 'form-control', 'id' => 'categorias_id', 'onChange' => 'modalNovaCategoria(this.value)']) ?>
        </div>

        <div class="col-sm-3">
            <label for="valor">Valor</label>
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <div class="input-group-text">R$</div>
                </div>
                <input type="text" name="valor" id="valor" value="<?php echo !empty($orcamento['valor']) ? $orcamento['valor'] : set_value('valor') ?>" class="form-control" placeholder="0,00">
                <?php if (!empty($errors['valor'])) : ?>
                    <div class="alert alert-danger mt-2"><?php echo $errors['valor'] ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group col-md-12">
            <label for="notificar">Notificar por e-mail?
                <div>
                    <small>Marque se desejar receber um e-mail quando os lançamentos atingirem 80% deste valor.</small>
                </div>
            </label>

            <div class="row col-sm-2">
                <?php echo form_dropdown('notificar_por_email', [2 => 'Não', 1 => 'Sim'], !empty($orcamento['notificar_por_email']) ? $orcamento['notificar_por_email'] : set_value('notificar_por_email'), ['id' => 'notificar_por_email', 'class' => 'form-control']); ?>
            </div>
        </div>
        <div class="form-group col-sm-12">
            <button type="submit" class="btn btn-primary" <?php echo checkLinkPermission('orcamento', 'store') ?>>Salvar</button>
        </div>
        <input type="hidden" name="chave" value="<?php echo !empty($orcamento['chave']) ? $orcamento['chave'] : set_value('chave') ?>">
    </div>

    <?php echo form_close(); ?>
</div>

<?php echo $this->include('_common/components/modalNovaCategoria') ?>

<?php echo $this->endSection('content') ?>