<?php 

if($grafica = "ventasMes"){ $ventasMes = $data; ?>

	<script>
		Highcharts.chart('graficaMes', {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Ventas de <?= $ventasMes['mes'].' del '.$ventasMes['anio'] ?>'
            },
            subtitle: {
                text: 'Total Ventas <?= SMONEY.' '.formatMoney($ventasMes['total']) ?>'
            },
            xAxis: {
                categories: [
                  <?php 
                    foreach ($ventasMes['ventas'] as $dia) {
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
                    foreach ($ventasMes['ventas'] as $dia) {
                      echo $dia['monto']['total'].",";
                    }
                  ?>
                ]
            }]});
	</script>

<?php } ?>
