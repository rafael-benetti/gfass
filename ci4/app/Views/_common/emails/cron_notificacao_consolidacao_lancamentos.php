<p>Prezado(a) <?php echo $usuario; ?></p>
<p>Este e-mail é para lhe informar que os lançamentos abaixo foram consolidados em seu controle financeiro na data de hoje: <?php echo date('d/m/Y')  ?></p>
<ul>
    <?php foreach ($categorias as $categoria) : ?>
        <ul>
            <?php foreach ($categoria as $nome_categoria => $lancamentos) : ?>
                <li><?php echo $nome_categoria ?></li>
                <ul>
                    <?php foreach ($lancamentos as $lancamento) : ?>
                        <li>
                            <?php echo $lancamento['descricao'] ?>
                            -
                            R$ <?php echo number_format($lancamento['valor'], '2', ',', '.') ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</ul>
<hr>
<p><small>Este é um e-mail automático. Não é preciso respondê-lo</small></p>
<p>GFASS - Sistema de Controle Financeiro 1.1</p>