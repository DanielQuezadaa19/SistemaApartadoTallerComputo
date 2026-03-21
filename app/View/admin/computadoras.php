<?php
session_start();
require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"]) || $_SESSION["rol"] != 1) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$filtroEstado = $_GET['estado'] ?? '';

$sql = "
SELECT 
    c.idComputadora,
    c.codigoComputadora,
    c.estado,
    c.idTaller,
    t.nombreSala
FROM computadora c
INNER JOIN tallercomputo t ON t.idTaller = c.idTaller
";

if ($filtroEstado !== '') {
    $sql .= " WHERE c.estado = :estado ";
}

$sql .= " ORDER BY c.idTaller ASC";

$stmt = $pdo->prepare($sql);
if ($filtroEstado !== '') {
    $stmt->execute(['estado' => $filtroEstado]);
} else {
    $stmt->execute();
}

$computadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

$queryC = $pdo->prepare("SELECT COUNT(*) AS totComputadoras FROM computadora");
$queryC->execute();
$totCompus = $queryC->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computadoras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200">

<header class="bg-white shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center px-4 md:px-6 w-full gap-2">
    <h1 class="text-blue-600 border-b p-4 md:p-5 text-2xl md:text-3xl font-bold">
        Gestión de computadoras
    </h1>
</header>

<main class="p-4 md:p-6">

<div class="m-2 md:m-5 flex flex-col md:flex-row md:justify-between md:items-center gap-4">

    <p class="text-lg md:text-xl font-semibold">
        Total de computadoras: <?= $totCompus["totComputadoras"] ?>
    </p>

    <form method="get" class="flex flex-col sm:flex-row gap-2 sm:items-center w-full md:w-auto">
        <label for="estado" class="font-semibold">Filtrar por estado:</label>
        <select name="estado" id="estado" class="border rounded p-2 w-full sm:w-auto">
            <option value="">Todos</option>
            <option value="Disponible" <?= $filtroEstado === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
            <option value="En mantenimiento" <?= $filtroEstado === 'En mantenimiento' ? 'selected' : '' ?>>En mantenimiento</option>
            <option value="Fuera de servicio" <?= $filtroEstado === 'Fuera de servicio' ? 'selected' : '' ?>>Fuera de servicio</option>
        </select>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 font-semibold text-white rounded-lg shadow p-2 w-full sm:w-auto">
            Filtrar
        </button>
    </form>

    <a href="agregarComputadora.php" class="w-full md:w-auto bg-blue-500 hover:bg-blue-700 text-center font-semibold text-white rounded-lg shadow p-2">
        Nueva computadora
    </a>

</div>

<section class="bg-white p-4 md:p-6 rounded-2xl shadow mt-4">
    <h2 class="text-xl md:text-2xl font-bold mb-4">Computadoras</h2>

    <div class="overflow-x-auto max-h-[70vh]">
        <table class="min-w-full divide-y divide-gray-200 text-sm md:text-base">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Código computadora</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">ID Taller</th>
                    <th class="px-4 py-3">Nombre taller</th>
                    <th class="px-4 py-3">Editar</th>
                    <th class="px-4 py-3">Eliminar</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                <?php foreach ($computadoras as $c): ?>
                <tr>
                    <td class="px-4 py-3 text-center"><?= $c["idComputadora"] ?></td>
                    <td class="px-4 py-3 text-center"><?= $c["codigoComputadora"] ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php
                        if ($c["estado"] === "Disponible") {
                            echo '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Disponible</span>';
                        } elseif ($c["estado"] === "En mantenimiento") {
                            echo '<span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">En mantenimiento</span>';
                        } elseif ($c["estado"] === "Fuera de servicio") {
                            echo '<span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">Fuera de servicio</span>';
                        } else {
                            echo htmlspecialchars($c["estado"]);
                        }
                        ?>
                    </td>
                    <td class="px-4 py-3 text-center"><?= $c["idTaller"] ?></td>
                    <td class="px-4 py-3 text-center"><?= $c["nombreSala"] ?></td>

                    <td class="px-4 py-3 text-center">
                        <a href="editarComputadora.php?idComputadora=<?= $c['idComputadora'] ?>" class="inline-block bg-yellow-400 px-3 py-1 font-semibold text-white rounded-lg shadow text-sm md:text-base">
                            Editar
                        </a>
                    </td>

                    <td class="px-4 py-3 text-center">
                        <a href="/sys_Taller_Computo/public/api/eliminarComputadora.php?idComputadora=<?= $c['idComputadora'] ?>" class="inline-block bg-red-600 px-3 py-1 font-semibold text-white rounded-lg shadow text-sm md:text-base">
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