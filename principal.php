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
    <?php require_once 'nav.php'; ?>
    <!-- Cierre NAV -->


    <!-- Header -->
    <?php require_once 'header.php' ?>
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