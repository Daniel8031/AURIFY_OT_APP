<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-home fa-fw"></i> &nbsp; Home
    </h3>
   
    <p class="text-justify" >
    ¡Bienvenido <strong><?php echo $_SESSION['nombre_svi']." ".$_SESSION['apellido_svi']; ?></strong>! Este es el panel principal del sistema acá podrá encontrar atajos para acceder a los distintos listados de cada módulo del sistema.
    </p>
</div>
<div class="container-fluid">
    <div class="full-box tile-container">

    <?php
        /*---------- Inicio Opciones del administrador ----------*/
        if($_SESSION['cargo_svi']=="Administrador"){

        $total_cajas=$lc->datos_tabla("Normal","rol","rol_id",0);
    ?>
    <a href="<?php echo SERVERURL; ?>cashier-list/" class="tile">
        <div class="tile-tittle">Rooles</div>
        <div class="tile-icon">
            <i class="fas fa-id-card fa-fw"></i>
            <p><?php echo $total_cajas->rowCount(); ?> Registrados</p>
        </div>
    </a>
    <!-- <?php
        $total_cajas->closeCursor();
        $total_cajas=$lc->desconectar($total_cajas);

        
        $total_proveedores=$lc->datos_tabla("Normal","proveedor","proveedor_id",0); 
    ?> -->
    <!-- <a href="<?php echo SERVERURL; ?>provider-list/" class="tile">
        <div class="tile-tittle">Proveedores</div>
        <div class="tile-icon">
            <i class="fas fa-shipping-fast fa-fw"></i>
            <p><?php echo $total_proveedores->rowCount(); ?> Registrados</p>
        </div>
    </a> -->
    <?php
        $total_proveedores->closeCursor();
        $total_proveedores=$lc->desconectar($total_proveedores);

            
        $total_categorias=$lc->datos_tabla("Normal","categoria","categoria_id",0); 
    ?>
   
    <?php 
        $total_categorias->closeCursor();
        $total_categorias=$lc->desconectar($total_categorias);

        
        $total_usuarios=$lc->datos_tabla("Normal","usuario WHERE usuario_id!='1' AND usuario_id!='".$_SESSION['id_svi']."'","usuario_id",0); 
    ?>
    <a href="<?php echo SERVERURL; ?>user-list/" class="tile">
        <div class="tile-tittle">Usuarios</div>
        <div class="tile-icon">
            <i class="fas fa-user-tie fa-fw"></i>
            <p><?php echo $total_usuarios->rowCount(); ?> Registrados</p>
        </div>
    </a>
    <?php 
        $total_usuarios->closeCursor();
        $total_usuarios=$lc->desconectar($total_usuarios);
        } 
        /*---------- Fin Opciones del administrador ----------*/ 
    ?>

    <?php $total_productos=$lc->datos_tabla("Normal","producto","producto_id",0); ?>
    <a href="<?php echo SERVERURL; ?>product-list/" class="tile">
        <div class="tile-tittle">Juegos</div>
        <div class="tile-icon">
            <i class="fas fa-gamepad fa-fw"></i>
            <p><?php echo $total_productos->rowCount(); ?> Registrados</p>
        </div>
    </a>
    <?php
        $total_productos->closeCursor();
        $total_productos=$lc->desconectar($total_productos);
        
        
        $total_clientes=$lc->datos_tabla("Normal","cliente WHERE cliente_id!='1'","cliente_id",0); 
    ?>
    <a href="<?php echo SERVERURL; ?>client-list/" class="tile">
        <div class="tile-tittle">Clientes</div>
        <div class="tile-icon">
            <i class="fas fa-child fa-fw"></i>
            <p><?php echo $total_clientes->rowCount(); ?> Registrados</p>
        </div>
    </a>
    <?php
        $total_clientes->closeCursor();
        $total_clientes=$lc->desconectar($total_clientes);
    ?>
    <a href="<?php echo SERVERURL; ?>my-empresa/" class="tile">
        <div class="tile-tittle">Empresa</div>
        <div class="tile-icon">
            <i class="fas fa-building fa-fw"></i>
            <p> &nbsp; </p>
        </div>
    </a>

    <a href="<?php echo SERVERURL; ?>movement-new/" class="tile">
        <div class="tile-tittle">Generar Reporte</div>
        <div class="tile-icon">
            <i class="fas fa-file-excel fa-fw"></i>
            <p> &nbsp; </p>
        </div>
    </a>

    <a href="<?php echo SERVERURL; ?>movement-list/" class="tile">
        <div class="tile-tittle">Reportes</div>
        <div class="tile-icon">
            <i class="fas fa-folder-open fa-fw"></i>
            
            <p> &nbsp; </p>
        </div>
    </a>

    <?php
        /*---------- Inicio Opciones del administrador ----------*/
        if($_SESSION['cargo_svi']=="Administrador"){ 
    ?>
    <a href="<?php echo SERVERURL; ?>return-list/" class="tile">
        <div class="tile-tittle">Devoluciones</div>
        <div class="tile-icon">
            <i class="fas fa-people-carry fa-fw"></i>
            <p> &nbsp; </p>
        </div>
    </a>

   
    <a href="<?php echo SERVERURL; ?>kardex/" class="tile">
        <div class="tile-tittle">Historial</div>
        <div class="tile-icon">
            <i class="fas fa-history fa-fw"></i>
            <p> &nbsp; </p>
        </div>
    </a>
    <a href="<?php echo SERVERURL; ?>sale-search-date/" class="tile">
        <div class="tile-tittle">Crear Grupo de Juego</div>
        <div class="tile-icon">
            <i class="far fa-file-excel fa-fw"></i>
            <p> &nbsp; </p>
        </div>
    </a>
    <?php } /*---------- Fin Opciones del administrador ----------*/ ?>

    </div>
</div>
