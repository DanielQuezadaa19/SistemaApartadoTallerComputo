<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"]) || $_SESSION["rol"] != 1) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$mensaje = "";
$error = false;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agregar carrera</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex flex-col justify-center items-center font-sans p-4 bg-gray-100">

<h1 class="text-blue-600 font-bold text-xl md:text-2xl border-b-2 border-gray-200 p-2 text-center w-full max-w-md">
    Agregar carrera
</h1>

<?php if ($mensaje): ?>
<div class="w-full max-w-md text-center mb-5 p-2 rounded 
    <?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/nuevaCarrera.php"
class="w-full max-w-md mx-auto bg-white p-5 md:p-6 rounded-lg flex flex-col gap-4 mt-5 shadow">

    <label class="font-bold">Nombre carrera</label>
    <input type="text" name="nombre" required class="border p-2 rounded w-full">

    <div class="text-center">
        <button type="submit"
        class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
            Crear carrera
        </button>
    </div>

</form>

</body>
</html>