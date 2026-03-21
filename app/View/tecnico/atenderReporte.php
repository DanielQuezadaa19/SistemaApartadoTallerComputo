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

<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">

<h1 class="text-3xl font-bold text-blue-600 mb-6 text-center w-full border-b-2 border-gray-200 p-3">
    Atender reporte
</h1>

<form method="POST" action="/sys_Taller_Computo/public/api/atenderReporte.php"
      class="w-full max-w-lg bg-white p-6 rounded-2xl shadow-md flex flex-col gap-5">

    <input type="hidden" name="idReporte" value="<?= $reporte['idReporte'] ?>">
    <input type="hidden" name="idTecnico" value="<?= $_SESSION["idDocente"] ?>">
    <input type="hidden" name="fechaAtendido" value="<?= date('Y-m-d H:i:s') ?>">

    
    <div class="grid grid-cols-1 gap-4">
        <div class="flex flex-col">
            <label class="font-semibold mb-1">Descripción</label>
            <input type="text" class="border p-2 rounded bg-gray-100" value="<?= htmlspecialchars($reporte['descripcionReporte']) ?>" disabled>
        </div>

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Tipo</label>
            <input type="text" class="border p-2 rounded bg-gray-100" value="<?= htmlspecialchars($reporte['tipoReporte']) ?>" disabled>
        </div>

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Prioridad</label>
            <input type="text" class="border p-2 rounded bg-gray-100" value="<?= htmlspecialchars($reporte['prioridad']) ?>" disabled>
        </div>

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Cambiar estado</label>
            <select name="estado" class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400" required>
                <option disabled selected value="">---- Seleccione estado ----</option>
                <option value="Atendido">Atendido</option>
                <option value="Cerrado">Cerrado</option>
            </select>
        </div>

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Observaciones técnico</label>
            <input type="text" name="observaciones" id="observaciones" class="border p-2 rounded bg-gray-100" placeholder="Observaciones" disabled>
        </div>
    </div>

    <button type="submit"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-lg shadow-md transition">
        Guardar cambios
    </button>
</form>

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

const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    if (estado.value === "") {
        e.preventDefault();
        alert("Error: Por favor, seleccione un estado válido.");
    }

    if (estado.value === "Atendido" && observaciones.value.trim() === "") {
        e.preventDefault();
        alert("Error: Debes agregar observaciones para marcar como Atendido.");
    }
});
</script>

</body>
</html>