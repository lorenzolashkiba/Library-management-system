<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Aggiungi Libro</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/insert_book_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<legend>Entra i dettagli del libro</legend>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" id="b_isbn" type="number" name="b_isbn" placeholder="ISBN" required />
				</div>
				
				<div class="icon">
					<input class="b-title" type="text" name="b_title" placeholder="Titolo" required />
				</div>
				
				<div class="icon">
					<input class="b-author" type="text" name="b_author" placeholder="Autore" required />
				</div>
				
				<div>
				<h4>Categoria</h4>
				
					<p class="cd-select icon">
						<select class="b-category" name="b_category">
							<option>Fiction</option>
							<option>Non-fiction</option>
							<option>Education</option>
						</select>
					</p>
				</div>
				
				<div class="icon">
					<input class="b-copies" type="number" name="b_copies" placeholder="Copie" required />
				</div>
				
				<br />
				<input class="b-isbn" type="submit" name="b_add" value="Aggiungi libro" />
		</form>
	<body>
	
	<?php
		if(isset($_POST['b_add']))
		{
			// creazione query per inserire libro e controllo se isbn e uguale
			$query = $con->prepare("SELECT isbn FROM book WHERE isbn = ?;");
			$query->bind_param("s", $_POST['b_isbn']);
			$query->execute();
			
			if(mysqli_num_rows($query->get_result()) != 0)
				echo error_with_field("Un Libro con quel ISBN esiste già", "b_isbn");
			else
			{
				$query = $con->prepare("INSERT INTO book VALUES(?, ?, ?, ?, ?);");
				$query->bind_param("ssssd", $_POST['b_isbn'], $_POST['b_title'], $_POST['b_author'], $_POST['b_category'], $_POST['b_copies']);
				
				if(!$query->execute())
					die(error_without_field("ERROR: Operazione non riuscita"));
				echo success("Libro aggiunto!");
			}
		}
	?>
</html>