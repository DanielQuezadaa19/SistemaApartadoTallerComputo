<?php
session_start();
require_once __DIR__ . "/../../db/Database.php";

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$pdo = Database::connect();

if (isset($_GET["idComputadora"])) {

    $id = $_GET["idComputadora"];

    $sql = "DELETE FROM computadora WHERE idComputadora = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: /sys_Taller_Computo/app/View/admin/computadoras.php");
exit;