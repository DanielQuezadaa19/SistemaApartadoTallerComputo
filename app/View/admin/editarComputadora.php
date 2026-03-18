<?php
session_start();
require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 1
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

if (!isset($_GET["idComputadora"])) {
    header("Location: computadoras.php");
    exit;
}

$id = $_GET["idComputadora"];

$stmt = $pdo->prepare("SELECT * FROM computadora WHERE idComputadora = ?");
$stmt->execute([$id]);

$computadora = $stmt->fetch(PDO::FETCH_ASSOC);

$mensaje = "";

$talleres = $pdo->query("SELECT * FROM tallercomputo")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar computadora</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col justify-center items-center font-sans p-5 bg-gray-100">

<h1 class="text-blue-600 font-bold text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Editar computadora
</h1>

<?php if ($mensaje): ?>
<div class="text-center mb-5 p-2 rounded 
<?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
<?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/actualizarComputadora.php"
class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full">

<input type="hidden" name="idComputadora" value="<?= $computadora['idComputadora'] ?>">

<label class="font-bold">Código computadora</label>
<input 
type="text" 
name="codigoComputadora"
value="<?= htmlspecialchars($computadora['codigoComputadora']) ?>"
required
class="border p-2 rounded">

<label class="font-bold">Taller a donde pertenecerá</label>
<select name="idTaller" required class="border p-2 rounded">
<option value="">Elige un taller</option>
<?php foreach($talleres as $t): ?>
<option 
value="<?= $t['idTaller'] ?>"
<?= $t['idTaller'] == $computadora['idTaller'] ? 'selected' : '' ?>>
<?= htmlspecialchars($t['nombreSala']) ?>
</option>
<?php endforeach ?>
</select>

<label class="font-bold">Estado de la computadora</label>
<select name="estado" required class="border p-2 rounded">
    <option value="Disponible" <?= $computadora['estado'] === 'Disponible' ? 'selected' : '' ?>>Disponible</option>
    <option value="En mantenimiento" <?= $computadora['estado'] === 'En mantenimiento' ? 'selected' : '' ?>>En mantenimiento</option>
    <option value="Fuera de servicio" <?= $computadora['estado'] === 'Fuera de servicio' ? 'selected' : '' ?>>Fuera de servicio</option>
</select>

<div class="text-center">
<button type="submit"
class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
Editar computadora
</button>
</div>

</form>

</body>
</html>