<?php
    include "./vistas/inc/admin_security.php";
?>
<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de rooles
    </h3>
    <p class="text-justify">
    <strong><?php echo $_SESSION['nombre_svi']." ".$_SESSION['apellido_svi']; ?></strong> Te encuentras en el apartado de Rooles donde podras visualizar y crear nuevos rooles, para asi llevar un buen control de las actividades de tus empleados.
</p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <li>
            <a href="<?php echo SERVERURL; ?>cashier-new/">
                <i class="fas fa-plus-circle fa-fw"></i> &nbsp; Nuevo rol
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>cashier-list/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de rooles
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>cashier-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; Buscar rol
            </a>
        </li>
    </ul>	
</div>

<div class="container-fluid">
    <?php
        require_once "./controladores/rolControlador.php";
        $ins_caja = new cajaControlador();

        echo $ins_caja->paginador_caja_controlador($pagina[1],15,$pagina[0],"");
    ?>
</div>