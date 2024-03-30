<?php
$product_id_del = limpiar_cadena($_GET['product_id_del']);

// Verificando producto
$check_product = conexion();
$check_product = $check_product->query("SELECT * FROM producto WHERE producto_id='$product_id_del'");

if ($check_product->rowCount() == 1) {
    $datos = $check_product->fetch();
    // Eliminando producto
    $eliminar_producto = conexion();
    $eliminar_producto = $eliminar_producto->prepare("DELETE FROM producto WHERE producto_id=:id");

    $eliminar_producto->execute([":id" => $product_id_del]);

    if ($eliminar_producto->rowCount() == 1) {

        if (is_file("./img/product/" . $datos['producto_foto'])) {
            chmod("./img/product/" . $datos['producto_foto'], 0777);
            unlink("./img/product/" . $datos['producto_foto']);
        }
        echo '
            <hr>
            <div class="content">
             <div class="notification is-info">
                 <button class="delete"></button>
                 <strong>¡El PRODUCTO ha sido eliminado!</strong></br>
                 EL PRODUCTO ha sido eliminado con exito.
             </div>
             </div>
             ';
    } else {
        echo '
            <hr>
            <div class="content">
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <strong>¡Ocurrio un error inesperado!</strong></br>
                No se pudo eliminar el producto, por favor intente nuevamente.
            </div>
            </div>
        ';
    }
    $eliminar_producto = null;
} else {
    echo '
        <hr>
        <div class="content">
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            El Producto que intenta eliminar no existe.
        </div>
        </div>
    ';
}
$check_product = null;
