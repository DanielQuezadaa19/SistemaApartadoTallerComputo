<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$mensaje = "";
$error = false;

$talleres = $pdo->query("SELECT * FROM tallercomputo")->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agrega computadora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col justify-center items-center font-sans p-5 bg-gray-100">

<h1 class="text-blue-600 font-bold text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Agregar computadora
</h1>

<?php if ($mensaje): ?>
<div class="text-center mb-5 p-2 rounded 
    <?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/nuevaComputadora.php"
class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full">

        <label class="font-bold">Código computadora</label>
        <input type="text" name="codigoComputadora" required class="border p-2 rounded">

        <label class="font-bold">Taller a donde pertenecerá</label>
        <select name="idTaller" required class="border p-2 rounded">
            <option>Elige un taller</option>
             <?php foreach($talleres as $t): ?>
            <option value="<?= $t['idTaller'] ?>">
                <?= htmlspecialchars($t['nombreSala']) ?>
            </option>
            <?php endforeach ?>
        </select>



        <div class="text-center">
            <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
                Crear carrera
            </button>
        </div>

</form>
    
</body>
</html>