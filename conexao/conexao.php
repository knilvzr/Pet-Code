<?php 

$hostname = "localhost";
$bancodedados = "login";
$usuario = "root";
$senha = "";
$table_name = "usuarios"; 

function conectar_banco() {
    global $hostname, $usuario, $senha, $bancodedados;
    
    $mysqli = new mysqli($hostname, $usuario, $senha, $bancodedados);

    if ($mysqli->connect_errno) {
        die("Falha ao conectar: (" . $mysqli->connect_errno . ")" . $mysqli->connect_error);
    }
    
    return $mysqli;
}
?>