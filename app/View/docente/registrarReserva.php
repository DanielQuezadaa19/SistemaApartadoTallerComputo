<?php
date_default_timezone_set('America/Monterrey');

session_start();

require_once __DIR__ . "/../../../db/Database.php";

$pdo = Database::connect();

if (
    !isset($_SESSION["idDocente"]) ||
    !isset($_SESSION["rol"]) ||
    $_SESSION["rol"] != 2
) {
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

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
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center p-4">

<h1 class="text-3xl font-bold text-blue-600 mb-6 text-center">Hacer Nueva Reserva</h1>

<?php if ($mensaje): ?>
<div class="w-full max-w-xl mb-6 p-4 rounded <?= $error ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
    <?= htmlspecialchars($mensaje) ?>
</div>
<?php endif; ?>

<form method="POST" class="w-full max-w-xl bg-white p-6 rounded-2xl shadow-md flex flex-col gap-6">

    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Selecciona Sala:</label>
            <select name="idTaller" required
                    class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">Elige una sala</option>
                <?php foreach ($salas as $sala): ?>
                    <option value="<?= $sala['idTaller'] ?>">
                        <?= htmlspecialchars($sala['nombreSala']) ?> (<?= $sala['cantidadComputadoras'] ?> computadoras) - <?= $sala['estado'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Fecha y hora de inicio:</label>
            <input type="datetime-local" name="fechaInicio" min="<?= date('Y-m-d\TH:i') ?>" required
                   class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="flex flex-col">
            <label class="font-semibold mb-1">Fecha y hora de fin:</label>
            <input type="datetime-local" name="fechaFin" min="<?= date('Y-m-d\TH:i') ?>" required
                   class="border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

    </div>

    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg shadow-md transition">
        Reservar
    </button>

</form>

</body>
</html>