<!DOCTYPE html>
<html lang="es">

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
    <?php require 'funciones/depurar.php' ?>
    <?php require 'objetos/producto.php' ?>
    <?php require 'funciones/base_datos_tienda.php' ?>

</head>

<body>
    <?php
    session_start();

    if ($_SESSION["rol"] != "admin") {
        header("Location: login.php");
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $temp_nombre = depurar($_POST["nombre"]);
        $temp_precio = depurar($_POST["precio"]);
        $temp_descripcion = depurar($_POST["descripcion"]);
        $temp_cantidad = depurar($_POST["cantidad"]);


        // # Imagen
        // $maxSize = 2097152; //2mb
        // $nombre_imagen = $_FILES["imagen"]["name"];
        // $ruta_temporal = $_FILES["imagen"]["tmp_name"];
    
        // if ($_FILES["imagen"]["size"] < $maxSize) {
    
        //     if ($_FILES["imagen"]["type"] == "image/jpg" || $_FILES["imagen"]["type"] == "image/png" || $_FILES["imagen"]["type"] == "image/jpeg") {
        //         $ruta_final = "img/" . $nombre_imagen;
        //         move_uploaded_file($ruta_temporal, $ruta_final);
        //     } else {
        //         echo '<h2 class="container">El tipo de la imagen no esta permitido, solo JPG,PNG ó JPEG</h2>';
        //     }
        // } else {
        //     echo "<h2>La imagen es demasiado grande</h2>";
        // }
    
        # Imagen
        $maxSize = 2097152; //2mb
        $nombre_imagen = $_FILES["imagen"]["name"];
        $ruta_temporal = $_FILES["imagen"]["tmp_name"];

        if ($_FILES["imagen"]["size"] < $maxSize) {

            if ($_FILES["imagen"]["type"] == "image/jpg" || $_FILES["imagen"]["type"] == "image/png" || $_FILES["imagen"]["type"] == "image/jpeg") {
                $ruta_final = "assets/img/" . $nombre_imagen;
                move_uploaded_file($ruta_temporal, $ruta_final);
            } else {
                $err_imagen = '<h2>El tipo de la imagen no está permitido, sólo JPG,PNG ó JPEG</h2>';
            }
        } else {
            $err_imagen = '<h2>La imagen es demasiado grande</h2>';
        }




        #Validación de nombre
        if (strlen($temp_nombre) == 0) {
            $err_nombre = "El nombre es obligatorio";
        } else {
            if (strlen($temp_nombre) > 40) {
                $err_nombre = "El nombre no puede tener más de 
                    40 caracteres";
            } else {
                $patron = "/^[a-zA-Z0-9áÁéÉíÍóÓúÚñÑäÄëËïÏöÖüÜ;:_() ]{0,40}$/";
                if (!preg_match($patron, $temp_nombre)) {
                    $err_nombre = "El nombre solo puede tener letras ó números";
                } else {
                    $temp_nombre = strtolower($temp_nombre);
                    $temp_nombre = ucwords($temp_nombre);
                    $nombre = $temp_nombre;
                }
            }
        }

        #Validar el precio
        if (strlen($temp_precio) == 0) {
            $err_precio = "El precio es obligatorio";
        } else {
            if ($temp_precio > 99999.99) {
                $err_precio = "El precio no debe ser mayor a 99999,99";
            } else if ($temp_precio < 0) {
                $err_precio = "El precio no debe ser menor a 0";
            } else {
                $precio = $temp_precio;
            }
        }

        #Validar la descripción
        if (strlen($temp_descripcion) == 0) {
            $err_descripcion = "La descripción es obligatoria";
        } else {
            if (strlen($temp_descripcion) > 255) {
                $err_descripcion = "La descripción no puede tener más de 255 caracteres";
            } else {
                $descripcion = $temp_descripcion;
            }
        }


        #Validar la cantidad
        if (strlen($temp_cantidad) == 0) {
            $err_cantidad = "La cantidad es obligatoria";
        } else {
            if ($temp_cantidad > 99999.99) {
                $err_cantidad = "La cantidad no debe ser mayor a 99999,99";
            } else if ($temp_cantidad < 0) {
                $err_cantidad = "La cantidad no puede ser menor a 0";
            } else {
                $cantidad = $temp_cantidad;
            }
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
                <?php
                if ($_SESSION["rol"] == "admin") { ?>
                    <img src="assets/img/estrella.gif" alt="" width="35px">
                    Bienvenido <br>
                    <strong>Satoru-Sama </strong>
                    <img src="assets/img/estrella.gif" alt="" width="35px">
                <?php } else { ?>
                    Bienvenido <br>
                    <?php echo $usuario;
                } ?>
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
                    </a>
                    <a class="nav-icon position-relative text-decoration-none" href="#">
                        <i class="fa fa-fw fa-user text-dark mr-3"></i>
                    </a>
                </div>
            </div>

        </div>
    </nav>
    <!-- Close Header -->
    <section class="bg-success py-5">
        <div class="container">
            <div class="row align-items-center py-5">
                <div class="col-md-8 text-white">
                    <section class="container my-4 text-center">
                        <h2>Registra un Nuevo Producto</h2>
                        <p class="fs-5">✨🎌¡Adéntrate en el mundo de Satoru! Como administrador, tienes el poder de dar
                            vida a nuevos tesoros de la cultura japonesa. Desde el cine hasta el anime, cada registro
                            agrega magia a nuestro rincón único. ¡Explora y comparte la esencia de Satoru!🎌✨</p>
                    </section>

                </div>
                <div class="col-md-4">
                    <img src="assets/img/satoru3.png" alt="About Hero">
                </div>
            </div>
        </div>
    </section>
    <style>
        .form-control {
            border: 1px solid #3498db;
            border-radius: 10px;
            padding: 8px;
            /* Espacio entre el borde y el texto */
        }
    </style>

    <section class="container my-4">
        <form action="" method="POST" enctype="multipart/form-data" class="row g-3 border p-4 bglight-">
            <legend>
                <div class="row text-center pt-5 pb-3">
                    <div class="col-lg-6 m-auto">
                        <h1 class="h1 fs-4"><img src="assets/img/carpa.gif" alt="carpa" width="50px"> Crear Producto
                            <img src="assets/img/carpa.gif" alt="carpa" width="50px">
                        </h1>
                        <p class="fs-5">Sā, kowagaranaide</p>
                    </div>
                </div>
            </legend>
            <div class="mb-4 col-md-6">
                <label class="form-label fs-5">Nombre <img src="assets/img/nombre.gif" alt="imagen"
                        width="45px"></label>
                <input type="text" class="form-control fs-5" name="nombre" required>

            </div>
            <div class="mb-4 col-md-6">
                <label class="form-label fs-5">Precio <img src="assets/img/precio.gif" alt="imagen"
                        width="45px"></label>
                <input type="text" class="form-control fs-5" name="precio" required>

            </div>
            <div class="mb-4 col-md-6">
                <label class="form-label fs-5">Descripción <img src="assets/img/descripcion.gif" alt="imagen"
                        width="45px"></label>
                <input type="text" class="form-control fs-5" name="descripcion">

            </div>
            <div class="mb-4 col-md-6">
                <label class="form-label fs-5">Cantidad <img src="assets/img/cantidad.gif" alt="imagen"
                        width="45px"></label>
                <input type="text" class="form-control fs-5" name="cantidad">

            </div>
            <div class="mb-4 text-center">
                <label class="form-label fs-5"><img src="assets/img/imagen.gif" alt="imagen" width="45px"></label>
                <label class="custom-file-upload">
                    <input type="file" class="form-control mb-2 fs-5" name="imagen">
                </label>

            </div>
            <div class="col-12 text-center mt-3">
                <?php
                if (isset($err_nombre) || isset($err_precio) || isset($err_descripcion) || isset($err_cantidad) || isset($err_imagen)) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo '¡Ups! Hubo un problema. Por favor, revisa los errores:';
                    echo '<ul>';
                    if (isset($err_nombre))
                        echo "<li>$err_nombre</li>";
                    if (isset($err_precio))
                        echo "<li>$err_precio</li>";
                    if (isset($err_descripcion))
                        echo "<li>$err_descripcion</li>";
                    if (isset($err_cantidad))
                        echo "<li>$err_cantidad</li>";
                    if (isset($err_imagen))
                        echo "<li>$err_imagen</li>";
                    echo '</ul>';
                    echo '</div>';
                } elseif (isset($nombre) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($ruta_final)) {
                    echo '<div class="alert alert-success" role="alert">';
                    echo '<h4 class="alert-heading">¡Producto registrado con éxito!</h4>';
                    echo "<p>Nombre: $nombre</p>";
                    echo "<p>Precio: $precio</p>";
                    echo "<p>Descripción: $descripcion</p>";
                    echo "<p>Cantidad: $cantidad</p>";
                    echo '</div>';
                }
                ?>
            </div>
            <div class="col-12 text-center">
                <button class="btn btn-primary fs-5" type="submit">Registrar producto</button>
            </div>
        </form>
    </section>

    <?php
    if (isset($nombre) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($ruta_final)) {
        $sql = "INSERT INTO productos (nombreProducto, precio, descripcion,
                                                cantidad, imagen)
                        VALUES('$nombre','$precio','$descripcion','$cantidad','$ruta_final')";

        $conexion->query($sql);
    }
    ?>
    <!-- Footer -->
    <footer class="bg-dark" id="tempaltemo_footer">
        <div class="container">
            <div class="row">

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-success border-bottom pb-3 border-light logo">Satoru no kōnā</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li>
                            <i class="fas fa-map-marker-alt fa-fw"></i>
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

</html>