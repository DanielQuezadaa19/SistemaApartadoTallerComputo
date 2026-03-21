<?php

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 4
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$idDocente     = $_SESSION["idDocente"];
$nombreDocente = $_SESSION["nombre"];
$apellidoPat   = $_SESSION["apellidoPaterno"];
$apellidoMat   = $_SESSION["apellidoMaterno"];
$gmailUsuario  = $_SESSION["correo"];

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
ORDER BY rt.fechaInicio DESC
";

$queryReportes = $pdo->prepare($sql);
$queryReportes->execute();
$usuarioReservas = $queryReportes->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM reportes 
    WHERE idDocenteReporto = ?
");

$stmt->execute([$idDocente]);

$totalReportes = (int)$stmt->fetchColumn();

$inicialesUsuario = substr($nombreDocente, 0, 1) . substr($apellidoPat, 0, 1);
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

<header class="w-full h-28 sm:h-36 bg-orange-500"></header>

<main class="w-full max-w-6xl mx-auto px-4 sm:px-6 -mt-14 sm:-mt-16">

    <section class="bg-white rounded-xl shadow p-4 sm:p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="rounded-full bg-orange-500 h-24 w-24 sm:h-32 sm:w-32 flex justify-center items-center">
            <p class="font-bold text-orange-200 text-3xl sm:text-5xl">
                <?= $inicialesUsuario ?>
            </p>
        </div>

        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-3xl font-bold text-gray-800 break-words">
                <?= $nombreDocente . ' ' . $apellidoPat . ' ' . $apellidoMat ?>
            </h1>

            <div class="flex justify-center sm:justify-start items-center gap-2 text-gray-600 mt-2">
                <img src="/sys_Taller_Computo/img/tarjeta-de-identificacion.png" class="w-5 h-5">
                <span><?= $idDocente ?></span>
            </div>

            <div class="flex justify-center sm:justify-start items-center gap-2 text-gray-600 mt-2 break-all">
                <img src="/sys_Taller_Computo/img/gmail.png" class="w-5 h-5">
                <span><?= $gmailUsuario ?></span>
            </div>
        </div>
    </section>

   <section class="mt-8 flex flex-col lg:flex-row gap-6">

    <div class="w-full lg:w-1/3 flex flex-col gap-6">

        <div class="bg-white p-4 sm:p-6 rounded-xl shadow">
            <h2 class="font-semibold mb-4 text-center sm:text-left">Total reportes propios</h2>
            <div class="bg-orange-50 p-4 rounded text-center">
                <p class="text-2xl sm:text-3xl font-bold text-orange-500">
                    <?= $totalReportes ?>
                </p>
            </div>
        </div>

         <div class="bg-white rounded-xl shadow p-4 sm:p-6">
                <h2 class="text-lg font-semibold mb-4 text-center sm:text-left">Acciones</h2>

                <div class="flex flex-col gap-3">
                    <a href="../docente/generarReporte.php"
                       class="w-full bg-orange-500 text-white py-2 rounded-lg text-center hover:bg-orange-600 transition">
                        + Nuevo reporte
                    </a>

                    <a href="../talleres.php"
                       class="w-full bg-gray-200 text-gray-800 py-2 rounded-lg text-center hover:bg-gray-300 transition">
                        Ver talleres 
                    </a>
                </div>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-xl shadow p-4 sm:p-6 w-full">
        <h2 class="text-xl sm:text-2xl font-bold mb-4 text-center sm:text-left">Reservas de docentes</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
                <thead class="border-b text-gray-500">
                    <tr>
                        <th class="px-3 sm:px-4 py-2 text-left">ID</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Sala</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Docente</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Correo</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Inicio</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Fin</th>
                        <th class="px-3 sm:px-4 py-2 text-left">Estado</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <?php if (count($usuarioReservas) > 0): ?>
                        <?php foreach ($usuarioReservas as $u): ?>
                            <tr>
                                <td class="px-3 sm:px-4 py-3"><?= $u["idRegistro"] ?></td>
                                <td class="px-3 sm:px-4 py-3 font-medium break-words"><?= $u["sala"] ?></td>
                                <td class="px-3 sm:px-4 py-3 break-words"><?= $u["docente"] ?></td>
                                <td class="px-3 sm:px-4 py-3 break-all"><?= $u["correo"] ?></td>
                                <td class="px-3 sm:px-4 py-3"><?= date("d/m/Y H:i", strtotime($u["fechaInicio"])) ?></td>
                                <td class="px-3 sm:px-4 py-3"><?= date("d/m/Y H:i", strtotime($u["fechaFin"])) ?></td>
                                <td class="px-3 sm:px-4 py-3">
                                    <?php if ($u["estado"] === "En proceso"): ?>
                                        <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs">En proceso</span>
                                    <?php elseif ($u["estado"] === "Finalizado"): ?>
                                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs">Finalizado</span>
                                    <?php elseif ($u["estado"] === "Cancelado"): ?>
                                        <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs">Cancelado</span>
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

</section>

</main>

</body>
</html>