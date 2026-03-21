<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$sqlSalas = $pdo->query("
    SELECT t.idTaller, t.nombreSala, t.cantidadComputadoras,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM registroTaller r
            WHERE r.idTaller = t.idTaller
            AND r.estado = 'En proceso'
            AND NOW() BETWEEN r.fechaInicio AND r.fechaFin
        )
        THEN 'Apartado'
        ELSE 'Libre'
    END AS estado
    FROM tallercomputo t
");

$salas = $sqlSalas->fetchAll(PDO::FETCH_ASSOC);

$computadoras = [];

if (!empty($_POST['idTaller'])) {
    $stmt = $pdo->prepare("SELECT * FROM computadora WHERE idTaller = ?");
    $stmt->execute([$_POST['idTaller']]);
    $computadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$mensaje = "";
$error = false;

if (isset($_GET['ok'])) {
    if ($_GET['ok'] == 1) {
        $mensaje = "Reporte enviado correctamente.";
        $error = false;
        
    }

    if ($_GET['ok'] == 0) {
        $mensaje = "Error al enviar el reporte.";
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Generar reporte</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col justify-center items-center font-sans p-4 sm:p-6 bg-gray-100">

<h1 class="text-blue-600 font-bold text-xl sm:text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Generar reporte
</h1>

<?php if ($mensaje): ?>
<div class="text-center mb-5 p-2 rounded w-full max-w-md
<?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/guardarReporte.php" 
class="w-full max-w-md mx-auto bg-white p-4 sm:p-5 rounded-lg flex flex-col gap-4 mt-5">

<label class="font-bold text-sm sm:text-base">Seleccione sala de cómputo</label>
<select name="idTaller" onchange="this.form.action=''; this.form.submit();" required class="p-2 rounded border text-sm sm:text-base">

<option value="">Elige una sala</option>

<?php foreach($salas as $sala): ?>

<option value="<?= $sala['idTaller'] ?>"
<?= (isset($_POST['idTaller']) && $_POST['idTaller'] == $sala['idTaller']) ? 'selected' : '' ?>>

<?= htmlspecialchars($sala['nombreSala']) ?>
(<?= $sala['cantidadComputadoras'] ?> computadoras)
- <?= $sala['estado'] ?>

</option>

<?php endforeach; ?>

</select>

<label class="font-bold text-sm sm:text-base">Seleccione una computadora</label>

<select name="idComputadora" required class="border p-2 rounded text-sm sm:text-base">

<option value="">Selecciona una computadora</option>

<?php foreach($computadoras as $computadora): ?>

<option value="<?= $computadora['idComputadora'] ?>">
<?= htmlspecialchars($computadora['codigoComputadora']) ?>
</option>

<?php endforeach; ?>

</select>

<label class="font-bold text-sm sm:text-base">Tipo de reporte</label>

<select name="tipoReporte" required class="border p-2 rounded text-sm sm:text-base">

<option value="">Seleccione una opción</option>
<option value="Hardware">Falla de hardware</option>
<option value="Software">Falla de software</option>
<option value="Red">Problema de red</option>
<option value="No enciende">No enciende</option>
<option value="Lento">Equipo lento</option>
<option value="Daño físico">Daño físico</option>
<option value="Otro">Otro</option>

</select>

<label class="font-bold text-sm sm:text-base">Descripción</label>

<textarea name="descripcion" required rows="3"
class="border p-2 rounded text-sm sm:text-base"
placeholder="Describe el problema..."></textarea>

<label class="font-bold text-sm sm:text-base">Prioridad</label>

<select name="prioridad" required class="border p-2 rounded text-sm sm:text-base">

<option value="Baja">Baja</option>
<option value="Media">Media</option>
<option value="Alta">Alta</option>
<option value="Urgente">Urgente</option>

</select>

<div class="text-center">

<button type="submit"
class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">

Enviar Reporte

</button>

</div>

</form>

</body>
</html>