<?php


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

    <body class="bg-gray-200">

<header class="bg-white shadow-lg flex justify-between items-center px-6 w-full">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Talleres de Cómputo
    </h1>

    <select id="filtroEstado" class="border rounded p-2 shadow-lg">
        <option value="todos">Todos</option>
        <option value="Libre">Libre</option>
        <option value="Apartado">Apartado</option>
        <option value="No disponible">No disponible</option>
    </select>
</header>
</body>
</html>