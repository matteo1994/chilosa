<?php
	session_start();
	$name = $_SESSION["Usr"];
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>Voti</title>
</head>

<!--Pagina che permette di visualizzare i nominativi dei votanti ad una determinata risposta in un sondaggio-->

<body>

<div id="Corpo">
	<div id="Intro">
		
		<?php
			$conn = createConnection();
				
				if(isset($_GET["a"])){
					$target = $_GET["a"];
					
					$query = pg_query($conn, 'SELECT "Utente" FROM "Votanti" WHERE "ID_Risposta"=\''.$target.'\' AND "Anonimo"<>\'T\'');
					
					if(!$query){
						echo 'Error connecting DB!';
					}
					else{
						//Se non ci sono tuple, allora tutti gli quelli che hanno espresso un voto lo hanno espresso Anonimo
						//questo perchè il link è attivo solo se il numero di voti ad una risposta è maggiore di 0
						if(pg_num_rows($query) == 0){
							echo 'Tutti gli utenti che hanno esperesso un voto per questa domanda hanno deciso di rimanere anonimi.';
							echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="ioloso.php">Back</a></div>';
						}
						else{
							echo 'Di seguito la lista degli utenti che hanno espresso una preferenza per questa risposta:';
							$array_di_utenti = fromQueryToArray($query);
							echo '<table class="ioloso" align="center">';
							echo "\n";
							foreach($array_di_utenti as $value){
								echo '<tr><td>';
								echo '<a class="table_ioloso" href="profilo.php?u='.$value->Utente.'">'.$value->Utente.'';
								echo '</td></tr>';
							}
							echo '</table><br>';
							echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="ioloso.php">Back</a></div>';
						}
					}
				}
				else{
					echo 'Sei stato spedito qui in qualche modo strano.<br>Tutte le funzioni verranno disabilitate per preservare gli altri utenti.';
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