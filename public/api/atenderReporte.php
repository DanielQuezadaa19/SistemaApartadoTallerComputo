<?php
session_start();
require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();


date_default_timezone_set('America/Monterrey');

$idReporte = $_POST["idReporte"];

$stmtCheck = $pdo->prepare("SELECT estadoReporte FROM reportes WHERE idReporte = ?");
$stmtCheck->execute([$idReporte]);
$reporte = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($reporte && $reporte["estadoReporte"] === "Atendido") {
    header("Location: /sys_Taller_Computo/public/View/tecnico/dashboardTecnico.php");
    exit;
}

$estado = $_POST["estado"];
$idTecnico = $_POST["idTecnico"];


$fecha = date("Y-m-d H:i:s");

$observaciones = $_POST["observaciones"] ?? null;

$stmt = $pdo->prepare("
    UPDATE reportes 
    SET estadoReporte = ?, 
        idTecnicoAtendio = ?, 
        fechaAtencion = ?, 
        observacionesTecnico = ?
    WHERE idReporte = ?
");

$stmt->execute([$estado, $idTecnico, $fecha, $observaciones, $idReporte]);

header("Location: /sys_Taller_Computo/app/View/tecnico/dashTecnico.php");
exit;