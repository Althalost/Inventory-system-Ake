<div class="container is-fluid mb-6 box">
    <h1 class="title">Usuarios</h1>
    <h2 class="subtitle">Buscar usuario</h2>
</div>

<div class="container pb-6 pt-6 box">

    <?php
    require_once "./php/main.php";

    if (isset($_POST['modulo_buscador'])) {
        require_once "./php/buscador.php";
    }

    if (!isset($_SESSION['busqueda_usuario']) && empty($_SESSION['busqueda_usuario'])) {
    ?>

        <div class="columns pt-1">
            <div class="column">
                <form action="" method="POST" autocomplete="off">
                    <input type="hidden" name="modulo_buscador" value="usuario">
                    <div class="field is-grouped">
                        <p class="control is-expanded">
                            <input class="input is-rounded" type="text" name="txt_buscador" placeholder="¿Qué estas buscando?" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{1,30}" maxlength="30">
                        </p>
                        <p class="control">
                            <button class="button is-info" type="submit">Buscar</button>
                        </p>
                    </div>
                </form>
            </div>
        </div>



        <div class="columns">


        <?php
    } else {
        ?>
            <div class="column">
                <form class="has-text-centered mt-1 mb-1" action="" method="POST" autocomplete="off">
                    <input type="hidden" name="modulo_buscador" value="usuario">
                    <input type="hidden" name="eliminar_buscador" value="usuario">
                    <p>Estas buscando <strong><?php echo $_SESSION['busqueda_usuario']; ?></strong></p>
                    <br>
                    <button type="submit" class="button is-danger is-rounded">Eliminar busqueda</button>
                </form>
            </div>
        </div>
    <?php
        //Eliminar usuario
        if (isset($_GET['user_id_del'])) {
            require_once "./php/usuario_eleminar.php";
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
        $url = "index.php?vista=user_search&page=";
        $registros   = 15;
        $busqueda = $_SESSION['busqueda_usuario'];

        require_once "./php/usuario_list.php";
    }
    ?>
</div>