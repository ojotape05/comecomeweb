<?php
include_once 'header.php';

// conexao com bd;
require_once 'bd_conectar.php';

// iniciar sessão
session_start();

// resetando o metodo post
$_SESSION['post'] = false;

// Verificar login
if(!isset($_SESSION['logado'])):
	header('Location: index.php');
endif;

$id = $_SESSION['id_usuario'];
$sql = "SELECT * FROM usuario WHERE codusu = $id";
$resultado = pg_query($connect, $sql);
$dados = pg_fetch_assoc($resultado);

//VALIDANDO COZINHEIROS
if(!empty($_GET['cozinheiros'])):
	$cozinheiros = $_GET['cozinheiros'];
else:
	$cozinheiros = false;
endif;

//VALIDANDO SEGUIDOS
if(!empty($_GET['seguidos'])):
	$seguidos = $_GET['seguidos'];
else:
	$seguidos = false;
endif;
?>
<header>
	<nav class="#fbc02d yellow darken-2" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="home.php" class="brand-logo left">ComeCome</a>
      <ul class="right">
        <li><a href="post.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> add_circle </i> </a> </li>
		<li><a href="perfil.php?id_usuario=<?php $meuperfil = true; echo $id.'&meuperfil='.$meuperfil;?>" class="btn-floating"> <img class="circle z-depth-2 navperfilogo" src="<?php echo $dados['imagem']; ?>"> </a> </li>
		<li><a href="logout.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> stop </i> </a> </li>
      </ul>
    </div>
    </nav>
</header>

<main>
	<?php
	if(!$seguidos):
		echo "<div id='divpesquisa' class='nav-wrapper container z-depth-1'>";
		if($cozinheiros):
			$servidor = $_SERVER['PHP_SELF'];
			echo "<form action='$servidor' method='GET'>
					<div class='input-field'>
					  <input name='pesquisa' type='search' required>
					  <label class='label-icon' for='search'><i class='material-icons'>search</i></label>
					  <i class='material-icons'>close</i>
					</div>
					<input name='cozinheiros' type='hidden' value='true'>
				  </form>";
		else:
			$servidor = $_SERVER['PHP_SELF'];
			echo "<form action='$servidor' method='GET'>
					<div class='input-field'>
					  <input name='pesquisa' type='search' required>
					  <label class='label-icon' for='search'><i class='material-icons'>search</i></label>
					  <i class='material-icons'>close</i>
					</div>
					<input name='cozinheiros' type='hidden' value=''>
				  </form>";
		endif;
		echo "</div>";
	endif;
    
	?>
	
	<div class="row container">
	<?php
	
	if(empty($_GET['pesquisa'])):
	
		if($seguidos): // TELA SEGUIDOS
			echo 
			"<div id='alternarpaginas' class='container collection'>
				<a href='home.php?' class='collection-item'>RECEITAS</a>
				<a href='home.php?cozinheiros=true' class='collection-item'>COZINHEIROS</a>
				<a href='home.php?seguidos=true' class='collection-item active'>SEGUIDOS</a>
			</div>";
			
			$sql = "SELECT * FROM seguidos WHERE seguindo = $id";
			$resultado = pg_query($connect, $sql);
			$seguir = Array();
			while ($row = pg_fetch_assoc($resultado)):
				$seguir[] = $row['seguido'];
			endwhile;
			
			if(count($seguir)>0):
				$n = 0;
				while($n < count($seguir)):
					$idseguido = $seguir[$n];
					
					// selecionando os dados da receita
					$sql = "SELECT * FROM receita WHERE autor = '$idseguido'";
					$resultado = pg_query($connect, $sql);
					$dados_receita = pg_fetch_assoc($resultado);
					
					// selecionando os dados do criador do post receita
					$donodopost = $dados_receita['autor'];
					$sql= "SELECT * FROM usuario WHERE codusu = '$donodopost'";
					$resultado = pg_query($connect, $sql);
					$dados_donodopost = pg_fetch_assoc($resultado);
					
					// atribuindo valores para melhor escrita e entendimento
					
					//valores do usuario dono do post:
					$id_donodopost = $dados_donodopost['codusu'];
					$foto_donodopost = $dados_donodopost['imagem'];
					$nome_donodopost = $dados_donodopost['nome'];
					
					//valores da receita:
					$id_receita = $dados_receita['codreceita'];
					$imagem = $dados_receita['imagem'];
					$nome_receita = $dados_receita['nomerec'];
					$descricao =  $dados_receita['sobre'];
					
					//verificando se id_donodopost == id_usuario: (para colocar no link da foto de perfil)
					if($id_donodopost != $id):
						$meuperfil = false;
					else:
						$meuperfil = true;
					endif;
					
					
					//exibindo as seleções na página home
					echo "<div class='row container postagem'>
								<div name='perfil'>
									<table>
									<tr id='perfil'>
										<td> <a href='perfil.php?id_usuario=$id_donodopost&meuperfil=$meuperfil'><img alt='Foto de Perfil' class='circle' height='90px' width='90px' src='$foto_donodopost'> </a></td>
										<td> $nome_donodopost </td>
									</tr>
									</table>
								</div>
								
								<div>
								  <div class='card medium' id='post'>
									<div class='card-image'>
									  <img class='responsive-img postshome' alt='$nome_receita' src='$imagem'>
									  <span class='card-title'>$nome_receita</span>
									</div>
									<div class='card-content'>
									  <p>Descrição:<br>$descricao</p>
									</div>
									<div class='card-action'>
									  <a href='receita.php?id_receita=$id_receita'>CLIQUE PARA CONFERIR</a>
									</div>
								  </div>
								</div>
						  </div>";
					$n = $n + 1;
				endwhile;
			else:
				echo "Você ainda não segue ninguém!";
			endif;
			
		else:

			if($cozinheiros): //TELA DE COZINHEIROS
			
				echo 
				"<div id='alternarpaginas' class='container collection'>
					<a href='home.php?' class='collection-item'>RECEITAS</a>
					<a href='home.php?cozinheiros=true' class='collection-item active'>COZINHEIROS</a>
					<a href='home.php?seguidos=true' class='collection-item'>SEGUIDOS</a>
				</div>";
				
				$sql = "SELECT DISTINCT autor FROM receita";
				$resultado = pg_query($connect, $sql);
				$ids = Array();
				while ($row = pg_fetch_assoc($resultado)):
					$ids[] = $row['autor'];
				endwhile;

				$n = 0;
				$id_qntd = Array(); //relação id com qntd de receitas do id
				while($n < count($ids)):
					$id_donodopost = $ids[$n];
					$sql = "SELECT COUNT(*) AS quantidade FROM receita WHERE autor = $id_donodopost";
					$num_receita = pg_fetch_assoc(pg_query($connect, $sql));
					$qntd_receita = $num_receita['quantidade'];
					$id_qntd = [
						$id_donodopost => $qntd_receita
					];
					$n = $n +1;
				endwhile;
				
				$n = 0;
				while($n < count($ids)):
					$id_donodopost = $ids[$n];

					if (pg_num_rows($resultado) > 0):
						$sql= "SELECT * FROM usuario WHERE codusu = '$id_donodopost'";
						$resultado = pg_query($connect, $sql);
						$dados_donodopost = pg_fetch_assoc($resultado);
						
						// atribuindo valores para melhor escrita e entendimento
						
						//valores do usuario dono de posts:
					
						$foto_donodopost = $dados_donodopost['imagem'];
						$nome_donodopost = $dados_donodopost['nome'];
						
						echo "<div class='cozinheiros'>
								<div name='perfil'>
									<td> <a href='perfil.php?id_usuario=$id_donodopost&meuperfil=$meuperfil'><img alt='Foto de Perfil' class='circle' height='120px' width='120px' src='$foto_donodopost'> </a></td>
									<br> <td> $nome_donodopost </td>
								</div>
							</div>";
					endif;
					
					$n = $n + 1;
				endwhile;
				
			else: //TELA DE RECEITAS
			
				echo 
				"<div id='alternarpaginas' class='container collection'>
					<a href='home.php?cozinheiros=false' class='collection-item active'>RECEITAS</a>
					<a href='home.php?cozinheiros=true' class='collection-item'>COZINHEIROS</a>
					<a href='home.php?seguidos=true' class='collection-item'>SEGUIDOS</a>
				</div>";

				$sql = "SELECT * FROM receita ORDER BY data DESC";
				$resultado = pg_query($connect, $sql);
				$receitas = Array();
				while ($row = pg_fetch_assoc($resultado)):
					$receitas[] = $row['codreceita'];
				endwhile;
				$n = 0;
				
				while($n < count($receitas)):
					$receita = $receitas[$n];
					
					// selecionando os dados da receita
					$sql = "SELECT * FROM receita WHERE codreceita = $receita";
					$resultado = pg_query($connect, $sql);
					$dados_receita = pg_fetch_assoc($resultado);
					
					// selecionando os dados do criador do post receita
					$donodopost = $dados_receita['autor'];
					$sql= "SELECT * FROM usuario WHERE codusu = $donodopost";
					$resultado = pg_query($connect, $sql);
					$dados_donodopost = pg_fetch_assoc($resultado);
					
					// atribuindo valores para melhor escrita e entendimento
					
					//valores do usuario dono do post:
					$id_donodopost = $dados_donodopost['codusu'];
					$foto_donodopost = $dados_donodopost['imagem'];
					$nome_donodopost = $dados_donodopost['nome'];
					
					//valores da receita:
					$id_receita = $dados_receita['codreceita'];
					$imagem = $dados_receita['imagem'];
					$nome_receita = $dados_receita['nomerec'];
					$descricao =  $dados_receita['sobre'];
					
					//verificando se id_donodopost == id_usuario: (para colocar no link da foto de perfil)
					if($id_donodopost != $id):
						$meuperfil = false;
					else:
						$meuperfil = true;
					endif;
					
					
					//exibindo as seleções na página home
					echo "<div class='row container postagem'>
								<div name='perfil'>
									<table>
									<tr id='perfil'>
										<td> <a href='perfil.php?id_usuario=$id_donodopost&meuperfil=$meuperfil'><img alt='Foto de Perfil' class='circle' height='90px' width='90px' src='$foto_donodopost'> </a></td>
										<td> $nome_donodopost </td>
									</tr>
									</table>
								</div>
								
								<div>
								  <div class='card medium' id='post'>
									<div class='card-image'>
									  <img class='responsive-img postshome' alt='$nome_receita' src='$imagem'>
									  <span class='card-title'>$nome_receita</span>
									</div>
									<div class='card-content'>
									  <p>Descrição:<br>$descricao</p>
									</div>
									<div class='card-action'>
									  <a href='receita.php?id_receita=$id_receita'>CLIQUE PARA CONFERIR</a>
									</div>
								  </div>
								</div>
						  </div>";
					$n = $n + 1;
				endwhile;	
				
			endif;
		endif;
	//////////// BARRA DE PESQUISA ///////////////
	else:
		
		if($cozinheiros):
			// pesquisa cozinheiros

			$pesquisa = $_GET['pesquisa'];
			
			$sql = "SELECT * FROM usuario WHERE nome ILIKE '%".$pesquisa."%'";
			$resultado = pg_query($connect, $sql);
			while ($row = pg_fetch_assoc($resultado)):
				$usuarios[] = $row['codusu'];
			endwhile;
			
			$n = 0;
			if(pg_num_rows($resultado) > 0):
				while($n < count($usuarios)):
					
					$id_donodopost = $usuarios[$n];
					
					//verificando se id_donodopost == id_usuario: (para colocar no link da foto de perfil)
					if($id_donodopost != $id):
						$meuperfil = false;
					else:
						$meuperfil = true;
					endif;
					
					$sql = "SELECT * FROM usuario WHERE codusu = $id_donodopost";
					$resultado = pg_query($connect, $sql);
					$dados_usuarios = pg_fetch_assoc($resultado);
					
					$foto_donodopost = $dados_usuarios['imagem'];
					$nome_donodopost = $dados_usuarios['nome'];
					
					echo "<div class='row container'>
							<div name='perfil' class='col s6 offset-s5'>
								<td> <a href='perfil.php?id_usuario=$id_donodopost&meuperfil=$meuperfil'><img alt='Foto de Perfil' class='circle' height='120px' width='120px' src='$foto_donodopost'> </a></td>
								<td> $nome_donodopost </td>
							</div>
					  </div>";
					
					$n = $n + 1;
				endwhile;
			else:
				echo "Nenhum resultado para a pesquisa";
			endif;
			

		else:
		
			$pesquisa = $_GET['pesquisa'];
			$resultado = pg_query($connect,"SELECT codreceita FROM receita WHERE nomerec ILIKE '%".$pesquisa."%' ORDER BY data");
			while ($row = pg_fetch_assoc($resultado)):
				$receitas[] = $row['codreceita'];	
			endwhile;
			$n = 0;
			
			if(pg_num_rows($resultado)>0):
				while($n < count($receitas)):
					$receita = $receitas[$n];

					// selecionando os dados da receita
					$sql = "SELECT * FROM receita WHERE codreceita = '$receita'";
					$resultado = pg_query($connect, $sql);
					$dados_receita = pg_fetch_assoc($resultado);

					// selecionando os dados do criador do post receita
					$donodopost = $dados_receita['autor'];
					$sql= "SELECT * FROM usuario WHERE codusu = '$donodopost'";
					$resultado = pg_query($connect, $sql);
					$dados_donodopost = pg_fetch_assoc($resultado);

					// atribuindo valores para melhor escrita e entendimento

					//valores do usuario dono do post:
					$id_donodopost = $dados_donodopost['codusu'];
					$foto_donodopost = $dados_donodopost['imagem'];
					$nome_donodopost = $dados_donodopost['nome'];

					//valores da receita:
					$id_receita = $dados_receita['codreceita'];
					$imagem = $dados_receita['imagem'];
					$nome_receita = $dados_receita['nomerec'];
					$descricao =  $dados_receita['sobre'];

					//verificando se id_donodopost == id_usuario: (para colocar no link da foto de perfil)
					if($id_donodopost != $id):
						$meuperfil = false;
					else:
						$meuperfil = true;
					endif;


					//exibindo as seleções na página home
					echo "<div class='row container postagem'>
								<div name='perfil'>
									<table>
									<tr id='perfil'>
										<td> <a href='perfil.php?id_usuario=$id_donodopost&meuperfil=$meuperfil'><img alt='Foto de Perfil' class='circle' height='90px' width='90px' src='$foto_donodopost'> </a></td>
										<td> $nome_donodopost </td>
									</tr>
									</table>
								</div>
								
								<div>
								  <div class='card medium' id='post'>
									<div class='card-image'>
									  <img class='responsive-img postshome' alt='$nome_receita' src='$imagem'>
									  <span class='card-title'>$nome_receita</span>
									</div>
									<div class='card-content'>
									  <p>Descrição:<br>$descricao</p>
									</div>
									<div class='card-action'>
									  <a href='receita.php?id_receita=$id_receita'>CLIQUE PARA CONFERIR</a>
									</div>
								  </div>
								</div>
						  </div>";
					$n = $n + 1;

				endwhile;
			else:
				echo "Nenhum resultado para a pesquisa";
			endif;
					
		endif;
		
	endif;
?>
	</div>
</main>


<?php
include_once 'footer.php';
?>
