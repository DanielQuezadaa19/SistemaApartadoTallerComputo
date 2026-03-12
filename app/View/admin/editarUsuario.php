<?php

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$mensaje = "";

$id = $_GET["id"];

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE idDocente = ?");
$stmt->execute([$id]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$roles = $pdo->query("SELECT * FROM rolusuario")->fetchAll(PDO::FETCH_ASSOC);


$carreras = $pdo->query("SELECT * FROM carrera")->fetchAll(PDO::FETCH_ASSOC);

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
    Editar usuario
</h1>

<?php if ($mensaje): ?>
<div class="text-center mb-5 p-2 rounded 
    <?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/actualizarUsuario.php"
class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full">


    <input type="hidden" name="idDocente" value="<?= $usuario['idDocente'] ?>">

        <label class="font-bold">Nombre</label>
        <input type="text" name="nombre" required class="border p-2 rounded"
        value="<?= htmlspecialchars($usuario['nombre']) ?>">

        <label class="font-bold">Apellido Paterno</label>
        <input type="text" name="apellidoPaterno" required class="border p-2 rounded"
        value="<?= htmlspecialchars($usuario['apellidoPaterno']) ?>">

        <label class="font-bold">Apellido Materno</label>
        <input type="text" name="apellidoMaterno" required class="border p-2 rounded"
        value="<?= htmlspecialchars($usuario['apellidoMaterno']) ?>">

        <label class="font-bold">Correo</label>
        <input type="email" name="correo" required class="border p-2 rounded"
        value="<?= htmlspecialchars($usuario['correo']) ?>">

        <label class="font-bold">Contraseña</label>
        <input type="password" name="password" required class="border p-2 rounded">

        <label class="font-bold">Rol</label>
        
        <select name="rol" required class="border p-2 rounded">
            <option value="">Seleccione un rol</option>

            <?php foreach($roles as $rol): ?>
            <option value="<?= $rol['idRol'] ?>"
            <?= $usuario['rol'] == $rol['idRol'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($rol['nombreRol']) ?>
            </option>
            <?php endforeach; ?>

        </select>

        <label class="font-bold">Carrera</label>
            <select name="idCarrera" class="border p-2 rounded">
            
            <?php foreach($carreras as $carrera): ?>
            <option value="<?= $carrera['idCarrera'] ?>">
                <?= htmlspecialchars($carrera['nombreCarrera']) ?>
            </option>
            <?php endforeach; ?>
            </select>

        <div class="text-center">
            <button type="submit"
            class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
                Editar usuario
            </button>
        </div>

</form>

</body>
</html>