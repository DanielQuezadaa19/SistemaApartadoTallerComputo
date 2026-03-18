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

$id = $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM carrera WHERE idCarrera = ?");
$stmt->execute([$id]);

$carrera = $stmt->fetch(PDO::FETCH_ASSOC);

$mensaje = "";
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col justify-center items-center font-sans p-5 bg-gray-100">

<h1 class="text-blue-600 font-bold text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Editar carrera
</h1>

<?php if ($mensaje): ?>
<div class="text-center mb-5 p-2 rounded 
    <?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/actualizarCarrera.php"
class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full">

<input type="hidden" name="idCarrera" value="<?= $carrera['idCarrera'] ?>">

<label class="font-bold">Nombre de carrera</label>

        <input 
            type="text" 
            name="nombre" 
            required 
            class="border p-2 rounded"
            value="<?= htmlspecialchars($carrera['nombreCarrera']) ?>">

        <div class="text-center">
        <button type="submit"
            class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
                 Editar carrera
        </button>
        </div>

</form>

</body>
</html>