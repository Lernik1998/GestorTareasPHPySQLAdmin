<?php

class ManagerTareas
{
    private $pdo;
    public function __construct()
    {
        require "../config.php";
        $this->pdo = $pdo;

    }

    // Crear una nueva tarea
    function crearTarea($tarea)
    {
        if (isset($_SESSION["idUsuario"])) {
            $idUsuario = $_SESSION["idUsuario"];
            try {

                // Guardar los valores en variables antes de usar bindParam
                $nombreTarea = $tarea->getNombre();
                $descripcionTarea = $tarea->getDescripcion();
                $prioridadTarea = $tarea->getPrioridad();
                $fechaLimite = $tarea->getFechaLim();

                $insertarTarea = $this->pdo->prepare("INSERT INTO tareas (nombre, descripcion, prioridad, fechaLimite, fk_Usuario) VALUES (:nombreTarea, :descripcionTarea, :prioridadTarea, :fechaLimite, :fkUsuario);");

                // Ahora pasamos las variables
                $insertarTarea->bindParam(":nombreTarea", $nombreTarea);
                $insertarTarea->bindParam(":descripcionTarea", $descripcionTarea);
                $insertarTarea->bindParam(":prioridadTarea", $prioridadTarea);
                $insertarTarea->bindParam(":fechaLimite", $fechaLimite);
                $insertarTarea->bindParam(":fkUsuario", $idUsuario);

                $insertarTarea->execute();

            } catch (PDOException $e) {
                echo "Error  " . $e->getMessage() . "";
            }
        } else {
            echo "Usuario no registrado";
        }

    }

    // Editar una tarea
    /*function editarTarea($idTarea, $nuevaTarea)
    {
        if ($_SESSION["idUsuario"]) {
            $idUsuario = $_SESSION["idUsuario"];
            echo "Entra";
            try {
                $nombreTarea = $nuevaTarea->getNombre();
                $descripcionTarea = $nuevaTarea->getDescripcion();
                $prioridadTarea = $nuevaTarea->getPrioridad();
                $fechaLimite = $nuevaTarea->getFechaLim();

                $updatearTarea = $this->pdo->prepare("UPDATE tareas SET nombre = :nombre, descripcion = :descripcion, prioridad = :prioridad, fechaLimite = :fechaLim WHERE id = :idTarea AND fk_Usuario = :fkUsuario;");

                $updatearTarea->bindParam(":nombre", $nombreTarea);
                $updatearTarea->bindParam(":descripcion", $descripcionTarea);
                $updatearTarea->bindParam(":prioridad", $prioridadTarea);
                $updatearTarea->bindParam(":fechaLim", $fechaLimite);
                $updatearTarea->bindParam(':idTarea', $idTarea); // Usar el ID de la tarea para actualizar
                $updatearTarea->bindParam(":fkUsuario", $idUsuario);// El que edita es el propietario

                $updatearTarea->execute();

            } catch (PDOException $e) {
                echo "Error " . $e->getMessage() . "";
            }

        } else {
            echo "Error: Usuario no autenticado. No se pudo editar la tarea.";
        }
    }*/

    // function editarTarea($idTarea, $nuevaTarea)
    // {
    //     if ($_SESSION["idUsuario"]) {
    //         $idUsuario = $_SESSION["idUsuario"];
    //         try {
    //             $nombreTarea = $nuevaTarea->getNombre();
    //             $descripcionTarea = $nuevaTarea->getDescripcion();
    //             $prioridadTarea = $nuevaTarea->getPrioridad();
    //             $fechaLimite = $nuevaTarea->getFechaLim();

    //             $updatearTarea = $this->pdo->prepare("UPDATE tareas SET nombre = :nombre, descripcion = :descripcion, prioridad = :prioridad, fechaLimite = :fechaLim WHERE id = :idTarea AND fk_Usuario = :fkUsuario;");

    //             $updatearTarea->bindParam(":nombre", $nombreTarea);
    //             $updatearTarea->bindParam(":descripcion", $descripcionTarea);
    //             $updatearTarea->bindParam(":prioridad", $prioridadTarea);
    //             $updatearTarea->bindParam(":fechaLim", $fechaLimite);
    //             $updatearTarea->bindParam(':idTarea', $idTarea);
    //             $updatearTarea->bindParam(":fkUsuario", $idUsuario);

    //             $updatearTarea->execute();

    //         } catch (PDOException $e) {
    //             echo "Error " . $e->getMessage();
    //         }
    //     }
    // }



    function editarTarea($idTarea, $nuevaTarea)
    {
        if (isset($_SESSION["idUsuario"])) {
            $idUsuario = $_SESSION["idUsuario"];
            try {
                $nombreTarea = $nuevaTarea->getNombre();
                $descripcionTarea = $nuevaTarea->getDescripcion();
                $prioridadTarea = $nuevaTarea->getPrioridad();
                $fechaLimite = $nuevaTarea->getFechaLim();

                $updatearTarea = $this->pdo->prepare("UPDATE tareas SET nombre = :nombre, descripcion = :descripcion, prioridad = :prioridad, fechaLimite = :fechaLim WHERE id = :idTarea AND fk_Usuario = :fkUsuario;");

                $updatearTarea->bindParam(":nombre", $nombreTarea);
                $updatearTarea->bindParam(":descripcion", $descripcionTarea);
                $updatearTarea->bindParam(":prioridad", $prioridadTarea);
                $updatearTarea->bindParam(":fechaLim", $fechaLimite);
                $updatearTarea->bindParam(':idTarea', $idTarea); // Usar el ID de la tarea para actualizar
                $updatearTarea->bindParam(":fkUsuario", $idUsuario);// El que edita es el propietario

                $updatearTarea->execute();

            } catch (PDOException $e) {
                echo "Error " . $e->getMessage();
            }
        } else {
            echo "Error: Usuario no autenticado. No se pudo editar la tarea.";
        }
    }




    // Obtener todas las tareas
    function obtenerTareas()
    {
        if (isset($_SESSION["idUsuario"])) {
            $selectTareas = $this->pdo->prepare('SELECT * FROM tareas WHERE fk_Usuario = :fkUsuario ORDER BY id DESC');
            $selectTareas->bindParam(':fkUsuario', $_SESSION["idUsuario"]);
            $selectTareas->execute();

            /*PDO::FETCH_ASSOC retorna un array asociativo en vez de objetos(Instancia de Tarea) */
            $tareasData = $selectTareas->fetchAll(PDO::FETCH_ASSOC);

            // Convierte el array de datos en objetos Tarea
            $arrayTareas = [];
            foreach ($tareasData as $tareaData) {
                $arrayTareas[] = new Tarea($tareaData['nombre'], $tareaData['descripcion'], $tareaData['prioridad'], $tareaData['fechaLimite'], $tareaData['id']);// Asignación del ID de la base de datos
            }

            return $arrayTareas;
        } else {
            echo "Usuario no identificado,no se han podido obtener las tareas de la base de datos.";
            return [];
        }

    }

    // Eliminar una tarea
    function eliminarTarea($indiceTarea)
    {

        if (isset(($_SESSION["idUsuario"]))) {

            try {
                $eliminarTarea = $this->pdo->prepare("DELETE FROM tareas WHERE id = :id");
                $eliminarTarea->bindParam(":id", $indiceTarea);
                $eliminarTarea->execute();
            } catch (PDOException $e) {
                echo "Error al eliminar tarea " . $e->getMessage() . "";
            }
        } else {
            echo "Usuario no identificado, no se ha podido borrar la tarea.";
        }

    }

}



