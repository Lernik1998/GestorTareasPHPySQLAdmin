<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

// Si que llega a obtener los datos actualizados
// echo "<pre>";
// print_r($arrayTareas);
// echo "</pre>";
// exit();


// Verifica si existe la tarea en ese índice, si no vuelvo al gestor
if (!isset($arrayTareas[$indice])) {
    header("Location: ./gestorTareas.php");
    exit();
}
// // Si existe, lo almaceno
$tarea = $arrayTareas[$indice];


// Continuación de editarTarea.php
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = $_POST["tarea"];
    $descripcion = $_POST["descripcion"];
    $prioridad = $_POST["prioridad"];
    $fechaLim = $_POST["fechaLim"];

    $ind = $arrayTareas[$indice]->getId(); // Asegúrate de tener un método getId() en tu clase Tarea

    if (isset($_POST["guardarCambios"])) {
        if (!empty($nombre) && !empty($descripcion)) {
            $tareaEditada = new Tarea($nombre, $descripcion, $prioridad, $fechaLim);
            $manager->editarTarea($ind, $tareaEditada);
            echo "Tarea editada"; // Para comprobar si se llega a este punto
            header("Location: ./gestorTareas.php");
            exit();
        } else {
            $_SESSION["error"] = "Faltan datos para editar la tarea";
        }
    } elseif (isset($_POST["borrarTarea"])) {
        $manager->eliminarTarea($ind);
        header("Location: ./gestorTareas.php");
        exit();
    }
}

require "../Vistas/view.editarTarea.php";




