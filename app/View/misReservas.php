<?php 
require_once __DIR__ . "/../../db/Database.php";
session_start();

if(!isset($_SESSION["idDocente"])){
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$pdo = Database::connect();
$idDocente = $_SESSION["idDocente"];

$queryReservas = $pdo->prepare("SELECT COUNT(*) AS total FROM registrotaller WHERE docenteAparto = ?");
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
INNER JOIN Docente d ON d.idDocente = rt.docenteAparto
WHERE rt.docenteAparto = ?
ORDER BY rt.fechaInicio DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$idDocente]);
$usuarioReservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">

<header class="bg-white shadow-lg">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Reservas de <?= htmlspecialchars($_SESSION["nombre"]) ?>
    </h1>
</header>

<main class="p-6">
    <div class="m-5">
        <p class="text-xl font-semibold">
            Total de reservas: <?= $reservas["total"] ?>
        </p>
    </div>

   <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Mis reservas</h2>
    <div class="overflow-x-auto max-h-80">
      <table class="min-w-full divide-y divide-gray-200 overflow-auto max-h-3">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Sala</th>
            <th class="px-4 py-3">Docente</th>
            <th class="px-4 py-3">Correo</th>
            <th class="px-4 py-3">Inicio</th>
            <th class="px-4 py-3">Fin</th>
            <th class="px-4 py-3">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($usuarioReservas as $u): ?>
          <tr>
            <td class="px-4 py-3"><?= $u["idRegistro"] ?></td>
            <td class="px-4 py-3 font-medium"><?= $u["sala"] ?></td>
            <td class="px-4 py-3"><?= $u["docente"] ?></td>
            <td class="px-4 py-3"><?= $u["correo"] ?></td>
            <td class="px-4 py-3"><?= date("d/m/Y H:i", strtotime($u["fechaInicio"])) ?></td>
            <td class="px-4 py-3"><?= date("d/m/Y H:i", strtotime($u["fechaFin"])) ?></td>
            <td class="px-4 py-3">
              <?php if ($u["estado"] === "En proceso"): ?>
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">En proceso</span>
              <?php elseif ($u["estado"] === "Finalizado"): ?>
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">Finalizado</span>
              <?php elseif ($u["estado"] === "Cancelado"): ?>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm">Cancelado</span>
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
