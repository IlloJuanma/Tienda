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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;200;300;400;500;700;900&display=swap">
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <!-- Cargamos los requires necesarios de PhP-->
    <?php require 'objetos/producto.php' ?>
    <?php require 'funciones/base_datos_tienda.php' ?>
    <?php require 'funciones/depurar.php' ?>

</head>

<!-- Pagina principal de la tienda -->

<body>
    <!-- Iniciamos sesión en la base de datos y almacenamos los datos IMPORTANTE 重要！！-->
    <?php
    session_start();
    $usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Invitado";

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
                    <a class="navbar-sm-brand text-light text-decoration-none" href="mailto:info@company.com">IlloJuanma@gmail.com</a>
                    <i class="fa fa-phone mx-2"></i>
                    <a class="navbar-sm-brand text-light text-decoration-none" href="tel:010-020-0340">050-254-6399</a>
                </div>
                <div>
                    <!-- Sigueme :D -->
                    <a href="https://steamcommunity.com/profiles/76561198093473164">
                        <img class="img-fluid brand-img" src="assets/img/steam2.png" alt="Brand Logo" style="width: 30px;">
                    </a>
                    <!-- Sigueme :D -->
                    <a href="https://www.instagram.com/juanma_rodrguez/">
                        <img class="img-fluid brand-img" src="assets/img/insta.png" alt="Brand Logo" style="width: 30px;">
                    </a>
                    <!-- Si eres de sensibilidad frágil, no entres en mi Twitter -->
                    <a href="https://twitter.com/MrFlexaverde">
                        <img class="img-fluid brand-img" src="assets/img/twitter.png" alt="Brand Logo" style="width: 30px;">
                    </a>
                    <!-- Sigueme :D -->
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

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#templatemo_main_nav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="align-self-center collapse navbar-collapse flex-fill  d-lg-flex justify-content-lg-between" id="templatemo_main_nav">
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
                    <a class="nav-icon d-none d-lg-inline" href="#" data-bs-toggle="modal" data-bs-target="#templatemo_search">
                        <i class="fa fa-fw fa-search text-dark mr-2"></i>
                    </a>
                    <?php
                    if (isset($_SESSION["rol"]) && ($_SESSION["rol"] == "admin" || $_SESSION["rol"] == "cliente")) { ?>
                        <a class="nav-icon position-relative text-decoration-none" href="pedido.php">
                            <i class="fa fa-fw fa-cart-arrow-down text-dark mr-1"></i>
                            <span class="position-absolute top-0 left-100 translate-middle badge rounded-pill bg-light text-dark">
                                <?php echo $totalProductosEnCesta; ?>
                            </span>
                        </a>
                    <?php } ?>
                    <a class="nav-icon position-relative text-decoration-none" href="#">
                        <i class="fa fa-fw fa-user text-dark mr-3"></i>
                    </a>
                </div>
            </div>

        </div>
    </nav>
    <!-- Cierre Header -->

    <!-- Modal -->
    <div class="modal fade bg-white" id="templatemo_search" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
    <!-- Empieza carrousel -->
    <!-- Creamos un array de cada producto que entre en la base de datos, usando un objeto de ese producto -->
    <!-- Con el while recorremos las filas de la tabla, y mientras haya creara objetos en el array -->
    <?php
    $sql = "SELECT * FROM productos";
    $resultado = $conexion->query($sql);
    $productos = [];

    while ($fila = $resultado->fetch_assoc()) {
        $nuevo_producto = new Producto(
            $fila["idProducto"],
            $fila["nombreProducto"],
            $fila["precio"],
            $fila["descripcion"],
            $fila["cantidad"],
            $fila["imagen"]
        );
        array_push($productos, $nuevo_producto);
    }
    ?>
    <!-- Mejora del sistema de carrousel para que se muestre adecuadamente NO TOCAAAR!! 重要！！ -->
    <?php
    // Mezcla el arreglo de productos de manera aleatoria, queda más bonito no?
    shuffle($productos);

    // Toma los primeros 3 productos después de la mezcla, solo quiero que se muestren 3, no todo. Las grandes fragancias vienen en frascos pequeños :)
    // array_slice devuelve una copia de una parte del array dentro de un nuevo array empezando por inicio hasta fin (fin no incluido)
    $primeros_tres_productos_aleatorios = array_slice($productos, 0, 3);
    ?>
    <div id="template-mo-zay-hero-carousel" class="carousel slide" data-bs-ride="carousel">
        <ol class="carousel-indicators">

            <!------------------------------- Iniciamos foreach de los 3 primeros productos ------------------------>
            <?php foreach ($primeros_tres_productos_aleatorios as $key => $producto) : ?>
                <li data-bs-target="#template-mo-zay-hero-carousel" data-bs-slide-to="<?php echo $key; ?>" class="<?php echo ($key === 0) ? 'active' : ''; ?>"></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner">
            <?php foreach ($primeros_tres_productos_aleatorios as $key => $producto) : ?>
                <div class="carousel-item <?php echo ($key === 0) ? 'active' : ''; ?>">
                    <div class="container">
                        <div class="row p-5">
                            <div class="mx-auto col-md-8 col-lg-6 order-lg-last">
                                <img class="img-fluid" src="<?php echo $producto->imagen ?>" alt="">
                            </div>
                            <div class="col-lg-6 mb-0 d-flex align-items-center">
                                <div class="text-align-left align-self-center">
                                    <h1 class="h1 text-success">
                                        <?php echo $producto->nombreProducto ?>
                                    </h1>
                                    <h3 class="h2">Solo por:
                                        <?php echo $producto->precio ?>€
                                    </h3>
                                    <p>
                                        <?php echo $producto->descripcion ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <!----------------------------------------- Fin de foreach -------------------------->
            
        </div>
        <a class="carousel-control-prev text-decoration-none w-auto ps-3" href="#template-mo-zay-hero-carousel" role="button" data-bs-slide="prev">
            <i class="fas fa-chevron-left"></i>
        </a>
        <a class="carousel-control-next text-decoration-none w-auto pe-3" href="#template-mo-zay-hero-carousel" role="button" data-bs-slide="next">
            <i class="fas fa-chevron-right"></i>
        </a>
    </div>
    <!-- Fin de bloque carrousel -->

    <!-- Bloque Productos -->
    <section class="bg-light">
        <div class="container py-5">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <!-- Oye si no sabes japones usa el traductor ;) -->
                    <h1 class="h1">Seihin</h1>
                    <p>
                        Intānetto-jō de mitsuke rareru saikō no seihin wa koko dakedesu!
                    </p>
                </div>
            </div>
            <!-- Mostramos los productos, usando un foreach para recorrer el array y de ese producto, mostramos lo que nos interesa -->
            <div class="row">
                <?php foreach ($primeros_tres_productos_aleatorios as $producto) : ?>
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card h-100 d-flex flex-column" style="min-height: 350px;">
                            <a href="productos.php">
                                <img src="<?php echo $producto->imagen; ?>" class="card-img-top h-100" alt="..." style="width: 100%; height: 100%; object-fit: cover;">
                            </a>
                            <div class="card-body d-flex flex-column">
                                <ul class="list-unstyled d-flex justify-content-between mb-2">
                                    <li>
                                        <i class="text-warning fa fa-star"></i>
                                        <i class="text-warning fa fa-star"></i>
                                        <i class="text-warning fa fa-star"></i>
                                        <i class="text-muted fa fa-star"></i>
                                        <i class="text-muted fa fa-star"></i>
                                    </li>
                                    <li class="text-muted text-right">
                                        <?php echo $producto->precio; ?>€
                                    </li>
                                </ul>
                                <a href="productos.php" class="h2 text-decoration-none text-dark">
                                    <?php echo $producto->nombreProducto; ?>
                                </a>
                                <p class="card-text flex-grow-1">
                                    <?php echo $producto->descripcion; ?>
                                </p>
                                <p class="text-muted mt-2">Reviews (
                                    <?php echo rand(1, 100); ?>)
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- Fin bloque producto -->

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
                        <!-- Sigueme :D -->
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://steamcommunity.com/profiles/76561198093473164"><img class="img-fluid brand-img" src="assets/img/steam2.png" alt="Brand Logo"></a>
                        </li>
                        <!-- Sigueme :D -->
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://www.instagram.com/juanma_rodrguez/"><img class="img-fluid brand-img" src="assets/img/insta.png" alt="Brand Logo"></a>
                        </li>
                        <!-- Si eres de sensibilidad frágil, no entres en mi Twitter -->
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://twitter.com/MrFlexaverde"><img class="img-fluid brand-img" src="assets/img/twitter.png" alt="Brand Logo"></a>
                        </li>
                        <!-- Sigueme :D -->
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a href="https://github.com/IlloJuanma"><img class="img-fluid brand-img" src="assets/img/git.png" alt="Brand Logo"></a>
                        </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <label class="sr-only" for="subscribeEmail">Email address</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control bg-dark border-light" id="subscribeEmail" placeholder="Email">
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