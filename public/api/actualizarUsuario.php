<?php

require_once __DIR__ . "/../../db/Database.php";
$pdo = Database::connect();

$id = $_POST["idDocente"];
$nombre = $_POST["nombre"];
$apellidoP = $_POST["apellidoPaterno"];
$apellidoM = $_POST["apellidoMaterno"];
$correo = $_POST["correo"];
$rol = $_POST["rol"];
$carrera = $_POST["idCarrera"];

$sql = "UPDATE usuarios 
SET nombre=?, apellidoPaterno=?, apellidoMaterno=?, correo=?, rol=?, idCarrera=? 
WHERE idDocente=?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre,$apellidoP,$apellidoM,$correo,$rol,$carrera,$id]);

header("Location: /sys_Taller_Computo/app/View/admin/usuarios.php");
exit;
?>