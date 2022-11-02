<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>

</script>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo base_url(); ?>">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Esqueci a senha</li>
    </ol>
</nav>

<h1>Esqueci a senha</h1>
<div class="card">
    <div class="card-header"> Digite abaixo o e-mail utilizado no cadastro </div>
    <div class="card-body">
        <div class="col-sm-6">
            <?php echo form_open('cadastro/get') ?>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" name="email" id="email" class='form-control' required autofocus>
            </div>
            <input type="submit" value="Continuar" class="btn btn-primary">

            <?php if (isset($mensagem)) : ?>
                <div class="alert alert-info mt-2"><?php echo $mensagem; ?></div>
            <?php endif; ?>
            </form>
        </div>
    </div>
</div>


<?php echo $this->endSection('content') ?>