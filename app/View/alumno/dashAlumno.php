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

date_default_timezone_set('America/Monterrey');

$computadorasFuncionando = (int)$pdo->query("SELECT COUNT(*) FROM computadora WHERE estado = 'Disponible'")->fetchColumn();

$pdo->query("
    UPDATE registroTaller
    SET estado = 'Finalizado'
    WHERE estado = 'En proceso'
      AND fechaFin <= NOW()
");

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

$totalReservas = (int)$pdo->query("SELECT COUNT(*) FROM registroTaller")->fetchColumn();
$totalEnProceso = (int)$pdo->query("SELECT COUNT(*) FROM registroTaller WHERE estado = 'En proceso'")->fetchColumn();
$totalFinalizadas = (int)$pdo->query("SELECT COUNT(*) FROM registroTaller WHERE estado = 'Finalizado'")->fetchColumn();

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
INNER JOIN Usuarios d ON d.idDocente = rt.docenteAparto
ORDER BY rt.fechaInicio DESC
";

$usuarios = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Docente</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

<button id="btnSidebar" class="md:hidden fixed top-4 left-4 z-50 bg-blue-600 text-white p-2 rounded-lg shadow">
☰
</button>

<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40">
  <div class="p-6 border-b border-gray-200">
    <h1 class="text-2xl font-bold text-blue-600 text-center">UPG Apartados</h1>
  </div>
  <nav class="flex-1 mt-4">
    <ul class="space-y-2">
      <li>
        <a href="#" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition">
          <img src="/sys_Taller_Computo/img/pagina-de-inicio.png" class="w-6 h-6">
          <span>Inicio</span>
        </a>
      </li>

      <li>
        <a href="../talleres.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition">
          <img src="/sys_Taller_Computo/img/ordenadores.png" class="w-6 h-6">
          <span>Talleres</span>
        </a>
      </li>

      <li>
        <a href="../docente/generarReporte.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-yellow-50 rounded-lg transition">
          <img src="/sys_Taller_Computo/img/oficina.png" class="w-6 h-6">
          <span>Generar reporte</span>
        </a>
      </li>

      <li>
        <a href="../docente/misReportes.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-yellow-50 rounded-lg transition">
          <img src="/sys_Taller_Computo/img/misReportes.png" class="w-6 h-6">
          <span>Mis reportes</span>
        </a>
      </li>

      <li>
        <a href="perfilAlumno.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition">
          <img src="/sys_Taller_Computo/img/usuario.png" class="w-6 h-6">
          <span>Mi perfil</span>
        </a>
      </li>

      <li>
        <a href="/sys_Taller_Computo/public/api/logout.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-red-50 rounded-lg transition">
          <img src="/sys_Taller_Computo/img/logout.png" class="w-6 h-6">
          <span>Cerrar sesión</span>
        </a>
      </li>
    </ul>
  </nav>
</aside>

<main class="md:ml-64 p-4 sm:p-6 md:p-8">
  <header class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-blue-600 text-center sm:text-left">
      Bienvenido, <?= htmlspecialchars($_SESSION["nombre"]) ?>
    </h1>
  </header>

  <div class="flex flex-col lg:flex-row lg:space-x-6 space-y-6 lg:space-y-0 mb-6">
    
    <div class="flex-1 bg-white p-4 sm:p-6 rounded-2xl shadow hover:shadow-lg transition">
      <div class="flex items-center gap-4 mb-4">
        <img src="/sys_Taller_Computo/img/ordenadores.png" class="w-10 h-10 sm:w-12 sm:h-12">
        <h2 class="text-lg sm:text-2xl font-semibold">Salas de Cómputo</h2>
      </div>
      <div class="flex justify-between">
        <div class="text-center">
          <p class="text-2xl sm:text-4xl font-bold text-blue-600"><?= $totalTalleres ?></p>
          <span>Total</span>
        </div>
        <div class="text-center">
          <p class="text-2xl sm:text-4xl font-bold text-green-500"><?= $totalTalleresLibres ?></p>
          <span>Libres</span>
        </div>
      </div>
    </div>

    <div class="flex-1 bg-white p-4 sm:p-6 rounded-2xl shadow hover:shadow-lg transition">
      <div class="flex items-center gap-4 mb-4">
        <img src="/sys_Taller_Computo/img/computadorass.png" class="w-10 h-10 sm:w-12 sm:h-12">
        <h2 class="text-lg sm:text-2xl font-semibold">Computadoras Funcionando</h2>
      </div>
      <div class="text-center">
        <p class="text-2xl sm:text-4xl font-bold text-green-500"><?= $computadorasFuncionando ?></p>
        <span>Funcionando</span>
      </div>
    </div>

  </div>

  <section class="bg-white p-4 sm:p-6 rounded-2xl shadow">
    <h2 class="text-xl sm:text-2xl font-bold mb-4">Reservas</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 sm:px-4 py-2 sm:py-3">ID</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Sala</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Docente</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Correo</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Inicio</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Fin</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($usuarios as $u): ?>
          <tr class="text-center">
            <td class="px-3 sm:px-4 py-2 sm:py-3"><?= $u["idRegistro"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 font-medium break-words"><?= $u["sala"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["docente"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["correo"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3"><?= date("d/m/Y H:i", strtotime($u["fechaInicio"])) ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3"><?= date("d/m/Y H:i", strtotime($u["fechaFin"])) ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3">
              <?php if ($u["estado"] === "En proceso"): ?>
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs sm:text-sm">En proceso</span>
              <?php elseif ($u["estado"] === "Finalizado"): ?>
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs sm:text-sm">Finalizado</span>
              <?php elseif ($u["estado"] === "Cancelado"): ?>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs sm:text-sm">Cancelado</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<script>
const btn = document.getElementById("btnSidebar");
const sidebar = document.getElementById("sidebar");

btn.addEventListener("click", () => {
    sidebar.classList.toggle("-translate-x-full");
});
</script>

</body>
</html>