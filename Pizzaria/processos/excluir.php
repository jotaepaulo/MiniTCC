<?php
require '../processos/config.php';

$id = intval($_GET['id']); 

$sqlUsuario = "SELECT id FROM usuario WHERE id = $id LIMIT 1";
$resultUsuario = $conn->query($sqlUsuario);

if ($resultUsuario->num_rows > 0) {
 
    $delete = "DELETE FROM usuario WHERE id = $id";
    if ($conn->query($delete) === TRUE) {
        header("Location: ../paginas/listagem_func.php");
        exit;
    } else {
        echo "Erro ao excluir usuário: " . $conn->error;
    }

}else {
        echo "ID não encontrado em nenhuma tabela.";
    }

