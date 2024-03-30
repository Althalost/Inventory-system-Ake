<?php

require_once "main.php";

$id = limpiar_cadena($_POST['img_up_id']);

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

#Comprobar si se selecciono una imagen #
if ($_FILES['producto_foto']['name'] == "" || $_FILES['producto_foto']['size'] == 0) {
    echo '
    <div class="content">
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            No ha seleccionado ninguna imagen valida
        </div>
    </div>
    ';
    exit();
}

#Creando directorio #
if (!file_exists($img_dir)) {
    if (!mkdir($img_dir, 0777)) {
        echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            Error al crear el directorio de imagenes.
        </div>
        ';
        exit();
    }
}

#Dando permisos de lectura y escritura #
chmod($img_dir, 0777);

#Verificar formato de imagenes #
if (
    mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/jpeg" &&
    mime_content_type($_FILES['producto_foto']['tmp_name']) != "image/png"
) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        La imagen que ha seleccionado es de un formato no permitido.
    </div>
    ';
    exit();
}

#Verificando peso de la imagen #
if (($_FILES['producto_foto']['size'] / 1024) > 3072) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        La imagen que ha seleccionada supera el tamaño permitido.
    </div>
    ';
    exit();
}

#Extension de la imagen #
switch (mime_content_type($_FILES['producto_foto']['tmp_name'])) {
    case 'image/jpeg':
        $img_ext = ".jpeg";
        break;
    case 'image/png':
        $img_ext = ".png";
        break;
}

chmod($img_dir, 0777);

$img_nombre = renombrar_fotos($datos['producto_nombre']);
$foto = $img_nombre . $img_ext;

#Moviendo imagen al directorio #
if (!move_uploaded_file($_FILES['producto_foto']['tmp_name'], $img_dir . $foto)) {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            No se pudo cargar la imagen al sistema en este momento.
        </div>
        ';
    exit();
}

#Eliminar imagen anterior #
if (is_file($img_dir . $datos['producto_foto']) && $datos['producto_foto'] != $foto) {
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
    ":foto" => $foto,
    ":id" => $datos['producto_id']
];

if ($actualizar_foto->execute($marcadores)) {
    echo '
    <div class="content">
    <div class="notification is-info is-light">
        <button class="delete"></button>
        <strong>¡Imagen eliminada!</strong></br>
        Se ha actualizado la imagen, por favor pulse aceptar para carga los cambios.

        <p class="has-text-centered pt-5 pb-5">
            <a href="index.php?vista=product_imgs&product_id_up=' . $id . '" class="button is-link is-rounded">Aceptar</a>
        </p>
    </div>
    </div>
    ';
} else {
    if (is_file($img_dir . $foto)) {
        chmod($img_dir . $foto, 0777);
        unlink($img_dir . $foto);
    }
    echo '
    <div class="content">
    <div class="notification is-warning is-light">
        <button class="delete"></button>
        <strong>¡Error al cargar la imagen!</strong></br>
        No se pudo cargar la iamgen, por favor pulce aceptar para recargar y vuelva a intentar.

        <p class="has-text-centered pt-5 pb-5">
            <a href="index.php?vista=product_imgs&product_id_up=' . $id . '" class="button is-link is-rounded">Aceptar</a>
        </p>
    </div>
    </div>
    ';
}
$actualizar_foto = null;
