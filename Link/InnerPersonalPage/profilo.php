<?php
	session_start();
	$name = $_SESSION["Usr"];
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>Profilo</title>
</head>

<!--Pagina che permette di visualizzare idati relativi ad un utente-->

<body>

<div id="Corpo">
	<div id="Intro">
		
		<?php
			$conn = createConnection();
				
			$from = "opening";
			
				if(isset($_GET["u"])){
					$target = $_GET["u"];
					
					//Recupera da quale pagina si è effettuta la richiesta del profilo in modo da settare adeguatamente il tasto Back.
					if(isset($_GET["from"])){
						$from = $_GET["from"];
					}
					
					//Query che permette di ricevere tutte le info relative ad un utente
					$query = pg_query($conn, 'SELECT * FROM "Utente" WHERE "Nome"=\''.$target.'\'');
					
					//Controllo errori
					if(!$query){
						echo 'Error connecting DB!';
					}
					else{
						if(pg_num_rows($query) == 0){
							echo 'L\'utente "'.$target.'" è inesistente';
							
							echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="'.$from.'.php">Back</a></div>';
						}
						else{
							$data = pg_fetch_object($query,0);
							$nascita = ($data->Data_di_Nascita != null)? 'Nato il '.$data->Data_di_Nascita : '';
							$residenza = ($data->Residenza != null)? 'Di '.$data->Residenza : '';
							echo '<table align=center ><tr>';
							echo '<td><img src="'.$data->Immagine.'" style="width: 140px;height: 140px;border:solid 2px #200134;" /></td>';
							echo '<td><b>'.$data->Nome.'<br>Nome: '.$data->Nome_Vero.'<br>Cognome: '.$data->Cognome_Vero.'<br>'.$nascita.'<br>'.$residenza.'</b></td>';
							echo '</tr></table>';
							echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="'.$from.'.php">Back</a></div>';
						}
					}
				}
				else{
					echo 'Sei stato spedito qui in qualche modo strano.<br>Tutte le funzioni verranno disabilitate per preservare gli altri utenti.';
					echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="'.$from.'.php">Back</a></div>';
				}
		?>
		
	</div>
</div>


<script>

	var w = window.innerWidth
	|| document.documentElement.clientWidth
	|| document.body.clientWidth;

	var h = window.innerHeight
	|| document.documentElement.clientHeight
	|| document.body.clientHeight;
	

	var introh = document.getElementById("Intro").clientHeight;
	if(introh < h){
	document.getElementById("Intro").style.position = "relative";
	document.getElementById("Intro").style.top = ((h-introh)/2) + "px";	
	}
	
</script>
	
</body>
</html>