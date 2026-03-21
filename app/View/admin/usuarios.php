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

$sql = "
SELECT 
    u.idDocente,
    u.nombre,
    u.apellidoPaterno,
    u.apellidoMaterno,
    u.correo,
    r.nombreRol,
    c.nombreCarrera
FROM usuarios u
INNER JOIN rolusuario r ON r.idRol = u.rol
LEFT JOIN carrera c ON c.idCarrera = u.idCarrera
ORDER BY u.nombre ASC;

";

$usuarios = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$queryUsuarios = $pdo->prepare("SELECT COUNT(*) AS totUsuarios FROM usuarios");
$queryUsuarios->execute();
$totUsuarios = $queryUsuarios->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 min-h-screen flex flex-col">

<header class="bg-white shadow-lg flex flex-col sm:flex-row justify-between items-center px-4 sm:px-6 w-full py-4 gap-3">
    <h1 class="text-blue-600 border-b sm:border-none p-2 sm:p-5 text-2xl sm:text-3xl font-bold text-center sm:text-left w-full sm:w-auto">
        Gestión de usuarios
    </h1>
</header>

<main class="p-4 sm:p-6 flex-1">

<div class="m-2 sm:m-5 flex flex-col sm:flex-row justify-between items-center gap-4">
        <p class="text-lg sm:text-xl font-semibold text-center sm:text-left">
            Total de usuarios: <?= $totUsuarios["totUsuarios"] ?>
        </p>

        <a href="agregarUsuario.php" class="w-full sm:w-auto bg-blue-500 hover:bg-blue-700 text-center font-semibold text-white rounded-lg shadow p-3">
            Nuevo usuario
        </a>
    </div>

    <section class="bg-white p-4 sm:p-6 rounded-2xl shadow w-full">
    <h2 class="text-xl sm:text-2xl font-bold mb-4 text-center sm:text-left">Usuarios</h2>
    
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-xs sm:text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 sm:px-4 py-2 sm:py-3">ID</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Nombre</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">AP Paterno</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">AP Materno</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Correo</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Rol</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Carrera</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Editar</th>
            <th class="px-3 sm:px-4 py-2 sm:py-3">Eliminar</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php foreach ($usuarios as $u): ?>
          <tr class="text-center">
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["idDocente"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["nombre"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["apellidoPaterno"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["apellidoMaterno"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["correo"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["nombreRol"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3 break-words"><?= $u["nombreCarrera"] ?></td>
            <td class="px-3 sm:px-4 py-2 sm:py-3">
                <a href="editarUsuario.php?id=<?= $u['idDocente'] ?>" class="block w-full sm:inline-block bg-yellow-400 p-2 font-semibold text-center rounded-lg shadow text-white">
                    Editar
                </a>
            </td>
           
            <td class="px-3 sm:px-4 py-2 sm:py-3">
                <a href="/sys_Taller_Computo/public/api/eliminarUsuario.php?id=<?= $u['idDocente'] ?>" class="block w-full sm:inline-block bg-red-600 p-2 font-semibold text-center shadow rounded-lg text-white">
                    Eliminar
                </a>
            </td>
            
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

</body>
</html>