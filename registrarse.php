<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <link rel="icon" type="image/x-icon" href="assets/logo-vt.svg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require 'funciones/depurar.php' ?>
    <?php require 'funciones/base_datos_tienda.php' ?>
</head>

<body class="bg-info d-flex justify-content-center align-items-center vh-100">
    <?php
    // Comprueba el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Depuramos los datos para controlar lo que el usuario introduce
        $temp_usuario = depurar($_POST["usuario"]);
        $temp_contrasena = depurar($_POST["contrasena"]);
        $temp_nacimiento = depurar($_POST["fecha_nacimiento"]);

        // He modificado esto porque verás en uno de mis muchos ensayo y error, me di cuenta que si ya tenia un usuario con el mismo nombre
        // me petaba todo y me explotaba un error de la leche en mi página y digo anda la wea no manches, habrá que controlar esto, pero como lo controlo
        // usuario y contraseña solo usuario, solo contraseña (este último es absurdo) asi me decidi por lo mas fácil, controlar si ya existe el usuario
        // que se puede cambiar po si, que lo haré, pues puede que también pero por ahora, disfruten de la pelicula...

        # Validación de usuario
        if (!strlen($temp_usuario) > 0) {
            $err_usuario = "El usuario es obligatorio";
        } else {
            $patron = "/^[A-Za-z_]{4,12}$/";
            if (!preg_match($patron, $temp_usuario)) {
                $err_usuario = "El usuario solo puede tener letras y '_', sin espacios";
            } else {
                $usuario = $temp_usuario;

                // Verificar si el usuario ya existe
                $sql_verificar_usuario = "SELECT Usuario FROM usuarios WHERE usuario = '$usuario'";
                $resultado_verificacion = $conexion->query($sql_verificar_usuario);

                if ($resultado_verificacion->num_rows > 0) {
                    $err_usuario = "Este usuario ya está siendo utilizado -- Betsu no mono o shiyō shite kudasai";
                } else {
                    // Validación de contraseña
                    $patron_contrasena = '/^(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s])\S{8,}$/'; // Patrón para contraseña: Al menos una mayus, número y un caracter especiá
                    if (!strlen($temp_contrasena) > 0) {
                        $err_contrasena = "La contraseña es obligatoria";
                    } else {
                        if (strlen($temp_contrasena) > 255) {
                            $err_contrasena = "La contraseña no puede tener más de 255 caracteres";
                        } else {
                            if (preg_match($patron_contrasena, $temp_contrasena)) {
                                $contrasena = $temp_contrasena;
                                $contrasena_cifrada = password_hash($contrasena, PASSWORD_DEFAULT);

                                // Validación fecha nacimiento
                                if (strlen($temp_nacimiento) == 0) {
                                    $err_fecha_nacimiento = "La fecha de nacimiento es obligatoria";
                                } else {
                                    $fecha_actual = date("Y-m-d");
                                    list($anyo_actual) = explode("-", $fecha_actual);
                                    list($anyo) = explode("-", $temp_nacimiento);
                                    if (($anyo_actual - $anyo > 12) && ($anyo_actual - $anyo < 120)) {
                                        $fecha_nacimiento = $temp_nacimiento;

                                        // Insertar el nuevo usuario solo si no hay errores
                                        if (empty($err_usuario) && empty($err_contrasena) && empty($err_fecha_nacimiento)) {
                                            // Tu código para insertar el usuario en la base de datos
                                        }
                                    } else {
                                        $err_fecha_nacimiento = "La fecha de nacimiento no es válida (menor de 120 años y mayor a 12 años)";
                                    }
                                }
                            } else {
                                $err_contrasena = "La contraseña debe tener al menos una mayúscula, un número y un caracter especial";
                            }
                        }
                    }
                }
            }
        }
    }
    ?>

    <div class="bg-white p-5 rounded-5 text-secondary shadow" style="width: 25rem">
        <div class="d-flex justify-content-center">
            <img src="assets/img/registrarse.gif" alt="login-icon" style="height: 7rem" />
        </div>
        <div class="text-center fs-1 fw-bold">Tōroku shite kudasai</div>
        <form action="" method="POST">
            <div class="input-group mt-4">
                <div class="input-group-text bg-info">
                    <img src="assets/img/user.gif" alt="username-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="text" placeholder="Usuario" name="usuario" />
                <?php if (isset($err_usuario))
                    echo $err_usuario ?>
                </div>
                <div class="input-group mt-1">
                    <div class="input-group-text bg-info">
                        <img src="assets/img/password.gif" alt="password-icon" style="height: 1rem" />
                    </div>
                    <input class="form-control bg-light" type="password" placeholder="Contraseña" name="contrasena" />
                <?php if (isset($err_contrasena))
                    echo $err_contrasena ?>
                </div>
                <div class="input-group mt-1">
                    <div class="input-group-text bg-info">
                        <img src="assets/img/date.gif" alt="password-icon" style="height: 1rem" />
                    </div>
                    <input class="form-control bg-light" type="date" name="fecha_nacimiento" />
                <?php if (isset($err_fecha_nacimiento))
                    echo $err_fecha_nacimiento ?>
                </div>
                <input class="btn btn-info text-white w-100 mt-4 fw-semibold shadow-sm" type="submit"
                    value="Registrar Usuario">
            </form>

            <?php
                if (isset($usuario) && isset($contrasena) && isset($fecha_nacimiento)) {
                    echo "<h2>Usuario registrado</h2>";

                    $sql1 = "INSERT INTO usuarios (usuario, contrasena, fechaNacimiento)
                                                
                        VALUES('$usuario', '$contrasena_cifrada', '$fecha_nacimiento')";

                    $sql2 = "INSERT INTO cestas (usuario, precioTotal)
                        VALUES('$usuario', '0')";

                    $conexion->query($sql1);
                    $conexion->query($sql2);
                }
                ?>
        <div class="d-flex gap-1 justify-content-center mt-1">
            <div>¿Ya tienes cuenta?</div>
            <a href="login.php" class="text-decoration-none text-info fw-semibold">Loguearse</a>
        </div>

        <div class="p-3">
            <div class="border-bottom text-center" style="height: 0.9rem">
                <span class="bg-white px-3">Entrar con</span>
            </div>
        </div>
        <div class="btn d-flex gap-2 justify-content-center border mt-3 shadow-sm">
            <img src="assets/img/google.png" alt="google-icon" style="height: 1.6rem" />
            <div class="fw-semibold text-secondary">Continuar con Google</div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>

</body>

</html>