<?php
$category_id_del = limpiar_cadena($_GET['category_id_del']);

// Verificando categoria
$check_category = conexion();
$check_category = $check_category->query("SELECT categoria_id FROM categoria WHERE categoria_id='$category_id_del'");
if ($check_category->rowCount() == 1) {

    // Verificando productos de usuario
    $check_productos = conexion();
    $check_productos = $check_productos->query("SELECT categoria_id FROM producto WHERE categoria_id='$category_id_del' LIMIT 1");

    if ($check_productos->rowCount() <= 0) {
        // Eliminando categoria
        $eliminar_category = conexion();
        $eliminar_category = $eliminar_category->prepare("DELETE FROM categoria WHERE categoria_id=:id");

        $eliminar_category->execute([":id" => $category_id_del]);

        if ($eliminar_category->rowCount() == 1) {
            echo '
                <div class="notification is-info is-light">
                    <button class="delete"></button>
                    <strong>¡La categoria ha sido eliminada!</strong></br>
                    La categoria ha sido eliminada con exito.
                </div>
                ';
        } else {
            echo '
                <div class="notification is-danger is-light">
                    <button class="delete"></button>
                    <strong>¡Ocurrio un error inesperado!</strong></br>
                    No se pudo eliminar la categoria, por favor intente nuevamente.
                </div>
                ';
        }
        $eliminar_category = null;
    } else {
        echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            No se puede eliminar porque tiene productos asociados.
        </div>
        ';
    }
    $check_productos = null;
} else {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            La categoria que intente eliminar no existe.
        </div>
        ';
}
$check_category = null;
