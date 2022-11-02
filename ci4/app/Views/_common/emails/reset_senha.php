<p>Prezado(a) <?php echo $usuario ?></p>
<p>Recebemos uma solicitação de redefinição de senha.</p>
<p>Para redefinir sua senha, clique no link abaixo:</p>
<p><?php echo anchor("cadastro/resetSenha/{$token}", '>>> Redefinir senha') ?></p>
<p>Este link é válido por 2 a horas a partir do seu recebimento.</p>
<p>Se você não fez esta solicitação, por favor, desconsidere esta mensagem.</p>
<hr>
<small>
    <p>Esta é uma mensagem automática, não é preciso respondê-la</p>
    <p><a href="https://sistema.asaisurf.com.br">GFASS - Sistema de Controle Financeiro 1.1</a></p>
</small>