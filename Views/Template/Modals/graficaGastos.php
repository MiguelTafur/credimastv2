<?php if($grafica = "gastosMes"){ $gastosMes = $data;?>

<script>
    
    Highcharts.chart('graficaMesGastos', {
        chart: {
            type: 'line'
        },
        title: {
            text: 'Gastos de <?= $gastosMes['mes'].' del '.$gastosMes['anio'] ?>'
        },
        subtitle: {
            text: 'Total Gastos <?= SMONEY.' '.formatMoney($gastosMes['total']) ?>'
        },
        xAxis: {
            categories: [
                <?php 
                foreach ($gastosMes['gastos'] as $dia) {
                    echo $dia['dia'].",";
                }
                ?>
            ]
        },
        yAxis: {
            title: {
                text: 'CREDIMAST'
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: '',
            data: [
                <?php 
                foreach ($gastosMes['gastos'] as $dia) {
                    echo $dia['gasto'].",";
                }
                ?>
            ]
        }]});
</script>

<?php } ?>