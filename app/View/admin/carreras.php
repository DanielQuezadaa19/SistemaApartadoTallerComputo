<?php

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"]) || $_SESSION["rol"] != 1) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$sql = "
SELECT idCarrera, nombreCarrera
FROM carrera
ORDER BY idCarrera ASC
";

$carreras = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$queryCarreras = $pdo->prepare("SELECT COUNT(*) AS totCarreras FROM carrera");
$queryCarreras->execute();
$totCarreras = $queryCarreras->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carreras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200">

<header class="bg-white shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center px-4 md:px-6 w-full gap-2">
    <h1 class="text-blue-600 border-b p-4 md:p-5 text-2xl md:text-3xl font-bold">
        Gestión de carreras
    </h1>
</header>

<main class="p-4 md:p-6">

<div class="m-2 md:m-5 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <p class="text-lg md:text-xl font-semibold">
        Total de carreras: <?= $totCarreras["totCarreras"] ?>
    </p>

    <a href="agregarCarrera.php" class="w-full md:w-auto bg-blue-500 hover:bg-blue-700 text-center font-semibold text-white rounded-lg shadow p-2">
        Nueva carrera
    </a>
</div>

<section class="bg-white p-4 md:p-6 rounded-2xl shadow">
    <h2 class="text-xl md:text-2xl font-bold mb-4">Carreras</h2>

    <div class="overflow-x-auto max-h-72">
        <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Nombre de carrera</th>
                    <th class="px-4 py-3">Editar</th>
                    <th class="px-4 py-3">Eliminar</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                <?php foreach ($carreras as $c): ?>
                <tr>
                    <td class="px-4 py-3 text-center"><?= $c["idCarrera"] ?></td>
                    <td class="px-4 py-3 text-center"><?= $c["nombreCarrera"] ?></td>

                    <td class="px-4 py-3 text-center">
                        <a href="editarCarrera.php?id=<?= $c['idCarrera'] ?>" class="inline-block bg-yellow-400 px-3 py-1 font-semibold rounded-lg shadow text-white text-sm md:text-base">
                            Editar
                        </a>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="/sys_Taller_Computo/public/api/eliminarCarrera.php?id=<?= $c['idCarrera'] ?>" class="inline-block bg-red-600 px-3 py-1 font-semibold shadow rounded-lg text-white text-sm md:text-base">
                            Eliminar
                        </a>
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