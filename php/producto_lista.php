<?php

$inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;
$tabla = "";

$campos = "producto.producto_id,producto.producto_codigo,producto.producto_nombre,
producto.producto_precio,producto.producto_stock,producto.producto_foto,
categoria.categoria_nombre,usuario.usuario_nombre,usuario.usuario_apellido";

if (isset($busqueda) && $busqueda != "") {
    $consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id 
    INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id WHERE producto.producto_codigo LIKE '%$busqueda%' OR
    producto.producto_nombre LIKE '%$busqueda%' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

    $consulta_total = "SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR
    producto_nombre LIKE '%$busqueda%' ";
} elseif ($category_selected > 0) {
    $consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id 
    INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id WHERE producto.categoria_id='$category_selected' 
    ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

    $consulta_total = "SELECT COUNT(producto_id) FROM producto WHERE categoria_id='$category_selected' ";
} else {
    $consulta_datos = "SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id 
    INNER JOIN usuario ON producto.usuario_id=usuario.usuario_id ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

    $consulta_total = "SELECT COUNT(producto_id) FROM producto";
}

$conexion = conexion();

$datos = $conexion->query($consulta_datos);
$datos = $datos->fetchAll();

$total = $conexion->query($consulta_total);
$total = (int) $total->fetchColumn();

$Npaginas = ceil($total / $registros);

if ($total >= 1 && $pagina <= $Npaginas) {
    $contador = $inicio + 1;
    $pag_inicio = $inicio + 1;
    foreach ($datos as $rows) {
        $tabla .= ' 
        <br>
        <article class="media">
            <figure class="media-left image is-64x64">';

        if (is_file("./img/product/" . $rows['producto_foto'])) {
            $tabla .= '<img src="./img/product/' . $rows['producto_foto'] . '"/>';
        } else {
            $tabla .= '<img src="./img/product/producto.png"/>';
        }

        $tabla .= '
            </figure>
            <div class="media-content">
                <div class="content">
                    <p class="has-text">
                        <strong>' . $contador . ' - ' . $rows['producto_nombre'] . '</strong>
                        <strong>CODIGO:</strong> ' . $rows['producto_codigo'] . ',
                        <strong>PRECIO:</strong> ' . $rows['producto_precio'] . ',
                        <strong>STOCK:</strong> ' . $rows['producto_stock'] . ',
                        <strong>CATEGORIA:</strong> ' . $rows['categoria_nombre'] . ',
                        <strong>REGISTRADO POR:</strong> ' . $rows['usuario_nombre'] . $rows['usuario_apellido'] . '
                    </p>
                </div>
                <div class="has-text-right">
                    <a href="index.php?vista=product_imgs&product_id_up=' . $rows['producto_id'] . '" class="button is-link is-rounded is-small">Imagen</a>
                    <a href="index.php?vista=product_update&product_id_up=' . $rows['producto_id'] . '" class="button is-success is-rounded is-small">Actualizar</a>
                    <a href="' . $url . $pagina . '&product_id_del=' . $rows['producto_id'] . '" class="button is-danger is-rounded is-small">Eliminar</a>
                </div>
            </div>
        </article>
        <hr/>
        ';
        $contador++;
    }
    $pag_final = $contador - 1;
} else {
    if ($total >= 1) {
        $tabla .= '
            <br>
            <div class="content">
            <p class="has-text-centered">
                <a href="' . $url . '1" class="button is-link is-rounded is-small mt-4 mb-4">
                    Haga clic acá para recargar el listado
                </a>
            </p>
            </div>
        ';
    } else {
        $tabla .= ' <br><br><hr><div class="block"><p class="has-text-centered">No hay registros en el sistema</p></div>';
    }
}


if ($total >= 1 && $pagina <= $Npaginas) {
    $tabla .= '
    <p class="has-text-right">Mostrando Productos <strong>' . $pag_inicio . '</strong> 
    al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>';
}

$conexion = null;
echo $tabla;

if ($total >= 1 && $pagina <= $Npaginas) {
    echo paginador_tablas($pagina, $Npaginas, $url, 7);
}
