<!DOCTYPE html>
<html lang="en">

<head>
    <title>Satoru no kōnā - Productos</title>
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
    <?php require 'objetos/producto.php' ?>
    <?php require 'funciones/base_datos_tienda.php' ?>

</head>

<body>
    <!-- Guardamos los datos de la sesion -->
    <?php
    session_start();
    $usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Invitado";


    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["usuario"])) {
        $idProducto = $_POST["id_Producto"];
        if ($_POST["accion"] == "aniadir") {
            $usuario = $_SESSION["usuario"];

            //Aqui puedo realizar validaciones si quiero
    
            // Obtengo el idCestas y la cantidad del usuario actual
            $sqlCesta = "SELECT idCesta FROM cestas WHERE usuario = '$usuario'";
            $resultadoCestas = $conexion->query($sqlCesta);

            if ($resultadoCestas->num_rows > 0) {
                $filaCestas = $resultadoCestas->fetch_assoc();
                $idCesta = $filaCestas["idCesta"];

                // Verifico si el producto ya está en la cesta
                $sqlProductoExistente =
                    "SELECT idProducto, cantidad FROM productosCestas WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                $resultadoProductoExistente = $conexion->query($sqlProductoExistente);

                if ($resultadoProductoExistente->num_rows > 0) {
                    // Si el producto ya está en la cesta, incremento la cantidad
                    $filaProductoExistente = $resultadoProductoExistente->fetch_assoc();
                    $cantidadExistente = $filaProductoExistente["cantidad"];
                    $nuevaCantidad = $cantidadExistente + 1;

                    // Actualizo la cantidad
                    $sqlActualizarCantidad =
                        "UPDATE productosCestas SET cantidad = '$nuevaCantidad' WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                    $conexion->query($sqlActualizarCantidad);
                } else {
                    // Si el producto no está en la cesta, inserto un nuevo registro
                    $sqlInsert =
                        "INSERT INTO productosCestas (idCesta, idProducto, cantidad) VALUES ('$idCesta', '$idProducto', '1')";
                    $conexion->query($sqlInsert);
                }

                $mensajeExito = "Producto añadido a la cesta!!";
            } else {
                $mensajeError = "Producto no añadido a la cesta, Error!";
            }
        }
        if ($_POST["accion"] == "borrar") {
            $id_Producto = $_POST["id_Producto"];

            // Vamos a borrar tanto el producto como la cesta y la direccion de la      
            //imagen para que al borrar el producto, borremos la imagen de nuestro pc
            $sqlBorrarProducto = "SELECT * FROM productos WHERE idProducto = '$id_Producto'";
            $resultado = $conexion->query($sqlBorrarProducto);

            //Si hay filas que leer...
            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $imagen = $row["imagen"];
                }
            }
            unlink($imagen);
            //Fin de borrar imagen
    
            //Necesito borrar antes las referencias a ese producto
            //ya que se relaciona con otra tabla
            $sqlBorrarCesta =
                "DELETE FROM productosCestas WHERE idProducto = $id_Producto";
            $sqlBorrarProducto =
                "DELETE FROM productos WHERE idProducto = $id_Producto";
            if (
                $conexion->query($sqlBorrarCesta) &&
                $conexion->query($sqlBorrarProducto) === TRUE
            ) {
                //unlink ("img/" . imagen);
                $mensajeBorrado = "Producto Borrado Correctamente!!";
            } else {
                $mensajeBorrado = "Error al borrar producto: " . $conn->error();
            }
        }
    }
    // Consultamos la cantidad total de productos en la cesta del usuario actual
    $sqlCantidadCesta =
        "SELECT SUM(cantidad) as totalProductos FROM productosCestas pc
     INNER JOIN cestas c ON pc.idCesta = c.idCesta
     WHERE c.usuario = '$usuario'";

    $resultadoCantidadCesta = $conexion->query($sqlCantidadCesta);

    // Obtenemos la cantidad total
    $totalProductosEnCesta = $resultadoCantidadCesta->fetch_assoc()["totalProductos"];

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
                <?php
                if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") { ?>
                    <img src="assets/img/estrella.gif" alt="" width="35px">
                    Bienvenido <br>
                    <strong>Satoru-Sama </strong>
                    <img src="assets/img/estrella.gif" alt="" width="35px">
                <?php } else { ?>
                    Bienvenido <br>
                    <?php echo $usuario; ?>
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
                            <input type="text" class="form-control" id="inputMobileSearch" placeholder="Buscar ...">
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
                        <span
                            class="position-absolute top-0 left-100 translate-middle badge rounded-pill bg-light text-dark"></span>
                    </a>
                </div>
            </div>

        </div>
    </nav>
    <!-- Close Header -->

    <!-- Modal -->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="w-100 pt-1 mb-5 text-right">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action method="get" class="modal-content modal-body border-0 p-0">
                <div class="input-group mb-2">
                    <input type="text" class="form-control" id="inputModalSearch" name="q" placeholder="Search ...">
                    <button type="submit" class="input-group-text bg-success text-light">
                        <i class="fa fa-fw fa-search text-white"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Start Content -->
    <div class="container py-5">
        <div class="row">

            <div class="col-lg-3">
                <h1 class="h2 pb-4">Nandeshou?</h1>
                <ul class="list-unstyled templatemo-accordion">
                    <li class="pb-3">
                        <a class="collapsed d-flex justify-content-between h3 text-decoration-none" href="#">
                            Anime
                            <i class="fa fa-fw fa-chevron-circle-down mt-1"></i>
                        </a>
                        <ul class="collapse show list-unstyled pl-3">
                            <li><a class="text-decoration-none" href="#">Accion</a></li>
                            <li><a class="text-decoration-none" href="#">Shounen</a></li>
                        </ul>
                    </li>
                    <li class="pb-3">
                        <a class="collapsed d-flex justify-content-between h3 text-decoration-none" href="#">
                            Cine
                            <i class="pull-right fa fa-fw fa-chevron-circle-down mt-1"></i>
                        </a>
                        <ul id="collapseTwo" class="collapse list-unstyled pl-3">
                            <li><a class="text-decoration-none" href="#">Aventura</a></li>
                            <li><a class="text-decoration-none" href="#">Romance</a></li>
                        </ul>
                    </li>
                    <li class="pb-3">
                        <a class="collapsed d-flex justify-content-between h3 text-decoration-none" href="#">
                            Videojuegos
                            <i class="pull-right fa fa-fw fa-chevron-circle-down mt-1"></i>
                        </a>
                        <ul id="collapseThree" class="collapse list-unstyled pl-3">
                            <li><a class="text-decoration-none" href="#">PS5</a></li>
                            <li><a class="text-decoration-none" href="#">Pc</a></li>
                            <li><a class="text-decoration-none" href="#">Xbox</a></li>
                        </ul>
                    </li>
                </ul>
            </div>

            <div class="col-lg-9">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-inline shop-top-menu pb-3 pt-1">
                            <li class="list-inline-item">
                                <a class="h3 text-dark text-decoration-none mr-3" href="#">Todo</a>
                            </li>
                            <li class="list-inline-item">
                                <a class="h3 text-dark text-decoration-none mr-3" href="#">Accion</a>
                            </li>
                            <li class="list-inline-item">
                                <a class="h3 text-dark text-decoration-none" href="#">Shounen</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6 pb-4">
                        <div class="d-flex">
                            <select class="form-control">
                                <option>Por defecto</option>
                                <option>A a Z</option>
                                <option>Mejor valorados</option>
                            </select>
                        </div>
                    </div>
                </div>

                <?php
                // Aquí empieza la fiesta de los productos, ¡preparaos para la diversión!
                
                // Consulta mágica para seleccionar todos los productos, ¡Abracadabra!
                $sql = "SELECT * FROM productos";

                // Ejecutar la consulta y guardar el resultado en la chistera
                $resultado = $conexion->query($sql);

                // Un array donde almacenaremos los productos, ¡es nuestro saco mágico!
                $productos = [];
                ?>

                <!-- ¡Bienvenidos al circo de Bootstrap! Aquí presentamos la actuación estelar: "El desfile de los productos" -->
                <div class="container">
                    <!-- Mensaje de Borrado -->
                    <div class="container mt-4">
                        <?php
                        // Mostrar mensaje de borrado si existe
                        if (isset($mensajeBorrado)) {
                            echo '<div class="alert alert-success" role="alert">' . $mensajeBorrado . '</div>';
                        }
                        ?>
                        <!-- Mostrar mensaje de éxito al añadir al carrito -->
                        <?php
                        if (isset($mensajeExito)) {
                            echo '<div class="alert alert-success" role="alert">' . $mensajeExito . '</div>';
                        }
                        ?>
                    </div>
                    <!-- Los artistas (productos) se alinean en el escenario -->
                    <div class="row">
                        <?php
                        // ¡Comienza el espectáculo! Aplausos para cada producto en el escenario
                        while ($fila = $resultado->fetch_assoc()) {
                            ?>
                            <!-- Cada producto es una estrella del espectáculo -->
                            <div class="col-md-4">
                                <!-- ¡Magia en acción! La tarjeta del producto aparece con un truco asombroso -->
                                <div class="card mb-4 product-wap rounded-0" style="min-height: 900px;">
                                    <!-- Aparece la imagen del producto, ¡oh sorpresa! -->
                                    <div class="card rounded-0">
                                        <!-- ¡Abracadabra! La imagen se muestra con estilo -->
                                        <img class="card-img rounded-0 img-fluid" src="<?php echo $fila["imagen"] ?>"
                                            alt="Imagen del producto"
                                            style="width: 100%; height: 450px; object-fit: cover;">

                                        <!-- ¡El gran overlay mágico! Botones de acción aparecen con un toque de magia -->
                                        <!-- --------------------- Aqui se añade al carrito o se borra si eres admi IMPORTANTE!!!!! OJO --------------------------- -->
                                        <div
                                            class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                                            <ul class="list-unstyled">
                                                <!-- Botón para borrar productos SOLO SI ERES ADMIN -->
                                                <?php
                                                if ($_SESSION["rol"] == "admin") { ?>
                                                    <form action="" method="POST">
                                                        <input type="hidden" value="<?php echo $fila["idProducto"]; ?>"
                                                            name="id_Producto">
                                                        <button type="submit" class="btn btn-success text-white mt-2"
                                                            name="accion" value="borrar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php } ?>
                                                </form>

                                                <!-- ¡La visión de futuro! Botón para ver la imagen en grande -->
                                                <li><a class="btn btn-success text-white mt-2"
                                                        href="<?php echo $fila["imagen"] ?>" target="_blank"><i
                                                            class="far fa-eye"></i></a></li>
                                                <!-- ¡Compra ya! Botón para agregar al carrito -->
                                                <form method="post" action="">
                                                    <input type="hidden" value="<?php echo $fila["idProducto"]; ?>"
                                                        name="id_Producto">
                                                    <!-- Otros campos ocultos si es necesario -->
                                                    <button type="submit" class="btn btn-success text-white mt-2"
                                                        name="accion" value="aniadir">
                                                        <i class="fas fa-cart-plus"></i>
                                                    </button>
                                                </form>

                                            </ul>
                                        </div>
                                    </div>


                                    <!-- ¡Aquí está el truco final! Detalles mágicos del producto -->
                                    <div class="card-body">
                                        <!-- La identidad secreta: el ID del producto -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Id:</strong>
                                            <?php echo $fila["idProducto"] ?>
                                        </p>
                                        <br>
                                        <!-- El nombre del producto, ¡una revelación extraordinaria! -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Nombre: </strong>
                                            <?php echo $fila["nombreProducto"] ?>
                                        </p>
                                        <br>
                                        <!-- La descripción, ¡una maravilla literaria! -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Descripción: </strong>
                                            <?php echo $fila["descripcion"] ?>
                                        </p>
                                        <br>
                                        <!-- La cantidad disponible, ¡un enigma cuantitativo! -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Cantidad: </strong>
                                            <?php echo $fila["cantidad"] ?>
                                        </p>

                                        <!-- La valoración de estrellas, ¡el cielo nos sonríe! -->
                                        <ul class="list-unstyled d-flex justify-content-center mb-1">
                                            <li>
                                                <?php
                                                // ¡El gran número aleatorio de estrellas! Un espectáculo de luces.
                                                $numEstrellas = rand(1, 5);

                                                // ¡Aplausos! Estrellas brillantes y estrellas apagadas, ¡el público enloquece!
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $numEstrellas) {
                                                        echo '<i class="text-warning fa fa-star"></i>';
                                                    } else {
                                                        echo '<i class="text-muted fa fa-star"></i>';
                                                    }
                                                }
                                                ?>
                                            </li>
                                        </ul>

                                        <!-- ¡El precio, el tesoro escondido! -->
                                        <p class="text-center mb-0">
                                            <strong>
                                                <?php echo $fila["precio"] ?>€
                                            </strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } // ¡Fin del acto! La cortina baja para cada producto.
                        ?>
                    </div>
                </div> <!-- ¡Aplausos! Fin del espectáculo -->
            </div>
        </div>
    </div> <!-- ¡Gracias, gracias! Fin del circo de Bootstrap -->
    <!-- End Content -->

    <!-- Start Brands -->
    <section class="bg-light py-5">
        <div class="container my-4">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <h1 class="h1">Colaboradores</h1>
                    <p>
                        Watashitachiha kono bun'ya no ōku no ōte kigyō to kyōryoku shite ori, irui ya guzzu
                        mo
                        fukumete!
                    </p>
                </div>
                <div class="col-lg-9 m-auto tempaltemo-carousel">
                    <div class="row d-flex flex-row">
                        <!--Controls-->
                        <div class="col-1 align-self-center">
                            <a class="h1" href="#multi-item-example" role="button" data-bs-slide="prev">
                                <i class="text-light fas fa-chevron-left"></i>
                            </a>
                        </div>
                        <!--End Controls-->

                        <!--Carousel Wrapper-->
                        <div class="col">
                            <div class="carousel slide carousel-multi-item pt-2 pt-md-0" id="multi-item-example"
                                data-bs-ride="carousel">
                                <!--Slides-->
                                <div class="carousel-inner product-links-wap" role="listbox">

                                    <!--First slide-->
                                    <div class="carousel-item active">
                                        <div class="row">
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img" src="assets/img/Xbox.png"
                                                        alt="Brand Logo"></a>
                                            </div>
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img" src="assets/img/PS5.png"
                                                        alt="Brand Logo"></a>
                                            </div>
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img" src="assets/img/cine.png"
                                                        alt=" Brand Logo"></a>
                                            </div>
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img" src="assets/img/ramen.png"
                                                        alt="Brand Logo"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--End First slide-->

                                    <!--Second slide-->
                                    <div class="carousel-item">
                                        <div class="row">
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img"
                                                        src="assets/img/brand_01.png" alt="Brand Logo"></a>
                                            </div>
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img"
                                                        src="assets/img/brand_02.png" alt="Brand Logo"></a>
                                            </div>
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img"
                                                        src="assets/img/brand_03.png" alt="Brand Logo"></a>
                                            </div>
                                            <div class="col-3 p-md-5">
                                                <a href="#"><img class="img-fluid brand-img"
                                                        src="assets/img/brand_04.png" alt="Brand Logo"></a>
                                            </div>
                                        </div>
                                    </div>
                                    <!--End Second slide-->

                                </div>
                                <!--End Slides-->
                            </div>
                        </div>
                        <!--End Carousel Wrapper-->

                        <!--Controls-->
                        <div class="col-1 align-self-center">
                            <a class="h1" href="#multi-item-example" role="button" data-bs-slide="next">
                                <i class="text-light fas fa-chevron-right"></i>
                            </a>
                        </div>
                        <!--End Controls-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--End Brands-->

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