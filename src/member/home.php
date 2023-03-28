<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";
?>

<html>
	<head>
		<title>Benvenuto</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="css/home_style.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_radio_button_style.css">
		<style>
			.s-container{
				width: 100%;
				display: flex;
				justify-content: right;
				align-items: center;
				gap: 2em;
			}
			.s-name{
				width: 20%;
				border: 1px solid gray;
			
				border-radius: .25em;
				padding: 16px 20px;
				font-family: "Open Sans", sans-serif;
				font-weight: bold;
		
				cursor: pointer;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				-webkit-appearance: none;
				-moz-appearance: none;
				-ms-appearance: none;
				-o-appearance: none;
				appearance: none;
			}		
			.selection-name{
				width:11em;
				border: 1px solid gray;
			
				border-radius: .25em;
				padding: 16px 20px;
				font-family: "Open Sans", sans-serif;
				font-weight: bold;

			}
			 .error-message {
				display: none;
			}

			.error-message p {
			background: #e94b35;
			color: #ffffff;
			font-size: 1.4rem;
			text-align: center;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			border-radius: .25em;
			padding: 16px;
			}

			.success-message p {
			background: #4caf50;
			color: #ffffff;
			font-size: 1.4rem;
			text-align: center;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
			border-radius: .25em;
			padding: 16px;
			}

			.error-field {
			border-color: #e94b35 !important;
			}

		</style>
	</head>
	<body>
		<div class='error-message' id='error-message'> <p id='error'></p></div>
		<?php
			
			if(isset($_GET['selection_field'])&&isset($_GET['search_field']))	{
				$column = $_GET['selection_field'];
				$query = $con->prepare("SELECT * FROM book WHERE $column LIKE ?");
				$query->bind_param("s", $_GET['search_field']);
				$query->execute();
				$result = $query->get_result();
			}else{
				$query = $con->prepare("SELECT * FROM book ORDER BY title");
				$query->execute();
				$result = $query->get_result();
			}
			
			if(!$result)
				die("ERROR: Couldn't fetch books");
			$rows = mysqli_num_rows($result);
		
			if($rows == 0){
				echo "<h2 align='center'>Nessun libro disponibile</h2>";
			
			}else{
				echo "<form class='cd-form' method='GET' action='".$_SERVER['PHP_SELF']."'>";
				
				echo "<legend>Libri disponibili</legend>";
				echo "<div class='s-container'>";
				echo "<select class='selection-name' name='selection_field'>";
				echo "<option value='title'> Titolo </option>";
				echo "<option value='author'> Autore </option>";
				echo "<option value='category'> Genere </option>";
				echo "</select>";
				echo "<input type='text'class='s-name' name='search_field' placeholder='Cerca Nome '>";
				echo "<input type='submit' name='search_request' value='Cerca libro' />";
				echo "</div>";
		
				
				echo "<table width='100%' cellpadding=10 cellspacing=10>";
				echo "<tr>
						<th></th>
			
						<th>ISBN<hr></th>
						<th>Titoli<hr></th>
						<th>Autori<hr></th>
						<th>Genere<hr></th>
						<th>Copie <hr></th>
					</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>
							<td>
								<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$row[0]." />	
								<div class='control__indicator'></div>
							</td>";
					for($j=0; $j<6; $j++)
						if($j == 4)
							echo "<td>".$row[$j]."</td>";
						else
							echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><input type='submit' name='m_request' value='Richiedi libro' />";
				echo "</form>";
			}
	
			if(isset($_GET['m_request']))
			{
				if(empty($_GET['rd_book']))
					echo error_without_field("Seleziona un libro da richiedere");
				else
				{
					$query = $con->prepare("SELECT copies FROM book WHERE dui = ?;");
					$query->bind_param("s", $_GET['rd_book']);
					$query->execute();
					$copies = mysqli_fetch_array($query->get_result())[0];
					if($copies == 0)
						echo error_without_field("Nessuna copia di questo libro è disponibile");
					else
					{
						$query = $con->prepare("SELECT request_id FROM pending_book_requests WHERE member = ?;");
						$query->bind_param("s", $_SESSION['username']);
						$query->execute();
						if(mysqli_num_rows($query->get_result()) >4)
							echo error_without_field("Puoi richiedere soltanto 4 libri alla volta");
						else
						{
							$query = $con->prepare("SELECT book_dui FROM book_issue_log WHERE member = ?;");
							$query->bind_param("s", $_SESSION['username']);
							$query->execute();
							$result = $query->get_result();
							if(mysqli_num_rows($result) >= 3)
								echo error_without_field("Non puoi richiedere piu di 3 libri alla volta");
							else
							{
								$rows = mysqli_num_rows($result);
								for($i=0; $i<$rows; $i++)
									if(strcmp(mysqli_fetch_array($result)[0], $_GET['rd_book']) == 0)
										break;
								if($i < $rows)
									echo error_without_field("hai già richiesto questo libro");
								else
								{
									$query = $con->prepare("INSERT INTO pending_book_requests(member, book_dui) VALUES(?, ?);");
									$query->bind_param("ss", $_SESSION['username'], $_GET['rd_book']);
								
									if(!$query->execute())
										echo error_without_field("ERROR: Couldn\'t request book");
									else
										echo success("Correttamente richiesto il libro. Sarai notificato da una email quando la tua richiesta sarà accettata");
										
								
								}
							}
						}
					}
				}
			}
		?>
	</body>
</html>
