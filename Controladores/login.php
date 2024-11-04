<?php

// Requiero clases Usuario y ContrasenaInvalida
require "../Clases/Usuario.php";
require "../Clases/ContrasenaInvalidaException.php";
// Incluyo también la configuración del php
require "../config.php";

// Inicio la sesion
session_start();

// Requiero la vista del login
require "../Vistas/view.login.php";

// Si hay un post con los datos
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si los datos están establecidos
    if (isset($_POST["nombre"]) && isset($_POST["contrasenya"])) {
        // Guardamos los datos
        $nombre = $_POST["nombre"];
        $contra = $_POST["contrasenya"];

        // Validamos y creamos en usuario 
        try {
            // Verifico clave
            Usuario::validarClave($contra);

            // Busco en la BD si existe el usuario, en caso opuesto creo el usuario              
            $selectUsuarios = $pdo->prepare("SELECT * FROM usuarios WHERE nombre =:nombre");
            $selectUsuarios->bindParam(":nombre", $nombre);
            $selectUsuarios->execute();

            // Almaceno los resultados de la busqueda
            $resultadosUsuarios = $selectUsuarios->fetch(PDO::FETCH_ASSOC);

            /*Métodos seguros como password_hash() y password_verify() 
            para almacenar y comprobar contraseñas. LUEGO LO HARÉ CON ESTO!!!*/

            // Si hay algo en el array, significa que existe el usuario, verifico su contrasenya
            if ($resultadosUsuarios) {
                if ($contra == $resultadosUsuarios["contrasenya"]) {
                    // Creamos una instancia del usuario
                    $usuario = new Usuario($resultadosUsuarios["nombre"], $resultadosUsuarios["contrasenya"]);

                    // Guardamos en la sesión(id,nombre y contra en un obj)
                    $_SESSION["idUsuario"] = $resultadosUsuarios["id"];
                    $_SESSION["usuario"] = serialize($usuario);

                    // Redirección al gestor de tareas
                    header("Location: ./gestorTareas.php");
                    exit();
                } else {
                    // Si la contraseña no es correcta
                    $_SESSION["error"] = "Contraseña incorrecta!";
                    header("Location: ./login.php");
                    exit();
                }

            } else {
                // No existe el usuario, lo creamos e insertamos en la BD
                echo "<p style='color: red;'>Usuario no encontrado, se ha registrado el usuario introducido.</p>";

                $insertUsuario = $pdo->prepare("INSERT INTO usuarios (`id`, `nombre`, `contrasenya`) VALUES (NULL, :nombre, :contra);");
                $insertUsuario->bindParam(":nombre", $nombre);
                $insertUsuario->bindParam(":contra", $contra);
                $insertUsuario->execute();

                $_SESSION["error"] = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
                header("Location: ./login.php");
                exit();
            }

        } catch (ContrasenaInvalidaException $e) {
            // Guardo en mensaje de error en variable de sesion
            $_SESSION["error"] = $e->getMessage();

            // Redirecciono a la misma pagina
            header("Location: ./login.php");
            exit();
        }
    } else {
        $_SESSION["error"] = "Falta algún dato!";
    }
}
