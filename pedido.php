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
    if ($resultadoCestas->num_rows > 0) {
        $filaCestas = $resultadoCestas->fetch_assoc();
        $idCesta = $filaCestas["idCesta"];
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["usuario"])) {


        // Insertar en la tabla Pedidos
        $sqlInsertarPedido = "INSERT INTO pedidos (usuario) VALUES ('$usuario')";

        if ($conexion->query($sqlInsertarPedido)) {

            // Obtener el idPedido recién insertado ()
            $idPedido = $conexion->insert_id;

            $contador = 1;
            // Obtener productos de la cesta
            $sqlPedido = "SELECT p.idProducto AS idProducto,
                             p.precio AS precio, 
                             c.cantidad AS cantidad
                      FROM productos p JOIN productosCestas c
                      ON p.idProducto = c.idProducto
                      WHERE idCesta ='$idCesta'";
            $resultado = $conexion->query($sqlPedido);

            // Insertar en la tabla lineasPedidos
            for ($contador = 1; $fila = $resultado->fetch_assoc(); $contador++) {
                $idProducto = $fila["idProducto"];
                $precioUnitario = $fila["precio"];
                $cantidad = $fila["cantidad"];

                // Insertar en la tabla lineasPedidos
                $sqlInsertLineaPedido = "INSERT INTO lineasPedidos (lineaPedido, idPedido, idProducto, precioUnitario, cantidad)
                                    VALUES ('$contador', '$idPedido', '$idProducto', '$precioUnitario', '$cantidad')";
                if ($conexion->query($sqlInsertLineaPedido)) {
                    //todo ha ido bien, mostramos un mensaje de éxito
                    $mensajeExito = "Pedido realizado con éxito. ID del pedido: $idPedido";
                } else {
                    // Hubo un error al insertar la línea de pedido
                    $mensajeError = "Error al insertar la línea de pedido.";
                }
            }

            // Actualizamos el precio total en la tabla pedidos
            $sqlUpdatePedido = "UPDATE pedidos SET precioTotal = (SELECT SUM(precioUnitario * cantidad) FROM lineasPedidos WHERE idPedido = '$idPedido') WHERE idPedido = '$idPedido'";
            $conexion->query($sqlUpdatePedido);

            // Vaciar la cesta después de realizar el pedido
            $sqlVaciarCesta = "DELETE FROM productosCestas WHERE idCesta = '$idCesta'";
            $conexion->query($sqlVaciarCesta);
        } else {
            // Hubo un error al insertar el pedido
            echo "Error al realizar el pedido.";
        }


    }
    ?>

    <!-- Start NAV -->
    <nav class="navbar navbar-expand-lg bg-dark navbar-light d-none d-lg-block" id="templatemo_nav_top">
        <div class="container text-light">
            <div class="w-100 d-flex justify-content-between">
                <div>
                    <i class="fa fa-envelope mx-2"></i>
                    <a class="navbar-sm-brand text-light text-decoration-none"
                        href="mailto:info@company.com">IlloJuanma@gmail.com</a>
                    <i class="fa fa-phone mx-2"></i>
                    <a class="navbar-sm-brand text-light text-decoration-none" href="tel:010-020-0340">050-254-6399</a>
                </div>
                <div>
                    <a href="https://steamcommunity.com/profiles/76561198093473164">
                        <img class="img-fluid brand-img" src="assets/img/steam2.png" alt="Brand Logo"
                            style="width: 30px;">
                    </a>
                    <a href="https://www.instagram.com/juanma_rodrguez/">
                        <img class="img-fluid brand-img" src="assets/img/insta.png" alt="Brand Logo"
                            style="width: 30px;">
                    </a>
                    <a href="https://twitter.com/MrFlexaverde">
                        <img class="img-fluid brand-img" src="assets/img/twitter.png" alt="Brand Logo"
                            style="width: 30px;">
                    </a>
                    <a href="https://github.com/IlloJuanma">
                        <img class="img-fluid brand-img" src="assets/img/git.png" alt="Brand Logo" style="width: 30px;">
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <!-- Cierre NAV -->
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light shadow">
        <div class="container d-flex justify-content-between align-items-center">

            <a class="navbar-brand text-success logo h1 align-self-center" href="index.html">
                <!-- De de esta forma controlamos un mensaje de bienvenida personalizado para el administrador.
                     Sino, el mensaje de bienvenida es normalito para el usuario "standar" -->
                <!-- Verifica si la clave "rol" está definida en la sesión antes de intentar acceder a ella -->
                <?php
                if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") { ?>
                    <img src="assets/img/estrella.gif" alt="" width="35px">
                    Bienvenido <br>
                    <strong>Satoru-Sama </strong>
                    <img src="assets/img/estrella.gif" alt="" width="35px">
                <?php } else { ?>
                    Bienvenido <br>
                    <?php echo htmlspecialchars($usuario); ?>
                <?php } ?>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between"
                id="templatemo_main_nav">
                <div class="flex-fill">
                    <ul class="nav navbar-nav d-flex justify-content-between mx-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="principal.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="sobreMi.php">Sobre Mi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productos.php">Tienda</a>
                        </li>

                        <!-- Solo cuando se es administrador, puedes entrar en la parte de registrar productos -->
                        <?php
                        if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="crear_producto.php">Registrar productos</a>
                            </li>
                        <?php } ?>

                        <li class="nav-item">
                            <a class="nav-link" href="cerrar_sesion.php">Cerrar Sesion</a>
                        </li>
                    </ul>
                </div>
                <div class="navbar align-self-center d-flex">
                    <div class="d-lg-none flex-sm-fill mt-3 mb-4 col-7 col-sm-auto pr-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="inputMobileSearch" placeholder="Search ...">
                            <div class="input-group-text">
                                <i class="fa fa-fw fa-search"></i>
                            </div>
                        </div>
                    </div>
                    <a class="nav-icon d-none d-lg-inline" href="#" data-bs-toggle="modal"
                        data-bs-target="#templatemo_search">
                        <i class="fa fa-fw fa-search text-dark mr-2"></i>
                    </a>
                    <a class="nav-icon position-relative text-decoration-none" href="#">
                        <i class="fa fa-fw fa-cart-arrow-down text-dark mr-1"></i>
                        <span
                            class="position-absolute top-0 left-100 translate-middle badge rounded-pill bg-light text-dark">
                            <?php echo $totalProductosEnCesta; ?>
                        </span>
                    </a>
                    <a class="nav-icon position-relative text-decoration-none" href="#">
                        <i class="fa fa-fw fa-user text-dark mr-3"></i>
                    </a>
                </div>
            </div>

        </div>
    </nav>
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
                                <th>Id del Producto</th>
                                <th>Id de la cesta</th>
                                <th>Imagen</th>
                                <th>Cantidad a comprar</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php foreach ($productoCesta as $producto) { ?>
                                <tr>
                                    <td class="align-middle">
                                        <?php echo $producto->idProducto ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php
                                        //Para obtener el nombre del producto en lugar del id de la cesta...
                                        $idProducto = $producto->idProducto; //Extraemos el ID del producto del objeto actual para usarlo en la consula SQL
                                        $sqlNombreProducto = "SELECT nombreProducto FROM productos WHERE idProducto ='$idProducto'";
                                        $resultadoNombreProducto = $conexion->query($sqlNombreProducto);
                                        $nombreProducto = $resultadoNombreProducto->fetch_assoc()["nombreProducto"];
                                        echo $nombreProducto;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        //Para obtener la imagen del producto...
                                        $idProducto = $producto->idProducto; //Extraemos el ID del producto del objeto actual para usarlo en la consula SQL
                                        $sqlImagenProducto = "SELECT imagen FROM productos WHERE idProducto ='$idProducto'";
                                        $resultadoImagenProducto = $conexion->query($sqlImagenProducto);
                                        $imagenProducto = $resultadoImagenProducto->fetch_assoc()["imagen"];
                                        echo "<img src='$imagenProducto' alt='Imagen del producto' style='width: 100px;'>";
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php echo $producto->cantidad ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot class="text-center">
                            <td colspan="4">
                                <form action="" method="POST">
                                    <button type="submit" class="btn btn-success text-white mt-2" value="comprar">
                                        <i class="fas fa-cart-plus"></i>Realizar pedido
                                    </button>
                                </form>
                            </td>
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
    <footer class="bg-dark" id="tempaltemo_footer">
        <div class="container">
            <div class="row">

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-success border-bottom pb-3 border-light logo">Satoru no kōnā</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li>
                            <i class="fas fa-map-marker-alt fa-fw"></i>
                            <!-- Es de verdad eh... -->
                            Calle falsa 123
                        </li>
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none" href="tel:010-020-0340">056-555-5149</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none" href="mailto:info@company.com">IlloJuanma@gmail.com</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-light border-bottom pb-3 border-light">Productos</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li><a class="text-decoration-none" href="#">Anime</a></li>
                        <li><a class="text-decoration-none" href="#">Videojuegos</a></li>
                        <li><a class="text-decoration-none" href="#">Cine</a></li>
                        <li><a class="text-decoration-none" href="#">Ropa de marca</a></li>
                        <li><a class="text-decoration-none" href="#">Merch</a></li>
                        <li><a class="text-decoration-none" href="#">Posters</a></li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-light border-bottom pb-3 border-light">Info</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li><a class="text-decoration-none" href="principal.php">Inicio</a></li>
                        <li><a class="text-decoration-none" href="sobreMi.php">Sobre Mi</a></li>
                        <li><a class="text-decoration-none" href="#">FAQs</a></li>
                        <li><a class="text-decoration-none" href="#">Contacto</a></li>
                    </ul>
                </div>
            </div>

            <!-- Redes sociales, en este caso si que son de verdad, en serio, son los mios, échale un vistazo. Al twitter no porfa, es un pozo de locura -->
            <div class="row text-light mb-4">
                <div class="col-12 mb-3">
                    <div class="w-100 my-3 border-top border-light"></div>
                </div>
                <div class="col-auto me-auto">
                    <ul class="list-inline text-left footer-icons">
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://steamcommunity.com/profiles/76561198093473164"><img
                                    class="img-fluid brand-img" src="assets/img/steam2.png" alt="Brand Logo"></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://www.instagram.com/juanma_rodrguez/"><img class="img-fluid brand-img"
                                    src="assets/img/insta.png" alt="Brand Logo"></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <!-- NO MIRAR 見ない！ Minai! 見ない！ Minai! 見ない！ Minai! 見ない！ Minai! 見ない！ Minai! 見ない -->
                            <a href="https://twitter.com/MrFlexaverde"><img class="img-fluid brand-img"
                                    src="assets/img/twitter.png" alt="Brand Logo"></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://github.com/IlloJuanma"><img class="img-fluid brand-img"
                                    src="assets/img/git.png" alt="Brand Logo"></a>
                        </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <label class="sr-only" for="subscribeEmail">Email address</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control bg-dark border-light" id="subscribeEmail"
                            placeholder="Email">
                        <div class="input-group-text btn-success text-light">Subscribirse</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 bg-black py-3">
            <div class="container">
                <div class="row pt-2">
                    <div class="col-12">
                        <p class="text-left text-light">
                            Copyright &copy; 2023 Satoru Company. All rights reserved
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
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