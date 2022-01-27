<?php
include_once 'header.php';

// iniciar sessão
session_start();

// conexao com bd;
require_once 'bd_conectar.php';

// Verificar login
if(!isset($_SESSION['logado'])):
	header('Location: index.php');
endif;

$id = $_SESSION['id_usuario'];
$sql = "SELECT * FROM usuario WHERE codusu = '$id'";
$resultado = pg_query($connect, $sql);
$dados = pg_fetch_assoc($resultado);

if(!empty( $_GET['id_usuario'])):
	$id_usuario = $_GET['id_usuario'];
	if($id_usuario == $id):
		$resultado = pg_query($connect,"SELECT * FROM usuario WHERE codusu = '$id_usuario'");
		$dados_usuario = pg_fetch_assoc($resultado);
	else:
		header("Location: home.php");
	endif;
else:
	header("Location: home.php");
endif;
?>

<body>
	<header>
		<nav class="#fbc02d yellow darken-2" role="navigation">
		<div class="nav-wrapper container"><a id="logo-container" href="home.php" class="brand-logo left">ComeCome</a>
		  <ul class="right">
			<li><a href="perfil.php?id_usuario=<?php $meuperfil = true; echo $id.'&meuperfil='.$meuperfil;?>" class="btn-floating"> <img class="circle z-depth-2" height='50px' width='50px' src="<?php echo $dados['imagem']; ?>"> </a> </li>
			<li><a href="logout.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> stop </i> </a> </li>
		  </ul>
		</div>
		</nav>
	</header>
	
	<main>
		<div class="row container z-depth-2">
			<form class="col s12" action="<?php echo $_SERVER['PHP_SELF']."?id_usuario=$id_usuario"; ?>" method="POST" enctype="multipart/form-data">
				<?php
				if($resultado):
					echo "<script> alert('Alteraçõs no perfil feitas com sucesso!') </script>";
				endif;
				?>	
				<h1 align="center"> Editar perfil </h1>
				
				<div align="center">
					<img id="fotopreview" class="circle" height="200px" width="200px" src="<?php echo $dados['imagem']; ?>"><br>
					<label> Foto de Perfil </label> <br>
					<input id="uploadfoto" type="file" name="imagem">
				</div>
				
				<script>
					var uploadfoto = document.getElementById('uploadfoto');
					var fotopreview = document.getElementById('fotopreview');

					uploadfoto.addEventListener('change', function(e) { //adiciona o evento "change" no input
						showThumbnail(this.files); //chama a função showThumbnail utilizando os arquivos carregados pelo input
					});

					function showThumbnail(files) { 
						if (files && files[0]) { // se existir algum arquivo
						var reader = new FileReader(); // adiciona a função de leitor à reader

						reader.onload = function (e) { // uma vez que o upload foi carregado
						   fotopreview.src = e.target.result; // troca o src da foto preview para a url do arquiv
						}

						reader.readAsDataURL(files[0]); // lê o caminho do arquivo que foi carregado
						}
					}
				</script>
				
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="<?php echo $dados_usuario['nome']?>" name="nome_usuario" type="text" class="validate">
					</div>
				</div>
				
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="<?php echo $dados_usuario['sobre'];?>" name="descricao" type="text" class="validate">
					</div>
				</div>
				
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="<?php echo $dados_usuario['email']; ?>" name="login" type="text" class="validate">
					</div>
				</div>

				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="Senha atual" name="senha" type="password" class="validate">
					</div>
				</div>
				
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="Nova senha" name="senha_nova" type="password" class="validate">
					</div>
				</div>
				
				<button type="submit" name="enviar" class="col s6 offset-s3 btn waves-effect #f57f17 yellow darken-4">
				Enviar  <i class="material-icons right">send</i> </button>

		</form>
			<?php
				if(isset($_POST['enviar'])):
					if(!empty($_FILES['imagem']['name'])):
						$erros = Array();
						$formatosPermitidos = array("png", "jpeg", "jpg", "PNG", "JPEG", "JPG");
						$extensao = pathinfo($_FILES['imagem']['name'],PATHINFO_EXTENSION);

						if(in_array($extensao, $formatosPermitidos)):
							$imagembase64 = base64_encode(file_get_contents($_FILES['imagem']['tmp_name'])); //selecionando o nome temporario do arqv;
							$imagem = 'data:imagem/'.$extensao.';base64,'.$imagembase64;
							$nome =  filter_input(INPUT_POST,'nome',FILTER_SANITIZE_SPECIAL_CHARS);
							$desc = filter_input(INPUT_POST,'descricao',FILTER_SANITIZE_SPECIAL_CHARS);

							if(!empty($_POST['senha'])):
								$senha = pg_escape_string($connect, md5($_POST['senha']));
								$sql = "SELECT * FROM usuario WHERE senha = $senha";
								$resultado = pg_query($connect,$sql);
								if(pg_num_rows($resultado) == 1):
									$senha_nova = pg_escape_string($connect, md5($_POST['senha_nova']));
									$n=0;
									$valores = [$nome,$senha_nova,$imagem,$desc];
									$colunasEditaveis = ['nomerec','senha','imagem','sobre'];
									while($n<4):
										if(!empty($valores[$n])):
											$sql = "UPDATE usuario SET $colunasEditaveis[$n] = '$valores[$n]' WHERE codusu = '$id_usuario'";
											 pg_query($connect,$sql);
										endif;
										$n = $n + 1;
									endwhile;
								else:
									echo "<script>alert('Senha incorreta')</script>";
								endif;	
							else:
								$n=0;
								$valores = [$nome,$imagem,$desc];
								$colunasEditaveis = ['nomerec','imagem','sobre'];
								while($n<3):
									if(!empty($valores[$n])):
										$sql = "UPDATE usuario SET $colunasEditaveis[$n] = '$valores[$n]' WHERE codusu = '$id_usuario'";
										 pg_query($connect,$sql);
									endif;
									$n = $n + 1;
								endwhile;
							endif;
							
							header("Location: perfil.php?id_usuario=$id_usuario&meuperfil=1");
							pg_close($connect);

						else:
							$erros[] = "<script>alert('Imagem com formato não suportado');</script>";
						endif;
						
						if(!empty($erros)):
						
							foreach($erros as $erro):
								echo $erro;
							endforeach;
							
						endif;
						
					else:
						$nome =  filter_input(INPUT_POST,'nome',FILTER_SANITIZE_SPECIAL_CHARS);
						$desc = filter_input(INPUT_POST,'descricao',FILTER_SANITIZE_SPECIAL_CHARS);
						
						if(!empty($_POST['senha'])):
							$senha = pg_escape_string($connect, md5($_POST['senha']));
							$sql = "SELECT * FROM usuario WHERE senha = $senha";
							$resultado = pg_query($connect,$sql);
							if(pg_num_rows($resultado) == 1):
								$senha_nova = pg_escape_string($connect, md5($_POST['senha_nova']));
								$n=0;
								$valores = [$nome,$senha_nova,$desc];
								$colunasEditaveis = ['nomerec','senha','sobre'];
								while($n<3):
									if(!empty($valores[$n])):
										$sql = "UPDATE usuario SET $colunasEditaveis[$n] = '$valores[$n]' WHERE codusu = '$id_usuario'";
										 pg_query($connect,$sql);
									endif;
									$n = $n + 1;
								endwhile;
							else:
								echo "<script>alert('Senha incorreta')</script>";
							endif;	
						else:
							$n=0;
							$valores = [$nome,$desc];
							$colunasEditaveis = ['nomerec','sobre'];
							while($n<2):
								if(!empty($valores[$n])):
									$sql = "UPDATE usuario SET $colunasEditaveis[$n] = '$valores[$n]' WHERE codusu = '$id_usuario'";
									 pg_query($connect,$sql);
								endif;
								$n = $n + 1;
							endwhile;
						endif;
							
						header("Location: perfil.php?id_usuario=$id_usuario&meuperfil=1");
						pg_close($connect);
					endif;
				endif;
			?>
			
			
			<script>
				var n = 1;
				function addInput(lines){
					var newdiv = document.createElement('div');
					newdiv.innerHTML  = 'Digite a quantidade necessária e o ingrediente';
					newdiv.innerHTML += '<input type="text" name="ingrediente'+n+'" placeholder="Ex.: Uma colher de manteiga">';
					document.getElementById(lines).appendChild(newdiv);
					n++;
				}
			</script>
	</main>
	



<?php
include_once 'footer.php';
?>
