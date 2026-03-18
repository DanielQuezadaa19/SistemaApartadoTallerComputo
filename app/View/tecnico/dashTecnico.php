<?php

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

$idTecnico = $_SESSION["idDocente"];
if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 3
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

date_default_timezone_set('America/Monterrey');

$totalComputadoras = (int)$pdo->query("SELECT COUNT(*) FROM computadora")->fetchColumn();
$computadorasFuncionando = (int)$pdo->query("SELECT COUNT(*) FROM computadora WHERE estado = 'Disponible'")->fetchColumn();
$computadorasEnMantenimiento = (int)$pdo->query("SELECT COUNT(*) FROM computadora WHERE estado = 'En mantenimiento'")->fetchColumn();
$computadorasFueraDeServicio = (int)$pdo->query("SELECT COUNT(*) FROM computadora WHERE estado = 'Fuera de servicio'")->fetchColumn();



$totalReportes = (int)$pdo->query("SELECT COUNT(*) FROM reportes WHERE estadoReporte = 'En proceso'")->fetchColumn();



$stmt = $pdo->prepare("SELECT COUNT(*) FROM reportes WHERE idTecnicoAtendio = ?");
$stmt->execute([$idTecnico]);

$totalReportesPropios = (int)$stmt->fetchColumn();




$sql = "
SELECT 
	r.idReporte,
    r.descripcionReporte,
    r.tipoReporte,
    r.prioridad,
    r.estadoReporte,
    r.fechaReporte,
    c.codigoComputadora,
    t.nombreSala
    FROM reportes r
    INNER JOIN computadora c ON c.idComputadora = r.idComputadoraReporte
    INNER JOIN tallercomputo t ON t.idTaller = c.idTaller;
";

$reportes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - Técnico</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">


<aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg flex flex-col">
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
    <a href="../../View/docente/generarReporte.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-yellow-50 rounded-lg transition">
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
    <a href="reportes.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-yellow-50 rounded-lg transition">
      <img src="/sys_Taller_Computo/img/verReportes.png" class="w-6 h-6">
      <span>Ver reportes</span>
    </a>
  </li>

 <li>
    <a href="perfilTecnico.php" class="flex items-center gap-3 p-3 text-gray-700 hover:bg-blue-50 rounded-lg transition">
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

  <main class="ml-64 p-8">
  <header class="flex justify-between items-center mb-8">
    <h1 class="text-4xl font-bold text-blue-600">Bienvenido, <?= htmlspecialchars($_SESSION["nombre"]) ?></h1>
  </header>


  <div class="flex flex-col md:flex-row md:space-x-6 space-y-6 md:space-y-0 mb-6">
    
    <div class="flex-1 bg-white p-6 rounded-2xl shadow hover:shadow-lg transition">
      <div class="flex items-center gap-4 mb-4">
        <img src="/sys_Taller_Computo/img/resumenReporte.png" class="w-12 h-12">
        <h2 class="text-2xl font-semibold">Reportes</h2>
      </div>
      <div class="flex justify-between">
        <div class="text-center">
          <p class="text-4xl font-bold text-blue-600"><?= $totalReportes ?></p>
          <span>Reportes por atender</span>
        </div>
        <div class="text-center">
          <p class="text-4xl font-bold text-green-500"><?= $totalReportesPropios ?></p>
          <span>Reportes atendidos</span>
        </div>
      </div>
    </div>



    <div class="flex-1 bg-white p-6 rounded-2xl shadow hover:shadow-lg transition flex flex-col justify-between">
  <div class="flex items-center gap-4 mb-4">
    <img src="/sys_Taller_Computo/img/cantidad.png" class="w-12 h-12">
    <h2 class="text-2xl font-semibold">Computadoras</h2>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center mb-4">
    <div>
      <p class="text-4xl font-bold text-blue-600"><?= $totalComputadoras ?></p>
      <span class="block mt-1">Total computadoras</span>
    </div>
    <div>
      <p class="text-4xl font-bold text-green-500"><?= $computadorasFuncionando ?></p>
      <span class="block mt-1">Funcionando</span>
    </div>
    <div>
      <p class="text-4xl font-bold text-yellow-500"><?= $computadorasEnMantenimiento ?></p>
      <span class="block mt-1">No Funcionando</span>
    </div>
    <div>
      <p class="text-4xl font-bold text-red-500 text-center"><?= $computadorasFueraDeServicio ?></p>
      <span class="block mt-1">Sin servicio</span>
    </div>
  </div>
  
  <a href="computadorasTecnico.php" class="mt-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg text-center transition">
    Ver computadoras
  </a>
</div>

  </div>


  <section class="bg-white p-6 rounded-2xl shadow">
    <h2 class="text-2xl font-bold mb-4">Reportes</h2>
    <div class="overflow-x-auto max-h-72">
      <table class="min-w-full divide-y divide-gray-200 max-h-3 overflow-auto">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Descripción</th>
            <th class="px-4 py-3">Tipo</th>
            <th class="px-4 py-3">Prioridad</th>
            <th class="px-4 py-3">Código Computador</th>
            <th class="px-4 py-3">Sala</th>
           
            <th class="px-4 py-3">Fecha Reporte</th>
            
            <th class="px-4 py-3">Estado</th>
            <th class="px-4 py-3">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($reportes as $r): ?>
          <tr>
            <td class="px-4 py-3 text-center"><?= $r["idReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["descripcionReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["tipoReporte"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["prioridad"] ?></td>
            <td class="px-4 py-3 text-center"><?= $r["codigoComputadora"] ?></td>
              <td class="px-4 py-3 text-center"><?= $r["nombreSala"] ?></td>
            <td class="px-4 py-3 text-center"><?= date("d/m/Y H:i", strtotime($r["fechaReporte"])) ?></td>
          
            <td class="px-4 py-3 w-40 text-center">
              <?php if ($r["estadoReporte"] === "En proceso"): ?>
                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm w-full text-center">Sin asignar</span>
              <?php elseif ($r["estadoReporte"] === "Atendido"): ?>
                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm text-center">Atendido</span>
              <?php elseif ($r["estadoReporte"] === "Cerrado"): ?>
                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm text-center">Cerrado</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 w-40 text-center">
  <?php if ($r["estadoReporte"] === "Atendido"): ?>
    <a href="#"
       class="p-3 bg-gray-400 font-semibold rounded-xl shadow text-white cursor-not-allowed pointer-events-none">
       Atender
    </a>
  <?php else: ?>
    <a href="atenderReporte.php?id=<?= $r["idReporte"] ?>" 
       class="p-3 bg-green-500 hover:bg-green-600 font-semibold rounded-xl shadow text-white">
       Atender
    </a>
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
