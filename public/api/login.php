<?php
session_start();

// Importar la clase Database
require_once __DIR__ . "/../../db/Database.php";


// Crear la conexión PDO
$pdo = Database::connect();

$mensaje = "";

// Si ya hay sesión, redirigir al dashboard
if (isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/app/View/dashboard.php");
    exit;
}

// Procesar formulario de login
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["email"] ?? ""); 
    $password = $_POST["password"] ?? "";

    // Validaciones básicas
    if ($correo === "" || $password === "") {
        $mensaje = "<p style='color: orange;'>Completa correo y contraseña.</p>";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = "<p style='color: orange;'>Correo no válido.</p>";
    } else {
        // Buscar usuario por correo
        $sql = "
            SELECT 
                d.idDocente,
                d.nombre,
                d.apellidoPaterno,
                d.apellidoMaterno,
                d.correo,
                d.password_hash,
                d.idCarrera
            FROM Docente d
            WHERE d.correo = ?
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {

            // Regenerar ID de sesión (seguridad)
            session_regenerate_id(true);

            // Guardar información en la sesión
            $_SESSION["idDocente"] = (int)$user["idDocente"];
            $_SESSION["nombre"] = $user["nombre"];
            $_SESSION["apellidoPaterno"] = $user["apellidoPaterno"];
            $_SESSION["apellidoMaterno"] = $user["apellidoMaterno"];
            $_SESSION["correo"] = $user["correo"];
            $_SESSION["idCarrera"] = (int)$user["idCarrera"];

            // Registrar sesión exitosa si existe la función
            if (function_exists('registrarSesion')) {
                registrarSesion($pdo, (int)$user["idDocente"], true);
            }

            header("Location: /sys_Taller_Computo/app/View/dashboard.php");
            exit;

        } else {
            $mensaje = "<p style='color: red; padding: 2px; background-color: #f3aaab; border-radius:10px; border: 1px solid red; text-align:center;'>Correo o contraseña incorrectos.</p>";

            if (function_exists('registrarSesion')) {
                registrarSesion($pdo, $user ? (int)$user["idDocente"] : 0, false);
            }
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
            <input type="email" name="email" placeholder="Correo" class="input-field">
            <input type="password" name="password" placeholder="Contraseña" class="input-field">
            <p>Olvidaste tu contraseña?
                <span style="color:#2987FF;">
                    <a href="/sys_Taller_Computo/public/recuperar_password.php">Reestablecer</a>
                </span>
            </p>
            <button type="submit" class="btn-submit">Entrar</button>
        </div>

        <div class="right-panel">
            <h2 class="title" id="whiteText">No tienes cuenta?</h2>
            <p id="white-text">Regístrate para acceder a los beneficios.</p>
            <button type="button" class="btn-register">Crear Cuenta</button>
        </div>

    </form>
</div>



</body>
</html>
