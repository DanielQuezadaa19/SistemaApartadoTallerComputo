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



$idDocente     = $_SESSION["idDocente"];
$nombreDocente = $_SESSION["nombre"];
$apellidoPat   = $_SESSION["apellidoPaterno"];
$apellidoMat   = $_SESSION["apellidoMaterno"];
$gmailUsuario  = $_SESSION["correo"];

$totalReportes = (int)$pdo->query("SELECT COUNT(*) FROM reportes WHERE estadoReporte = 'En proceso'")->fetchColumn();



$stmt = $pdo->prepare("SELECT COUNT(*) FROM reportes WHERE idTecnicoAtendio = ?");
$stmt->execute([$idDocente]);

$totalReportesPropios = (int)$stmt->fetchColumn();

$queryReportes = $pdo->prepare("
    SELECT r.idReporte, r.descripcionReporte, r.tipoReporte, r.prioridad,
           c.codigoComputadora, t.nombreSala, r.fechaReporte, r.estadoReporte
    FROM reportes r
    INNER JOIN computadora c ON r.idComputadoraReporte = c.idComputadora
    INNER JOIN tallercomputo t ON c.idTaller = t.idTaller
    ORDER BY r.fechaReporte DESC
");

$queryReportes->execute();
$reportes = $queryReportes->fetchAll(PDO::FETCH_ASSOC);

$inicialesUsuario = substr($nombreDocente, 0, 1) . substr($apellidoPat, 0, 1);


$queryUsuariosTotales = $pdo->prepare("
    SELECT COUNT(*) AS totalUsuarios 
    FROM usuarios 
");

$queryUsuariosTotales->execute();
$usuariosTotales = $queryUsuariosTotales->fetch(PDO::FETCH_ASSOC);

$queryReservasTotales = $pdo->prepare("
    SELECT COUNT(*) AS reservasTotales
    FROM registrotaller
    ");

$queryReservasTotales->execute();
$reservasTotales = $queryReservasTotales->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<header class="w-full h-36 bg-gray-800"></header>

<main class="w-3/4 mx-auto px-6 -mt-16">


    <section class="bg-white rounded-xl shadow p-6 flex items-center gap-6">
        <div class="rounded-full bg-gray-800 h-32 w-32 shadow-lg flex justify-center items-center">
            <p class="font-bold text-gray-200 text-5xl">
                <?= $inicialesUsuario ?>
            </p>
        </div>

        <div class="flex flex-col">
            <h1 class="text-3xl font-bold text-gray-800">
                <?= $nombreDocente . ' ' . $apellidoPat . ' ' . $apellidoMat ?>
            </h1>

             <div class="flex items-center gap-2 text-gray-600 mt-2">
                <img src="/sys_Taller_Computo/img/tarjeta-de-identificacion.png" class="w-5 h-5">
                <span><?= $idDocente ?></span>
            </div>

            <div class="flex items-center gap-2 text-gray-600 mt-2">
                <img src="/sys_Taller_Computo/img/gmail.png" class="w-5 h-5">
                <span><?= $gmailUsuario ?></span>
            </div>



        </div>
    </section>


    <section class="mt-8 flex flex-col lg:flex-row gap-6">

      
        <div class="flex flex-col gap-6 w-full lg:w-1/3">

            <div class="bg-white rounded-xl shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Resumen</h2>

                <div class="bg-gray-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-gray-600">
                        <?= $totalReportes ?>
                    </p>
                    <p class="text-sm text-gray-600">Reportes por atender</p>
                </div>
            </div>

             <div class="bg-white rounded-xl shadow p-6">
                

                <div class="bg-gray-600 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-gray-50">
                        <?= $totalReportesPropios ?>
                    </p>
                    <p class="text-sm text-gray-50">Reportes atendidos por mí</p>
                </div>
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
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>



</div>

  
    </section>

    <div class="w-full bg-gray-800 rounded font-semibold p-4 mt-5 text-white shadow hover:bg-grayx-900 transition">
   
  </div>

</main>

</body>
</html>
