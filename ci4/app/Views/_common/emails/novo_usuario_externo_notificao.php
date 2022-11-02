<p>Prezado(a) <?php echo $usuario ?></p>
<p>Para confirmar seu cadastro, por favor, clique no link abaixo:</p>
<a href="<?php echo base_url("cadastro/confirm/{$token_confirmacao}") ?>">Confirmar meu e-mail</a>
<hr>
<small>
    <p>Esta é uma mensagem automática, não é preciso respondê-la.</p>
    <p><?php echo anchor(site_url(), "GFASS - Sistema de Controle Financeiro 1.1") ?></p>
</small>