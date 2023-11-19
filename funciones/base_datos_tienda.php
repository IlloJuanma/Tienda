<?php
    $_servidor ='localhost'; // Esto es para almacenar la ip
    $_usuario = 'root';
    $_contrasena = 'medac';
    $_base_de_datos = 'db_tienda';

    $conexion = new Mysqli($_servidor, $_usuario, $_contrasena, $_base_de_datos)
        or die("Error de conexión");
?>