<?php session_start();


?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Login - Sistema</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="../css/styleDados.css">
<body>
    
<form class = "login-form" action="../processos/valida_login.php" method="POST">
    <div class="logo-container">
    <img src="../img/LOGO.png" class="logo" alt="Logo">
</div>
<h4 class="text-center mb-3 text-white">Entrar</h4>
<h6 class="text-center mb-3 text-white">Digite Email e Senha para entrar!</h6>

<input type="email" name="email" class="form-control mb-2" placeholder="E-mail" require>

<input type="password" name="senha" class="form-control mb-2" placeholder="Senha" require>

<button onclick="" type="submit" class="btn-cadastrar">Entrar</button>



<br>

Ainda não possui uma conta?<a href="cadastrar.php">Cadastrar</a>
<?php
if(isset($_SESSION['erro'])):
?>
<div class="text-danger text-center mt-3">
<?=
$_SESSION['erro']; unset($_SESSION['erro']);
?>
</div>
<?php endif; ?>

</form>
 
</body>
</html>
