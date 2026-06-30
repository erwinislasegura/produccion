<header class="main-header">
  <div class="d-flex align-items-center logo-box pl-20">
    <a href="#" class="waves-effect waves-light nav-link rounded d-none d-md-inline-block push-btn" data-toggle="push-menu" role="button">
      <img src="../../api/cryptioadmin10/html/images/svg-icon/collapse.svg" class="img-fluid svg-icon" alt="">
    </a>
    <a href="index.php" class="logo">
      <div class="logo-lg">
        <span class="light-logo"><img src="../../assest/img/logo.png" alt="logo"></span>
        <span class="dark-logo"><img src="../../assest/img/logo.png" alt="logo"></span>
      </div>
    </a>
  </div>
  <nav class="navbar navbar-static-top pl-10">
    <div class="app-menu">
      <ul class="header-megamenu nav">
        <li class="btn-group nav-item d-md-none">
          <a href="#" class="waves-effect waves-light nav-link rounded push-btn" data-toggle="push-menu" role="button">
            <img src="../../api/cryptioadmin10/html/images/svg-icon/collapse.svg" class="img-fluid svg-icon" alt="">
          </a>
        </li>
        <li class="btn-group nav-item">
          <a href="#" data-provide="fullscreen" class="waves-effect waves-light nav-link rounded full-screen" title="Full Screen">
            <img src="../../api/cryptioadmin10/html/images/svg-icon/fullscreen.svg" class="img-fluid svg-icon" alt="">
          </a>
        </li>
        <li class="btn-group nav-item">
          <div class="search-bx ml-10">
            <div class="input-group" style="font-size: 12px;">
              <?php
              if (isset($_SESSION["NOMBRE_USUARIO"])) {
                $ARRAYEMPRESAS = $EMPRESA_ADO->verEmpresa($EMPRESAS);
                if ($ARRAYEMPRESAS) {
                  echo $ARRAYEMPRESAS[0]['NOMBRE_EMPRESA'];
                } else {
                  echo "<script type='text/javascript'> location.href ='iniciarSessionSeleccion.php';</script>";
                }
              } else {
                echo "<script type='text/javascript'> location.href ='iniciarSession.php';</script>";
              }
              ?>
              <br>
              <?php
              if (isset($_SESSION["NOMBRE_USUARIO"])) {
                $ARRAYPLANTAS = $PLANTA_ADO->verPlanta($PLANTAS);
                if ($ARRAYPLANTAS) {
                  echo $ARRAYPLANTAS[0]['NOMBRE_PLANTA'];
                } else {
                  echo "<script type='text/javascript'> location.href ='iniciarSessionSeleccion.php';</script>";
                }
              } else {
                echo "<script type='text/javascript'> location.href ='iniciarSession.php';</script>";
              }
              ?>
              <br>
              <?php
              if (isset($_SESSION["NOMBRE_USUARIO"])) {
                $ARRAYTEMPORADAS = $TEMPORADA_ADO->verTemporada($TEMPORADAS);
                if ($ARRAYTEMPORADAS) {
                  echo $ARRAYTEMPORADAS[0]['NOMBRE_TEMPORADA'];
                } else {
                  echo "<script type='text/javascript'> location.href ='iniciarSessionSeleccion.php';</script>";
                }
              } else {
                echo "<script type='text/javascript'> location.href ='iniciarSession.php';</script>";
              }
              ?>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="navbar-custom-menu r-side">
      <ul class="nav navbar-nav">
        <li class="dropdown notifications-menu">
          <a href="#" class="waves-effect waves-light dropdown-toggle" data-toggle="dropdown" title="Notifications">
            <img src="../../api/cryptioadmin10/html/images/svg-icon/notifications.svg" class="img-fluid svg-icon" alt="">
          </a>
          <ul class="dropdown-menu animated bounceIn">
            <li class="header">
              <div class="p-20">
                <div class="flexbox">
                  <div>
                    <h4 class="mb-0 mt-0">Notificaciones</h4>
                  </div>
                  <div>
                    <a href="#" class="text-danger">Limpiar Todo</a>
                  </div>
                </div>
              </div>
            </li>
            <li>
              <ul class="menu sm-scrol">
                <li>
                  <a href="#">
                    <i class="fa fa-users text-info"></i> Curabitur id eros quis nunc suscipit blandit.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-warning text-warning"></i> Duis malesuada justo eu sapien elementum, in semper diam posuere.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-users text-danger"></i> Donec at nisi sit amet tortor commodo porttitor pretium a erat.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-shopping-cart text-success"></i> In gravida mauris et nisi
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-user text-danger"></i> Praesent eu lacus in libero dictum fermentum.
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-user text-primary"></i> Nunc fringilla lorem
                  </a>
                </li>
                <li>
                  <a href="#">
                    <i class="fa fa-user text-success"></i> Nullam euismod dolor ut quam interdum, at scelerisque ipsum imperdiet.
                  </a>
                </li>
              </ul>
            </li>
            <li class="footer">
              <a href="#">Ver Todo</a>
            </li>
          </ul>
        </li>
        <li class="dropdown user user-menu">
          <a href="#" class="waves-effect waves-light dropdown-toggle" data-toggle="dropdown" title="User">
            <img src="../../api/cryptioadmin10/html/images/svg-icon/user.svg" class="rounded svg-icon" alt="" />
          </a>
          <ul class="dropdown-menu animated flipInX">
            <li class="user-header bg-img" style="background-image: url(../../api/cryptioadmin10/html/images/user-info.jpg)" data-overlay="3">
              <div class="flexbox align-self-center">
                <img src="../../api/cryptioadmin10/html/images/avatar/7.jpg" class="float-left rounded-circle" alt="User Image">
                <h4 class="user-name align-self-center">
                  <?php
                  if (isset($_SESSION["NOMBRE_USUARIO"])) {
                    $ARRAYNOMBRESUSUARIOSLOGIN = $USUARIO_ADO->ObtenerNombreCompleto($IDUSUARIOS);
                    $NOMBRESUSUARIOSLOGIN = $ARRAYNOMBRESUSUARIOSLOGIN[0]["NOMBRE_COMPLETO"];
                  }
                  ?>
                  <span> <?php echo $NOMBRESUSUARIOSLOGIN; ?> </span>
                  <br>
                  <small>
                    <?php
                    if (isset($_SESSION["NOMBRE_USUARIO"])) {
                      $ARRAYTUSUARIO = $TUSUARIO_ADO->verTusuario($_SESSION["TIPO_USUARIO"]);

                      if ($ARRAYTUSUARIO) {
                        echo $ARRAYTUSUARIO[0]['NOMBRE_TUSUARIO'];
                      }
                    } else {
                      session_destroy();
                      echo "<script type='text/javascript'> location.href ='iniciarSession.php';</script>";
                    }
                    ?>
                  </small>
                </h4>
              </div>
            </li>
            <li class="user-body">
              <a class="dropdown-item" href="verUsuario.php"><i class="ion ion-person"></i> Mi Perfil</a>
              <a class="dropdown-item" href="editarUsuario.php"><i class="ion ion-email-unread"></i> Editar Perfil</a>
              <a class="dropdown-item" href="editarUsuarioClave.php"><i class="ion ion-settings"></i> Cambiar Contrasena</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="verUsuarioActividad.php"><i class="ion ion-bag"></i> Mi Actividad</a>
              <div class="dropdown-divider"></div>
              <span class="dropdown-header">Cambiar Módulo</span>
              <a class="dropdown-item" href="../../fruta/index.php"><i class="ion ion-leaf"></i> Fruta</a>
              <a class="dropdown-item" href="../../exportadora/index.php"><i class="ion ion-plane"></i> Exportadora</a>
              <a class="dropdown-item" href="../../material/index.php"><i class="ion ion-cube"></i> Materiales</a>
              <a class="dropdown-item" href="../../estadistica/index.php"><i class="ion ion-stats-bars"></i> Estadísticas</a>
              <a class="dropdown-item" href="../../calidad/index.php"><i class="ion ion-ribbon-b"></i> Calidad</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" data-toggle="modal" data-target="#modal-empresa" title="Cambiar">
                <i class="ti-settings"></i>Cambiar Empresa
              </a>
              <a class="dropdown-item" data-toggle="modal" data-target="#modal-planta" title="Cambiar">
                <i class="ti-settings"></i>Cambiar Planta
              </a>
              <div class="dropdown-divider"></div>
              <div class="p-10">
                <center>
                  <form class="form-one-line" data-form-layout="oneline-1" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="post">
                    <button type="submit" class="btn btn-rounded btn-danger " name="CERRARS" value="CERRARS">
                      <i class="ion-log-out"></i>
                      Cerrar Sesion
                    </button>
                  </form>
                </center>
              </div>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
<?php
$ARRAYEMPRESACAMBIAR = $EMPRESA_ADO->listarEmpresaCBX();
$ARRAYPLANTACAMBIAR = $PLANTA_ADO->listarPlantaPropiaCBX();
?>
<!-- modal Area -->
<div class="modal center-modal fade" id="modal-empresa" tabindex="-1">
  <form class="form-one-line" data-form-layout="oneline-2" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cambiar </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>
          <div class="form-group">
            <label>Empresa</label>
            <select class="form-control select2" id="EMPRESACAMBIAR" name="EMPRESACAMBIAR" style="width: 100%;" <?php echo $DISABLEDMENU; ?>>
              <option></option>
              <?php foreach ($ARRAYEMPRESACAMBIAR as $r) : ?>
                <?php if ($ARRAYEMPRESACAMBIAR) {    ?>
                  <option value="<?php echo $r['ID_EMPRESA']; ?>" <?php if ($EMPRESAS == $r['ID_EMPRESA']) {
                                                                    echo "selected";
                                                                  } ?>> <?php echo $r['NOMBRE_EMPRESA'] ?> </option>
                <?php } else { ?>
                  <option>No Hay Datos Registrados </option>
                <?php } ?>
              <?php endforeach; ?>
            </select>
            <label id="val_empresa" class="validacion"> </label>
          </div>
          </p>
        </div>
        <div class="modal-footer modal-footer-uniform">
          <button type="button" class="btn btn-rounded btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-rounded btn-primary float-right" id="CAMBIARE" name="CAMBIARE" <?php echo $DISABLEDMENU; ?>>Cambiar</button>
        </div>
      </div>
    </div>
  </form>
</div>
<div class="modal center-modal fade" id="modal-planta" tabindex="-1">
  <form class="form-one-line" data-form-layout="oneline-3" style="display:flex;flex-wrap:wrap;align-items:flex-end;gap:0.65rem;padding:0.65rem 0;border-bottom:1px solid #3a3a3a;" method="POST">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Cambiar </h5>
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>
          <div class="form-group">
            <label>Planta</label>
            <select class="form-control select2" id="PLANTACAMBIAR" name="PLANTACAMBIAR" style="width: 100%;" <?php echo $DISABLEDMENU; ?>>
              <option></option>
              <?php foreach ($ARRAYPLANTACAMBIAR as $r) : ?>
                <?php if ($ARRAYPLANTACAMBIAR) {    ?>
                  <option value="<?php echo $r['ID_PLANTA']; ?>" <?php if ($PLANTAS == $r['ID_PLANTA']) {
                                                                    echo "selected";
                                                                  } ?>> <?php echo $r['NOMBRE_PLANTA'] ?> </option>
                <?php } else { ?>
                  <option>No Hay Datos Registrados </option>
                <?php } ?>
              <?php endforeach; ?>
            </select>
            <label id="val_planta" class="validacion"> </label>
          </div>
          </p>
        </div>
        <div class="modal-footer modal-footer-uniform">
          <button type="button" class="btn btn-rounded btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-rounded btn-primary float-right" id="CAMBIARP" name="CAMBIARP" <?php echo $DISABLEDMENU; ?>>Cambiar</button>
        </div>
      </div>
    </div>
  </form>
</div>

<aside class="main-sidebar">
  <section class="sidebar position-relative">
    <ul class="sidebar-menu" data-widget="tree">
      <li>
        <a href="index.php">
          <img src="../../api/cryptioadmin10/html/images/svg-icon/sidebar-menu/dashboard.svg" class="svg-icon" alt="">
          <span>Inicio</span>
        </a>
      </li>
      <li class="header">MENÚ</li>
      <li class="treeview">
        <a href="#">
          <img src="../../api/cryptioadmin10/html/images/svg-icon/sidebar-menu/dashboard.svg" class="svg-icon" alt="">
          <span>Calidad de la fruta</span>
          <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li class="treeview">
            <a href="#">Rechazo <span class="pull-left-container"><i class="fa fa-angle-right pull-right"></i></span></a>
            <ul class="treeview-menu">
              <li><a href="registroRechazomp.php">Registro Rechazo MP</a></li>
              <li><a href="listarRechazomp.php">Agrupado Rechazo MP</a></li>
              <li><a href="listarRechazompDetallado.php">Detallado Rechazo MP</a></li>
              <li><a href="registroRechazopt.php">Registro Rechazo PT</a></li>
              <li><a href="listarRechazopt.php">Agrupado Rechazo PT</a></li>
              <li><a href="listarRechazoptDetallado.php">Detallado Rechazo PT</a></li>
            </ul>
          </li>
          <li class="treeview">
            <a href="#">Levantamiento <span class="pull-left-container"><i class="fa fa-angle-right pull-right"></i></span></a>
            <ul class="treeview-menu">
              <li><a href="registroLevantamientomp.php">Registro Levantamiento MP</a></li>
              <li><a href="listarLevantamientomp.php">Agrupado Levantamiento MP</a></li>
              <li><a href="listarLevantamientompDetallado.php">Detallado Levantamiento MP</a></li>
              <li><a href="registroLevantamientopt.php">Registro Levantamiento PT</a></li>
              <li><a href="listarLevantamientopt.php">Agrupado Levantamiento PT</a></li>
              <li><a href="listarLevantamientoptDetallado.php">Detallado Levantamiento PT</a></li>
            </ul>
          </li>
        </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <img src="../../api/cryptioadmin10/html/images/svg-icon/sidebar-menu/tickers.svg" class="svg-icon" alt="">
          <span>Inspecciones</span>
          <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="../../calidad/inspection-mp/index.php">Inspección MP</a></li>
          <li><a href="../../calidad/vista/listarInspectionmp.php">Agrupado MP</a></li>
          <li><a href="../../calidad/inspection-pt/index.php">Inspección PT</a></li>
          <li><a href="../../calidad/vista/listarInspectionpt.php">Agrupado PT</a></li>
          <li><a href="../../calidad/inspection-destination/index.php">Inspección en Destino</a></li>
          <li><a href="../../calidad/vista/listarInspectionDestino.php">Agrupado Destino</a></li>
        </ul>
      </li>
      <li class="treeview">
        <a href="#">
          <img src="../../api/cryptioadmin10/html/images/svg-icon/sidebar-menu/forms3.svg" class="svg-icon" alt="">
          <span>Mantenedor</span>
          <span class="pull-right-container"><i class="fa fa-angle-right pull-right"></i></span>
        </a>
        <ul class="treeview-menu">
          <li><a href="../../calidad/vista/mantenedorInspectionMpInspectores.php">Inspectores</a></li>
          <li><a href="../../calidad/vista/mantenedorInspectionMpDefectosCalidad.php">Defectos de Calidad</a></li>
          <li><a href="../../calidad/vista/mantenedorInspectionMpDefectosCondicion.php">Defectos de Condición</a></li>
        </ul>
      </li>
      <li class="header">OPCIONES</li>
      <li><a href="../../calidad/vista/listarExiRegistroCalidad.php">Registro de Calidad</a></li>
      <li><a href="../../calidad/vista/listarResumenRegistroCalidad.php">Agrupado Registro de Calidad</a></li>
      <li><a href="../../calidad/vista/listarProductorDocumento.php">Documentos por Productor</a></li>
    </ul>
  </section>
</aside>
