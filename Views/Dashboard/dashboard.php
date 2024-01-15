<?php headerAdmin($data); getModal('modalDashboard',$data);?>
    <main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-dashboard"></i><?= $data['page_title'] ?></h1>
        </div>
        <ul class="app-breadcrumb breadcrumb d-none d-lg-flex">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="<?= base_url(); ?>/dashboard">Dashboard</a></li>
        </ul>
      </div>
      <?php 
        if($data['pagamentos'] != 2)
        {
      ?>
      <div class="alert alert-danger" role="alert">
        <strong>Error Resumen - </strong> Debes cerrar el resumen anterior!
      </div>
      <?php } ?>
      
      <div class="row">
        <!-- USUARIOS -->
        <?php if(!empty($_SESSION['permisos'][2]['r']) AND$_SESSION['idUser'] == 1){ ?>
        <div class="col-md-6 col-lg-3">
          <a href="<?= base_url() ?>/usuarios" class="linkw">
            <div class="widget-small info coloured-icon"><i class="icon fa fa-users fa-3x"></i>
              <div class="info">
                <h4>Usuarios</h4>
                <p><b><?= $data['usuarios']; ?></b></p>
              </div>
            </div>
          </a>
        </div>
        <?php } ?>

        <!-- CLIENTES -->
        <?php if(!empty($_SESSION['permisos'][3]['r'])){ ?>
          <div class="col-md-6 col-lg-3">
            <a href="<?= base_url() ?>/clientes" class="linkw">
              <div class="widget-small info coloured-icon"><i class="icon fa fa-user fa-3x"></i>
                <div class="info">
                  <h4>Clientes</h4>
                  <p>
                    <b>Total: </b><i class="text-secondary"><?php if(!empty($data['clientes'])){echo $data['clientes'];}else{echo "0";} ?></i>
                  </p>
                  <p>
                    <b>Activos: </b><i class="text-success"><?php if(!empty($data['clientes'])){echo $data['prestamos'];}else{echo "0";} ?></i>
                  </p>
                </div>
              </div>
            </a>
          </div>
        <?php } ?>

        <!-- PRESTAMOS -->
        <?php if(!empty($_SESSION['permisos'][4]['d']) AND $data['prestamos'] > 0 AND $_SESSION['idRol'] == 1){ ?>
          <div class="col-md-6 col-lg-3">
            <a href="<?= base_url() ?>/prestamos" class="linkw">
              <div class="widget-small primary coloured-icon"><i class="icon fas fa-hand-holding-usd fa-3x"></i>
                <div class="info">
                  <h4>Prestamos</h4>
                  <p>
                    <b>Valor Neto:</b> 
                    <i class="text-secondary">
                      <?php if(!empty($data['prestamos'])){ echo $data['cartera']['monto'];}else{echo "0";} ?>
                    </i>
                  </p>
                  <p >
                    <b>Valor Activo:</b> <i class="text-success"><?php if(!empty($data['prestamos'])){ echo $data['cartera']['total'];}else{echo "0";} ?></i>
                  </p>
                </div>
              </div>
            </a>
          </div>
        <?php } ?>

        <!-- ESTIMADO -->
        <?php if(!empty($_SESSION['permisos'][1]['r'])) { ?>
        <div class="col-md-6 col-lg-3">
          <a href="#" class="linkw">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-usd fa-3x"></i>
              <div class="info">
                <h4>Estimado Cobrar</h4>
                <p><b><?php if(!empty($data['totalResumen'])){ echo $data['cartera']['parcela'];}else{echo "0";} ?></b></p>
              </div>
            </div>
          </a>
        </div>
        <?php } ?>
        
        <!-- CARTERA -->
        <?php if(!empty($_SESSION['permisos'][1]['d']) && $_SESSION['idRol'] == 1){ ?>
        <div class="col-md-6 col-lg-3">
          <a href="#" class="linkw">
            <div class="widget-small warning coloured-icon"><i class="icon fa fa-bank fa-3x"></i>
              <div class="info">
                <h4>Cartera</h4>
                <p>
                  <b>Total:</b> 
                  <i class="text-secondary">
                  <?php 
                    if(!empty($data['prestamos']))
                    { 
                      echo $data['cartera']['total'] + $data['totalCartera'];
                      
                    }else{
                      echo "0";
                    } 
                    ?>
                  </i>
                </p>
                <p>
                  <b>Caja:</b> 
                  <?php 
                    if($data['totalCartera'] > 0)
                    {
                  ?>
                  <i class="text-success">
                  <?php }else{ ?>
                    <i class="text-danger">                  
                  <?php } ?>
                  <?php if(!empty($data['totalCartera'])){ echo $data['totalCartera'];}else{echo "0";} ?>
                  </i>
                </p>
              </div>
            </div>
          </a>
        </div>
        <?php } ?>
      </div>

      <!-- INFORMACION GENEREAL -->
      <div class="tile py-3 m-0 mt-5">
        <h3 class="tile-title text-center">INFORMACIÓN GENERAL</h3>
        <div id="accordion">
          <h3>Últimos Resumenes</h3>
          <div>
            <div class="tile px-2">
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr class="text-center">
                      <th>Fecha</th>
                      <th>Base</th>
                      <th>Cobrado</th>
                      <th>Ventas</th>
                      <th>Gastos</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody id="ultimosResumenes">
                   
                  </tbody>
                </table>
              </div>
              <br>
              <button class="btn btn-warning btn-sm" onclick="fntViewDetalleR()"><i class="fa fa-search" aria-hidden="true"></i> Buscar Resumen</button>
            </div>
          </div>
          <h3>Últimos Préstamos Finalizados</h3>
          <div>
            <div class="tile">
              <div class="table-responsive">
                <table class="table table-striped table-sm">
                  <thead>
                    <tr class="text-center">
                      <th>Cierre</th>
                      <th>Cliente</th>
                      <th>Pagamentos</th>
                      <th>Detalles</th>
                    </tr>
                  </thead>
                  <tbody class="text-center">
                    <?php if(COUNT($data['ultimosPrestamo']) > 0){

                        for ($i=0; $i < COUNT($data['ultimosPrestamo']); $i++) { 
                          $arrPrestamos = explode("|",$data['ultimosPrestamo'][$i]);
                    ?>
                    <tr>
                      <td><?= $arrPrestamos[0] ?></td>
                      <td><?= $arrPrestamos[1] ?></td>
                      <td>
                        <a tabindex="0" role="button" class="btn btn-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="<?= $arrPrestamos[2]; ?>" title="PAGAMENTOS&nbsp; <small><?= substr($arrPrestamos[1], 0, 12 ); ?></small>">
                          <i class="fas fa-hand-holding-usd fa-sm text-white" aria-hidden="true"></i>
                        </a>
                      </td>
                      <td>
                        <a tabindex="0" role="button" class="btn btn-secondary btn-sm" data-toggle="popover" data-placement="left" data-content="<?= $arrPrestamos[3]; ?>" title="DETALLES&nbsp; <small><?= substr($arrPrestamos[1], 0, 12 ); ?></small>">
                          <i class="fas fa-eye fa-sm text-white" aria-hidden="true"></i>
                        </a>
                      </td>
                    </tr>
                  <?php }} ?>
                  </tbody>
                </table>
                <br>
              </div>
              <button class="btn btn-warning btn-sm" onclick="fntViewDetallePF()"><i class="fa fa-search" aria-hidden="true"></i> Buscar Préstamo F.</button>
            </div>
          </div>
        </div>
      </div>

      <!-- GRÁFICA MENSUAL -->                            
      <div class="tile py-3 m-0 mt-5">
        <h3 class="tile-title text-center">GRÁFICA POR DIA DEL MES</h3>
        <div id="accordion2">
          <h3>Cobrado</h3>
          <div>
            <div class="tile">
              <div class="container-title">
                <div class="dflex">
                  <input class="date-picker cobradoMes" name="cobradoMes" placeholder="Mes y Año">
                  <button type="button" class="btnVentaMes btn btn-info btn-sm"><i class="fas fa-search" onclick="fntSearchCMes()"></i></button>
                </div>
              </div>
              <div id="graficaMesCobrado"></div>
              <button class="btn btn-warning btn-sm" onclick="fntViewDetalleC()"><i class="fa fa-eye" aria-hidden="true"></i> Cobrado Detallado</button>
            </div>
          </div>
          <h3>Ventas</h3>
          <div>
            <div class="tile">
              <div class="container-title">
                <div class="dflex">
                  <input class="date-picker ventasMes" name="ventasMes" placeholder="Mes y Año">
                  <button type="button" class="btnVentaMes btn btn-info btn-sm"><i class="fas fa-search" onclick="fntSearchVMes()"></i></button>
                </div>
              </div>
              <div id="graficaMes"></div>
              <button class="btn btn-warning btn-sm" onclick="fntViewDetalleV()"><i class="fa fa-eye" aria-hidden="true"></i> Ventas Detalladas</button>
            </div>
          </div>
          <h3>Gastos</h3>
          <div>
            <div class="tile">
              <div class="container-title">
                <div class="dflex">
                  <input class="date-picker gastosMes" name="gastosMes" placeholder="Mes y Año">
                  <button type="button" class="btnGastosMes btn btn-info btn-sm"><i class="fas fa-search" onclick="fntSearchGMes()"></i></button>
                </div>
              </div>
              <div id="graficaMesGastos"></div>
              <button class="btn btn-warning btn-sm" onclick="fntViewDetalleG()"><i class="fa fa-eye" aria-hidden="true"></i> Gastos Detallados</button>
            </div>
          </div>
        </div>
      </div>

      <!-- GRÁFICA ANUAL -->
      <div class="tile py-3 m-0 mt-5">
      <h3 class="tile-title text-center">GRÁFICA POR MES DEL AÑO</h3>
        <div id="accordion3">
          <h3>Cobrado</h3>
          <div class="">
            <div class="tile">
              <div class="container-title">
                <div class="dflex">
                  <input class="cobradoAnio" name="cobradoAnio" placeholder="Ano" minlength="4" maxlength="4" onkeypress="return controlTag(event);">
                  <button type="button" class="btnCobradoAnio btn btn-info btn-sm" onclick="fntSearchCAnio()"> <i class="fas fa-search"></i> </button>
                </div>
              </div>
              <div id="graficaCAnio"></div>
            </div>
          </div>
          <h3>Ventas</h3>
          <div>
            <div class="tile">
              <div class="container-title">
                <div class="dflex">
                  <input class="ventasAnio" name="ventasAnio" placeholder="Ano" minlength="4" maxlength="4" onkeypress="return controlTag(event);">
                  <button type="button" class="btnVentasAnio btn btn-info btn-sm" onclick="fntSearchVAnio()"> <i class="fas fa-search"></i> </button>
                </div>
              </div>
              <div id="graficaAnio"></div>
            </div>
          </div>
          <h3>Gastos</h3>
          <div>
            <div class="tile">
              <div class="container-title">
                <div class="dflex">
                  <input class="gastosAnio" name="gastosAnio" placeholder="Ano" minlength="4" maxlength="4" onkeypress="return controlTag(event);">
                  <button type="button" class="btnGastosAnio btn btn-info btn-sm" onclick="fntSearchGAnio()"> <i class="fas fa-search"></i> </button>
                </div>
              </div>
              <div id="graficaGAnio"></div>
            </div>
          </div>
        </div>
      </div>
    </main>
<?php footerAdmin($data); ?>

<script>
  //COBRADO
  Highcharts.chart('graficaMesCobrado', {
      chart: {
          type: 'line'
      },
      title: {
          text: 'Cobrado de <?= $data['CobradoMDia']['mes'].' del '.$data['CobradoMDia']['anio'] ?>'
      },
      subtitle: {
          text: 'Total Cobrado <?= formatMoney($data['CobradoMDia']['total']) ?>'
      },
      xAxis: {
          categories: [
            <?php 
              foreach ($data['CobradoMDia']['ventas'] as $dia) {
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
              foreach ($data['CobradoMDia']['ventas'] as $cobrado) {
                echo $cobrado['cobrado'].",";
              }
            ?>
          ]
      }]});
  //VENTAS
  Highcharts.chart('graficaMes', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Ventas de <?= $data['ventasMDia']['mes'].' del '.$data['ventasMDia']['anio'] ?>'
    },
    subtitle: {
        text: 'Total Ventas <?= formatMoney($data['ventasMDia']['total']) ?>'
    },
    xAxis: {
        categories: [
          <?php 
            foreach ($data['ventasMDia']['ventas'] as $dia) {
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
            foreach ($data['ventasMDia']['ventas'] as $dia) {
              echo $dia['monto']['total'].",";
            }
          ?>
        ]
    }]});

  //GASTOS
  Highcharts.chart('graficaMesGastos', {
    chart: {
        type: 'line'
    },
    title: {
        text: 'Gastos de <?= $data['gastosMDia']['mes'].' del '.$data['gastosMDia']['anio'] ?>'
    },
    subtitle: {
        text: 'Total Gastos <?= formatMoney($data['gastosMDia']['total']) ?>'
    },
    xAxis: {
        categories: [
          <?php 
            foreach ($data['gastosMDia']['gastos'] as $dia) {
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
            foreach ($data['gastosMDia']['gastos'] as $dia) {
              echo $dia['gasto'].",";
            }
          ?>
        ]
     }]});

  
  //GRAFICAS ANIO
  
  //VENTAS
  Highcharts.chart('graficaAnio', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Ventas del año <?= $data['ventasAnio']['anio'] ?> '
      },
      subtitle: {
          text: 'Estadísticas de ventas por mes<br><b>Total: <?= $data['ventasAnio']['totalVentas'] ?></b> '
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
              foreach ($data['ventasAnio']['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['ventas']."],";
              }
             ?>                 
          ],
          dataLabels: {
              enabled: true,
              rotation: -90,
              color: '#FFFFFF',
              align: 'right',
              format: '{point.y:.0f}', // one decimal
              y: 10, // 10 pixels down from the top
              style: {
                  fontSize: '13px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }
      }]
  });

  //COBRADO
  Highcharts.chart('graficaCAnio', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Cobrado del año <?= $data['cobradoAnio']['anio'] ?> '
      },
      subtitle: {
          text: 'Estadísticas de cobrado por mes<br><b>Total: <?= $data['cobradoAnio']['totalCobrado'] ?></b> '
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
              foreach ($data['cobradoAnio']['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['cobrado']."],";
              }
             ?>                 
          ],
          dataLabels: {
              enabled: true,
              rotation: -90,
              color: '#FFFFFF',
              align: 'right',
              format: '{point.y:.0f}', // one decimal
              y: 10, // 10 pixels down from the top
              style: {
                  fontSize: '13px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }
      }]
  });

  //GASTOS
  Highcharts.chart('graficaGAnio', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Gastos del año <?= $data['gastosAnio']['anio'] ?> '
      },
      subtitle: {
          text: 'Estadísticas de Gastos por mes<br><b>Total: <?= $data['gastosAnio']['totalGastos'] ?></b> '
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
              foreach ($data['gastosAnio']['meses'] as $mes) {
                echo "['".$mes['mes']."',".$mes['gastos']."],";
              }
             ?>                 
          ],
          dataLabels: {
              enabled: true,
              rotation: -90,
              color: '#FFFFFF',
              align: 'right',
              format: '{point.y:.0f}', // one decimal
              y: 10, // 10 pixels down from the top
              style: {
                  fontSize: '13px',
                  fontFamily: 'Verdana, sans-serif'
              }
          }
      }]
  });

</script>
    