<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>Pending Book Requests</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_book_requests_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM pending_book_requests;");
			$query->execute();
			$result = $query->get_result();;
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>Nessuna richiesta in attesa</h2>";
			else
			{
				$serverName =$_SERVER['PHP_SELF'];
				echo "<form class='cd-form' method='POST' action='$serverName'>";
				echo "<legend>Richiesta libri in attesa</legend>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>"; 
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Username<hr></th>
							<th>Book<hr></th>
							<th>Time<hr></th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>";
					echo "<td>
							<label class='control control--checkbox'>
								<input type='checkbox' name='cb_".$i."' value='".$row[0]."' />
								<div class='control__indicator'></div>
							</label>
						</td>";
					for($j=1; $j<4; $j++)
						echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><div style='float: right;'>";
				echo "<input type='submit' value='Rifiuta selezionato' name='l_reject' />&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Accetta selezionato' name='l_grant'/>";
				echo "</div>";
				echo "</form>";
			}
			
			$header = 'From: <noreply@library.com>' . "\r\n";
			
			if(isset($_POST['l_grant']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$request_id =  $_POST['cb_'.$i];
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						
						$query = $con->prepare("INSERT INTO book_issue_log(member, book_isbn) VALUES(?, ?);");
						$query->bind_param("ss", $member, $isbn);
						if(!$query->execute())
							die(error_without_field("ERROR:  Impossibile richiedere il libro1"));
						$requests++;

						//preparazione email e invio
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$subject = "Libro accettato";
						
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];
						
						$query = $con->prepare("SELECT due_date FROM book_issue_log WHERE member = ? AND book_isbn = ?;");
						$query->bind_param("ss", $member, $isbn);
						$query->execute();
						$due_date = mysqli_fetch_array($query->get_result())[0];
						$message = "Il libro'".$title."' con ISBN ".$isbn." è stato rilascito al tuo account. La data per ritornare il libro è ".$due_date.".";
						
						mail($to, $subject, $message, $header);
					}
				}
				if($requests > 0)
					echo success("Correttamente accettato".$requests." richieste");
				else
					echo error_without_field("Nessuna richiesta accettata");
			}
			
			if(isset($_POST['l_reject']))
			{
				$requests = 0;
				for($i=0; $i<$rows; $i++)
				{
					if(isset($_POST['cb_'.$i]))
					{
						$requests++;
						$request_id =  $_POST['cb_'.$i];
						
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						//preparazione email e invio
							$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
							$query->bind_param("s", $member);
							$query->execute();
							$to = mysqli_fetch_array($query->get_result())[0];
							$subject = "Richiesta del libro rifiutata";
							
							$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
							$query->bind_param("s", $isbn);
							$query->execute();
							$title = mysqli_fetch_array($query->get_result())[0];
							$message = "La tua richiesta del libro '".$title."' con ISBN ".$isbn." è stata rifiutata. Puoi richiedere il libro di nuovo oppure puoi visitare la libreria per ulteriori formazioni";
							
							$query = $con->prepare("DELETE FROM pending_book_requests WHERE request_id = ?");
							$query->bind_param("d", $request_id);
							if(!$query->execute())
								die(error_without_field("ERROR: Impossibile eliminare i valori"));
							
							mail($to, $subject, $message, $header);
					}
				}
				if($requests > 0)
					echo success("Correttamente rifiutato ".$requests." richieste");
				else
					echo error_without_field("Nessuna richiesta selezionata");
			}