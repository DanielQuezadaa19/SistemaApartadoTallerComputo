<?php

session_start();
require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();

$codigo = $_POST["codigoComputadora"];
$idTaller = $_POST["idTaller"];
$estado = "Disponible";

$stmt = $pdo->prepare("SELECT COUNT(*) FROM computadora WHERE codigoComputadora = ?");
$stmt->execute([$codigo]);

$existe = $stmt->fetchColumn();

if ($existe > 0) {
    echo "<script>
        alert('Ya existe una computadora con ese código');
        window.history.back();
    </script>";
    exit;
}

$sql = "INSERT INTO computadora
(codigoComputadora, estado, idTaller)
VALUES (?,?,?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$codigo, $estado, $idTaller]);

header("Location: /sys_Taller_Computo/app/View/admin/computadoras.php");
exit;

?>