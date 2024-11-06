<?php

// Requiero clases Usuario y ContrasenaInvalida
require "../Clases/Usuario.php";
require "../Clases/ContrasenaInvalidaException.php";
// Incluyo también la configuración del php
require "../config.php";

// Inicio la sesion
session_start();


// Si hay un post 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verifico si los datos están establecidos
    if (isset($_POST["nombre"]) && isset($_POST["contrasenya"])) {
        // Guardo los datos
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

            // Almaceno los resultados de la busqueda en []
            $resultadosUsuarios = $selectUsuarios->fetch(PDO::FETCH_ASSOC);


            /*Métodos seguros como password_hash("contraDelUsuario,MétodoCifrado,Opciones)
            Opciones --> Opciones de cifrado(Dimensión de cifrado).
            Y password_verify() para almacenar y comprobar contraseñas.*/

            // Si hay algo en el [], significa que existe el usuario
            if ($resultadosUsuarios) {

                // Verifico su contraseña HASH
                if (password_verify($contra, $resultadosUsuarios["contrasenya"])) {
                    // Creamos una instancia del usuario
                    $usuario = new Usuario($resultadosUsuarios["nombre"], $resultadosUsuarios["contrasenya"]);

                    // Guardamos en la sesión(id & nombre y contra en un obj)
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

                // Encripto la contra antes de insertarlo 
                $contra = password_hash($_POST["contrasenya"], PASSWORD_DEFAULT);
                // Intentamos ejecutar la inserción en la base de datos
                try {
                    // Se podría gestionar desde la clase Usuario, pero al tener poca interracción lo hago aquí
                    $insertUsuario = $pdo->prepare("INSERT INTO usuarios (`id`, `nombre`, `contrasenya`) VALUES (NULL, :nombre, :contra);");
                    $insertUsuario->bindParam(":nombre", $nombre);
                    $insertUsuario->bindParam(":contra", $contra);
                    $insertUsuario->execute(); // Ejecutar la consulta

                    // Informo y redirecciono al login si todo salió bien
                    $_SESSION["error"] = "Usuario registrado exitosamente. Ahora puedes iniciar sesión.";
                    header("Location: ./login.php");
                    exit();
                } catch (PDOException $e) {
                    // Captura errores relacionados con la base de datos
                    $_SESSION["error"] = "Error al registrar el usuario: " . $e->getMessage();
                    header("Location: ./login.php");
                    exit();
                }
            }

        } catch (ContrasenaInvalidaException $e) {
            // Guardo en mensaje de error en variable de sesion
            $_SESSION["error"] = $e->getMessage();

            // Redirecciono a la misma pagina
            header("Location: ./login.php");
            exit();
        }
    }
}

// Requiero la vista del login
require "../Vistas/view.login.php";
