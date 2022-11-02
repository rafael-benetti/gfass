<?php echo $this->extend('_common/layout') ?>
<?php echo $this->section('content') ?>

<script>
    function changeBackgroundMes(link) {
        $('.link-meses').removeClass('bg-warning');
        $(link).addClass('bg-warning');
    }
</script>

<div class="row no-gutters d-flex justify-content-center mb-3">
    <?php $meses = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12] ?>
    <?php foreach ($meses as $mes_loop) : ?>
        <?php $classBg = $mes_loop == date('m') ? 'bg-warning' : '' ?>
        <a href="javascript:;" class="nav-link link-meses <?php echo $classBg ?> " onclick="changeBackgroundMes(this);graficoPorCategoria(<?php echo $ano ?>, <?php echo $mes_loop ?>) "><span class="text-uppercase small"><?php echo nomeMes($mes_loop) ?></span></a>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        Gráfico Atual por Categoria
    </div>
    <div class="card-body">
        <div id="chart_div_atual" style="width: 100%; height: 400px;"></div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header">
        Gráfico Anual
    </div>
    <div class="card-body">
        <div id="chart_div_anual" style="width: 100%; height: 400px;"></div>
    </div>
</div>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="<?php echo base_url('assets/jquery/jquery-3.5.1.min.js') ?>"></script>
<!-- <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> -->

<script>
    //Carregar a API de visualização do pacote gráfico.
    google.charts.load('current', {
        'packages': [
            'corechart',
            'bar'
        ],
        'language': 'pt-BR'
    });

    google.charts.setOnLoadCallback(callGraficos);

    function callGraficos() {
        graficoAnual();
        graficoPorCategoria();
    }

    function graficoPorCategoria(ano, mes) {
        $.ajax({
            url: base_url + '/ajax/grafico/getPorCategoria',
            method: 'POST',
            data: {
                ano: ano,
                mes: mes
            },
            dataType: 'json',
            async: true
        }).done(function(responseText) {
            var data = new google.visualization.DataTable(responseText);
            var formatter = new google.visualization.NumberFormat({
                decimalSymbol: ',',
                groupingSymbol: '.',
                negativeColor: 'red',
                negativeParens: true,
                prefix: 'R$ '
            });

            formatter.format(data, 1);
            var chart = new google.visualization.BarChart(document.getElementById('chart_div_atual'));

            var options = {
                animation: {
                    duration: 500,
                    easing: 'out',
                    startup: true
                },
                title: 'Análise mensal por Categoria',
                chartArea: {
                    width: '80%'
                },
                legend: {
                    position: 'top',
                    alignment: 'center'
                },
                hAxis: {
                    format: 'currency',
                    title: 'Total',
                    minValue: 0
                },
                vAxis: {
                    vAxis: {
                        title: 'Categoria'
                    },
                    titleTextStyle: {
                        color: 'red'
                    }
                },
                colors: ['green', 'red']
            }
            chart.draw(data, options);

        });
    }


    function graficoAnual() {
        $.ajax({
            url: base_url + '/ajax/grafico/getPorAno',
            dataType: 'json',
            async: true
        }).done(function(responseText) {
            var data = new google.visualization.DataTable(responseText);
            var formatter = new google.visualization.NumberFormat({
                decimalSymbol: ',',
                groupingSymbol: '.',
                negativeColor: 'red',
                negativeParens: true,
                prefix: 'R$ '
            });
            formatter.format(data, 1);
            formatter.format(data, 2);

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_anual'));
            var options = {
                legend: {
                    position: 'top',
                    alignment: 'center'
                },
                title: 'Análise do ano atual',
                chartArea: {
                    'width': '100%',
                    'height': '80%'
                },
                vAxis: {
                    titleTextStyle: {
                        color: 'red'
                    }
                },
                colors: [
                    'green', 'red'
                ],
                animation: {
                    duration: 500,
                    easing: 'out',
                    startup: true
                }
            }
            chart.draw(data, options);
        });
    }
</script>

<?php echo $this->endSection('content') ?>