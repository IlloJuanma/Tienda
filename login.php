<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
    <link rel="icon" type="image/x-icon" href="assets/logo-vt.svg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <?php require 'funciones/base_datos_tienda.php' ?>
    <?php require 'funciones/depurar.php' ?>
</head>

<body class="bg-info d-flex justify-content-center align-items-center vh-100">
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usuario = depurar($_POST["usuario"]);
        $contrasena = depurar($_POST["contrasena"]);

        $sql = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows === 0) {
            echo '<div class="alert alert-danger text-center" role="alert">
            <strong>Error:</strong> Usuario no encontrado
                  </div>';


        } else {
            while ($fila = $resultado->fetch_assoc()) {
                $contrasena_cifrada = $fila["contrasena"];
                $rol_temp = $fila["rol"];
            }

            $acceso_valido = password_verify($contrasena, $contrasena_cifrada);


            if ($acceso_valido) {
                echo "<h2>Usuario encontrado</h2>";
                session_start();
                $_SESSION["usuario"] = $usuario;
                $_SESSION["rol"] = $rol_temp;
                header("Location: principal.php");
            } else {
                echo "<h2>Contraseña incorrecta</h2>";
            }
        }
    }
    ?>

    <div class="bg-white p-5 rounded-5 text-secondary shadow" style="width: 25rem">
        <div class="d-flex justify-content-center">
            <img src="assets/img/login.gif" alt="login-icon" style="height: 7rem" />
        </div>
        <div class="text-center fs-1 fw-bold">Irashaimase</div>
        <form action="" method="POST">
            <div class="input-group mt-4">
                <div class="input-group-text bg-info">
                    <img src="assets/img/user.gif" alt="username-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="text" placeholder="Usuario" name="usuario" />
            </div>
            <div class="input-group mt-1">
                <div class="input-group-text bg-info">
                    <img src="assets/img/password.gif" alt="password-icon" style="height: 1rem" />
                </div>
                <input class="form-control bg-light" type="password" placeholder="Contraseña" name="contrasena" />
            </div>
            <div class="d-flex justify-content-around mt-1">
                <div class="d-flex align-items-center gap-1">
                    <input class="form-check-input bg-primary" type="checkbox" />
                    <div class="pt-1" style="font-size: 0.9rem">Recordar</div>
                </div>
                <div class="pt-1">
                    <a href="https://www.youtube.com/watch?v=ERSsOAR4w6c" target="_blank"
                        class="text-decoration-none text-info fw-semibold fst-italic" style="font-size: 0.9rem">¿Olvidó
                        la
                        contraseña?</a>
                </div>
            </div>
            <input class="btn btn-info text-white w-100 mt-4 fw-semibold shadow-sm" type="submit"
                value="Iniciar Sesión">
        </form>

        <div class="d-flex gap-1 justify-content-center mt-1">
            <div>¿No tienes cuenta?</div>
            <a href="registrarse.php" class="text-decoration-none text-info fw-semibold">Registrar</a>
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
        <!-- Enlace "Continuar como invitado" -->
        <a href="principal.php" class="btn d-flex gap-2 justify-content-center border mt-3 shadow-sm">
            <div class="d-flex align-items-center gap-2">
                <img src="assets/img/invitado.gif" alt="guest-icon" style="height: 1.6rem" />
                <div class="fw-semibold text-secondary">Continuar como invitado</div>
            </div>
        </a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
</body>

</html>