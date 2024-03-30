<?php
require_once "../inc/session_start.php";
require_once "main.php";

#Almacenando datos
$codigo = limpiar_cadena($_POST['producto_codigo']);
$nombre = limpiar_cadena($_POST['producto_nombre']);
$precio = limpiar_cadena($_POST['producto_precio']);
$stock = limpiar_cadena($_POST['producto_stock']);
$categoria = limpiar_cadena($_POST['producto_categoria']);

#Verificando campos obligatorios
if ($codigo == "" || $nombre == "" || $precio == "" || $stock == "" || $categoria == "") {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            No has llenado todos los datos que son obligatorios.
        </div>
    ';
    exit();
}

#Verificando integridad de los datos
if (verificar_datos("[a-zA-Z0-9- ]{1,70}", $codigo)) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        EL CODIGO de Producto no coincide con el formato solicitado.
    </div>
';
    exit();
}

if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,70}", $nombre)) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        EL NOMBRE de Producto no coincide con el formato solicitado.
    </div>
';
    exit();
}

if (verificar_datos("[0-9.]{1,25}", $precio)) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        EL PRECIO de Producto no coincide con el formato solicitado.
    </div>
';
    exit();
}

if (verificar_datos("[0-9]{1,25}", $stock)) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        EL STOCK de Producto no coincide con el formato solicitado.
    </div>
';
    exit();
}

#Verificando codigo de producto
$check_productcode = conexion();
$check_productcode = $check_productcode->query("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
if ($check_productcode->rowCount() > 0) {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            EL CODIGO ingresado ya esta registrado, por favor utilice otro
        </div>
    ';
    exit();
}
$check_productcode = null;

#Verificando nombre de producto
$check_name = conexion();
$check_name = $check_name->query("SELECT producto_nombre FROM producto WHERE producto_nombre='$nombre'");
if ($check_name->rowCount() > 0) {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            EL NOMBRE ingresado ya esta registrado, por favor utilice otro
        </div>
    ';
    exit();
}
$check_name = null;

#Verificando categoria de producto
$check_categoria = conexion();
$check_categoria = $check_categoria->query("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
if ($check_categoria->rowCount() <= 0) {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            LA CATEGORIA seleccionada no existe, por favor, elija otra.
        </div>
    ';
    exit();
}
$check_categoria = null;


#Directorio de imagenes #
$img_dir = "../img/product/";

#Comprobar si se selecciono una imagen #
if ($_FILES['producto_foto']['name'] != "" && $_FILES['producto_foto']['size'] > 0) {

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

    $img_nombre = renombrar_fotos($nombre);
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
} else {
    $foto = "";
}

# Guandando datos #
$guardar_producto = conexion();
$guardar_producto = $guardar_producto->prepare("INSERT INTO producto(producto_codigo,producto_nombre,producto_precio,
producto_stock,producto_foto,categoria_id,usuario_id)
 VALUES(:codigo,:nombre,:precio,:stock,:foto,:categoria,:usuario)");

$marcadores = [
    ":codigo" => $codigo,
    ":nombre" => $nombre,
    ":precio" => $precio,
    ":stock" => $stock,
    ":foto" => $foto,
    ":categoria" => $categoria,
    ":usuario" => $_SESSION['id']
];

$guardar_producto->execute($marcadores);

if ($guardar_producto->rowCount() == 1) {
    echo '
    <div class="notification is-info is-light">
        <button class="delete"></button>
        <strong>¡PRODUCTO REGISTRADO!</strong></br>
        El producto se registro con exito
    </div>
';
} else {
    if (is_file($img_dir . $foto)) {
        chmod($img_dir . $foto, 0777);
        unlink($img_dir . $foto);
    }
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        No se pudo registrar el producto, por favor intente de nuevo
    </div>
    ';
}

$guardar_producto = null;
