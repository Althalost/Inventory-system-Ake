<div class="container is-fluid mb-6 box">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Lista de productos</h2>
</div>

<div class="container pb-6 pt-1 box">
    <?php
    require_once "./php/main.php";

    //Eliminar Producto
    if (isset($_GET['product_id_del'])) {
        require_once "./php/producto_eliminar.php";
    }

    if (!isset($_GET['page'])) {
        $pagina = 1;
    } else {
        $pagina = (int) $_GET['page'];
        if ($pagina <= 1) {
            $pagina = 1;
        }
    }

    $category_selected = (isset($_GET['category_id']) ? $_GET['category_id'] : 0);
    $pagina = limpiar_cadena($pagina);
    $url = "index.php?vista=product_list&page=";
    $registros   = 15;
    $busqueda = "";

    require_once "./php/producto_lista.php";
    ?>
</div>