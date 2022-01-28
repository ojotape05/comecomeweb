<?php
include_once 'header.php';
?>

<?php

// iniciar sessão
session_start();

// conexao com bd;
require_once 'bd_conectar.php';

// Verificar login
if(!isset($_SESSION['logado'])):
	header('Location: index.php');
endif;

$id = $_SESSION['id_usuario'];
$sql = "SELECT * FROM usuario WHERE codusu = $id";
$resultado = pg_query($connect,$sql);
$dados_logado = pg_fetch_assoc($resultado);

// dados usuário
if(!empty( $_GET['id_usuario'])):
	$id_usuario = $_GET['id_usuario'];
	$sql = "SELECT * FROM usuario WHERE codusu = '$id_usuario'";
	$resultado = pg_query($connect, $sql);
	$dados = pg_fetch_assoc($resultado);
else:
	header('Location: home.php');
endif;
?>

<body>
	
	<header>
		<nav class="#fbc02d yellow darken-2" role="navigation">
		<div class="nav-wrapper container"><a id="logo-container" href="home.php" class="brand-logo left">ComeCome</a>
		  <ul class="right">
			<li><a href="post.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> add_circle </i> </a> </li>
			<li><a href="perfil.php?id_usuario=<?php $meuperfil = true; echo $id.'&meuperfil='.$meuperfil;?>" class="btn-floating"> <img class="circle z-depth-2 navperfilogo" src="<?php echo $dados_logado['imagem']; ?>"> </a> </li>
			<li><a href="logout.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> stop </i> </a> </li>
		  </ul>
		</div>
		</nav>
	</header>
	
	<main>	
		<div name="conteudo" class="container row">
		
			<?php
			$meuperfil = $_GET['meuperfil'];
			if($meuperfil):
				echo "<div class='fotonome'>
						<ul>
							<a href='editarperfil.php?id_usuario=$id_usuario' class='btn-floating #f57f17 yellow darken-4'> <i class= 'material-icons'> create </i> </a>
							<a href='deleteperfil.php?id_usuario=$id_usuario' class='btn-floating red'> <i class= 'material-icons'> delete </i> </a>
						</ul>
					</div>";
			endif;
			
			?>
		
			<div id="perfilarea" class='col s12 #fbc02d yellow darken-2 z-depth-2'>
				<div class="white-text fotonome">
					<img class="circle z-depth-2" height='300px' width='300px' src="<?php echo $dados['imagem']; ?>">
					<h3 class='texto'> <?php echo $dados['nome']; ?></h3>
					<h5 class='texto'>
						<?php 
							$sql = "SELECT COUNT(*) AS seguidores FROM seguidos WHERE seguido = '$id_usuario'";
							$resultado = pg_query($connect, $sql);
							$num_seguidores = pg_fetch_assoc($resultado);
							echo "Seguidores: ".$num_seguidores['seguidores'];
							
							$sql = "SELECT COUNT(*) AS receitas FROM receita WHERE autor = '$id_usuario'";
							$resultado = pg_query($connect, $sql);
							$num_receitas = pg_fetch_assoc($resultado);
							echo " Receitas: ".$num_receitas['receitas']."</div>";
						?>
				</h5>
				<?php
				// FUNÇÃO DESCRIÇÃO
				
				$descricaoPerfil = $dados['sobre'];
				echo "<br><br><div id='divdescricao'>";
				echo "<label> Descrição de Perfil </label><br>";
				echo "<input id='descricao' type='text' value='$descricaoPerfil' name='descricao' readonly> </div>";
				
				
				//FUNÇÃO DO BUTTON
					$meuperfil = $_GET['meuperfil'];
					if(!$meuperfil):
					
						$sql = "SELECT seguido FROM seguidos WHERE seguindo = $id";
						$resultado = pg_query($connect, $sql);
						$seguido = pg_fetch_assoc($resultado);
						$seguido = $seguido['seguido'];
					
						if($seguido != $id_usuario):
							$server = $_SERVER['PHP_SELF'];
							echo "<form id='seguir' action='$server' method='GET'>
								<input type='hidden' name='id_usuario'value='$id_usuario'>
								<input type='hidden' name='meuperfil' value=''>
								<button type='submit' name='seguir'> SEGUIR </button>
								</form>";
							if(isset($_GET['seguir'])):
								
								$sql = "INSERT INTO seguidos(seguindo, seguido) values ('$id','$id_usuario')";
								$validacao = pg_query($connect, $sql);
								if($validacao):
									echo "<script>
									alert('Você agora segue esse perfil')
									window.location.reload();
									</script>";
								endif;
							endif;
						else:
							$server = $_SERVER['PHP_SELF'];
							echo "<form id='seguir' action='$server' method='GET'>
								<input type='hidden' name='id_usuario'value='$id_usuario'>
								<input type='hidden' name='meuperfil' value=''>
								<button type='submit' name='unfollow'> DEIXAR DE SEGUIR </button>
								</form>";
							if(isset($_GET['unfollow'])):
								
								$sql = "DELETE FROM seguidos WHERE seguindo = $id AND seguido = $id_usuario";
								$validacao = pg_query($connect, $sql);
								if($validacao):
									echo "<script>
									alert('Você deixou de seguir esse perfil')
									window.location.reload();
									</script>";
								endif;
							endif;
						endif;
					endif;
				?>	
			</div>
			
			<div>
				<?php
				if($meuperfil):
					echo "<label class='receitade'> MINHAS RECEITAS: </label>";
				else:
					$nome = $dados['nome'];
					$nome = strtoupper($nome);
					echo "<label class='receitade'> RECEITAS DE $nome: </label>";
				endif;
				
				?>
				<table>
				<tr>
				<?php
					$sql = "SELECT codreceita FROM receita WHERE autor = '$id_usuario'";
					$resultado = pg_query($connect, $sql);
					$receitas = Array();
					while ($row = pg_fetch_assoc($resultado)):
						$receitas[] = $row['codreceita'];
					endwhile;
					$n = 0;
					if(count($receitas) > 0):
						while($n < count($receitas)):
							$receita = $receitas[$n];
							
							// selecionando os dados da receita
							$sql = "SELECT * FROM receita WHERE codreceita = '$receita'";
							$resultado = pg_query($connect, $sql);
							$dados_receita = pg_fetch_assoc($resultado);
							
							$id_receita = $dados_receita['codreceita'];
							$imagem_receita = $dados_receita['imagem'];
							
							echo "<td> <a href='receita.php?id_receita=$id_receita'><img class='minhasreceitas' src='$imagem_receita'></a></td>";
							$n = $n + 1;
						endwhile;
					else:
						echo "<td> Esse usuário não tem receitas postadas </td>";
					endif;
				?> </tr>
				</table>
				
				
			
				
			</div>
			
			<div>
				<label class='receitade'> FAVORITADAS: </label>

				<table>
				<tr>
				<?php
					$sql = "SELECT codreceita FROM favorito WHERE codusu = '$id_usuario'";
					$resultado = pg_query($connect, $sql);
					$receitasFav = Array();
					while ($rowFav = pg_fetch_assoc($resultado)):
						$receitasFav[] = $rowFav['codreceita'];
					endwhile;
				
					$n = 0;
					if(count($receitasFav) > 0):
						while($n < count($receitasFav)):
							$receita = $receitasFav[$n];
							
							// selecionando os dados da receita
							$sql = "SELECT * FROM receita WHERE codreceita = '$receita'";
							$resultado = pg_query($connect, $sql);
							$dados_receita = pg_fetch_assoc($resultado);
							
							$id_receita = $dados_receita['codreceita'];
							$imagem_receita = $dados_receita['imagem'];
							
							echo "<td> <a href='receita.php?id_receita=$id_receita'><img class='minhasreceitas' src='$imagem_receita'></a></td>";
							$n = $n + 1;
						endwhile;
					else:
						echo "<td> Esse usuário não tem receitas favoritadas </td>";
					endif;
				?> </tr>
				</table>
				
			</div>
		</div>
			
		
		
	</main>
<?php
include_once 'footer.php';
?>
