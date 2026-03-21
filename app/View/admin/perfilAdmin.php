<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 1
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$idDocente     = $_SESSION["idDocente"];
$nombreDocente = $_SESSION["nombre"];
$apellidoPat   = $_SESSION["apellidoPaterno"];
$apellidoMat   = $_SESSION["apellidoMaterno"];
$gmailUsuario  = $_SESSION["correo"];

$inicialesUsuario = substr($nombreDocente, 0, 1) . substr($apellidoPat, 0, 1);

$queryUsuariosTotales = $pdo->prepare("
    SELECT COUNT(*) AS totalUsuarios 
    FROM usuarios 
");
$queryUsuariosTotales->execute();
$usuariosTotales = $queryUsuariosTotales->fetch(PDO::FETCH_ASSOC);

$queryReservasTotales = $pdo->prepare("
    SELECT COUNT(*) AS reservasTotales
    FROM registrotaller
");
$queryReservasTotales->execute();
$reservasTotales = $queryReservasTotales->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="w-full h-28 md:h-36 bg-green-600"></header>

<main class="w-full md:w-3/4 mx-auto px-4 md:px-6 -mt-12 md:-mt-16">

    <section class="bg-white rounded-xl shadow p-4 md:p-6 flex flex-col md:flex-row items-center gap-4 md:gap-6">
        <div class="rounded-full bg-green-400 h-24 w-24 md:h-32 md:w-32 shadow-lg flex justify-center items-center">
            <p class="font-bold text-green-800 text-3xl md:text-5xl">
                <?= $inicialesUsuario ?>
            </p>
        </div>

        <div class="flex flex-col text-center md:text-left">
            <h1 class="text-xl md:text-3xl font-bold text-gray-800">
                <?= $nombreDocente . ' ' . $apellidoPat . ' ' . $apellidoMat ?>
            </h1>

            <div class="flex items-center justify-center md:justify-start gap-2 text-gray-600 mt-2">
                <img src="/sys_Taller_Computo/img/tarjeta-de-identificacion.png" class="w-5 h-5">
                <span><?= $idDocente ?></span>
            </div>

            <div class="flex items-center justify-center md:justify-start gap-2 text-gray-600 mt-2">
                <img src="/sys_Taller_Computo/img/gmail.png" class="w-5 h-5">
                <span class="break-all"><?= $gmailUsuario ?></span>
            </div>
        </div>
    </section>

    <section class="mt-8 flex flex-col lg:flex-row gap-6">

        <div class="flex flex-col gap-6 w-full lg:w-1/3">

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Resumen</h2>

                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-2xl md:text-3xl font-bold text-green-600">
                        <?= $usuariosTotales["totalUsuarios"] ?>
                    </p>
                    <p class="text-sm text-gray-600">Usuarios totales en sistema</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="bg-green-500 p-4 rounded-lg text-center">
                    <p class="text-2xl md:text-3xl font-bold text-green-50">
                        <?= $reservasTotales["reservasTotales"] ?>
                    </p>
                    <p class="text-sm text-gray-50">Reservas totales en sistema</p>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 w-full">

            <div class="bg-white border rounded-xl shadow hover:shadow-lg transition p-6 flex flex-col justify-between h-full">
                <div>
                    <h2 class="text-lg font-semibold text-green-700 mb-4 text-center">Usuarios</h2>
                    <ul class="text-sm text-gray-600 space-y-1 mb-6 list-disc list-inside text-left">
                        <li>Añadir</li>
                        <li>Editar</li>
                        <li>Eliminar</li>
                        <li>Visualizar</li>
                    </ul>
                </div>
                <a href="usuarios.php" class="bg-green-600 text-white py-2 rounded-lg text-center hover:bg-green-700 transition">
                    Ver
                </a>
            </div>

            <div class="bg-white border rounded-xl shadow hover:shadow-lg transition p-6 flex flex-col justify-between h-full">
                <div>
                    <h2 class="text-lg font-semibold text-yellow-700 mb-4 text-center">Carreras</h2>
                    <ul class="text-sm text-gray-600 space-y-1 mb-6 list-disc list-inside text-left">
                        <li>Añadir</li>
                        <li>Editar</li>
                        <li>Eliminar</li>
                        <li>Visualizar</li>
                    </ul>
                </div>
                <a href="carreras.php" class="bg-yellow-500 text-white py-2 rounded-lg text-center hover:bg-yellow-600 transition">
                    Ver
                </a>
            </div>

            <div class="bg-white border rounded-xl shadow hover:shadow-lg transition p-6 flex flex-col justify-between h-full">
                <div>
                    <h2 class="text-lg font-semibold text-red-700 mb-4 text-center">Talleres</h2>
                    <ul class="text-sm text-gray-600 space-y-1 mb-6 list-disc list-inside text-left">
                        <li>Añadir</li>
                        <li>Editar</li>
                        <li>Eliminar</li>
                        <li>Visualizar</li>
                    </ul>
                </div>
                <a href="talleresAdmin.php" class="bg-red-600 text-white py-2 rounded-lg text-center hover:bg-red-700 transition">
                    Ver
                </a>
            </div>

            <div class="bg-white border rounded-xl shadow hover:shadow-lg transition p-6 flex flex-col justify-between h-full">
                <div>
                    <h2 class="text-lg font-semibold text-blue-700 mb-4 text-center">Computadoras</h2>
                    <ul class="text-sm text-gray-600 space-y-1 mb-6 list-disc list-inside text-left">
                        <li>Añadir</li>
                        <li>Editar</li>
                        <li>Eliminar</li>
                        <li>Visualizar</li>
                    </ul>
                </div>
                <a href="computadoras.php" class="bg-blue-600 text-white py-2 rounded-lg text-center hover:bg-blue-700 transition">
                    Ver 
                </a>
            </div>

        </div>

    </section>

    <div class="w-full bg-green-500 rounded font-semibold p-4 mt-5 text-white shadow hover:bg-green-700 transition"></div>

</main>

</body>
</html>