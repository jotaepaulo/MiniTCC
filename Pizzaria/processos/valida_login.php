<?php
session_start();
require '../processos/config.php';

$email = $_POST['email'];
$senha = md5($_POST['senha']); 


function verificarLogin($conn, $tabela, $email, $senha) {
    $sql = "SELECT id, nome FROM $tabela WHERE email = ? AND senha = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    return $stmt->get_result();
}


$usuario = verificarLogin($conn, "usuario", $email, $senha);

if ($usuario->num_rows > 0) {
    $u = $usuario->fetch_assoc();

    $_SESSION['logado'] = true;
    $_SESSION['id'] = $u['id'];
    $_SESSION['nome'] = $u['nome'];
    $_SESSION['tipo_conta'] = 'usuario';

    header("Location: painel_admin.php");
    exit;
}


$cliente = verificarLogin($conn, "cliente", $email, $senha);

if ($cliente->num_rows > 0) {
    $c = $cliente->fetch_assoc();

    $_SESSION['logado'] = true;
    $_SESSION['id'] = $c['id'];
    $_SESSION['nome'] = $c['nome'];
    $_SESSION['tipo_conta'] = 'cliente';

    header("Location: ../paginas/principal.php");
    exit;
}


$_SESSION['erro'] = "Usuário ou senha inválidos";
header("Location: ../paginas/login.php");
exit;
?>
