<div class="container is-fluid mb-6 box">
    <h1 class="title">Productos</h1>
    <h2 class="subtitle">Lista de productos por categoría</h2>
</div>

<div class="container pb-6 box">
    <?php
    require_once "./php/main.php";
    ?>
    <div class="columns">


        <div class="column is-one-third">
            <h2 class="title has-text-centered">Categorías</h2>
            <?php
            $categorias = conexion();
            $categorias = $categorias->query("SELECT * FROM categoria");
            if ($categorias->rowCount() > 0) {
                $categorias = $categorias->fetchAll();
                foreach ($categorias as $row) {
                    echo ' 
                            <a href="index.php?vista=product_category&category_id=' . $row['categoria_id'] . '" 
                            class="button is-link is-inverted is-fullwidth">' . $row['categoria_nombre'] . '</a>
                            ';
                }
            } else {
                echo '<p class="has-text-centered">No hay categorías registradas</p>';
            }
            $categorias = null;
            ?>


        </div>



        <div class="column">

            <?php
            $category_selected = (isset($_GET['category_id']) ? $_GET['category_id'] : 0);
            $categoria = conexion();
            $categoria = $categoria->query("SELECT * FROM categoria WHERE categoria_id='$category_selected'");
            if ($categoria->rowCount() > 0) {

                $categoria = $categoria->fetch();
                echo '
                    <h2 class="title has-text-centered">' . $categoria['categoria_nombre'] . '</h2>
                    <p class="has-text-centered pb-6">' . $categoria['categoria_ubicacion'] . '</p
                ';

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

                $pagina = limpiar_cadena($pagina);
                $url = "index.php?vista=product_category&category_id=$category_selected&page=";
                $registros   = 10;
                $busqueda = "";

                require_once "./php/producto_lista.php";
            } else {
                echo '<h2 class="has-text-centered title">Seleccione una categoría para empezar</h2>';
            }
            $categoria = null;


            ?>


        </div>

    </div>
</div>