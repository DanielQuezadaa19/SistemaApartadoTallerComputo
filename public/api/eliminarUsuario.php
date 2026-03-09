<?php
session_start();
require_once __DIR__ . "/../../db/Database.php";

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$pdo = Database::connect();

if (isset($_GET["id"])) {

    $id = $_GET["id"];

    $sql = "DELETE FROM usuarios WHERE idDocente = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
}

header("Location: /sys_Taller_Computo/app/View/admin/usuarios.php");
exit;