<?php
session_start();
require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"]) || $_SESSION["rol"] != 3) {
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
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">

<header class="bg-white shadow-lg w-full rounded-2xl mb-6 p-4 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <h1 class="text-blue-600 text-3xl font-bold border-b md:border-none pb-2 md:pb-0">
        Gestión de computadoras
    </h1>

    <div class="flex flex-col md:flex-row gap-2 md:gap-4 items-center w-full md:w-auto">
        <p class="text-xl font-semibold">
            Total de computadoras: <?= $totCompus["totComputadoras"] ?>
        </p>

        <form method="get" class="flex gap-2 items-center flex-wrap">
            <label for="estado" class="font-semibold">Filtrar por estado:</label>
            <select name="estado" id="estado" class="border rounded p-1">
                <option value="">Todos</option>
                <option value="Disponible" <?= $filtroEstado === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                <option value="En mantenimiento" <?= $filtroEstado === 'En mantenimiento' ? 'selected' : '' ?>>En mantenimiento</option>
                <option value="Fuera de servicio" <?= $filtroEstado === 'Fuera de servicio' ? 'selected' : '' ?>>Fuera de servicio</option>
            </select>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-semibold rounded-lg shadow px-4 py-2">
                Filtrar
            </button>
        </form>

    </div>
</header>

<main class="w-full max-w-6xl">

<section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Lista de computadoras</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-center">ID</th>
            <th class="px-4 py-3 text-center">Código computadora</th>
            <th class="px-4 py-3 text-center">Estado</th>
            <th class="px-4 py-3 text-center">ID Taller</th>
            <th class="px-4 py-3 text-center">Nombre taller</th>
            <th class="px-4 py-3 text-center">Editar</th>
            <th class="px-4 py-3 text-center">Eliminar</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($computadoras as $c): ?>
          <tr class="hover:bg-gray-50">
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
                <a href="../admin/editarComputadora.php?idComputadora=<?= $c['idComputadora'] ?>" class="bg-yellow-400 hover:bg-yellow-500 p-2 font-semibold text-white rounded-lg shadow">
                    Editar
                </a>
            </td>
           
            <td class="px-4 py-3 text-center">
                <a href="/sys_Taller_Computo/public/api/eliminarComputadora.php?idComputadora=<?= $c['idComputadora'] ?>" class="bg-red-600 hover:bg-red-700 p-2 font-semibold text-white rounded-lg shadow">
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