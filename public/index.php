<?php
    require_once "../config/APP.php";
    $peticion_ajax=true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include "../vistas/inc/Head.php"; ?>
    <link rel="stylesheet" href="<?php echo SERVERURL; ?>public/css/styles.css">
</head>
<body>
    <nav class="nav-verificator roboto-medium">
    <i class="fas fa-search-dollar"></i> &nbsp; VERIFICADOR DE PRECIOS
    </nav>
    <div class="container-fluid" style="padding-top: 25px;">
        <div class="row">
            <div class="col-12 col-md-3">
                <figure>
                    <img src="<?php echo SERVERURL; ?>vistas/assets/img/logo.png" alt="<?php echo COMPANY; ?>" class="img-fluid img-verificator">
                </figure>
            </div>
            <div class="col-12 col-md-9">
                <p class="text-justify">
                    Para obtener información de un producto por favor escanee el código de barra con el lector en el siguiente campo. O digite el código de barras y presione la tecla ENTER
                </p>
                <form action="<?php echo SERVERURL; ?>public/index.php" id="sale-barcode-form" autocomplete="off" name="formVerificator" method="POST">
                    <div class="form-group">
                        <label class="bmd-label-floating" for="barcode-input">Código de producto</label>
                        <input type="text" pattern="[a-zA-Z0-9- ]{1,70}" class="form-control sale-input-barcode" id="sale-barcode-input" name="sale-barcode-input" maxlength="70"  autofocus="autofocus" >
                    </div>
                </form>
            </div>
        </div>
    </div>
    <hr>
    <div class="container-fluid">
        <?php
            if(isset($_POST['sale-barcode-input'])){
                include "../controladores/productoControlador.php";
                $ins_producto = new productoControlador();

                $check_product=$ins_producto->verificar_producto_controlador();

                if($check_product->rowCount()==1){
                    $data_prod=$check_product->fetch();
        ?>
        <h3 class="text-center roboto-condensed-light text-uppercase"><?php echo $data_prod['producto_nombre']; ?></h3>
        <hr>
        <br>
        <div class="row">
            <div class="col-12 col-md-4">
                <figure>
                    <?php if(is_file("../vistas/assets/product/".$data_prod['producto_foto'])){ ?>
                    <img src="<?php echo SERVERURL; ?>vistas/assets/product/<?php echo $data_prod['producto_foto']; ?>" alt="img_product" class="img-fluid img-product-verificator">
                    <?php }else{ ?>
                    <img src="<?php echo SERVERURL; ?>vistas/assets/img/producto.png" alt="img_product" class="img-fluid img-product-verificator">
                    <?php } ?>
                </figure>
            </div>
            <div class="col-12 col-md-4">
                <h5 class="roboto-condensed-light text-uppercase">Características del producto</h5>
                <br>
                <ul class="list-unstyled list-desc-product">
                    <li><i class="far fa-star"></i> &nbsp; Garantía: <?php if($data_prod['producto_garantia_unidad']=="0" || $data_prod['producto_garantia_tiempo']=="N/A"){ echo "No tiene"; }else{ echo $data_prod['producto_garantia_unidad']." ".$data_prod['producto_garantia_tiempo']; } ?></li>
                    <li><i class="far fa-star"></i> &nbsp; Tipo de unidad: <?php echo $data_prod['producto_tipo_unidad']; ?></li>
                    <li><i class="far fa-star"></i> &nbsp; Marca: <?php echo $data_prod['producto_marca']; ?></li>
                    <li><i class="far fa-star"></i> &nbsp; Modelo: <?php echo $data_prod['producto_modelo']; ?></li>
                </ul>
            </div>
            <div class="col-12 col-md-4">
                <p class="roboto-condensed-light text-uppercase text-price text-muted"><i class="fas fa-money-check-alt"></i> &nbsp; Precio regular</p>
                <p class="roboto-condensed-light text-price-badge font-weight-bold text-danger"><span class="badge badge-success"><?php echo MONEDA_SIMBOLO.$data_prod['producto_precio_venta']." ".MONEDA_NOMBRE; ?></span></p>
                <br><br>
                <p class="roboto-condensed-light text-uppercase text-price text-muted"><i class="fas fa-file-invoice-dollar"></i> &nbsp; Precio por mayoreo</p>
                <p class="roboto-condensed-light text-price-badge font-weight-bold"><span class="badge badge-success"><?php echo MONEDA_SIMBOLO.$data_prod['producto_precio_mayoreo']." ".MONEDA_NOMBRE; ?></span></p>
            </div>
        </div>
        <?php
                }else{
        ?>
        <p class="text-center text-info icon-verificator"><i class="fas fa-satellite"></i></p>
        <p class="text-center lead">No hemos encontrado ningún producto con código de barras <strong>“<?php echo $_POST['sale-barcode-input']; ?>”</strong></p>
        <?php 
                }
            }else{ 
        ?>
        <p class="text-center text-info icon-verificator"><i class="fas fa-search-dollar"></i></p>
        <p class="text-center lead">Por favor escanee un código de barras de un producto en el campo que se encuentra en la parte superior</p>
        <?php } ?>
    </div>
    <?php
		include "../vistas/inc/Script.php"; 
    ?>
    <script>
        let sale_form_barcode = document.querySelector("#sale-barcode-form");
        sale_form_barcode.addEventListener('submit', function(event){
            event.preventDefault();
            setTimeout('agregar_producto()',100);
        });

        let sale_input_barcode = document.querySelector("#sale-barcode-input");

        sale_input_barcode.addEventListener('paste',function(){
            setTimeout('agregar_producto()',100);
        });

        /* Agregar producto */
        function agregar_producto(){
            let codigo_producto=document.querySelector('#sale-barcode-input').value;

            codigo_producto=codigo_producto.trim();

            if(codigo_producto!=""){
                document.formVerificator.submit();
            }else{
                Swal.fire({
                    title: 'Ocurrió un error inesperado',
                    text: 'Debes de introducir el código del producto',
                    type: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
            
        }

    </script>
</body>
</html>