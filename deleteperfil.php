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

	if(!empty( $_GET['id_usuario'])):
		$id_usuario = $_GET['id_usuario'];
		if($id_usuario == $id):
			pg_query($connect,"DELETE FROM usuario WHERE codusu = '$id_usuario'");
			pg_query($connect,"DELETE FROM receita WHERE autor = '$id_usuario'");
			pg_query($connect,"DELETE FROM favoritos WHERE codusu = '$id_usuario'");
			pg_query($connect,"DELETE FROM seguidos WHERE seguindo = '$id_usuario' OR seguido = '$id_usuario'");
		else:
			header("Location: home.php");
		endif;
	else:
		header("Location: home.php");
	endif;
	
	header("Location: index.php");
?>
