<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}


$sql = "
SELECT 
	c.idComputadora,
    c.codigoComputadora,
    c.estado,
    c.idTaller,
    t.nombreSala
FROM computadora c
INNER JOIN tallercomputo t ON t.idTaller = c.idTaller
ORDER BY c.idTaller ASC;

";

$computadoras = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);



$queryC = $pdo->prepare("SELECT COUNT(*) AS totComputadoras FROM computadora");
$queryC->execute();
$totCompus = $queryC->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Computadoras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">

<header class="bg-white shadow-lg flex justify-between items-center px-6 w-full">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Gestión de computadoras
    </h1>

</header>
    

<main class="p-6">

<div class="m-5 flex justify-between">
        <p class="text-xl font-semibold ">
            Total de computadoras: <?= $totCompus["totComputadoras"] ?>
        </p>

        <a href="agregarComputadora.php" class="bg-blue-500 hover:bg-blue-700 text-center font-semibold text-white rounded-lg shadow p-2">Nueva computadora</a>
    </div>

    <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Computadoras</h2>
    <div class="overflow-x-auto max-h-3/4">
      <table class="min-w-full divide-y divide-gray-200 max-h-3 overflow-auto">
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
            <td class="px-4 py-3 text-center"><?= $c["estado"] ?></td>
            <td class="px-4 py-3 text-center"><?= $c["idTaller"] ?></td>
            <td class="px-4 py-3 text-center"><?= $c["nombreSala"] ?></td>

            <td class="px-4 py-3 text-center">
                <a href="editarComputadora.php?idComputadora=<?= $c['idComputadora'] ?>" class="bg-yellow-400 p-2 font-semibold text-center rounded-lg m-2 shadow text-white">Editar</a>
            </td>
           
            <td class="px-4 py-3 text-center">
                <a href="/sys_Taller_Computo/public/api/eliminarComputadora.php?idComputadora=<?= $c['idComputadora'] ?>" class="bg-red-600 p-2 font-semibold text-center shadow rounded-lg m-2 text-white" >Eliminar</a>
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