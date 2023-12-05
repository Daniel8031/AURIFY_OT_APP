<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-file-pdf fa-fw"></i> &nbsp; Reportes realizados
    </h3>
    <?php include "./vistas/desc/desc_movimiento.php"; ?>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <li>
            <a href="<?php echo SERVERURL; ?>movement-new/">
                <i class="fas fa-table fa-fw"></i> &nbsp; Nuevo reporte
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>movement-list/">
                <i class="fas fa-file-pdf fa-fw"></i> &nbsp; Reportes realizados
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>movement-search/">
                <i class="fas fa-search-dollar fa-fw"></i> &nbsp; Buscar reportes
            </a>
        </li>
    </ul>	
</div>

<div class="container-fluid">
    <?php
        require_once "./controladores/movimientoControlador.php";
        $ins_movimiento = new movimientoControlador();

        echo $ins_movimiento->paginador_movimiento_controlador($pagina[1],15,$pagina[0],"Listado","","");
    ?>
</div>