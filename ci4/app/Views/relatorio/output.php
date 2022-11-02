<style>
    .container {
        width: 100%;
    }

    h1 {
        background-color: #ccc;
        margin: 0;
        padding: 10px;
        text-align: center;
    }

    p {
        margin: 0;
        text-align: center;
        background-color: #ccc;
        padding-bottom: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    tr th {
        padding: 15px;
        color: #fff;
        background-color: #555;
        border-right: 1px solid;
    }

    tr th:last-child {
        border-right: none;
    }

    table,
    tr,
    td {
        border: 1px solid;
        padding: 10px;

    }

    .text-success {
        color: forestgreen;
    }

    .text-danger {
        color: red;
    }

    .nome-categoria {
        font-weight: bolder;
        background-color: #ccc;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center
    }
</style>

<div class="container">
    <h1>Relatório de Lançamentos</h1>
    <p><small>Gerado em: <?php echo date("d/m/Y H:i:s") ?></small></p>
    <table>
        <thead>
            <tr class="bg-dark text-white">
                <th style="width: 100px">Descrição</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Consolidado?</th>
                <th>Notificar?</th>
                <th style="width: 100px">Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $categoria) : ?>
                <tr>
                    <td colspan="6" class="nome-categoria"><?php echo $categoria['descricao'] ?></td>
                </tr>
                <?php foreach ($categoria['lancamentos'] as $lancamento) : ?>
                    <tr>
                        <td><?php echo $lancamento['descricao'] ?></td>
                        <td><?php echo toDataBR($lancamento['data']) ?></td>
                        <td><?php echo $lancamento['tipo'] == 'r' ? 'Receita' : 'Despesa' ?></td>
                        <td><?php echo $lancamento['consolidado'] == 1 ? 'Sim' : 'Não' ?></td>
                        <td><?php echo $lancamento['notificar_por_email'] == '1' ? 'Sim' : 'Não' ?></td>
                        <td>R$ <?php echo number_format($lancamento['valor'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="text-right">Subtotal:</td>
                    <td colspan="1">R$ <?php echo isset($categoria['totalPorCategoria']) ? number_format($categoria['totalPorCategoria'], 2, ',', '.') : '' ?></td>
                </tr>

            <?php endforeach; ?>
            <?php if (empty($search)) : ?>
                <tr>
                    <td colspan="6" class="text-center">Totalizador</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-success text-right">Total de Receitas (A):</td>
                    <td colspan="1" class="text-success">R$ <?php echo number_format($totalReceitas, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-danger text-right">Total de Despesas (B):</td>
                    <td colspan="1" class="text-danger">R$ <?php echo number_format($totalDespesas, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right">Saldo (A - B):</td>
                    <?php if ($saldo > 0) : ?>
                        <td colspan="1" class="text-success font-weight-bold">R$ <?php echo number_format($saldo, 2, ',', '.'); ?></td>
                    <?php else : ?>
                        <td colspan="1" class="text-danger font-weight-bold">R$ <?php echo number_format($saldo, 2, ',', '.') ?></td>
                    <?php endif; ?>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>