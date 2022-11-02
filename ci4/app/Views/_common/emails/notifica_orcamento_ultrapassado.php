<p>Prezado(a) <?php echo $usuario ?></p>
<p>Este é um e-mail para lhe informar que os gastos da categoria <strong><?php echo $categoria ?></strong> acabam de atingir 80% ou mais do valor do orçamento cadastrado.</p>
<p>Veja abaixo:</p>
<ul>
    <li>Total lançado na categoria <strong><?php echo $categoria ?></strong> neste mês: R$ <?php echo number_format($total_categoria, '2', ',', '.') ?></li>
    <li>Valor cadastrado para o orçamento <?php echo $nome_orcamento ?>: R$ <?php echo number_format($total_orcamento, '2', ',', '.') ?></li>
    <li>Orçamento ainda disponível: R$ <?php echo number_format((float) ($total_orcamento - $total_categoria), '2', ',', '.') ?></li>
</ul>
<hr>
<p>Você configurou o orçamento acima para lhe enviar uma mensagem de aviso quando o total lançado atingisse 80% ou mais do valor do orçamento.</p>
<p><small>Esta é uma mensagem automática. Não é preciso respondê-la.</small></p>
<p><small>GFASS - Sistema de Controle Financeiro 1.1</small></p>