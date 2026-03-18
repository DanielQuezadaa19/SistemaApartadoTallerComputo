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

$id = $_GET["idTaller"];




$stmt = $pdo->prepare("SELECT * FROM tallercomputo WHERE idTaller = ?");
$stmt->execute([$id]);

$taller = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar taller</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col justify-center items-center font-sans p-5 bg-gray-100">

<h1 class="text-blue-600 font-bold text-2xl border-b-2 border-gray-200 p-2 text-center w-full">
    Editar taller
</h1>
    
</body>

<form method="POST" action="/sys_Taller_Computo/public/api/editarTaller.php" onsubmit="return verificarCantidad()"
    class="max-w-md mx-auto bg-white p-5 rounded-lg flex flex-col gap-4 mt-5 w-full">

    <input type="hidden" name="idTaller" value="<?= $taller['idTaller'] ?>">
    
    <label class="font-bold">Nombre taller</label>
<input type="text" name="nombreSala" required class="border p-2 rounded" value="<?= $taller['nombreSala'] ?>">

<label class="font-bold">Cantidad computadoras</label>
<input type="number" name="cantidadComputadoras" required class="border p-2 rounded" id="cantidadComputadoras" value="<?= $taller['totalComputadoras'] ?>">

<label class="font-bold">Computadoras funcionando</label>
<input type="number" name="computadorasFuncionando" required class="border p-2 rounded" id="cantidadComputadorasFuncionando" value="<?= $taller['computadorasFuncionando'] ?>">


        <div class="text-center">
            <button type="submit" 
            class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold px-6 py-2 rounded-lg shadow-md">
                Editar taller
            </button>
        </div>
    </form>


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