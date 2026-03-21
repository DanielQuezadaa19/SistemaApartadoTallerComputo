<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 2
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$idDocente     = $_SESSION["idDocente"];
$nombreDocente = $_SESSION["nombre"];
$apellidoPat   = $_SESSION["apellidoPaterno"];
$apellidoMat   = $_SESSION["apellidoMaterno"];
$gmailUsuario  = $_SESSION["correo"];

$idCarrera = $_SESSION["idCarrera"];
$queryCarrera = $pdo->prepare("SELECT nombreCarrera FROM carrera WHERE idCarrera = ?");
$queryCarrera->execute(["$idCarrera"]);
$carreraUsuario = $queryCarrera->fetch(PDO::FETCH_ASSOC);

$inicialesUsuario = substr($nombreDocente, 0, 1) . substr($apellidoPat, 0, 1);

$queryReservas = $pdo->prepare("
    SELECT COUNT(*) AS total 
    FROM registroTaller 
    WHERE docenteAparto = ?
");
$queryReservas->execute([$idDocente]);
$reservas = $queryReservas->fetch(PDO::FETCH_ASSOC);

$sql = "
SELECT 
    rt.idRegistro,
    tc.nombreSala AS sala,
    CONCAT(d.nombre, ' ', d.apellidoPaterno, ' ', d.apellidoMaterno) AS docente,
    d.correo,
    rt.fechaInicio,
    rt.fechaFin,
    rt.estado
FROM registroTaller rt
INNER JOIN tallerComputo tc ON tc.idTaller = rt.idTaller
INNER JOIN usuarios d ON d.idDocente = rt.docenteAparto
WHERE rt.docenteAparto = ?
ORDER BY rt.fechaInicio DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idDocente]);
$usuarioReservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

<header class="w-full h-36 bg-blue-600"></header>

<main class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16">

    <section class="bg-white rounded-xl shadow p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="rounded-full bg-blue-400 h-32 w-32 shadow-lg flex justify-center items-center flex-shrink-0">
            <p class="font-bold text-blue-800 text-5xl">
                <?= $inicialesUsuario ?>
            </p>
        </div>

        <div class="flex flex-col text-center sm:text-left">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">
                <?= $nombreDocente . ' ' . $apellidoPat . ' ' . $apellidoMat ?>
            </h1>

            <div class="flex justify-center sm:justify-start items-center gap-2 text-gray-600 mt-2 flex-wrap">
                <img src="/sys_Taller_Computo/img/tarjeta-de-identificacion.png" class="w-5 h-5">
                <span><?= $idDocente ?></span>
            </div>

            <div class="flex justify-center sm:justify-start items-center gap-2 text-gray-600 mt-2 flex-wrap">
                <img src="/sys_Taller_Computo/img/gmail.png" class="w-5 h-5">
                <span><?= $gmailUsuario ?></span>
            </div>

            <div class="flex justify-center sm:justify-start items-center gap-2 text-gray-600 mt-2 flex-wrap">
                <img src="/sys_Taller_Computo/img/sombrero-de-graduacion.png" class="w-5 h-5">
                <span><?= $carreraUsuario["nombreCarrera"] ?></span>
            </div>
        </div>
    </section>

    <section class="mt-8 flex flex-col lg:flex-row gap-6">

        <div class="flex flex-col gap-6 w-full lg:w-1/4">

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Resumen</h2>

                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-blue-600">
                        <?= $reservas["total"] ?>
                    </p>
                    <p class="text-sm text-gray-600">Reservas totales</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Acciones</h2>

                <div class="flex flex-col gap-3">
                    <a href="registrarReserva.php"
                       class="bg-blue-600 text-white py-2 rounded-lg text-center hover:bg-blue-700 transition">
                        + Nueva reserva
                    </a>

                    <a href="misReservas.php"
                       class="bg-gray-200 text-gray-800 py-2 rounded-lg text-center hover:bg-gray-300 transition">
                        Ver mis reservas
                    </a>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-6 w-full flex-1">

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-2xl font-bold mb-4">Mis reservas</h2>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="border-b text-gray-500 text-sm sm:text-base">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Sala</th>
                                <th class="px-4 py-2 text-left">Docente</th>
                                <th class="px-4 py-2 text-left">Correo</th>
                                <th class="px-4 py-2 text-left">Inicio</th>
                                <th class="px-4 py-2 text-left">Fin</th>
                                <th class="px-4 py-2 text-left w-36">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y text-sm sm:text-base">
                            <?php if (count($usuarioReservas) > 0): ?>
                                <?php foreach ($usuarioReservas as $u): ?>
                                    <tr>
                                        <td class="px-4 py-3"><?= $u["idRegistro"] ?></td>
                                        <td class="px-4 py-3 font-medium"><?= $u["sala"] ?></td>
                                        <td class="px-4 py-3"><?= $u["docente"] ?></td>
                                        <td class="px-4 py-3"><?= $u["correo"] ?></td>
                                        <td class="px-4 py-3"><?= date("d/m/Y H:i", strtotime($u["fechaInicio"])) ?></td>
                                        <td class="px-4 py-3"><?= date("d/m/Y H:i", strtotime($u["fechaFin"])) ?></td>
                                        <td class="px-4 py-3 w-36">
                                            <?php if ($u["estado"] === "En proceso"): ?>
                                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs sm:text-sm">En proceso</span>
                                            <?php elseif ($u["estado"] === "Finalizado"): ?>
                                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs sm:text-sm">Finalizado</span>
                                            <?php elseif ($u["estado"] === "Cancelado"): ?>
                                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs sm:text-sm">Cancelado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-6 text-gray-500">
                                        No tienes reservas registradas.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>

                    </table>
                </div>
            </div>

        </div>
    </section>

</main>

</body>
</html>