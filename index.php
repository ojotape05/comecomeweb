<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
include_once 'header.php';
?>

<?php
	// conexao com bd;
	require_once 'bd_conectar.php';

	// iniciar sessão
	session_start();

	// botao enviar
	if(isset($_POST['logar'])): //chegando se o usuário clicou em Enviar
		$erros = Array();
		
		$login = filter_input(INPUT_POST,'email',FILTER_SANITIZE_EMAIL);
		$senha = pg_escape_string($connect, $_POST['senha']);
		
		if(empty($login) or empty($senha)):
			$erros[] = "<script>alert('O campo login/senha precisa ser preenchido');</script>";
		else:
			$sql = "SELECT email FROM usuario WHERE email = '$login'";
			$resultado = pg_query($connect, $sql);
			
			if(pg_num_rows($resultado) > 0):
				
				$senha = md5($senha);
				$sql = "SELECT * FROM usuario WHERE email = '$login' AND senha = '$senha'";
				$resultado = pg_query($connect,$sql);
				
				if (pg_num_rows($resultado) == 1):
					$dados = pg_fetch_assoc($resultado); //transformando o resultado sql em um array para $dados
					pg_close();
					$_SESSION['logado'] = true;
					$_SESSION['id_usuario'] = $dados['codusu'];
					header('Location: home.php');
				else:
					$erros[] = "<script>alert('Usuário e senha não conferem');</script>";
				endif;
				
			else:
				$erros[] = "<script>alert('Usuário inexiste');</script>";
			endif;
		endif;
		
		if(!empty($erros)):
			foreach($erros as $erro):
				echo $erro;
			endforeach;
		endif;
		
	endif;
?>
	<header>
		<nav class="#fbc02d yellow darken-2" role="navigation">
			<div class="nav-wrapper container"><a id="logo-container" href="home.php" class="brand-logo left">ComeCome</a>
			  <ul class="right">
				<li><a href="cadastrar.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> subdirectory_arrow_left </i> </a> </li>
			  </ul>
			</div>
		</nav>
	</header>
	
	<main>
		<div class="row container"> 
			<form class="col s12"action= "<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<h1 align="center"> Login </h1>
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="Email" name="email" type="email" class="validate">
					</div>
				</div>
				<div class="row">
					<div class="input-field col s6 offset-s3">
					  <input placeholder="Senha" name="senha" type="password" class="validate">
					</div>
				</div>
				<button type="submit" name="logar" class="col s6 offset-s3 btn waves-effect #f57f17 yellow darken-4">
				Enviar  <i class="material-icons right">send</i> </button>
			</form>
		</div>
	</main>

<?php
include_once 'footer.php';
?>
