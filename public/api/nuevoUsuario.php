<?php

session_start();
require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$nombre = $_POST["nombre"];
$apellidoP = $_POST["apellidoPaterno"];
$apellidoM = $_POST["apellidoMaterno"];
$correo = $_POST["correo"];
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);
$rol = $_POST["rol"];
$carrera = $_POST["idCarrera"];

$sql = "INSERT INTO usuarios 
(nombre, apellidoPaterno, apellidoMaterno, correo, password_hash, rol, idCarrera)
VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre,$apellidoP,$apellidoM,$correo,$password,$rol,$carrera]);

header("Location: /sys_Taller_Computo/app/View/admin/usuarios.php");
exit;

?>