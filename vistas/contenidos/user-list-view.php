<?php
    include "./vistas/inc/admin_security.php";
?>
<div class="full-box page-header">
    <h3 class="text-left text-uppercase">
        <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de usuarios
    </h3>
    <?php include "./vistas/desc/desc_usuario.php"; ?>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs text-uppercase">
        <li>
            <a href="<?php echo SERVERURL; ?>user-new/">
                <i class="fas fa-user-tie fa-fw"></i> &nbsp; Nuevo usuario
            </a>
        </li>
        <li>
            <a class="active" href="<?php echo SERVERURL; ?>user-list/">
                <i class="fas fa-clipboard-list fa-fw"></i> &nbsp; Lista de usuarios
            </a>
        </li>
        <li>
            <a href="<?php echo SERVERURL; ?>user-search/">
                <i class="fas fa-search fa-fw"></i> &nbsp; Buscar usuario
            </a>
        </li>
    </ul>	
</div>

<div class="container-fluid">
    <?php
        require_once "./controladores/usuarioControlador.php";
        $ins_usuario = new usuarioControlador();

        echo $ins_usuario->paginador_usuario_controlador($pagina[1],15,$pagina[0],"",$_SESSION['id_svi']);
    ?>
</div>