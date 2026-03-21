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

<body class="min-h-screen flex flex-col items-center font-sans p-4 sm:p-6 bg-gray-100">

<h1 class="text-blue-600 font-bold text-xl sm:text-2xl border-b-2 border-gray-200 p-3 text-center w-full max-w-4xl">
    Editar usuario
</h1>

<?php if ($mensaje): ?>
<div class="w-full max-w-4xl text-center mb-5 p-3 rounded 
    <?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" action="/sys_Taller_Computo/public/api/actualizarUsuario.php"
class="w-full max-w-4xl bg-white p-4 sm:p-6 rounded-lg flex flex-col gap-4 mt-5 shadow">

    <input type="hidden" name="idDocente" value="<?= $usuario['idDocente'] ?>">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="flex flex-col">
            <label class="font-bold">Nombre</label>
            <input type="text" name="nombre" required class="border p-2 rounded w-full"
            value="<?= htmlspecialchars($usuario['nombre']) ?>">
        </div>

        <div class="flex flex-col">
            <label class="font-bold">Apellido Paterno</label>
            <input type="text" name="apellidoPaterno" required class="border p-2 rounded w-full"
            value="<?= htmlspecialchars($usuario['apellidoPaterno']) ?>">
        </div>

        <div class="flex flex-col">
            <label class="font-bold">Apellido Materno</label>
            <input type="text" name="apellidoMaterno" required class="border p-2 rounded w-full"
            value="<?= htmlspecialchars($usuario['apellidoMaterno']) ?>">
        </div>

        <div class="flex flex-col">
            <label class="font-bold">Correo</label>
            <input type="email" name="correo" required class="border p-2 rounded w-full"
            value="<?= htmlspecialchars($usuario['correo']) ?>">
        </div>

        <div class="flex flex-col">
            <label class="font-bold">Contraseña</label>
            <input type="password" name="password" required class="border p-2 rounded w-full">
        </div>

        <div class="flex flex-col">
            <label class="font-bold">Rol</label>
            <select name="rol" required class="border p-2 rounded w-full">
                <option value="">Seleccione un rol</option>

                <?php foreach($roles as $rol): ?>
                <option value="<?= $rol['idRol'] ?>"
                <?= $usuario['rol'] == $rol['idRol'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($rol['nombreRol']) ?>
                </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="flex flex-col md:col-span-2">
            <label class="font-bold">Carrera</label>
            <select name="idCarrera" class="border p-2 rounded w-full">
                <?php foreach($carreras as $carrera): ?>
                <option value="<?= $carrera['idCarrera'] ?>">
                    <?= htmlspecialchars($carrera['nombreCarrera']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

    </div>

    <div class="text-center mt-4">
        <button type="submit"
        class="w-full sm:w-auto bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
            Editar usuario
        </button>
    </div>

</form>

</body>
</html>