<?php
date_default_timezone_set('America/Monterrey');

require_once __DIR__ . "/../../db/Database.php";
session_start();

if (!isset($_SESSION["idDocente"])) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$pdo = Database::connect();

$stmtSalas = $pdo->query("
    SELECT t.idTaller, t.nombreSala, t.cantidadComputadoras,
    CASE 
        WHEN EXISTS (
            SELECT 1 FROM registroTaller r
            WHERE r.idTaller = t.idTaller
            AND r.estado = 'En proceso'
            AND NOW() BETWEEN r.fechaInicio AND r.fechaFin
        )
        THEN 'Apartado'
        ELSE 'Libre'
    END AS estado
    FROM tallercomputo t
");
$salas = $stmtSalas->fetchAll(PDO::FETCH_ASSOC);

$docenteAparto = $_SESSION["idDocente"];
$mensaje = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idTaller = $_POST['idTaller'];
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];

    $ahora = date("Y-m-d H:i:s");

    if (strtotime($fechaInicio) <= strtotime($ahora)) {
        $mensaje = "No puedes hacer reservas en fechas u horas pasadas.";
        $error = true;
    } elseif (strtotime($fechaFin) <= strtotime($fechaInicio)) {
        $mensaje = "La fecha de fin debe ser mayor que la fecha de inicio.";
        $error = true;
    } else {
        $checkStmt = $pdo->prepare("
            SELECT COUNT(*) FROM registroTaller
            WHERE idTaller = :idTaller
            AND estado = 'En proceso'
            AND (fechaInicio <= :fechaFin AND fechaFin >= :fechaInicio)
        ");
        $checkStmt->execute([
            ':idTaller' => $idTaller,
            ':fechaInicio' => $fechaInicio,
            ':fechaFin' => $fechaFin
        ]);
        $ocupada = $checkStmt->fetchColumn();

        if ($ocupada > 0) {
            $mensaje = "La sala ya está apartada en ese rango de tiempo.";
            $error = true;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO registroTaller (fechaInicio, fechaFin, docenteAparto, idTaller, estado)
                VALUES (:fechaInicio, :fechaFin, :docenteAparto, :idTaller, 'En proceso')
            ");
            $stmt->execute([
                ':fechaInicio' => $fechaInicio,
                ':fechaFin' => $fechaFin,
                ':docenteAparto' => $docenteAparto,
                ':idTaller' => $idTaller
            ]);

            $mensaje = "Reserva registrada correctamente.";
            $error = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva Reserva</title>
<link rel="stylesheet" href="/sys_Taller_Computo/public/css/nuevaReserva-output.css">
</head>
<body>

<h1>Hacer Nueva Reserva</h1>

<?php if ($mensaje): ?>
<div class="mensaje <?= $error ? 'error' : 'success' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST">
    <label for="idTaller">Selecciona Sala:</label>
    <select name="idTaller" required>
        <option value="">Elige una sala</option>
        <?php foreach ($salas as $sala): ?>
            <option value="<?= $sala['idTaller'] ?>" <?= $sala['estado'] === 'Apartado' ? '' : '' ?>>
                <?= htmlspecialchars($sala['nombreSala']) ?>
                (<?= $sala['cantidadComputadoras'] ?> computadoras)
                - <?= $sala['estado'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Fecha y hora de inicio:</label>
    <input type="datetime-local" name="fechaInicio" min="<?= date('Y-m-d\TH:i') ?>" required>

    <label>Fecha y hora de fin:</label>
    <input type="datetime-local" name="fechaFin" min="<?= date('Y-m-d\TH:i') ?>" required>

    <button type="submit">Reservar</button>
</form>

</body>
</html>
