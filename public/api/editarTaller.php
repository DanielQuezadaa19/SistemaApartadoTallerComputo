<?php

require_once __DIR__ . "/../../db/Database.php";
$pdo = Database::connect();

$id = $_POST["idTaller"];
$nombre = $_POST["nombreSala"];
$cantidadComputadoras = $_POST["cantidadComputadoras"];
$computadorasFuncionando = $_POST["computadorasFuncionando"];
$estado = $_POST["estado"];

$sql = "UPDATE tallercomputo
SET nombreSala=?, cantidadComputadoras=?, estado=?, totalComputadoras=?, computadorasFuncionando=?
WHERE idTaller=?";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $nombre,
    $cantidadComputadoras,
    $estado,
    $cantidadComputadoras,
    $computadorasFuncionando,
    $id
]);

header("Location: /sys_Taller_Computo/app/View/admin/talleresAdmin.php");
exit;
?>