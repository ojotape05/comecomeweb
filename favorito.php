<?php
	
	// iniciar sessão
	session_start();

	// conexao com bd;
	require_once 'bd_conectar.php';

	// Verificar login
	if(!isset($_SESSION['logado'])):
		header('Location: index.php');
	endif;
	
	$id_usuario = $_SESSION['id_usuario'];
	
	if(!empty($_GET['id_receita'])):
		$id_receita = $_GET['id_receita'];
		
		$sql = "SELECT codreceita FROM favorito WHERE codusu = $id_usuario";
		$resultado = pg_query($connect,$sql);
		
		while ($row = pg_fetch_assoc($resultado)):
			$codreceita = $row['codreceita'];
			if($codreceita == $id_receita):
				$fav = 1; //se fav tiver valor, quer dizer q o usu ja fav a receita.
				header("Location: receita.php?id_receita=$id_receita&fav=$fav");
			endif;
		endwhile;
		
		if(empty($fav)):
			$sql = "INSERT INTO favorito (codreceita,codusu) VALUES ($id_receita,$id_usuario)";
			pg_query($connect,$sql);
			header("Location: receita.php?id_receita=$id_receita");
		endif;
		
	else:
		header("Location: home.php");
	endif;
	

?>
