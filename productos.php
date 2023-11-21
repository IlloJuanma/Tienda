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
    <?php require 'funciones/depurar.php' ?>

</head>

<body>
    <!-- Guardamos los datos de la sesion -->
    <?php
    session_start();
    $usuario = isset($_SESSION["usuario"]) ? $_SESSION["usuario"] : "Invitado";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["usuario"])) {
        $idProducto = $_POST["id_Producto"];
        // Verificar si la clave 'cantidad' está presente en $_POST
        $cantidadSeleccionada = isset($_POST["cantidad"]) ? $_POST["cantidad"] : 0;
        if ($cantidadSeleccionada)

            if ($_POST["accion"] == "aniadir") {
                $usuario = $_SESSION["usuario"];

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
                        // Si el producto ya está en la cesta, actualizo la cantidad
                        $filaProductoExistente = $resultadoProductoExistente->fetch_assoc();
                        $cantidadExistente = $filaProductoExistente["cantidad"];
                        $nuevaCantidad = $cantidadExistente + $cantidadSeleccionada;

                        // Obtengo la cantidad disponible del producto
                        $sqlCantidadProducto = "SELECT cantidad FROM productos WHERE idProducto = '$idProducto'";
                        $resultadoCantidadProducto = $conexion->query($sqlCantidadProducto);

                        if ($resultadoCantidadProducto->num_rows > 0) {
                            $filaCantidadProducto = $resultadoCantidadProducto->fetch_assoc();
                            $cantidadDisponible = $filaCantidadProducto["cantidad"];

                            // Verifico si hay suficiente cantidad disponible
                            if ($cantidadDisponible >= $cantidadSeleccionada && $cantidadSeleccionada > 0) {

                                // Actualizo la cantidad en la tabla productosCestas
                                $sqlActualizarCantidadExistente = "UPDATE productosCestas SET cantidad = '$nuevaCantidad' WHERE idCesta = '$idCesta' AND idProducto = '$idProducto'";
                                $conexion->query($sqlActualizarCantidadExistente);

                                // Actualizo la cantidad disponible en la tabla productos
                                $sqlActualizarCantidadProducto = "UPDATE productos SET cantidad = cantidad - '$cantidadSeleccionada' WHERE idProducto = '$idProducto'";
                                $conexion->query($sqlActualizarCantidadProducto);

                                $mensajeExito = "Producto añadido a la cesta!!";
                            } else {
                                // No hay suficiente cantidad disponible, muestra un mensaje de error
                                $mensajeError = "No hay suficiente stock por el momento Gomen'nasai!!!";
                            }
                        } else {
                            // No se pudo obtener la cantidad del producto, muestra un mensaje de error
                            $mensajeError = "Error al obtener la cantidad del producto.";
                        }
                    } else {
                        // Si el producto no está en la cesta, inserto un nuevo registro
                        $sqlInsert =
                            "INSERT INTO productosCestas (idCesta, idProducto, cantidad) VALUES ('$idCesta', '$idProducto', '$cantidadSeleccionada')";
                        $conexion->query($sqlInsert);

                        // Obtengo la cantidad disponible del producto
                        $sqlCantidadProducto = "SELECT cantidad FROM productos WHERE idProducto = '$idProducto'";
                        $resultadoCantidadProducto = $conexion->query($sqlCantidadProducto);

                        if ($resultadoCantidadProducto->num_rows > 0) {
                            $filaCantidadProducto = $resultadoCantidadProducto->fetch_assoc();
                            $cantidadDisponible = $filaCantidadProducto["cantidad"];

                            // Verifico si hay suficiente cantidad disponible
                            if ($cantidadDisponible >= $cantidadSeleccionada && $cantidadSeleccionada > 0) {

                                // Actualizo la cantidad disponible en la tabla productos
                                $sqlActualizarCantidadProducto = "UPDATE productos SET cantidad = cantidad - '$cantidadSeleccionada' WHERE idProducto = '$idProducto'";
                                $conexion->query($sqlActualizarCantidadProducto);

                                $mensajeExito = "Producto añadido a la cesta!!";
                            } else {
                                // No hay suficiente cantidad disponible, muestra un mensaje de error
                                $mensajeError = "No hay suficiente stock por el momento Gomen'nasai!!!";
                            }
                        } else {
                            // No se pudo obtener la cantidad del producto, muestra un mensaje de error
                            $mensajeError = "Error al obtener la cantidad del producto.";
                        }
                    }
                } else {
                    $mensajeError = "Producto no añadido a la cesta, Error!";
                }
            }
    }
    if (isset($_POST["accion"]) && $_POST["accion"] == "borrar") {
        $id_Producto = $_POST["id_Producto"];

        // Vamos a borrar tanto el producto como la cesta y la direccion de la      
        // imagen para que al borrar el producto, borremos la imagen de nuestro pc
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
        //ya que se relaciona con otras tablas
        $sqlBorrarCesta =
            "DELETE FROM productosCestas WHERE idProducto = $id_Producto";
        $sqlBorrarProducto =
            "DELETE FROM productos WHERE idProducto = $id_Producto";
        // ----- ESTO DEBERIA BORRARLO PORQ NO SABES LA QUE ME DIO ME DABA FALLOS AL BORRAR Y NO SABIA PORQUE Y ERA ESTO DIOS, RECORDATORIO DE MI SUFRIMIENTO EN EL TÁRTARO!!! ----
        //SI HAY LINEAS DE PEDIDO, DEBERIA DE COMPROBARLO ANTES Y LUEGO BORRAR...
        // $slqBorrarlineasPedido =
        //     "DELETE FROM lineasPedidos WHERE idProducto = $id_Producto";
        // ---------------------------------------------------------------------------------------------------------------------------------------------------------
        if (
            $conexion->query($sqlBorrarCesta) &&
            //------------------------------------------------------------------------------------------------------------------------------------------------------
            // $conexion->query($sqlBorrarlineasPedido) &&
            //------------------------------------------------------------------------------------------------------------------------------------------------------
            $conexion->query($sqlBorrarProducto) === TRUE
        ) {
            $mensajeBorrado = "Producto Borrado Correctamente!!";
        } else {
            $mensajeBorrado = "Error al borrar producto: " . $conn->error();
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

    <!-- Aqui vamos a realizar un cambio de ultima hora, gracias Zambrana por la ayuda eres un grande
         Vamos a controlar el totalPrecio en la tabla cestas, para ello...sigan viendo -->
    <?php
    // Buscamos la cesta del usuario actual OJO CUIDAO CON ESTO, que vaya comedero de cabeza pa solucionarlo la virgeeeen
    if ($usuario != "Invitado") {
        //Comprobamos si es un invitado, pues el invitado no tiene cesta
        $sql = "SELECT idCesta FROM cestas WHERE usuario ='$usuario'";
        $idCestaActual = $conexion->query($sql)->fetch_assoc()["idCesta"];
        $sql = "SELECT idProducto,cantidad FROM productosCestas WHERE idCesta = '$idCestaActual'";
        $precioTemp = $conexion->query($sql);
        //  Reiniciamos el valor a 0 para cada vez que actualicemos datos o vaciemos la cesta
        $precioCestaTotal = 0;
        // Recorremos las fila de la busqueda de arriba y mientras haya filas...
        while ($fila = $precioTemp->fetch_assoc()) {
            $idProductoCesta = $fila["idProducto"];
            $cantidadTemporal = $fila["cantidad"];
            $sql = "SELECT precio FROM productos WHERE idProducto = $idProductoCesta";
            // obtiene el valor de la columna 'precio' de la primera fila del resultado de la consulta SQL y lo asignamos a una variable
            $precioUnidad = ($conexion->query($sql))->fetch_assoc()['precio'];
            $precioCestaTotal = $precioCestaTotal + ($precioUnidad * $cantidadTemporal);
        }
        // Actualizamos precioTotal de cestas
        $sql = "UPDATE cestas SET precioTotal = $precioCestaTotal WHERE idCesta ='$idCestaActual'";
        $conexion->query($sql);
    } else {
        $mensajeInvitado = "El invitado no tiene cesta";
    }

    ?>
    <!-- Start NAV -->
    <?php require_once 'nav.php'; ?>
    <!-- Cierre NAV -->


    <!-- Header -->
    <?php require_once 'header.php' ?>
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
    <div class="container py-5k mt-3">
        <div class="row">
            <!-- Esto es el desplegable del tipo de productos, no tiene funcionalidad, es como un juguete roto -->
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
                <!-- Fin del  -->

                <?php
                // Aquí empieza la fiesta de los productos, ¡READY FOR THE PARTY!
                
                // Consulta mágica para seleccionar todos los productos, Abracadabra!
                $sql = "SELECT * FROM productos";

                // Ejecutar la consulta y guardar el resultado en la chistera, de esas guapas negras de fieltro
                $resultado = $conexion->query($sql);

                // Un array donde almacenaremos los productos, es nuestro saco mágico! EH, tu aparta la mano
                $productos = [];
                ?>

                <!-- Bienvenidos al circo de Satoru! Aquí presentamos la actuación estelar: "El desfile de los productos" -->
                <div class="container">
                    <div class="container mt-4">

                        <?php
                        //Mostrar mensaje invitado si existe
                        if(isset($mensajeInvitado)){
                            echo '<div class="alert alert-success" role="alert">' . $mensajeInvitado . '</div>';
                        }
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
                        if (isset($mensajeError)) {
                            echo '<div class="alert alert-success" role="alert">' . $mensajeError . '</div>';
                        }
                        ?>
                    </div>

                    <!-- Los artistas (productos) salen al escenario -->
                    <div class="row d-flex align-items-stretch">

                        <?php
                        // Comienza el espectáculo! Aplausos para cada producto en el escenario, Arigatō Arigatō...
                        while ($fila = $resultado->fetch_assoc()) {
                            ?>
                            <!-- Cada producto es una estrella del espectáculo -->
                            <div class="col-md-5">
                                <!-- Magia en acción! La tarjeta del producto aparece con un truco asombroso -->
                                <div class="card mb-4 product-wap rounded-0 w-auto p-3">
                                    <!-- Aparece la imagen del producto -->
                                    <div class="card rounded-0">
                                        <!-- Abracadabra! La imagen se muestra con estilo -->
                                        <img class="card-img rounded-0 img-fluid" src="<?php echo $fila["imagen"] ?>"
                                            alt="Imagen del producto"
                                            style="width: 100%; height: 500px; object-fit: cover;">
                                        <!-- El gran overlay mágico! Botones de acción aparecen con un toque de magia -->

                                        <!-- --------------------- Aquí se añade al carrito o se borra si eres admin IMPORTANTE!!!!! OJO CUIDAO --------------------------- -->
                                        <div
                                            class="card-img-overlay rounded-0 product-overlay d-flex align-items-center justify-content-center">
                                            <ul class="list-unstyled">
                                                <!-- Botón para borrar productos SOLO SI ERES ADMIN, REPITO SOLOOOO SIII EREEES AAAADMIN-->
                                                <?php
                                                if (isset($_SESSION["rol"]) && $_SESSION["rol"] == "admin") { ?>
                                                    <form action="" method="POST">
                                                        <input type="hidden" value="<?php echo $fila["idProducto"]; ?>"
                                                            name="id_Producto">
                                                        <button type="submit" class="btn btn-success text-white mt-2"
                                                            name="accion" value="borrar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php } ?>
                                                <!-- La visión de futuro! Botón para ver la imagen en grande -->
                                                <li><a class="btn btn-success text-white mt-2"
                                                        href="<?php echo $fila["imagen"] ?>" target="_blank"><i
                                                            class="far fa-eye"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Aquí está el truco final! Detalles mágicos del producto -->
                                    <div class="card-body w-auto p-3">
                                        <!-- El nombre del producto, una revelación extraordinaria! GLORIOUS! -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Nombre: </strong>
                                            <?php echo $fila["nombreProducto"] ?>
                                        </p>
                                        <br>
                                        <!-- La descripción, una maravilla literaria! -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Descripción: </strong>
                                            <?php echo $fila["descripcion"] ?>
                                        </p>
                                        <br>
                                        <!-- La cantidad disponible, un enigma cuantitativo! -->
                                        <p class="h3 text-decoration-none">
                                            <strong>Cantidad: </strong>
                                            <?php echo $fila["cantidad"] ?>
                                        </p>
                                        <!-- La valoración de estrellas, el cielo nos sonríe! -->
                                        <ul class="list-unstyled d-flex justify-content-center mb-1">
                                            <li>
                                                <?php
                                                // El gran número aleatorio de estrellas! Un espectáculo de luces.
                                                $numEstrellas = rand(1, 5);

                                                // Aplausos! Estrellas brillantes y estrellas apagadas, el público enloquece 大衆は熱狂する！!!!
                                                // esto da un numero aleatorios de estrellas (valoraciones) usando cantidad aleatorias de <i></i>
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
                                        <!-- El precio, el tesoro escondido! 
                                        Quisiera ser pirata, no por el oro ni la plata sino.... -->
                                        <p class="text-center mb-0">
                                            <strong>
                                                <?php echo $fila["precio"] ?>€
                                            </strong>
                                        </p>
                                        <!-- Selección de cantidad para añadir al carrito -->
                                        <?php
                                        if ($fila["cantidad"] > 0) { ?>
                                            <form method="POST" action="" class="mt-3">
                                                <input type="hidden" value="<?php echo $fila["idProducto"]; ?>"
                                                    name="id_Producto">
                                                <label for="cantidad">Cantidad:</label>
                                                <select name="cantidad" id="cantidad">
                                                    <?php

                                                    // Puedes ajustar el rango según tus necesidades
                                                    /**
                                                     * Escapando el '\' backSlash
                                                     * Si un string está delimitado con comillas dobles ("), 
                                                     * PHP interpretará más secuencias de escape como caracteres especiales
                                                     * Controla la cantidad de los selects, si hay muchos se coge el valor de cantidad
                                                     */
                                                    for ($i = 1; ($i <= 5) && ($i <= $fila["cantidad"]); $i++) {
                                                        echo "<option value=\"$i\">$i</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <button type="submit" class="btn btn-success text-white mt-2" name="accion"
                                                    value="aniadir">
                                                    <i class="fas fa-cart-plus"></i> Añadir al carrito
                                                </button>
                                            </form>
                                        <?php } else { ?>
                                            <label for="cantidad">Cantidad: </label>
                                            <button type="text" class="btn btn-danger text-white mt-2" name="" value="">
                                                <i class="fas fa-cart-plus"></i> AGOTADO
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <!-- Aplausos! Fin del espectáculo ---- Minasan, hontōni arigatō,-gai ni dete kudasai みなさん、本当にありがとう、外に出てください -->
            </div>
        </div>
    </div> <!-- ¡Gracias, gracias! Fin del circo de Satoru -->
    <!-- End Content -->
    <!-- Start Brands -->
    <hr>
    <section class="bg-light py-5">
        <div class="container my-4">
            <div class="row text-center py-3">
                <div class="col-lg-6 m-auto">
                    <h1 class="h1">Colaboradores</h1>
                    <p>
                        <!-- Po eso, a lo japo -->
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
                        <!--Carousel-->
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
                        <!--End Carousel-->
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
    <hr>
    <!--End Brands-->
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