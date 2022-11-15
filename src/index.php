<?php
	require "db_connect.php";
	require "header.php";

	session_start();
	
	if(empty($_SESSION['type']));
	else if(strcmp($_SESSION['type'], "librarian") == 0)
		header("Location: librarian/home.php");
	else if(strcmp($_SESSION['type'], "member") == 0)
		header("Location: member/home.php");
?>
<style>
content{
	width: 100%;
	height: 100vh;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
}
.background-image{
	width: 100%;
	height: 100%;
	position: absolute;
	z-index: 0;
	opacity: 0.7;
}
content h1{
	font-size: 3em;
	color: white;
	z-index: 1;

}
content a{
	margin-top: 2em;
	font-size: 1.3em;
	color: white;
	z-index: 1;
	border: 3px solid white ;
	padding: 0.6em 2em 0.6em 2em;
	font-weight: bold;
	background-color: none;
	transition: background-color,color 200ms ease-in;
}
content a:hover{

	color: black;
	border: 3px solid white ;
	background-color: white;
}
body{
	background-color: black;
}
</style>
<html>
	<head>
		<title>Libreria di San Vendemiano</title>
		<link rel="stylesheet" type="text/css" href="css/index_style.css" />
	</head>
	<body>
	<img class="background-image"src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/Statsbiblioteket_l%C3%A6sesalen-2.jpg/1530px-Statsbiblioteket_l%C3%A6sesalen-2.jpg">		
		<content>
			<h1> Biblioteca di San Vendemiano</h1>
			<a href="login.php"> clicca per entrare </a>
		</content>
	</body>
</html>