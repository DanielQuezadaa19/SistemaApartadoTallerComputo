<?php
session_start();

require_once __DIR__ . "/../../db/Database.php";

$pdo = Database::connect();

if(!isset($_SESSION["idDocente"])){
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$queryTalleres = $pdo->query("
    SELECT 
        t.idTaller, 
        t.nombreSala, 
        t.totalComputadoras,
        t.computadorasFuncionando,
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM registroTaller r
                WHERE r.idTaller = t.idTaller
                AND r.estado = 'En proceso'
                AND NOW() BETWEEN r.fechaInicio AND r.fechaFin
            )
            THEN 'Apartado'
            
            WHEN EXISTS (
                SELECT 1 FROM registroTaller r
                WHERE r.idTaller = t.idTaller
                AND r.estado = 'En proceso'
                AND r.fechaInicio > NOW()
            )
            THEN 'No disponible'
            
            ELSE 'Libre'
        END AS estado
    FROM tallercomputo t
");

$talleres = $queryTalleres->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talleres</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200 min-h-screen flex flex-col">

<header class="bg-white shadow-lg flex flex-col sm:flex-row justify-between items-center px-4 sm:px-6 w-full gap-4 sm:gap-0 py-4">
    <h1 class="text-blue-600 border-b sm:border-none p-2 sm:p-5 text-2xl sm:text-3xl font-bold text-center sm:text-left w-full sm:w-auto">
        Talleres de Cómputo
    </h1>

    <select id="filtroEstado" class="border rounded p-2 shadow-lg w-full sm:w-auto">
        <option value="todos">Todos</option>
        <option value="Libre">Libre</option>
        <option value="Apartado">Apartado</option>
        <option value="No disponible">No disponible</option>
    </select>
</header>

<main class="p-4 sm:p-6 flex-1">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
        <?php foreach ($talleres as $t): ?>
            
            <?php 
                $noFuncionando = max(0, $t['totalComputadoras'] - $t['computadorasFuncionando']);
                $porcentaje = $t['totalComputadoras'] > 0 
                    ? ($t['computadorasFuncionando'] / $t['totalComputadoras']) * 100 
                    : 0;
            ?>

            <section 
                class="card w-full bg-white rounded-xl shadow-md p-4 sm:p-5 space-y-3 hover:shadow-xl transition"
                data-estado="<?= $t['estado'] ?>">
                
                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 text-center tracking-wide break-words">
                    <?= htmlspecialchars($t['nombreSala']) ?>
                </h2>

                <div class="text-xs sm:text-sm text-gray-600 text-center">
                    <span class="font-medium">ID:</span>
                    <?= $t['idTaller'] ?>
                </div>

                <div class="space-y-1 text-xs sm:text-sm">

                    <div>
                        <span class="font-medium text-gray-700">Total:</span>
                        <span class="font-semibold text-blue-600">
                            <?= $t['totalComputadoras'] ?>
                        </span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-700">Funcionando:</span>
                        <span class="font-semibold text-green-600">
                            <?= $t['computadorasFuncionando'] ?>
                        </span>
                    </div>

                    <div>
                        <span class="font-medium text-gray-700">No funcionando:</span>
                        <span class="font-semibold text-red-600">
                            <?= $noFuncionando ?>
                        </span>
                    </div>

                </div>

                <div class="w-full bg-gray-300 rounded-full h-3 mt-2">
                    <div 
                        class="bg-blue-500 h-3 rounded-full transition-all duration-500"
                        style="width: <?= $porcentaje ?>%">
                    </div>
                </div>

                <div class="text-xs sm:text-sm text-center mt-2">
                    <span class="font-medium text-gray-700">Estado:</span>
                    <span class="font-semibold
                        <?= $t['estado'] === 'Libre' ? 'text-green-500' : '' ?>
                        <?= $t['estado'] === 'Apartado' ? 'text-yellow-500' : '' ?>
                        <?= $t['estado'] === 'No disponible' ? 'text-red-500' : '' ?>">
                        <?= $t['estado'] ?>
                    </span>
                </div>

            </section>

        <?php endforeach; ?>
    </div>
</main>

<script>
document.getElementById("filtroEstado").addEventListener("change", function () {
    let estadoSeleccionado = this.value;
    let cards = document.querySelectorAll(".card");

    cards.forEach(card => {
        let estadoCard = card.dataset.estado;

        if (estadoSeleccionado === "todos" || estadoCard === estadoSeleccionado) {
            card.classList.remove("hidden");
        } else {
            card.classList.add("hidden");
        }
    });
});
</script>

</body>
</html>