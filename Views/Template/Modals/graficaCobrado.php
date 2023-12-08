<?php if($grafica = "cobradoMes"){ $cobradoMes = $data; ?>

	<script>
		Highcharts.chart('graficaMesCobrado', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Cobrado de <?= $cobradoMes['mes'].' del '.$cobradoMes['anio'] ?>'
            },
            subtitle: {
                text: 'Total Cobrado <?= SMONEY.' '.formatMoney($cobradoMes['total']) ?>'
            },
            xAxis: {
                categories: [
                  <?php 
                    foreach ($cobradoMes['ventas'] as $dia) {
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
                    foreach ($cobradoMes['ventas'] as $dia) {
                      echo $dia['cobrado'].",";
                    }
                  ?>
                ]
            }]});
	</script>

<?php } ?>