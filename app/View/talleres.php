<?php
require_once __DIR__ . "/../../db/Database.php";
session_start();

if(!isset($_SESSION["idDocente"])){
    header("Location: /sys_Taller_Computo/public/api/login.php");
    exit;
}

$pdo = Database::connect();

$queryTalleres = $pdo->query("
    SELECT t.idTaller, t.nombreSala, t.cantidadComputadoras,
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talleres</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200">

<header class="bg-white shadow-lg flex justify-between items-center px-6 w-full">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Talleres de cómputo
    </h1>

    <select id="filtroEstado" class="border rounded p-2 shadow-lg">
        <option value="todos">Todos</option>
        <option value="Libre">Libre</option>
        <option value="Apartado">Apartado</option>
        <option value="No disponible">No disponible</option>
    </select>
</header>

<main class="p-6">
    <div class="flex flex-wrap justify-center gap-6">
        <?php foreach ($talleres as $t): ?>
            <section 
                class="card 
                       w-full 
                       sm:w-[48%] 
                       md:w-[31%] 
                       lg:w-[23%] 
                       xl:w-[19%] 
                       bg-white rounded-xl shadow-md p-5 space-y-2 hover:shadow-xl transition"
                data-estado="<?= $t['estado'] ?>">
                
                <h2 class="text-xl font-semibold text-gray-800 text-center tracking-wide">
                    <?= htmlspecialchars($t['nombreSala']) ?>
                </h2>

                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-700">ID Taller:</span>
                    <?= $t['idTaller'] ?>
                </div>

                <div class="text-sm text-gray-600">
                    <span class="font-medium text-gray-700">Computadoras:</span>
                    <?= $t['cantidadComputadoras'] ?>
                </div>

                <div class="text-sm">
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
