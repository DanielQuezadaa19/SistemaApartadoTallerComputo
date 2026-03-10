<?php

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carreras</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">

<header class="bg-white shadow-lg flex justify-between items-center px-6 w-full">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Gestión de carreras
    </h1>

</header>

<main class="p-6">

<div class="m-5 flex justify-between">
        <p class="text-xl font-semibold ">
            Total de carreras: <?= $totCarreras["totCarreras"] ?>
        </p>

        <a href="agregarCarrera.php" class="bg-blue-500 hover:bg-blue-700 text-center font-semibold text-white rounded-lg shadow p-2">Nueva carrera</a>
    </div>

    <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Carreras</h2>
    <div class="overflow-x-auto max-h-72">
      <table class="min-w-full divide-y divide-gray-200 max-h-3 overflow-auto">
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
                <a href="editarCarrera.php?id=<?= $c['idCarrera'] ?>" class="bg-yellow-400 p-2 font-semibold text-center  rounded-lg m-2 shadow text-white" >Editar</a>
            </td>
           
            <td class="px-4 py-3 text-center">
                <a href="/sys_Taller_Computo/public/api/eliminarCarrera.php?id=<?= $c['idCarrera'] ?>" class="bg-red-600 p-2 font-semibold text-center shadow rounded-lg m-2 text-white" >Eliminar</a>
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