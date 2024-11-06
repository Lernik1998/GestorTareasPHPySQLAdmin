<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Inicio la sesión
session_start();

// Incluyo los modelos
require "../Clases/Tarea.php";
require "../Clases/ManagerTareas.php";


// Verificar que se envió un índice 
if (!isset($_SESSION["indice"])) {
    header("Location: ./gestorTareas.php");
    exit();
}

// Almaceno un indice
$indice = $_SESSION["indice"];


// Instancia de ManagerTareas para obtener el array de tareas con el metodo obtenerTareas
$manager = new ManagerTareas();
$arrayTareas = $manager->obtenerTareas();

// Verifica si existe la tarea en ese índice, si no vuelvo al gestor
if (!isset($arrayTareas[$indice])) {
    header("Location: ./gestorTareas.php");
    exit();
}
// Si existe, lo almaceno
$tarea = $arrayTareas[$indice];

// Si recibimos un POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Almaceno las variables
    $nombre = $_POST["tarea"];
    $descripcion = $_POST["descripcion"];
    $prioridad = $_POST["prioridad"];
    $fechaLim = $_POST["fechaLim"];

    $ind = $arrayTareas[$indice]->getId(); // Obtengo el id de Tarea creada y lo almaceno en ind (indice)

    // Si se ha seleccionado guardar 
    if (isset($_POST["guardarCambios"])) {
        if (!empty($nombre) && !empty($descripcion)) {
            // Creo la nueva tarea
            $tareaEditada = new Tarea($nombre, $descripcion, $prioridad, $fechaLim);
            $manager->editarTarea($ind, $tareaEditada);

            // Notifico al cliente o (Depuro)
            echo "Tarea editada"; 
            header("Location: ./gestorTareas.php");
            exit();
        } else {
            $_SESSION["error"] = "Faltan datos para editar la tarea";
        }
        // Si se ha seleccionado borrarTarea
    } elseif (isset($_POST["borrarTarea"])) {
        // Indico el indice 
        $manager->eliminarTarea($ind);
        header("Location: ./gestorTareas.php");
        exit();
    }
}

// Solicito las vistas
require "../Vistas/view.editarTarea.php";