<?php 
	if($grafica = "gastosAnio"){
		$gastosAnio = $data;
    //dep($gastosAnio);exit;
 ?>
 <script>
 	Highcharts.chart('graficaGAnio', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Gastos del año <?= $gastosAnio['anio'] ?> '
      },
      subtitle: {
        text: 'Estadísticas de Gastos por mes<br><b>Total: <?= $gastosAnio['totalGastos'] ?></b> '
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
              foreach ($gastosAnio['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['gastos']."],";
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