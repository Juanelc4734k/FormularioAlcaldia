<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "alcaldiaformulario";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$nombres = ["Juan", "María", "Carlos", "Ana", "Pedro", "Laura", "José", "Sandra", "Luis", "Carmen"];
$apellidos = ["García", "Rodríguez", "López", "Martínez", "González", "Pérez", "Sánchez", "Ramírez", "Torres", "Díaz"];
$veredas = ["El Olival", "La Quiebra", "El Churimo", "La Trinidad", "El Gavilar", "San Antonio", "El Tablazo", "La Granja"];
$situaciones = ["poblacion_victima", "afro", "discapacitado", "indigena", "adulto_mayor", "joven", "poblacion_lgbt", "servidor_publico"];
$generos = ["masculino", "femenino", "otro"];

for ($i = 0; $i < 50; $i++) {
    $nombre = $nombres[array_rand($nombres)];
    $apellido = $apellidos[array_rand($apellidos)];
    $email = strtolower($nombre . "." . $apellido . rand(1, 999) . "@email.com");
    $telefono = "3" . rand(100000000, 999999999);
    $genero = $generos[array_rand($generos)];
    $vereda = $veredas[array_rand($veredas)];
    $situacion = $situaciones[array_rand($situaciones)];
    $autorizacion = 1;

    $sql = "INSERT INTO registros (nombre, apellido, email, telefono, genero, vereda, municipio, situacion, autorizacion) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $municipio = "Montebello";
    $stmt->bind_param("sssssssss", 
        $nombre,
        $apellido, 
        $email,
        $telefono,
        $genero,
        $vereda,
        $municipio,
        $situacion,
        $autorizacion
    );

    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "Se han generado 50 registros de prueba exitosamente.";
?> 