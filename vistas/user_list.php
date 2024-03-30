<div class="container is-fluid mb-6 box">
    <h1 class="title">Usuarios</h1>
    <h2 class="subtitle">lista de usuarios</h2>
</div>

<div class="container pb-6 pt-6 box">

    <?php
    require_once "./php/main.php";

    //Eliminar usuario
    if (isset($_GET['user_id_del'])) {
        require_once "./php/usuario_eliminar.php";
    }

    if (!isset($_GET['page'])) {
        $pagina = 1;
    } else {
        $pagina = (int) $_GET['page'];
        if ($pagina <= 1) {
            $pagina = 1;
        }
    }

    $pagina = limpiar_cadena($pagina);
    $url = "index.php?vista=user_list&page=";
    $registros   = 15;
    $busqueda = "";

    require_once "./php/usuario_list.php";
    ?>

</div>