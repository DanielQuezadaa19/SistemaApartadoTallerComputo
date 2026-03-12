<?php
session_start();

require_once __DIR__ . "/../../../db/Database.php";

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
    <title>Talleres</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200">

<header class="bg-white shadow-lg flex justify-between items-center px-6 w-full">
    <h1 class="text-blue-600 border-b p-5 text-3xl font-bold">
        Talleres de Cómputo
    </h1>

    
    <select id="filtroEstado" class="border rounded p-2 shadow-lg">
        <option value="todos">Todos</option>
        <option value="Libre">Libre</option>
        <option value="Apartado">Apartado</option>
        <option value="No disponible">No disponible</option>
    </select>
</header>

<main class="p-6">
    <div class="flex justify-end mr-6 mb-6">
        <a href="agregarTaller.php" class="bg-blue-500 hover:bg-blue-700 text-center font-semibold text-white rounded-lg shadow p-2">Nuevo taller</a>
    </div>
    <div class="flex flex-wrap justify-center gap-6">
        <?php foreach ($talleres as $t): ?>
            
            <?php 
                $noFuncionando = max(0, $t['totalComputadoras'] - $t['computadorasFuncionando']);
                $porcentaje = $t['totalComputadoras'] > 0 
                    ? ($t['computadorasFuncionando'] / $t['totalComputadoras']) * 100 
                    : 0;
            ?>

            <section 
                class="card 
                       w-full 
                       sm:w-[48%] 
                       md:w-[31%] 
                       lg:w-[23%] 
                       xl:w-[19%] 
                       bg-white rounded-xl shadow-md p-5 space-y-3 hover:shadow-xl transition"
                data-estado="<?= $t['estado'] ?>">
                
                <h2 class="text-xl font-semibold text-gray-800 text-center tracking-wide">
                    <?= htmlspecialchars($t['nombreSala']) ?>
                </h2>

                <div class="text-sm text-gray-600 text-center">
                    <span class="font-medium">ID:</span>
                    <?= $t['idTaller'] ?>
                </div>

                
                <div class="space-y-1 text-sm">

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



          
                <div class="text-sm text-center mt-2">
                    <span class="font-medium text-gray-700">Estado:</span>
                    <span class="font-semibold
                        <?= $t['estado'] === 'Libre' ? 'text-green-500' : '' ?>
                        <?= $t['estado'] === 'Apartado' ? 'text-yellow-500' : '' ?>
                        <?= $t['estado'] === 'No disponible' ? 'text-red-500' : '' ?>">
                        <?= $t['estado'] ?>
                    </span>
                </div>

                <div class="flex justify-evenly p-3 border-t border-gray-300">
                    <a href="editarTaller.php?idTaller=<?= $t['idTaller'] ?>" class="bg-yellow-400 p-2 font-semibold text-center  rounded-lg m-2 shadow text-white">Editar</a>
                    <a href="/sys_Taller_Computo/public/api/eliminarTaller.php?idTaller=<?= $t['idTaller'] ?>" class="bg-red-600 p-2 font-semibold text-center  rounded-lg m-2 shadow text-white">Eliminar</a>
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