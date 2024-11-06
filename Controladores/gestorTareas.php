<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Inicio la sesion
session_start();

// Incluyo las clases
require "../Clases/Tarea.php";
require "../Clases/ManagerTareas.php";
require "../Clases/Usuario.php";

// Incluyo también la configuración del php
require "../config.php";

// Si no hay una sesión activa, al login
if (!isset($_SESSION["idUsuario"])) {
    header("Location: ./login.php");
    exit();
}

// Deserializamos el objeto usuario
$usuario = unserialize($_SESSION["usuario"]);
// También obtengo el id del usuario
$idUsuario = $_SESSION["idUsuario"];

// Instancia de manager
$manager = new ManagerTareas();

// Verificamos el POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Amaceno los datos
    $tarea = $_POST["tarea"];
    $descrip = $_POST["descripcion"];
    $prioridad = $_POST["prioridad"];
    $fechaLim = $_POST["fechaLim"];

    if (!empty($tarea) && !empty($descrip) && !empty($prioridad) && !empty($fechaLim)) {
        // Verifico si se ha seleccionado crear una tarea
        if (isset($_POST["crearTarea"])) {
            $tarea = new Tarea($tarea, $descrip, $prioridad, $fechaLim);
            $manager->crearTarea($tarea);
        } else {
            $_SESSION["error"] = "Faltan datos para crear una tarea";
            header("Location: ./gestorTareas.php");
            exit();
        }
    }

    // Verificar si es edición
    if (isset($_POST["idTarea"])) {
        // Guardo índice en sesión
        $_SESSION["indice"] = $_POST["idTarea"];
        // Redirigir a edición
        header("Location: ../Controladores/editarTarea.php");
        exit();
    }

}
// Requiero la vista
require "../Vistas/view.gestorTareas.php";

// Depuración echo "ID del usuario en sesión: " . $_SESSION["idUsuario"];