<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>

<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/datepicker/css/datepicker.css') ?>">
<script type="text/javascript" src="<?php echo base_url('assets/datepicker/js/bootstrap-datepicker.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/datepicker/js/locales/bootstrap-datepicker.pt-BR.js') ?>" charset="UTF-8"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $(".reset").click(function() {
            $('#formBusca').find("input[type=text]").val('');
        });

        $('#dataInicial').datepicker({
            format: 'dd/mm/yyyy',
            todayBtn: 'linked',
            language: 'pt-BR',
            autoclose: true
        });
        $('#dataFinal').datepicker({
            format: 'dd/mm/yyyy',
            todayBtn: 'linked',
            language: 'pt-BR',
            autoclose: true
        });
    });
</script>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo anchor('', "Home") ?></li>
        <li class="breadcrumb-item active" aria-current="pag">Relatório</li>
    </ol>
</nav>
<h1>Relatório</h1>
<div class="card">
    <div class="card-header">
        Relatório
    </div>
    <div class="card-body">
        <?php echo form_open('relatorio/getDados', ['autocomplete' => 'off', 'method' => 'GET', 'id' => 'formBusca']) ?>
        <div class="mx-auto col-sm-8">
            <div class="form-row">
                <div class="col">
                    <label for="descricao">Descrição</label>
                    <input type="text" name="descricao" id="descricao" class="form-control" value="<?php echo !empty($descricao) ? $descricao : '' ?>">
                </div>
                <div class="col">
                    <label for="categorias_id">Categoria</label>
                    <?php echo form_dropdown('categorias_id', $dropDownCategorias, !empty($categorias_id) ? $categorias_id : '', ['class' => 'form-control']) ?>
                </div>
                <div class="col">
                    <label for="tipo">Tipo</label>
                    <?php echo form_dropdown('tipo', ['' => 'Tudo', 'd' => 'Despesa', 'r' => 'Receita'], !empty($tipo) ? $tipo : '', ['id' => 'tipo', 'class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-row mt-3">
                <div class="col">
                    <label for="dataInicial">Data Inicial</label>
                    <input type="text" name="dataInicial" id="dataInicial" class="form-control" value="<?php echo !empty($dataInicial) ? $dataInicial : '' ?>">
                </div>
                <div class="col">
                    <label for="dataFinal">Data Final</label>
                    <input type="text" name="dataFinal" id="dataFinal" class="form-control" value="<?php echo !empty($dataFinal) ? $dataFinal : '' ?>">
                </div>
                <div class="col">
                    <label for="tipo">Consolidados?</label>
                    <?php echo form_dropdown('consolidado', ['' => 'Todos', 1 => 'Sim', 2 => 'Não'], !empty($consolidado) ? $consolidado : '', ['id' => 'consolidado', 'class' => 'form-control']) ?>
                </div>
            </div>
            <div class="form-group mt-3">
                <div class="custom-control custom-radio">
                    <input type="radio" id="pdf" name="tipo_impressao" class="custom-control-input" value="pdf">
                    <label class="custom-control-label text-default" for="pdf">Gerar PDF</label>
                </div>
                <div class="custom-control custom-radio ">
                    <input type="radio" id="csv" name="tipo_impressao" class="custom-control-input" value="csv">
                    <label class="custom-control-label text-default" for="csv">Gerar CSV</label>
                </div>
            </div>
            <div class="form-row mt-3 d-flex justify-content-end">
                <input type="button" value="Limpar Campos" class="btn btn-secondary btn-sm reset">
            </div>
            <div class="form-row mt-3">
                <div class="col text-center">
                    <button type="submit" class="btn btn-primary" <?php echo checkLinkPermission('relatorio', 'getDados') ?>>Pesquisar</button>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
        <hr>
        <?php if (isset($categorias) > 0) : ?>
            <?php if ($totalLancamentos > 0) : ?>
                <p>Lançamentos retornados: <?php echo $totalLancamentos ?></p>
                <div class="table-responsive">
                    <table class="table table-stripped table-hover">
                        <thead>
                            <tr class="bg-dark text-white">
                                <th>Descrição</th>
                                <th>Data</th>
                                <th>Tipo</th>
                                <th>Consolidado?</th>
                                <th>Notificar?</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categorias as $categoria) : ?>
                                <tr>
                                    <td colspan="6" class="bg-light text-uppercase font-weight-bold"><?php echo $categoria['descricao'] ?></td>
                                </tr>
                                <?php foreach ($categoria['lancamentos'] as $lancamento) : ?>
                                    <?php $classeLancamentos = $lancamento['tipo'] === 'd' ? 'text-danger' : 'text-success' ?>
                                    <tr>
                                        <td class="pl-5 <?php echo $classeLancamentos ?>"><?php echo $lancamento['descricao'] ?></td>
                                        <td class="<?php echo $classeLancamentos ?>"><?php echo toDataBr($lancamento['data']) ?></td>
                                        <td class="<?php echo $classeLancamentos ?>"><?php echo $lancamento['tipo_formatado'] ?></td>
                                        <td class="<?php echo $classeLancamentos ?>"><?php echo $lancamento['consolidado_formatado'] ?></td>
                                        <td class="<?php echo $classeLancamentos ?>"><?php echo $lancamento['notificar_formatado'] ?></td>
                                        <td class="<?php echo $classeLancamentos ?>">R$ <?php echo number_format($lancamento['valor'], 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="5" class="text-rght">Subtotal:</td>
                                    <td class="text-uppercase font-weight-bold text-left">R$ <?php echo isset($categoria['totalPorCategoria']) ? number_format($categoria['totalPorCategoria'], 2, ',', '.') : '' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($descricao)) : ?>
                                <tr>
                                    <td colspan="6" class="bg-light font-weight-bold text-uppercase">Totalizador</td>
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
            <?php else : ?>
                <div class="text-center">Nenhum registro encontrado.</div>
            <?php endif; ?>
        <?php else : ?>
            <div class="text-center">Utilize os campos acima para criar sua pesquisa.</div>
        <?php endif; ?>
    </div>
</div>


<?php echo $this->endSection('content') ?>