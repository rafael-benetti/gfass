<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>

<div class="row p-3">
    <div class="col-sm-12">
        <div class="row d-flex justify-content-center">
            <img src="<?php echo base_url('assets/imagens/logo_php_exp.png') ?>" alt="Logo do GFASS" style="height: 150px;">
        </div>
        <div class="row d-flex justify-content-center">
       
            <p style="text-align: center;">GFASS - Controle financeiro simples</br> <a href="https://#" target="_blank">Baixe o APP na PlayStore</a></p>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <!--<div class="col-sm-6 p-5"> -->
    <div class="col-sm-12">
        <h2>Logue-se</h2>
        <?php echo form_open('login/signin') ?>
        <div class="form-group">
            <label for="login_email">E-mail</label>
            <input type="email" name="login_email" id="login_email" autocomplete="off" class="form-control">
            <?php if (!empty($errors['login_email'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['login_email'] ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="login_senha">Senha</label>
            <input type="password" name="login_senha" id="login_senha" class="form-control">
            <?php if (!empty($errors['login_senha'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['login_senha'] ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <?php echo anchor('cadastro/esqueciSenha', 'Esqueci a senha') ?>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Entrar</button>
        </div>
        <?php echo form_close() ?>
        <?php $errorLogin = session()->getFlashdata('errorLogin'); ?>
        <?php if (!empty($errorLogin)) : ?>
            <div class="alert alert-danger">
                <?php echo $errorLogin; ?>
            </div>
        <?php endif; ?>
    </div>


       <!-- CADASTRO 
    <div class="col-sm-6 p-5">
        <h2>Cadastre-se</h2>
        <?php /* echo form_open('cadastro/store') ?>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" class="form-control" value="<?php echo set_value('email') ?>">
            <?php if (!empty($errors['email'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['email'] ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="nome">Nome Completo</label>
            <input type="text" name="nome" id="nome" class="form-control" value="<?php echo set_value('nome') ?>">
            <?php if (!empty($errors['nome'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['nome'] ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="senha">Senha</label>
            <input type="password" name="senha" id="senha" class="form-control">
            <?php if (!empty($errors['senha'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['senha'] ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <label for="senha_confirm">Repita a Senha</label>
            <input type="password" name="senha_confirm" id="senha_confirm" class="form-control">
            <?php if (!empty($errors['senha_confirm'])) : ?>
                <div class="alert alert-danger mt-2"><?php echo $errors['senha_confirm'] ?></div>
            <?php endif; ?>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Cadastrar-se</button>
               <!-- <p>Teste grátis por 30 dias</p> -->
        </div>
        <?php echo form_close() ?>
        <?php $mensagem = session()->getFlashdata('mensagem'); ?>
        <?php if (!empty($mensagem)) : ?>
            <div class="alert alert-success">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; */?>
    </div> -->


</div>

<?php echo $this->endSection('content') ?>