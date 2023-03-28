<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
	if (!extension_loaded('sockets')) {
		die('The sockets extension is not loaded.');
	}
	/* create unix udp socket
	$address = "192.168.1.14";
	$port = 8188;
	// No Timeout 
	set_time_limit(0);
	
	$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
	$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");
	$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");

	// socket_close($spawn);
	// socket_close($socket);
	*/
	function startsWith ($string, $startString)
	{
		$len = strlen($startString);
		if (substr($string, 0, $len) === $startString){
			return substr($string, 0, $len);
		}
		return false;
	}
	  
?>
<html>
	<head>
	<title>Pending Registrations</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_radio_button_style.css">
		<link rel="stylesheet" type="text/css" href="../member/css/home_style.css">
		<style>
		.btn {
			width: 100%;
			border: none;
			background: #d75069;
			border-radius: .25em;
			padding: 16px 20px;
			margin-bottom: 20px;
			color: #ffffff;
			font-weight: bold;
			font-family: "Open Sans", sans-serif;
			font-size: 1.6rem;
			float: right;
			cursor: pointer;
			
		}	
		.btn:hover{
			background: #a23045;
			
		}
			#hide{
				visibility: hidden;
			}
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
	<?php	

		if(isset($_GET['pos']))
		{
			$pos= substr($_GET['pos'],8);
			var_dump($pos);
			$query = $con->prepare("SELECT ripiano,posizione,scaffale FROM book WHERE isbn like ".$pos."");
			//$query->bind_param("s", $pos);
			echo "j";
			$query->execute();
			echo "j";	
			$result = $query->get_result();
			//var_dump($result->fetch_all(MYSQLI_ASSOC));
			$result = $result->fetch_all(MYSQLI_ASSOC);
			echo $result[0]['scaffale'];
			
			if(!$result)
				die("ERROR: Couldn't fetch books");

			if($result[0]['scaffale']==1){
				header("Location:http://scaffale1.local/libro?ripiano=".$result[0]['ripiano']."&pos=".$result[0]['posizione']."");
			}else{
				header("Location:http://scaffale2.local/libro?ripiano=".$result[0]['ripiano']."&pos=".$result[0]['posizione']."");
			}
		}else if(isset($_GET['selection_field'])&&isset($_GET['search_field']))	{
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
		}else
		{	
				//echo "<iframe name='dummyframe' id='dummyframe' style='display: none;'></iframe>";
				echo "<form class='cd-form' method='GET' action='".$_SERVER['PHP_SELF']."'>";
				echo "<legend>Libri disponibili</legend>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<div class='s-container'>";
				echo "<select class='selection-name' name='selection_field'>";
				echo "<option value='title'> Titolo </option>";
				echo "<option value='author'> Attore </option>";
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
						<th>Copie disponibili<hr></th>
						<th>Scaffale<hr></th>
						<th>Ripiano<hr></th>
						<th>Posizione<hr></th>
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
					for($j=0; $j<8; $j++){
					
						if($j == 4){
							echo "<td>".$row[$j]."</td>";
						}else{
							echo "<td>".$row[$j]."</td>";
							
						}
						
						
					}		
					echo "<td><input type='button' class='btn' value='illumina' id='".$row[$j-3]."-".$row[$j-2]."-".$row[$j-1]."' /></td>";		
					//echo "<td><input type='hidden' name='ripiano' value='".$row[$j-2]."' /></td>";	
					
					echo "</tr>";
					
				}
				echo "</table>";
				
				echo "</form>";
		}
		
		
		


			// if (($sock = socket_create(AF_INET, SOCK_STREAM, 0)) === false) {
			// 	echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
			// }
			
			// socket_bind($sock, "localhost", $port);
			// if (socket_bind($sock, $address, $port) === false) {
			// echo "socket_bind() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			// }
			
			// // Start listening for connection
			// socket_listen($sock, 5); // Maximum is 5 connection
			// if (socket_listen($sock, 5) === false) {
			// echo "socket_listen() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			// }
			
			// // Handling connection from client
			
			// $msgsock = socket_accept($sock); // msgsock is a client connect to webserver
			// if ($msgsock === false) 
			// 	echo "socket_accept() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
			
			
			// echo "tutto ok";

			
			?>
    
    </body>
	<script src="./js/event_listener.js"></script>

</html>
