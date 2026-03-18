<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"]) || $_SESSION["rol"] != 1) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}



$stmtSalas = $pdo->query("
    SELECT idTaller, nombreSala, cantidadComputadoras
    FROM tallercomputo 
");
$salas = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar taller</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col justify-center items-center font-sans p-5 bg-gray-100">

<h1 class="text-blue-600 font-bold text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Agregar taller
</h1>
    

<form method="POST" action="/sys_Taller_Computo/public/api/nuevoTaller.php" onsubmit="return verificarCantidad()"
    class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full">

    <label class="font-bold">Nombre taller</label>
        <input type="text" name="nombreTaller" required class="border p-2 rounded">

        <label class="font-bold">Cantidad computadoras</label>
        <input type="number" name="cantidadComputadoras" required class="border p-2 rounded" id="cantidadComputadoras">

        <label class="font-bold">Computadoras funcionando</label>
        <input type="number" name="cantidadComputadorasFuncionando" required class="border p-2 rounded" id="cantidadComputadorasFuncionando">


        <div class="text-center">
            <button type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
                Crear taller
            </button>
        </div>
    </form>
</body>

<script>
function verificarCantidad(){

    let cantidadComputadoras = parseInt(document.getElementById('cantidadComputadoras').value);
    let cantidadComputadorasFuncionando = parseInt(document.getElementById('cantidadComputadorasFuncionando').value);

    if(cantidadComputadorasFuncionando > cantidadComputadoras){
    alert("La cantidad de computadoras funcionando no puede ser mayor al total.");
    return false;
    
    }else{
        return true;
    }


}
    
  
</script>
</html>