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
    <!-- Cargamos los requires necesarios de PHP-->
    <?php require 'funciones/depurar.php' ?>
    <?php require 'objetos/producto.php' ?>
    <?php require 'funciones/base_datos_tienda.php' ?>

</head>

<body>
    <?php
    //Iniciamos sesión
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


    //Si no eres admin NO PUEDES PASAR!!! SOY SIERVO DEL FUEGO SECRETO, ADMINISTRADOR DE LA LLAMA DE ANOR, EL FUEGO OSCURO NO TE SERVIRÁ DE NADA, LLAMA DE UDÛN!!!
    if ($_SESSION["rol"] != "admin") {
        header("Location: login.php");
    }


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $temp_nombre = depurar($_POST["nombre"]);
        $temp_precio = depurar($_POST["precio"]);
        $temp_descripcion = depurar($_POST["descripcion"]);
        $temp_cantidad = depurar($_POST["cantidad"]);

        # Imagen
        $maxSize = 1048576; //1mb de tamaño maximo
        $nombre_imagen = $_FILES["imagen"]["name"];
        $ruta_temporal = $_FILES["imagen"]["tmp_name"];

        if ($_FILES["imagen"]["size"] < $maxSize) {

            //Controlamos el tipo de la imagen
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
    <?php require_once 'nav.php'; ?>
    <!-- Cierre NAV -->

    <!-- Header -->
    <?php require_once 'header.php' ?>
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
    <!-- Sección crear productos -->
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
                // Si alguno de los datos no está correcto, lo mostramos
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
                    // Si todo es correcto, lo mostramos
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
    // En este momento estamos listo para enviar todos los datos a la base de datos con los datos correctos
    if (isset($nombre) && isset($precio) && isset($descripcion) && isset($cantidad) && isset($ruta_final)) {
        $sql = "INSERT INTO productos (nombreProducto, precio, descripcion,
                                                cantidad, imagen)
                        VALUES('$nombre','$precio','$descripcion','$cantidad','$ruta_final')";

        $conexion->query($sql);
    }
    ?>
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

</html>