<?php
session_start();
require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idComputadora = $_POST["idComputadora"] ?? null;
    $codigoComputadora = $_POST["codigoComputadora"] ?? "";
    $idTaller = $_POST["idTaller"] ?? null;

    if (!$idComputadora || !$codigoComputadora || !$idTaller) {
        header("Location: ../views/computadoras.php?error=Datos incompletos");
        exit;
    }

    try {

        $stmt = $pdo->prepare("
            UPDATE computadora
            SET codigoComputadora = ?, idTaller = ?
            WHERE idComputadora = ?
        ");

        $stmt->execute([
            $codigoComputadora,
            $idTaller,
            $idComputadora
        ]);

       header("Location: /sys_Taller_Computo/app/View/admin/computadoras.php?success=Computadora%20actualizada");
        exit;
       

    }catch (PDOException $e) {

header("Location: /sys_Taller_Computo/app/View/admin/computadoras.php?error=Computadora%20con%20el%20codigo%20existente");
exit;

} 
    
}
?>