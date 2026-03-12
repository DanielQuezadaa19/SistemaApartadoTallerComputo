<?php
session_start();
require_once __DIR__ . "/../../db/Database.php";

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$pdo = Database::connect();

if (
    empty($_POST['idComputadora']) ||
    empty($_POST['tipoReporte']) ||
    empty($_POST['descripcion']) ||
    empty($_POST['prioridad'])
) {
    header("Location: /sys_Taller_Computo/app/View/generarReporte.php?ok=0");
    exit;
}

$sql = "INSERT INTO reportes
(descripcionReporte, idDocenteReporto, idComputadoraReporte, tipoReporte, prioridad)
VALUES (?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);

$guardado = $stmt->execute([
    trim($_POST['descripcion']),
    $_SESSION['idDocente'],
    $_POST['idComputadora'],
    $_POST['tipoReporte'],
    $_POST['prioridad']
]);

if ($guardado) {
    header("Location: /sys_Taller_Computo/app/View/docente/generarReporte.php?ok=1");
} else {
    header("Location: /sys_Taller_Computo/app/View/docente/generarReporte.php?ok=0");
}

exit;