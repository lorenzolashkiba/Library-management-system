<?php
	require "../db_connect.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Welcome</title>
		<link rel="stylesheet" type="text/css" href="css/home_style.css" />
	</head>
	<body>
		<div id="allTheThings">
			<a href="pending_registrations.php">
				<input type="button" value="Registrazioni in corso" />
			</a><br />
			<a href="pending_book_requests.php">
				<input type="button" value="Richieste libri " />
			</a><br />
			<a href="insert_book.php">
				<input type="button" value="Aggiungi libro" />
			</a><br />
			<a href="update_copies.php">
				<input type="button" value="Aggiorna copie di un libro" />
			</a><br />
			<a href="due_handler.php">
				<input type="button" value="Promemoria" />
			</a><br />
			<a href="all_books.php">
				<input type="button" value="Tutti i libri" />
			</a><br /><br />
		</div>
	</body>
</html>