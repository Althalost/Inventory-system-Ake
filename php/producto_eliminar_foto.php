<?php

require_once "main.php";

$id = limpiar_cadena($_POST['img_del_id']);

//Verificar producto
$check_product = conexion();
$check_product = $check_product->query("SELECT *FROM producto WHERE producto_id='$id'");


if ($check_product->rowCount() == 1) {
    $datos = $check_product->fetch();
} else {
    echo '
    <div class="content">
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        El producto no existe en el sistema
    </div>
    </div>
    ';
    exit();
}
$check_product = null;


#Directorio de imagenes #
$img_dir = "../img/product/";

chmod($img_dir, 0777);

if (is_file($img_dir . $datos['producto_foto'])) {
    chmod($img_dir . $datos['producto_foto'], 0777);
    if (!unlink($img_dir . $datos['producto_foto'])) {
        echo '
            <div class="content">
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <strong>¡Ocurrio un error inesperado!</strong></br>
                Error al eliminar la imagen del producto, por favor intente nuevamente.
            </div>
            </div>
        ';
        exit();
    }
}

# Actualizar datos #
$actualizar_foto = conexion();
$actualizar_foto = $actualizar_foto->prepare("UPDATE producto SET producto_foto=:foto WHERE producto_id=:id");

$marcadores = [
    ":foto" => "",
    ":id" => $id
];

if ($actualizar_foto->execute($marcadores)) {
    echo '
    <div class="content">
    <div class="notification is-info is-light">
        <button class="delete"></button>
        <strong>¡Imagen eliminada!</strong></br>
        Se ha eliminado la imagen, por favor pulse aceptar para carga los cambios.

        <p class="has-text-centered pt-5 pb-5">
            <a href="index.php?vista=product_imgs&product_id_up=' . $id . '" class="button is-link is-rounded">Aceptar</a>
        </p>
    </div>
    </div>
    ';
} else {
    echo '
    <div class="content">
    <div class="notification is-warning is-light">
        <button class="delete"></button>
        <strong>¡imagen eliminada!</strong></br>
        Ocorrieron inconvenientes, sin enbargo se pudo eliminar la imagen, por favor pulse aceptar para recarga.

        <p class="has-text-centered pt-5 pb-5">
            <a href="index.php?vista=product_imgs&product_id_up=' . $id . '" class="button is-link is-rounded">Aceptar</a>
        </p>
    </div>
    </div>
    ';
}
$actualizar_foto = null;
