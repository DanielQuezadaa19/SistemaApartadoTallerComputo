<?php
session_start();
require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"]) || $_SESSION["rol"] != 3) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}


$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: dashboardTecnico.php");
    exit;
}


$stmt = $pdo->prepare("SELECT * FROM reportes WHERE idReporte = ?");
$stmt->execute([$id]);
$reporte = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reporte) {
    header("Location: dashboardTecnico.php");
    exit;
}

$mensaje = "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atender reporte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col items-center font-sans p-5 bg-gray-100">

<h1 class="text-blue-600 font-bold text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Atender reporte
</h1>

<form method="POST" action="/sys_Taller_Computo/public/api/atenderReporte.php"
class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full shadow">

    
    <input type="hidden" name="idReporte" value="<?= $reporte['idReporte'] ?>">
    <input type="hidden" name="idTecnico" value="<?= $_SESSION["idDocente"] ?>">
<input type="hidden" name="fechaAtendido" value="<?= date('Y-m-d H:i:s') ?>">

   
    <label class="font-bold">Descripción</label>
    <input 
        type="text" 
        class="border p-2 rounded bg-gray-100"
        value="<?= htmlspecialchars($reporte['descripcionReporte']) ?>"
        disabled>


    <label class="font-bold">Tipo</label>
    <input 
        type="text" 
        class="border p-2 rounded bg-gray-100"
        value="<?= htmlspecialchars($reporte['tipoReporte']) ?>"
        disabled>

  
    <label class="font-bold">Prioridad</label>
    <input 
        type="text" 
        class="border p-2 rounded bg-gray-100"
        value="<?= htmlspecialchars($reporte['prioridad']) ?>"
        disabled>




    
    <label class="font-bold">Cambiar estado</label>
    <select name="estado" class="border p-2 rounded" required>
        <option disabled selected>---- Seleccione estado ----</option>
        <option value="Atendido">Atendido</option>
        <option value="Cerrado">Cerrado</option>
    </select>

    <label class="font-bold">Observaciones técnico</label>
    <input 
        type="text" 
        name="observaciones"
        id="observaciones"
        class="border p-2 rounded bg-gray-100"
        placeholder="Observaciones"
        disabled >


    <div class="text-center">
        <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
            Guardar cambios
        </button>
    </div>

</form>

</body>

<script>
const estado = document.querySelector('select[name="estado"]');
const observaciones = document.getElementById('observaciones');

estado.addEventListener('change', function() {
    if (this.value === "Atendido") {
        observaciones.disabled = false;
        observaciones.required = true; 
        observaciones.classList.remove("bg-gray-100");
    } else {
        observaciones.disabled = true;
        observaciones.required = false; 
        observaciones.value = "";
        observaciones.classList.add("bg-gray-100");
    }
});
</script>
</html>