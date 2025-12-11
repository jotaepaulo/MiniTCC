<?php
include "../processos/config.php";

$usuario  = $_POST['usuario'];
$episodio = $_POST['episodio'];
$tempo    = $_POST['tempo'];


$sql = "SELECT * FROM assistidos 
        WHERE usuario_id = $usuario AND episodio_id = $episodio";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    

    $sql = "UPDATE assistidos 
            SET tempo_assistido = $tempo, 
                ultima_visualizacao = NOW() 
            WHERE usuario_id = $usuario AND episodio_id = $episodio";

} else {


    $sql = "INSERT INTO assistidos (usuario_id, episodio_id, tempo_assistido)
            VALUES ($usuario, $episodio, $tempo)";
}

$conn->query($sql);
