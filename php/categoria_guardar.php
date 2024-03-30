<?php
require_once "main.php";

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

# Verificando categoria #
$check_nombre = conexion();
$check_nombre = $check_nombre->query("SELECT categoria_nombre FROM categoria WHERE categoria_nombre='$nombre'");
if ($check_nombre->rowCount() > 0) {
    echo '
        <div class="notification is-danger is-light">
            <button class="delete"></button>
            <strong>¡Ocurrio un error inesperado!</strong></br>
            EL NOMBRE que ha ingresado ya esta registrado, por favor eleja otro
        </div>
        ';
    exit();
}
$check_nombre = null;

# Guandando datos #
$guardar_categoria = conexion();
$guardar_categoria = $guardar_categoria->prepare("INSERT INTO categoria(categoria_nombre,categoria_ubicacion)
 VALUES(:nombre,:ubicacion)");


$marcadores = [
    ":nombre" => $nombre,
    ":ubicacion" => $ubicacion
];

$guardar_categoria->execute($marcadores);

if ($guardar_categoria->rowCount() == 1) {
    echo '
    <div class="notification is-info is-light">
        <button class="delete"></button>
        <strong>¡Categoria registrada!</strong></br>
        La categoria se ha registrado con exito
    </div>
    ';
} else {
    echo '
    <div class="notification is-danger is-light">
        <button class="delete"></button>
        <strong>¡Ocurrio un error inesperado!</strong></br>
        No se pudo registrar la categoria, por favor intente nuevamente.
    </div>
    ';
}
$guardar_categoria = null;
