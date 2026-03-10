<?php

session_start();
require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();
$nombre = $_POST["nombreTaller"];
$cantidad = $_POST["cantidadComputadoras"];
$estado = "Libre";
$funcionando = $_POST["cantidadComputadorasFuncionando"];


$stmt = $pdo->prepare("SELECT COUNT(*) FROM tallercomputo WHERE nombreSala = ?");
$stmt->execute([$nombre]);

$existe = $stmt->fetchColumn();

if ($existe > 0) {
    echo "<script>
        alert('Ya existe un taller con ese nombre');
        window.history.back();
    </script>";
    exit;
}


$sql = "INSERT INTO tallercomputo
(nombreSala, cantidadComputadoras, estado, totalComputadoras, computadorasFuncionando)
VALUES (?,?,?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre, $cantidad, $estado, $cantidad, $funcionando]);

header("Location: /sys_Taller_Computo/app/View/admin/talleresAdmin.php");
exit;

?>
