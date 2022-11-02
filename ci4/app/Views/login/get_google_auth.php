<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>

<div class="row">
    <div class="col-sm-12">
        <div class="row d-flex justify-content-center">
            <img src="<?php echo base_url('assets/imagens/logo_php_exp.png') ?>" alt="Logo do GFASS" style="height: 150px;">
        </div>
        <div class="row d-flex justify-content-center">
            <h2>Controle financeiro simples feito com PHP e CodeIgniter 4.0</h2>
            <p>Aprenda a programar em PHP e CodeIgniter 4.0. <a href="https://www.codeigniter.com.br/curso" target="_blank">https://codeigniter.com.br/curso</a></p>
        </div>
    </div>
</div>
<hr>
<div class="row d-flex justify-content-center">
    <div class="card">
        <div class="card-header">
            Autenticação em 2 passos
        </div>
        <div class="card-body">
            <p>Digite abaixo o código gerado pelo Google Authenticator:</p>
            <div class="row d-flex justify-content-center mb-3">
                <?php echo form_open('login/checkGoogleAuth', ['autocomplete' => 'off']) ?>
                <div class="form-group">
                    <input type="text" name="code" class="form-control mr-2" placeholder="Digite o código" autofocus>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" value="1" name="salvar" id="salvar" checked>
                    <label for="salvar" class="form-check-label"> Não perguntar novamente neste dispositivo</label>
                </div>
                <input type="hidden" name="chave" value="<?php echo $chave ?>">
                <input type="submit" value="OK" class="btn btn-primary">
                </form>
            </div>
            <div class="d-flex justify-content-center">
                <?php $mensagem = session()->getFlashdata('mensagem'); ?>
                <?php if (!empty($mensagem)) : ?>
                    <div class="alert alert-info text-center">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>



<?php echo $this->endSection('content') ?>