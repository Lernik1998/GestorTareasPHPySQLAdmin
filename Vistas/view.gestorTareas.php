<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Obtengo las tareas y las guardo en el array tareas
$arrayTareas = $manager->obtenerTareas();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de tareas</title>
    <link rel="stylesheet" href="../Assets/CSS/general.css">
    <link rel="stylesheet" href="../Assets/CSS/gestorTareas.css">

</head>

<body>
    <div class="container">
        <h1>Gestor de tareas de <?php echo $usuario->getNombre(); ?></h1>

        <div class="task-form">
            <form action="" method="post">
                <label for="tarea">Tarea</label>
                <input type="text" name="tarea" id="tarea">
                <br><br>
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion"></textarea>
                <br><br>
                <label for="prioridad">Prioridad</label>
                <select name="prioridad" id="prioridad">
                    <option value="baja">Baja</option>
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                </select>
                <br><br>
                <label for="fechaLim">Fecha límite</label>
                <input type="date" name="fechaLim" id="fechaLim">
                <br>
                <button type="submit" name="crearTarea">Crear tarea</button>
                <br>
            </form>

            <br>

            <?php if (isset($_SESSION["error"])): ?>
                <div class="error-message">
                    <p style="color: red;"><?php echo $_SESSION["error"];
                    unset($_SESSION["error"]); ?></p>
                </div>
            <?php endif; ?>

            <form action="./login.php" method="post">
                <button type="submit">Volver al inicio</button>
            </form>
        </div>

        <div class="task-list">
            <h1>Visualizar tareas</h1>

            <!-- Si está vacio, saca mensaje -->
            <?php if (empty($arrayTareas)): ?>
                <h3>No hay tareas creadas</h3>
            <?php else: ?>
                <!-- Si no,recorre el array -->
                <?php foreach ($arrayTareas as $index => $tarea): ?>
                    <div class="task-item">
                        <h2><?php echo htmlspecialchars($tarea->getNombre()); ?></h2>
                        <p>Descripción: <?php echo htmlspecialchars($tarea->getDescripcion()); ?></p>
                        <p>Prioridad: <?php echo htmlspecialchars($tarea->getPrioridad()); ?></p>
                        <p>Fecha Límite: <?php echo htmlspecialchars($tarea->getFechaLim()); ?></p>

                        <!-- Formulario de edición -->
                        <form action="" method="post">
                            <!-- Paso el indice de la tarea de manera oculta -->
                            <input type="hidden" name="idTarea" value="<?php echo htmlspecialchars($index) ?>">
                            <button type="submit">Editar</button>
                        </form>

                        <!-- Depurando -->
                        <!-- < ?php echo "El indice de la tarea en el ARRAY: " . htmlspecialchars($index) ?> -->
                        <!-- < ?php echo "El indice de la tarea en el ARRAY: " . htmlspecialchars($tarea->getId()) ?> -->

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</body>

</html>