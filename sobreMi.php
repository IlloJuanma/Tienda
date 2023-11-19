<!DOCTYPE html>
<html lang="en">

<head>
    <title>Satoru no kōnā - Sobre Mi</title>
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
                    <a class="nav-icon position-relative text-decoration-none" href="pedido.php">
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
    <!-- Close Header -->

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

    <section class="bg-success py-4">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-md-8 text-white">

                    <h1><img src="assets/img/tori.gif" alt="tori" width="50px"> Satoru no kōnā <img
                            src="assets/img/tori.gif" alt="tori" width="50px"></h1>
                    <p>
                        ¡Bienvenidos a <strong>Satoru no kōnā</strong>! Soy <em>Satoru</em>, el amante apasionado de la
                        cultura japonesa detrás de esta colorida esquina virtual. Mi rincón es más que una tienda en
                        línea; es un espacio donde la magia del cine, anime, manga y videojuegos se fusiona con la moda
                        y la diversión.
                        <br><br>
                        Con una pasión desenfrenada por todo lo relacionado con el arte de los videojuegos, cine y
                        anime, decidí crear este espacio único para compartir mi entusiasmo contigo. Bajo el seudónimo
                        de <strong>Satoru</strong>, me sumerjo en el mundo de los productos que amo, seleccionando
                        cuidadosamente una gama diversa que abarca desde los clásicos del cine hasta los últimos
                        lanzamientos de videojuegos y las <em>tendencias más cool en moda japonesa</em>. <strong>Dōmo
                            arigatōgozaimasu!</strong>
                        <br><br>
                        Aquí, en <strong>Satoru no kōnā</strong>, no solo encontrarás productos de alta calidad, sino
                        también una experiencia de compra llena de energía mística, ¡uuuhh!, y estilo auténtico.
                        ¡Sumérgete en mi universo, donde las expresiones en japonés se entrelazan con la pasión por la
                        cultura pop, creando una atmósfera única y emocionante!
                        <br><br>
                    <h3>Únete a mí en este viaje, descubre tesoros de la cultura japonesa y vístete con la esencia de
                        <strong>Satoru</strong>. <em>Arittake no yume wo kakiatsume!!</em>
                    </h3>
                    </p>
                </div>
                <div class="col-md-4">
                    <img src="assets/img/satoru2.png" alt="About Hero">
                </div>
            </div>
        </div>
    </section>
    <!-- Close Banner -->

    <!-- Start Section -->
    <section class="container py-5">
        <div class="row text-center pt-5 pb-3">
            <div class="col-lg-6 m-auto">
                <h1><img src="assets/img/fuji.gif" alt="fuji" width="50px"> Watashi no sābisu <img
                        src="assets/img/fuji2.gif" alt="fuji" width="50px"></h1>
                <p>
                    Descubre la atención excepcional las <strong>24 horas</strong> en <strong>Satoru no kōnā</strong>.
                    Aprovecha nuestras <em>promociones irresistibles</em> y disfruta de envíos rápidos a todo el mundo.
                    Sumérgete en la experiencia única de servicio y estilo. ¡Tu satisfacción es nuestra prioridad!
                    <br>
                    <strong>Nani o matte iru nda, orokamono!</strong>
                </p>
            </div>
        </div>
        <div class="row">

            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-5 services-icon-wap shadow">
                    <div class="h1 text-success text-center"><i class="fa fa-truck fa-lg"></i></div>
                    <h2 class="h5 mt-4 text-center">Envio Express</h2>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-5 services-icon-wap shadow">
                    <div class="h1 text-success text-center"><i class="fas fa-exchange-alt"></i></div>
                    <h2 class="h5 mt-4 text-center">Devoluciones</h2>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-5 services-icon-wap shadow">
                    <div class="h1 text-success text-center"><i class="fa fa-percent"></i></div>
                    <h2 class="h5 mt-4 text-center">Promociones</h2>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 pb-5">
                <div class="h-100 py-5 services-icon-wap shadow">
                    <div class="h1 text-success text-center"><i class="fa fa-user"></i></div>
                    <h2 class="h5 mt-4 text-center">Atencion al cliente</h2>
                </div>
            </div>
        </div>
    </section>
    <!-- End Section -->

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