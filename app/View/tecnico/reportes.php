<?php

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 3
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$sql = "
SELECT 
    r.idReporte,
    r.descripcionReporte,
    r.tipoReporte,
    r.prioridad,
    u.nombre,
    u.apellidoPaterno,
    u.apellidoMaterno,
    r.estadoReporte,
    r.fechaReporte,
    r.fechaAtencion,

    tec.nombre AS nombreTecnico,
    tec.apellidoPaterno AS apPaternoTecnico,
    tec.apellidoMaterno AS apMaternoTecnico,

    r.observacionesTecnico,
    c.codigoComputadora,
    t.nombreSala
FROM reportes r
INNER JOIN computadora c ON c.idComputadora = r.idComputadoraReporte
INNER JOIN tallercomputo t ON t.idTaller = c.idTaller
INNER JOIN usuarios u ON u.idDocente = r.idDocenteReporto
LEFT JOIN usuarios tec 
    ON tec.idDocente = r.idTecnicoAtendio;
";

$reportes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


$queryReportes = $pdo->prepare("SELECT COUNT(*) AS totReportes FROM reportes");
$queryReportes->execute();
$totReportes = $queryReportes->fetch(PDO::FETCH_ASSOC);
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

<header class="bg-white shadow-lg flex justify-between items-center px-6 w-full">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Gestión de Reportes
    </h1>
</header>

<main class="p-6">

<div class="m-5 flex justify-between">
    <p class="text-xl font-semibold ">
        Total de reportes: <?= $totReportes["totReportes"] ?>
    </p>
</div>

<section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Lista de reportes</h2>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50 text-sm">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Descripción</th>
            <th class="px-4 py-3">Tipo</th>
            <th class="px-4 py-3">Prioridad</th>
            <th class="px-4 py-3">Docente/Alumno</th>
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3">Fecha Reporte</th>
           
            <th class="px-4 py-3">Fecha Atención</th>
             <th class="px-4 py-3">Técnico</th>
            <th class="px-4 py-3">Observaciones</th>
            <th class="px-4 py-3">Computadora</th>
            <th class="px-4 py-3">Sala</th>
          </tr>
        </thead>

        <tbody class="divide-y text-sm">
          <?php foreach ($reportes as $r): ?>
          <tr>
            <td class="px-4 py-3 text-center"><?= $r["idReporte"] ?></td>
            <td class="px-4 py-3"><?= $r["descripcionReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["tipoReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["prioridad"] ?></td>
            <td class="px-4 py-3 text-center">
                <?= $r["nombre"] . " " . $r["apellidoPaterno"] . " " . $r["apellidoMaterno"] ?>
            </td>
            <td class="px-4 py-3 text-center"><?= $r["estadoReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["fechaReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["fechaAtencion"] ?? 'Pendiente' ?></td>
            <td class="px-4 py-3 text-center">
                <?php if ($r["nombreTecnico"]): ?>
                    <?= $r["nombreTecnico"] . " " . $r["apPaternoTecnico"] ?>
                <?php else: ?>
                    <span>Sin asignar</span>
                <?php endif; ?>
            </td>
            <td class="px-4 py-3"><?= $r["observacionesTecnico"] ?? 'Sin observaciones' ?></td>
            <td class="px-4 py-3 text-center"><?= $r["codigoComputadora"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["nombreSala"] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
</section>

</main>

</body>
</html>