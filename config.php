<?php
// Archivo encargado de establecer la conexión con PhpMyAdmin
$host = 'localhost';
$dbname = 'GestorDeTareas';
$username = 'root';
$password = '';

// Verifico conexión
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
