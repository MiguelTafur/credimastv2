<?php 
	if($grafica = "ventasAnio"){
		$ventasAnio = $data;
    //dep($ventasAnio);exit;
 ?>
 <script>
 	Highcharts.chart('graficaAnio', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Ventas del año <?= $ventasAnio['anio'] ?> '
      },
      subtitle: {
        text: 'Estadísticas de Gastos por mes<br><b>Total: <?= $ventasAnio['totalVentas'] ?></b> '
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
              foreach ($ventasAnio['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['ventas']."],";
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