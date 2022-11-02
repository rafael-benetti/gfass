<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>

</script>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Esqueci a senha</li>
    </ol>
</nav>
<h1>Redefinição de Senha</h1>
<div class="card">
    <div class="card-header"> Digite abaixo sua nova senha:</div>
    <div class="card-body">
        <div class="col-sm-6">
            <?php echo form_open('cadastro/update') ?>
            <div class="form-group">
                <label for="senha">Nova senha</label>
                <input type="password" name="senha" id="senha" class='form-control' required autofocus>
                <?php if (!empty($errors['senha'])) : ?>
                    <div class="alert alert-danger mt-2"><?php echo $errors['senha'] ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="senha_confirm">Repita a nova senha</label>
                <input type="password" name="senha_confirm" id="senha_confirm" class='form-control' required>
                <?php if (!empty($errors['senha_confirm'])) : ?>
                    <div class="alert alert-danger mt-2"><?php echo $errors['senha_confirm'] ?></div>
                <?php endif; ?>
            </div>
            <input type="submit" value="Salvar" class="btn btn-primary">

            <?php if (isset($mensagem)) : ?>
                <div class="alert alert-info mt-2"><?php echo $mensagem; ?></div>
            <?php endif; ?>
            <input type="hidden" name="chave" value="<?php echo $chave; ?>">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            </form>
        </div>
    </div>
</div>
</div>

<?php echo $this->endSection('content') ?>