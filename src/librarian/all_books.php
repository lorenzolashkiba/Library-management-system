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
    </head>

    <body>
	<?php
			$query = $con->prepare("SELECT * FROM book ORDER BY title");
			$query->execute();
			$result = $query->get_result();
			if(!$result)
				die("ERROR: Couldn't fetch books");
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>Nessun libro disponibile</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<legend>Libri disponibili</legend>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>";
				echo "<tr>
						<th></th>
						<th>ISBN<hr></th>
						<th>Titoli<hr></th>
						<th>Autori<hr></th>
						<th>Genere<hr></th>
						<th>Copie disponibili<hr></th>
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
					for($j=0; $j<5; $j++)
						if($j == 4)
							echo "<td>".$row[$j]."</td>";
						else
							echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><input type='submit' name='m_request' value='illumina' />";
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

			if(isset($_POST['m_request']))
			{
				if(empty($_POST['rd_book']))
					echo error_without_field("Seleziona un libro da richiedere");
				else
				{

					$query = $con->prepare("SELECT copies FROM book WHERE isbn = ?;");
					$query->bind_param("s", $_POST['rd_book']);
					$query->execute();		
					if(mysqli_fetch_array($query->get_result())[0]>0){
							$buf = $_POST['rd_book'];
					}
				
					$buf = "host:".$buf;
					$host = "172.20.10.11";
					$port = 8188;
					set_time_limit(0);

					//Create Socket
					$sock = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

					//Connect to the server
					$result = socket_connect($sock, $host, $port) or die("Could not connect toserver\n");

					//Write to server socket
					socket_write($sock, $buf, strlen($buf)) or die("Could not send data to server\n");

					//Read server respond message
					$result = socket_read($sock, 1024) or die("Could not read server response\n");
					if(startsWith($result,"h:")){
						echo "ok".$result;
					}
					

					//Close the socket
					socket_close($sock);

					//$bytes_sent = socket_sendto($sock, $buf, $len, 0, "255.255.255.255",8888);
					if ($bytes_sent == -1)
							die('An error occured while sending to the socket');
					else if ($bytes_sent != $len)
							die($bytes_sent . ' bytes have been sent instead of the ' . $len . ' bytes expected');
					echo "Request processed\n";
				}
			}

			?>
    
    </body>

</html>
