<?php

require_once "main.php";

$id = limpiar_cadena($_POST['producto_id']);

//Verificar producto
$check_product = conexion();
$check_product = $check_product->query("SELECT *FROM producto WHERE producto_id='$id'");


if ($check_product->rowCount() <= 0) {
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
} else {
    $datos = $check_product->fetch();
}
$check_product = null;

# Almacenando datos #
$codigo = limpiar_cadena($_POST['producto_codigo']);
$nombre = limpiar_cadena($_POST['producto_nombre']);
$precio = limpiar_cadena($_POST['producto_precio']);
$stock = limpiar_cadena($_POST['producto_stock']);
$categoria = limpiar_cadena($_POST['producto_categoria']);

# Verificando campos obligatorios #
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
if ($codigo != $datos['producto_codigo']) {
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
}

#Verificando nombre de producto
if ($nombre != $datos['producto_nombre']) {
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
}

#Verificando categoria de producto
if ($categoria != $datos['categoria_id']) {
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
}

# Actualizar datos #
$actualizar_producto = conexion();
$actualizar_producto = $actualizar_producto->prepare("UPDATE producto SET producto_codigo=:codigo,producto_nombre=:nombre,
producto_precio=:precio,producto_stock=:stock,categoria_id=:categoria WHERE producto_id=:id");

$marcadores = [
    ":codigo" => $codigo,
    ":nombre" => $nombre,
    ":precio" => $precio,
    ":stock" => $stock,
    ":categoria" => $categoria,
    ":id" => $id
];

if ($actualizar_producto->execute($marcadores)) {
    echo '
    <div class="content">
    <div class="notification is-info is-light">
        <button class="delete"></button>
        <strong>¡Producto actualizado!</strong></br>
        El producto se ha actualizado con exito
    </div>
    </div>
    ';
} else {
    echo '
    <div class="content">
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        No se pudo actualizar el producto, por favor intente nuevamente.
    </div>
    </div>
    ';
}
$actualizar_producto = null;
