<?php

require_once __DIR__ . "/../../db/Database.php";
$pdo = Database::connect();

$id = $_POST["idCarrera"];
$nombre = $_POST["nombre"];

$sql = "UPDATE carrera
SET nombreCarrera = ?
WHERE idCarrera = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre, $id]);

header("Location: /sys_Taller_Computo/app/View/admin/carreras.php");
exit;