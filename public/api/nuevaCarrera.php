<?php

session_start();
require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$nombre = $_POST["nombre"];


$sql = "INSERT INTO carrera 
(nombreCarrera)
VALUES (?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre]);

header("Location: /sys_Taller_Computo/app/View/admin/carreras.php");
exit;

?>