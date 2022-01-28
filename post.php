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
$sql = "SELECT * FROM usuario WHERE codusu = '$id'";
$resultado = pg_query($connect, $sql);
$dados = pg_fetch_assoc($resultado);
?>

<body>
	<header>
		<nav class="#fbc02d yellow darken-2" role="navigation">
		<div class="nav-wrapper container"><a id="logo-container" href="home.php" class="brand-logo left">ComeCome</a>
		  <ul class="right">
			<li><a href="perfil.php?id_usuario=<?php $meuperfil = true; echo $id.'&meuperfil='.$meuperfil;?>" class="btn-floating"> <img class="circle z-depth-2" height="40px" width="40px" src="<?php echo $dados['imagem']; ?>"> </a> </li>
			<li><a href="logout.php" class="btn-floating #f57f17 yellow darken-4"> <i class= "material-icons"> stop </i> </a> </li>
		  </ul>
		</div>
		</nav>
	</header>
	
	<main>
		<div class="row container z-depth-2">
			<form class="col s12" action= "<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
				<h1 align="center"> Postar Receita </h1>
				
				<div align="center">
					<img id="fotopreview" class="post" src=""><br>
					<label> Foto da Receita </label> <br>
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
						<input placeholder="Nome da Receita" name="nome_receita" type="text" class="validate">
					</div>
				</div>
				
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<input placeholder="Descrição" name="descricao" type="text" class="validate">
					</div>
				</div>
				
				<div class="row">
					<div class="input-field col s6 offset-s3">
						<textarea placeholder="Modo de Preparo" name="preparo" type="text" class="validate"></textarea>
					</div>
				</div>

				<div class="row">
					<div class="input-field col s6 offset-s3">
						<label> Ingredientes </label><br><br>
							<div id='lines'></div>
							<button type="button" onclick="addInput('lines')">+</button><br>
							<button type="submit" name="enviar" class="col s6 offset-s3 btn waves-effect #f57f17 yellow darken-4">
					Enviar  <i class="material-icons right">send</i> </button>
					</div>
				</div>
		</form>
			<?php
				if(isset($_POST['enviar'])):
					$erros = Array();
					$formatosPermitidos = array("png", "jpeg", "jpg", "PNG", "JPEG", "JPG");
					$extensao = pathinfo($_FILES['imagem']['name'],PATHINFO_EXTENSION);

					if(in_array($extensao, $formatosPermitidos)):
						$imagembase64 = base64_encode(file_get_contents($_FILES['imagem']['tmp_name'])); //selecionando o nome temporario do arqv;
						$imagem = 'data:imagem/'.$extensao.';base64,'.$imagembase64;					
						$ingredientes = "<ul>";
						$nome =  filter_input(INPUT_POST,'nome_receita',FILTER_SANITIZE_SPECIAL_CHARS);
						$desc = filter_input(INPUT_POST,'descricao',FILTER_SANITIZE_SPECIAL_CHARS);
						$preparo = filter_input(INPUT_POST,'preparo',FILTER_SANITIZE_SPECIAL_CHARS);
						$n=1;
						while(!empty($_POST['ingrediente'.$n])):
							$ingrediente = $_POST["ingrediente".$n];
							$ingredientes = $ingredientes."<li> $ingrediente </li>";
							$n = $n + 1;
						endwhile;
						$ingredientes = $ingredientes."</ul>";
						if (empty($nome) or empty($preparo) or empty($ingredientes) or empty($desc)):
							$erros[] = "<script>alert('Todos os campos precisam ser preenchidos');</script>";
						else:
							$id_usuario = $_SESSION['id_usuario'];
							$sql = "INSERT INTO receita (nomerec,preparo,sobre,ingrediente,autor,imagem) VALUES ('$nome','$preparo','$desc','$ingredientes','$id_usuario','$imagem') RETURNING codreceita";
							$postagem = pg_query($connect,$sql);
							if ($postagem):
								$_SESSION['post'] = true; 
								$insert_row = pg_fetch_row($resultado);
								$lastid = $insert_row[0];
								$_SESSION['id_receita'] = $lastid;
								echo "<script>
								alert('Receita enviada com sucesso!')
								window.location.href = 'receita.php?id_receita=$lastid';
								</script>";
								pg_close($connect);
							else:
								$erros[] = "<script>alert('Erro, não foi possível inserir no banco de dados');</script>";
							endif;
						endif;
					else:
						$erros[] = "<script>alert('Imagem com formato não suportado ou vazia');</script>";
					endif;
					
					if(!empty($erros)):
					
						foreach($erros as $erro):
							echo $erro;
						endforeach;
						
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
