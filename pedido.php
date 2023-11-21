<!DOCTYPE html>
<html lang="en">

<head>
    <title>Satoru no kōnā</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="assets/img/apple-icon.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/templatemo.css">
    <link rel="stylesheet" href="assets/css/custom.css">

    <!-- Load fonts style after rendering the layout styles -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <!-- Cargamos los requires necesarios de PhP-->
    <?php require 'objetos/producto.php' ?>
    <?php require 'objetos/productoCesta.php' ?>
    <?php require 'funciones/base_datos_tienda.php' ?>
    <?php require 'funciones/depurar.php' ?>
</head>

<body>
    <!-- Iniciamos sesión en la base de datos y almacenamos los datos IMPORTANTE 重要！！-->
    <?php
    session_start();
    $usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Invitado";
    // Consultamos la cantidad total de productos en la cesta del usuario actual 
    // esto es solo para mostar la cantidad de productos en la cesta, en el icono del carrito
    $sqlCantidadCesta =
        "SELECT SUM(cantidad) as totalProductos FROM productosCestas pc
         INNER JOIN cestas c ON pc.idCesta = c.idCesta
         WHERE c.usuario = '$usuario'";

    $resultadoCantidadCesta = $conexion->query($sqlCantidadCesta);

    // Obtenemos la cantidad total
    $totalProductosEnCesta = $resultadoCantidadCesta->fetch_assoc()["totalProductos"]; //Esto se usará para mostrarlo en pantalla junto al carro de la cesta
    
    // Obtengo el idCestas y la cantidad del usuario actual
    $sqlCesta = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
    $resultadoCestas = $conexion->query($sqlCesta);
    // Mientras haya cestas en la bbdd...(sea mayor a 0)
    if ($resultadoCestas->num_rows > 0) {
        $filaCestas = $resultadoCestas->fetch_assoc();
        $idCesta = $filaCestas["idCesta"];
    }

    // Si hay usuario registrado (ya sea como admin o usuario no como invitado CUIDADO OJO GASTEN CUIDAO!!)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["usuario"])) {
        // Vaciar la cesta antes de realizar el pedido si se hace clic en el botón "Vaciar cesta"
        if (isset($_POST["vaciar_cesta"])) {
            // Obtén la cantidad de cada producto en la cesta
            $sqlCantidadProductosCesta = "SELECT idProducto, cantidad FROM productosCestas WHERE idCesta = '$idCesta'";
            $resultadoCantidadProductosCesta = $conexion->query($sqlCantidadProductosCesta);

            while ($filaCantidad = $resultadoCantidadProductosCesta->fetch_assoc()) {
                $idProducto = $filaCantidad["idProducto"];
                $cantidadProducto = $filaCantidad["cantidad"];

                // Suma la cantidad de cada producto a la cantidad total de productos
                $sqlSumarCantidad = "UPDATE productos SET cantidad = cantidad + '$cantidadProducto' WHERE idProducto = '$idProducto'";
                $conexion->query($sqlSumarCantidad);
            }

            // Vacía la cesta
            $sqlVaciarCesta = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion->query($sqlVaciarCesta);

            // Redirige para evitar reenvío del formulario al actualizar la página
            header("location: productos.php");
            exit();
        }

        // Insertar en la tabla Pedidos
        $sqlInsertarPedido = "INSERT INTO pedidos (usuario) VALUES ('$usuario')";

        //Si la conexión se realiza con éxito
        if ($conexion->query($sqlInsertarPedido)) {

            // Obtener el idPedido recién insertado ()
            $idPedido = $conexion->insert_id;


            // Obtener productos de la cesta no me preguntes esta parte alejandra que e molto difficile
            $sqlPedido = "SELECT p.idProducto AS idProducto,
                             p.precio AS precio, 
                             c.cantidad AS cantidad
                          FROM productos p JOIN productosCestas c
                          ON p.idProducto = c.idProducto
                          WHERE idCesta ='$idCesta'";
            $resultado = $conexion->query($sqlPedido);

            // Inicializo el contador a 1 para empezar el bucle for de abajo, pero algo no funciona bien...mmm... Omoshiroi....
            $contador = 1;

            // Insertar en la tabla lineasPedidos
            while ($fila = $resultado->fetch_assoc()) {
                $idProducto = $fila["idProducto"];
                $precioUnitario = $fila["precio"];
                $cantidad = $fila["cantidad"];

                // Insertar en la tabla lineasPedidos
                $sqlInsertLineaPedido = "INSERT INTO lineasPedidos (lineaPedido, idPedido, idProducto, precioUnitario, cantidad)
                                         VALUES ('$contador','$idPedido', '$idProducto', '$precioUnitario', '$cantidad')";
                // Ejecutamos la consulta
                if ($conexion->query($sqlInsertLineaPedido)) {
                    // Si todo ha ido bien mostramos mensaje de exito
                    $mensajeExito = "Pedido realizado con éxito. ID del pedido: $idPedido";
                } else {
                    // Si hay errores al insertar la línea de pedido mostramos mensaje :(
                    $mensajeError = "Error al insertar la línea de pedido." . $conexion->error;
                }
                $contador++;
            }

            // Actualizamos el precio total en la tabla pedidos, por si hemos añadido más productos, somos ricos ya sabe
            $sqlUpdatePedido = "UPDATE pedidos SET precioTotal = (SELECT SUM(precioUnitario * cantidad) FROM lineasPedidos WHERE idPedido = '$idPedido') WHERE idPedido = '$idPedido'";
            $conexion->query($sqlUpdatePedido);

            // Vaciar la cesta después de realizar el pedido
            $sqlVaciarCesta = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion->query($sqlVaciarCesta);
        } else {
            // Hubo un error al insertar el pedido :(
            echo "Error al realizar el pedido.";
        }
    }
    ?>

    <!-- Start NAV -->
    <?php require_once 'nav.php'; ?>
    <!-- Cierre NAV -->
    <!-- Header -->
    <?php require_once 'header.php' ?>
    <!-- Cierre Header -->
    <!-- Modal -->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="w-100 pt-1 mb-5 text-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="get" class="modal-content modal-body border-0 p-0">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="inputModalSearch" name="q" placeholder="Search ...">
                    <button type="submit" class="input-group-text bg-success text-light">
                        <i class="fa fa-fw fa-search text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- body principal -->
    <?php

    // Aqui tomamos el objeto de productosCestas
    $cestas = "SELECT * FROM productosCestas WHERE idCesta = '$idCesta'";
    $resultadoCestas = $conexion->query($cestas);
    $productoCesta = [];

    while ($fila = $resultadoCestas->fetch_assoc()) {
        $nuevo_productoCesta = new productoCesta(
            $fila["idProducto"],
            $fila["idCesta"],
            $fila["cantidad"]

        );
        array_push($productoCesta, $nuevo_productoCesta);
    }
    ?>
    <div class="container">
        <div class="col-md-8">
            <div class="col-12">
                <h1 class="mt-5 mb-4">Productos</h1>
                <div class="table-responsive">
                    <table class="table table-hover table-primary">
                        <thead class="table-dark">
                            <tr>
                                <th>Nombre</th>
                                <th>Imagen</th>
                                <th>Precio Unidad</th>
                                <th>Cantidad a comprar</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php foreach ($productoCesta as $producto) { ?>
                                <tr>
                                    <td class="align-middle">
                                        <?php
                                        //Para obtener el nombre del producto en lugar del id de la cesta...
                                        $idProducto = $producto->idProducto; //Extraemos el ID del producto del objeto actual para usarlo en la consula SQL de abajo
                                        $sqlNombreProducto = "SELECT nombreProducto FROM productos WHERE idProducto ='$idProducto'";
                                        $resultadoNombreProducto = $conexion->query($sqlNombreProducto);
                                        $nombreProducto = $resultadoNombreProducto->fetch_assoc()["nombreProducto"];
                                        echo $nombreProducto;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        //Para obtener la imagen del producto...
                                        $idProducto = $producto->idProducto; //Extraemos el ID del producto del objeto actual para usarlo en la consula SQL de abajo
                                        $sqlImagenProducto = "SELECT imagen FROM productos WHERE idProducto ='$idProducto'";
                                        $resultadoImagenProducto = $conexion->query($sqlImagenProducto);
                                        $imagenProducto = $resultadoImagenProducto->fetch_assoc()["imagen"];
                                        echo "<img src='$imagenProducto' alt='Imagen del producto' style='width: 100px;'>";
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php
                                        //Para obtener el precio del producto...
                                        $idProducto = $producto->idProducto; //Extraemos el ID del producto del objeto actual para usarlo en la consula SQL de abajo
                                        $sqlPrecioProducto = "SELECT precio FROM productos WHERE idProducto ='$idProducto'";
                                        $resultadoPrecioProducto = $conexion->query($sqlPrecioProducto);
                                        $precioProducto = $resultadoPrecioProducto->fetch_assoc()["precio"];
                                        echo $precioProducto;
                                        ?>
                                        €
                                    </td>
                                    <td class="align-middle">
                                        <?php echo $producto->cantidad ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot class="text-center">
                            <!-- Vamos a comprobar si hay articulos en la cesta, para ellos hacemos un count del total de productos en productosCestas
                                 si hay mas de uno nos sale el boton de realizar pedido, sino nos sale un texto alternativo -->
                            <?php
                            $cantidadCesta = "SELECT COUNT(*) AS totalProductos FROM productosCestas WHERE idCesta = '$idCesta'";
                            $resultadoCantidadCesta = $conexion->query($cantidadCesta);
                            $totalProductosEnCesta = $resultadoCantidadCesta->fetch_assoc()["totalProductos"];
                            if ($totalProductosEnCesta > 0) { ?>
                                <td colspan="2">
                                    <form action="" method="POST">
                                        <button type="submit" class="btn btn-success text-white mt-2" value="comprar">
                                            <i class="fas fa-cart-plus"></i>Realizar pedido
                                        </button>
                                    </form>
                                </td>
                                <td colspan="2">
                                    <form action="" method="POST">
                                        <button type="submit" class="btn btn-danger text-white mt-2" name="vaciar_cesta"
                                            value="vaciar">
                                            <i class="fas fa-trash"></i>Vaciar cesta
                                        </button>
                                    </form>
                                </td>
                            <?php } else { ?>
                                <td colspan="4">
                                    No hay artículos en la cesta
                                </td>
                            <?php } ?>
                        </tfoot>
                    </table>
                    <div class="container mt-4">
                        <?php
                        // Mostrar mensaje de borrado si existe
                        if (isset($mensajeExito)) {
                            echo '<div class="alert alert-success" role="alert">' . $mensajeExito . '</div>';
                        }
                        ?>
                        <!-- Mostrar mensaje de éxito al añadir al carrito -->
                        <?php
                        if (isset($mensajeError)) {
                            echo '<div class="alert alert-success" role="alert">' . $mensajeError . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php require_once 'footer.php'; ?>
    <!-- End Footer -->

    <!-- Start Script -->
    <script src="assets/js/jquery-1.11.0.min.js"></script>
    <script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/templatemo.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- End Script -->

</body>
<!-- Gracias :) :) :) -->

</html>