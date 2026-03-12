<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();
$mensaje = "";


if (isset($_SESSION["idDocente"])) {

    $rol = $_SESSION["rol"];

    if ($rol == 1) {
        header("Location: /sys_Taller_Computo/app/View/admin/dashAdmin.php");
        exit;
    }

    if ($rol == 2) {
        header("Location: /sys_Taller_Computo/app/View/docente/dashboard.php");
        exit;
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($correo === "" || $password === "") {

        $mensaje = "<p class='text-orange-500'>Completa correo y contraseña.</p>";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "<p class='text-orange-500'>Correo no válido.</p>";

    } else {

        $sql = "SELECT * FROM usuarios WHERE correo = ? LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {

            session_regenerate_id(true);

            $_SESSION["idDocente"] = $user["idDocente"];
            $_SESSION["nombre"] = $user["nombre"];
            $_SESSION["apellidoPaterno"] = $user["apellidoPaterno"];
            $_SESSION["apellidoMaterno"] = $user["apellidoMaterno"];
            $_SESSION["correo"] = $user["correo"];
            $_SESSION["rol"] = $user["rol"];
            $_SESSION["idCarrera"] = $user["idCarrera"];

            if ($_SESSION["rol"] == 1) {
                header("Location: /sys_Taller_Computo/app/View/admin/dashAdmin.php");
                exit;
            }

            if ($_SESSION["rol"] == 2) {
                header("Location: /sys_Taller_Computo/app/View/docente/dashboard.php");
                exit;
            }

        } else {

            $mensaje = "<p class='text-red-600 bg-red-500 p-2 rounded text-center'>Correo o contraseña incorrectos.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login</title>

<link rel="stylesheet" href="/sys_Taller_Computo/public/css/login-output.css">

</head>

<body>

        <div class="login-container">

        <form class="login-form" action="" method="POST" id="form-container">

        <div class="left-panel" id="left-panel">

            <h2 class="title" id="blackText">Iniciar Sesión</h2>

            <p>Inserta tus credenciales para ingresar al sistema.</p>

        <?php if ($mensaje !== "") echo $mensaje; ?>

        <input type="email" name="email" placeholder="Correo" class="input-field" required>

        <input type="password" name="password" placeholder="Contraseña" class="input-field" required>

        <p>
            ¿Olvidaste tu contraseña?
        <span style="color:#2987FF;">
                <a href="/sys_Taller_Computo/public/recuperar_password.php">Restablecer</a>
        </span>
        </p>

            <button type="submit" class="btn-submit">Entrar</button>

        </div>

        <div class="right-panel">

            <h2 class="title" id="whiteText">¿No estás registrado?</h2>

        <p id="white-text">
                Contacta a tu institucion o administrador para obtener permisos.
        </p>

   

        </div>

        </form>

        </div>

        </body>
</html>