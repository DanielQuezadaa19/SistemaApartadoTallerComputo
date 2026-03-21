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

    if ($rol == 3) {
        header("Location: /sys_Taller_Computo/app/View/tecnico/dashTecnico.php");
        exit;
    }

    if ($rol == 4) {
        header("Location: /sys_Taller_Computo/app/View/alumno/dashAlumno.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($correo === "" || $password === "") {

        $mensaje = "<p class='text-orange-500 text-sm text-center'>Completa correo y contraseña.</p>";

    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

        $mensaje = "<p class='text-orange-500 text-sm text-center'>Correo no válido.</p>";

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

            if ($_SESSION["rol"] == 3) {
                header("Location: /sys_Taller_Computo/app/View/tecnico/dashTecnico.php");
                exit;
            }

            if ($_SESSION["rol"] == 4) {
                header("Location: /sys_Taller_Computo/app/View/alumno/dashAlumno.php");
            }

        } else {

            $mensaje = "<p class='text-red-600 bg-red-100 p-2 rounded text-center text-sm'>Correo o contraseña incorrectos.</p>";
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

<script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="min-h-screen flex items-center justify-center bg-gray-100 px-4">

<div class="w-full max-w-4xl bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col md:flex-row">

    <form class="w-full md:w-1/2 p-6 md:p-10 flex flex-col justify-center" action="" method="POST" id="form-container">

        <h2 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2 text-center md:text-left">Iniciar Sesión</h2>

        <p class="text-gray-500 mb-6 text-center md:text-left">Inserta tus credenciales para ingresar al sistema.</p>

        <?php if ($mensaje !== "") echo $mensaje; ?>

        <input type="email" name="email" placeholder="Correo" class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <input type="password" name="password" placeholder="Contraseña" class="w-full mb-4 p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>

        <p class="text-sm text-center md:text-left mb-4">
            ¿Olvidaste tu contraseña?
            <span class="text-blue-600">
                Contacta a tu Administrador
            </span>
        </p>

        <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition">
            Entrar
        </button>

    </form>

    <div class="w-full md:w-1/2 bg-blue-600 text-white p-6 md:p-10 flex flex-col justify-center items-center text-center">
        <h2 class="text-2xl md:text-3xl font-bold mb-4">¿No estás registrado?</h2>
        <p class="text-sm md:text-base">
            Contacta a tu institucion o administrador para obtener permisos.
        </p>
    </div>

</div>

</body>
</html>