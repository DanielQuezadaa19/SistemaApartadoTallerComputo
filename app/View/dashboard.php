<?php
session_start();
require_once __DIR__ . "/../../db/Database.php";

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

date_default_timezone_set('America/Monterrey');

$pdo = Database::connect();

/* Finalizar reservas vencidas */
$pdo->query("
    UPDATE registroTaller
    SET estado = 'Finalizado'
    WHERE estado = 'En proceso'
      AND fechaFin <= NOW()
");

/* Estadísticas de salas (tiempo real) */
$stats = $pdo->query("
SELECT 
    COUNT(*) AS totalTalleres,
    SUM(
        CASE 
            WHEN t.estado = 'No disponible' THEN 0
            WHEN EXISTS (
                SELECT 1
                FROM registroTaller r
                WHERE r.idTaller = t.idTaller
                  AND r.estado = 'En proceso'
                  AND NOW() BETWEEN r.fechaInicio AND r.fechaFin
            ) THEN 0
            ELSE 1
        END
    ) AS libres
FROM tallerComputo t
")->fetch(PDO::FETCH_ASSOC);

$totalTalleres = (int)$stats['totalTalleres'];
$totalTalleresLibres = (int)$stats['libres'];

/* Totales de reservas */
$totalReservas = (int)$pdo->query("SELECT COUNT(*) FROM registroTaller")->fetchColumn();
$totalEnProceso = (int)$pdo->query("SELECT COUNT(*) FROM registroTaller WHERE estado = 'En proceso'")->fetchColumn();
$totalFinalizadas = (int)$pdo->query("SELECT COUNT(*) FROM registroTaller WHERE estado = 'Finalizado'")->fetchColumn();

/* Listado de reservas */
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
ORDER BY rt.fechaInicio DESC
";

$usuarios = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">


<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col">
  <div class="p-6 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-blue-600 text-center">UPG Apartados</h1>
  </div>
  <nav class="flex-1 mt-4">
    <ul class="space-y-2">
      <li><a href="#" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition"><img src="/sys_Taller_Computo/img/pagina-de-inicio.png" class="w-6 h-6"><span>Inicio</span></a></li>
      <li><a href="misReservas.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition"><img src="/sys_Taller_Computo/img/cita.png" class="w-6 h-6"><span>Mis Reservas</span></a></li>
      <li><a href="talleres.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition"><img src="/sys_Taller_Computo/img/ordenadores.png" class="w-6 h-6"><span>Talleres</span></a></li>
      <li><a href="#" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition"><img src="/sys_Taller_Computo/img/usuario.png" class="w-6 h-6"><span>Mi perfil</span></a></li>
      <li><a href="/sys_Taller_Computo/public/api/logout.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-red-50 rounded-lg transition"><img src="/sys_Taller_Computo/img/logout.png" class="w-6 h-6"><span>Cerrar sesión</span></a></li>
    </ul>
  </nav>
</aside>

  <main class="ml-64 p-8">
  <header class="flex justify-between items-center mb-8">
    <h1 class="text-4xl font-bold text-blue-600">Bienvenido, <?= htmlspecialchars($_SESSION["nombre"]) ?></h1>
  </header>


  <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0 mb-6">
    
    <div class="flex-1 bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
      <div class="flex items-center gap-4 mb-4">
        <img src="/sys_Taller_Computo/img/ordenadores.png" class="w-12 h-12">
        <h2 class="text-2xl font-semibold">Salas de Cómputo</h2>
      </div>
      <div class="flex justify-between">
        <div class="text-center">
          <p class="text-4xl font-bold text-blue-600"><?= $totalTalleres ?></p>
          <span>Total</span>
        </div>
        <div class="text-center">
          <p class="text-4xl font-bold text-green-500"><?= $totalTalleresLibres ?></p>
          <span>Libres</span>
        </div>
      </div>
    </div>

    <div class="flex-1 bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
      <div class="flex items-center gap-4 mb-4">
        <img src="/sys_Taller_Computo/img/ordenador-personal.png" class="w-12 h-12">
        <h2 class="text-2xl font-semibold">Reservas</h2>
      </div>
      <div class="flex justify-between">
        <div class="text-center">
          <p class="text-4xl font-bold text-yellow-500"><?= $totalEnProceso ?></p>
          <span>En proceso</span>
        </div>
        <div class="text-center">
          <p class="text-4xl font-bold text-blue-500"><?= $totalFinalizadas ?></p>
          <span>Finalizadas</span>
        </div>
      </div>
    </div>

    <div class="flex-1 bg-white p-6 rounded-2xl shadow hover:shadow-lg transition flex flex-col justify-between">
      <div class="flex items-center gap-4 mb-4">
        <img src="/sys_Taller_Computo/img/mas.png" class="w-12 h-12">
        <h2 class="text-2xl font-semibold">Nueva Reserva</h2>
      </div>
      <a href="registrarReserva.php" class="mt-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-center transition">Nueva reserva</a>
    </div>

  </div>


  <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Reservas</h2>
    <div class="overflow-x-auto max-h-72">
      <table class="min-w-full divide-y divide-gray-200 max-h-3 overflow-auto">
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
          <?php foreach ($usuarios as $u): ?>
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
