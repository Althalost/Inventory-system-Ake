<?php

require_once "main.php";
$id = limpiar_cadena($_POST['categoria_id']);

//Verificar categoria
$check_category = conexion();
$check_category = $check_category->query("SELECT *FROM categoria WHERE categoria_id='$id'");


if ($check_category->rowCount() <= 0) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        La categoria no existe en el sistema
    </div>
    ';
    exit();
} else {
    $datos = $check_category->fetch();
}
$check_category = null;

# Almacenando datos #
$nombre = limpiar_cadena($_POST['categoria_nombre']);
$ubicacion = limpiar_cadena($_POST['categoria_ubicacion']);

# Verificando campos obligatorios #
if ($nombre == "") {
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
if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{4,50}", $nombre)) {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        EL NOMBRE no coincide con el formato solicitado.
    </div>
    ';
    exit();
}


if ($ubicacion != "") {
    if (verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ ]{5,150}", $ubicacion)) {
        echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            LA UBICACION no coincide con el formato solicitado.
        </div>
        ';
        exit();
    }
}

# Verificando nombre #

if ($nombre != $datos['categoria_nombre']) {
    $check_nombre = conexion();
    $check_nombre = $check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
    if ($check_nombre->rowCount() > 0) {
        echo '
            <div class="notification is-danger is-light">
                <button class="delete"></button>
                <strong>¡Ocurrio un error inesperado!</strong></br>
                EL NOMBRE que ha ingresado ya esta registrado, por favor elija otro
            </div>
            ';
        exit();
    }
    $check_nombre = null;
}

# Actualizar datos #
$actualizar_categoria = conexion();
$actualizar_categoria = $actualizar_categoria->prepare("UPDATE categoria SET categoria_nombre=:nombre,
categoria_ubicacion=:ubicacion WHERE categoria_id=:id");

$marcadores = [
    ":nombre" => $nombre,
    ":ubicacion" => $ubicacion,
    ":id" => $id
];

if ($actualizar_categoria->execute($marcadores)) {
    echo '
    <div class="notification is-info is-light">
        <button class="delete"></button>
        <strong>¡Categoria actualizada!</strong></br>
        La categoria se ha actualizado con exito
    </div>
    ';
} else {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        No se pudo actualizar la categoria, por favor intente nuevamente.
    </div>
    ';
}
$actualizar_categoria = null;
