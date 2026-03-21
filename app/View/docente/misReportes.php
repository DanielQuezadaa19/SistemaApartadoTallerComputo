<?php 
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$idDocente = $_SESSION["idDocente"];

$queryReservas = $pdo->prepare("
    SELECT COUNT(*) AS total 
    FROM reportes 
    WHERE idDocenteReporto = ?
");
$queryReservas->execute([$idDocente]);
$reservas = $queryReservas->fetch(PDO::FETCH_ASSOC);

$sql = "
SELECT 
    tr.idReporte,
    tr.tipoReporte,
    tr.descripcionReporte,
    tr.EstadoReporte,
    tr.fechaReporte,

    CONCAT(u.nombre,' ',u.apellidoPaterno,' ',u.apellidoMaterno) AS docente,

    IFNULL(
        CONCAT(t.nombre,' ',t.apellidoPaterno,' ',t.apellidoMaterno),
        'Sin asignar'
    ) AS tecnico,

    c.codigoComputadora

FROM reportes tr

INNER JOIN usuarios u
    ON u.idDocente = tr.idDocenteReporto

LEFT JOIN usuarios t
    ON t.idDocente = tr.idTecnicoAtendio

INNER JOIN computadora c
    ON c.idComputadora = tr.idComputadoraReporte

WHERE tr.idDocenteReporto = ?
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
<title>Reportes</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200">

<header class="bg-white shadow-lg">
    <h1 class="text-blue-600 border-b p-4 sm:p-5 text-xl sm:text-2xl md:text-3xl font-bold text-center sm:text-left">
        Reportes de <?= htmlspecialchars($_SESSION["nombre"]) ?>
    </h1>
</header>

<main class="p-4 sm:p-6">

    <div class="m-3 sm:m-5">
        <p class="text-lg sm:text-xl font-semibold text-center sm:text-left">
            Total de reportes: <?= $reservas["total"] ?>
        </p>
    </div>

    <section class="bg-white p-4 sm:p-6 rounded-2xl shadow">

        <h2 class="text-xl sm:text-2xl font-bold mb-4 text-center sm:text-left">
            Mis reportes
        </h2>

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">

                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">ID</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Tipo</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Computadora</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Descripción</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Técnico</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Fecha</th>
                        <th class="px-3 sm:px-4 py-2 sm:py-3">Estado</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                <?php foreach ($usuarioReservas as $u): ?>

                <tr class="text-center">

                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                        <?= $u["idReporte"] ?>
                    </td>

                    <td class="px-3 sm:px-4 py-2 sm:py-3 break-words">
                        <?= $u["tipoReporte"] ?>
                    </td>

                    <td class="px-3 sm:px-4 py-2 sm:py-3 break-words">
                        <?= $u["codigoComputadora"] ?>
                    </td>

                    <td class="px-3 sm:px-4 py-2 sm:py-3 break-words">
                        <?= $u["descripcionReporte"] ?>
                    </td>

                    <td class="px-3 sm:px-4 py-2 sm:py-3 break-words">
                        <?= $u["tecnico"] ?>
                    </td>

                    <td class="px-3 sm:px-4 py-2 sm:py-3">
                        <?= date("d/m/Y", strtotime($u["fechaReporte"])) ?>
                    </td>

                    <td class="px-3 sm:px-4 py-2 sm:py-3">

                    <?php if ($u["EstadoReporte"] === "Pendiente"): ?>

                    <span class="bg-yellow-100 text-yellow-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">
                    Pendiente
                    </span>

                    <?php elseif ($u["EstadoReporte"] === "En proceso"): ?>

                    <span class="bg-blue-100 text-blue-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">
                    En proceso
                    </span>

                    <?php elseif ($u["EstadoReporte"] === "Atendido"): ?>

                    <span class="bg-green-100 text-green-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">
                    Resuelto
                    </span>

                    <?php endif; ?>

                    </td>

                </tr>

                <?php endforeach; ?>

                </tbody>

            </table>
        </div>

    </section>

</main>

</body>
</html>