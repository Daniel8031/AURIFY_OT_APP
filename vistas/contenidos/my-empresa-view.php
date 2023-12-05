<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-boxes fa-fw"></i> &nbsp; Datos de la empresa
    </h3>
    <?php if($_SESSION['cargo_svi']=="Administrador"){ ?>

    <?php include "./vistas/desc/desc_producto.php"; ?>

    <?php } ?>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <?php if($_SESSION['cargo_svi']=="Administrador"){ ?>
           
            <li>
            <a class="active" href="<?php echo SERVERURL; ?>product-list/">
                <i class="fas fa-boxes fa-fw"></i> &nbsp; DATOS DE LA EMPRESA
            </a>
        </li>
        <?php } ?>
       
    </ul>	
</div>

<!-- <div class="container-fluid" style="background-color: #FFF; padding-bottom: 20px;">
    <?php
    
        require_once "./controladores/productoControlador.php";
        $ins_producto = new productoControlador();

        echo $ins_producto->paginador_producto_controlador($pagina[1],15,$pagina[0],"",$_SESSION['cargo_svi']);
    ?> -->

<?php
        $datos_empresa=$lc->datos_tabla("Normal","empresa LIMIT 1","*",0);

        if($datos_empresa->rowCount()>=1){
            $campos=$datos_empresa->fetch();
             }
             
    ?>
    
    <div class="container-fluid" style="background-color: #FFF; padding-bottom: 20px;">
    <ul class="list-unstyled" style="padding: 5px;" >
    <li class="media media-product">
    <?php if(is_file("./vistas/assets/empresa/".$campos['empresa_foto'])){ ?>
    <img class="mr-3 img-fluid img-logo-list" src=" <?php echo SERVERURL ?>vistas/assets/empresa/<?php echo $campos['empresa_foto']; ?>" alt="<?php echo $campos['empresa_nombre']; ?>">
    <?php }else{ ?>
        <img class="mr-3 img-fluid img-product-list" src="<?php echo SERVERURL ?>vistas/assets/empresa/company.png" alt="'.$rows['producto_nombre'].'">
        <?php } ?>
    
    <div class="media-body product-media-body">
    <p class="text-uppercase text-center media-product-title"><strong>"<?php echo $campos['empresa_nombre']; ?>"</strong></p>
    <div class="container-fluid">
    <div class="row">
    <div class="col-12 col-md-6 col-lg-3 col-product"><i class="fas fa-address-card"></i> <strong>Identificador: </strong><?php echo $campos['empresa_numero_documento']; ?></div>

            <div class="col-12 col-md-6 col-lg-3 col-product"><i class="fas fa-folder-open"></i> <strong>Documento: </strong><?php echo $campos['empresa_tipo_documento']; ?></div>

            <div class="col-12 col-md-6 col-lg-3 col-product"><i class="fas fa-phone-square"></i><strong> Telefono: </strong><?php echo $campos['empresa_telefono']; ?>
            <span class="badge badge-success">En espera de asignacion</span>
            </div>
            <div class="col-12 col-md-6 col-lg-3 col-product" ><i class="fas fa-clipboard-check"></i> <strong>Estado:</strong> Habilitado</div>

            <div class="col-12 col-md-6 col-lg-3 col-product"><i class="fas fa-box"></i> <strong>Direción:</strong> '<?php echo $campos['empresa_direccion']; ?>'</div>
                                                <div class="col-12 col-md-6 col-lg-3 col-product"><i class="fas fa-calendar-alt"></i> <strong>Fundada desde:</strong> '2019'</div>
                                                <div class="col-12 col-md-6 col-lg-3 col-product"><i class="fas fa-calendar-alt"></i> <strong>Codigo Postal:</strong>
                                                <span class="badge badge-success">45140</span>
            
     </div> 
     
    </div>
    </div>
    <div class="text-right media-product-options">
    <?php if($_SESSION['cargo_svi']=="Administrador"){ ?>
								<span><i class="fas fa-tools"></i> &nbsp; OPCIONES: </span>
                               
        
                                <a href="<?php echo SERVERURL."product-image/".$lc->encryption($_SESSION['id_svi'])."/"; ?>" class="btn btn-secondary" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Gestionar imagen">
										<i class="far fa-image"></i>
									</a>
									<a href="<?php echo SERVERURL."user-update/".$lc->encryption($_SESSION['id_svi'])."/"; ?>" class="btn btn-success" data-toggle="popover" data-trigger="hover" data-placement="top" data-content="Actualizar datos">
										<i class="fas fa-sync"></i>
									</a>

                                    <?php } ?>	
                                    <?php if($_SESSION['cargo_svi']=="Usuario"){ ?>
                                    <a href="" class="btn btn-danger" data-toggle="popover"  data-trigger="hover" data-placement="top" data-content="No puedes modificar los datos">
										<i class="fas fa-exclamation-triangle "></i>
									</a>
                                   
                                    
                                    <?php } ?>	
                                    
    </div>
    </div>
    </li>

    </ul>
    <p class="text-right">Mostrando targeta de presentación de: <strong>' <?php echo $campos['empresa_nombre']; ?> '</strong></p>
  
</div>
