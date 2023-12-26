<?php 
	if($grafica = "cobradoAnio"){
		$cobradoAnio = $data;
 ?>
 <script>
 	Highcharts.chart('graficaCAnio', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Cobrado del año <?= $cobradoAnio['anio'] ?> '
      },
      subtitle: {
        text: 'Estadísticas de Gastos por mes<br><b>Total: <?= $cobradoAnio['totalCobrado'] ?></b> '
      },
      xAxis: {
          type: 'category',
          labels: {
              rotation: -45,
              style: {
                  fontSize: '13px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }
      },
      yAxis: {
          min: 0,
          title: {
              text: ''
          }
      },
      legend: {
          enabled: false
      },
      tooltip: {
          pointFormat: ''
      },
      series: [{
          name: 'Population',
          data: [
            <?php 
              foreach ($cobradoAnio['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['cobrado']."],";
              }
             ?>                 
          ],
          dataLabels: {
              enabled: true,
              rotation: -90,
              color: '#FFFFFF',
              align: 'right',
              format: '{point.y:.1f}', // one decimal
              y: 10, // 10 pixels down from the top
              style: {
                  fontSize: '13px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }
      }]
  });
 </script>

 <?php } ?>