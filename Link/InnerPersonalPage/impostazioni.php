<?php
	session_start();
	$name = $_SESSION["Usr"];
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>Impostazioni</title>
</head>

<!--Pagina che permette di aggiornare il proprio profilo inserendo dati aggiuntivi visualizzabili dagli altri utenti-->

<body>

<div id="Corpo">
	<div id="Intro">
		
		<?php
		
			$conn = createConnection();
			
			//Secondo flusso di esecuzione in seguito al submit dei dati da aggiornare
			if ($_SERVER["REQUEST_METHOD"] == "POST"){
				$bday = $_POST["bday"];
				$address = checkChar($_POST["address"]);  //Questa funzione permette di inserire nel DB anche caratteri speciali
				$img = checkChar($_POST["img"]);;
				
				//Creazione delle stringhe per l'update di tutte le impostazioni aggiornate in un colpo solo
				$dati[0] = 'UPDATE "Utente" SET "Data_di_Nascita" = \''.$bday.'\' WHERE "Nome"= \''.$name.'\';';
				$dati[1] = 'UPDATE "Utente" SET "Residenza" = \''.$address.'\' WHERE "Nome"= \''.$name.'\';';
				$dati[2] = 'UPDATE "Utente" SET "Immagine" = \''.$img.'\' WHERE "Nome"= \''.$name.'\';';
				
				$connect = false;
				
				//Collegamento delle stringhe
				$query = '';
				if($bday != null){
					$query = $query.' '.$dati[0];
					$connect = true;
				}
				if($address != null){
					$query = $query.' '.$dati[1];
					$connect = true;
				}
				if($img != null){
					$query = $query.' '.$dati[2];
					$connect = true;
				}
				
				//Se sono avvenute delle modifiche effettua la query di aggiornamento del profilo
				if($connect){
					$query = pg_query($conn, $query);				
					
					if(!$query){
						echo 'Error connecting DB!';
					}
					else{
						echo '<p>Profilo aggiornato correttamente!</p>';
						echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="opening.php">Prosegui</a></div>';
					}
				}
				else{
					echo '<p>Non hai completato nessun campo!</p>';
					echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="impostazioni.php">Back</a></div>';
				}
			}
			//Flusso di esecuzione primario visualizza i dati ancora da inserire.
			else{
				
				//Query per le informazioni dell'utente
				$query = pg_query($conn, 'SELECT * FROM "Utente" WHERE "Nome"=\''.$name.'\'');
			
				if(!$query){
					echo 'Error connecting on DB';
				}
				else{
					if(pg_num_rows($query) == 0){
						echo 'Non è stato trovato l\'utente specificato, errore dovuto ad un possibile problema interno, riprovare.<br>Se il problema
						persiste contattare gli amministratori di ChiLoSa';
						echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="opening.php">Back</a></div>';
					}
					else{
						echo '<h1>Impostazioni</h1>';
						echo 'Tramite questa pagina sarà possibile completare il proprio profilo inserendo ulteriori dati personali.<br>
						nelle future versioni di ChiLoSa sarà possibile anche modificare i dati già inseriti!<br><br>';
						//Recupero dei dati dal DB
						$data = pg_fetch_object($query,0);	
						$nascita = $data->Data_di_Nascita;
						$residenza = $data->Residenza;
						$img = $data->Immagine;
						
						$complete = true;
						
						echo '<form class="init" name="quest-template" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
						echo '<table align="center">';
						
						//In base a quali dati non si ha ancora inserito viene mostrato un form adeguato.
						if($nascita == null){
							echo '<tr><td><label for="bday"><b>Data di Nascita:</b></label></td><td><input id="bday" type="date" name="bday"></td></tr>';
							$complete = false;
						}
						else{
							echo '<tr><td>Hai già impostato la tua data di nascita.</td></tr>';
						}
						
						if($residenza == null){
							echo '<tr><td><label for="address"><b>Residenza:</b></label></td><td><input id="address" type="text" name="address"></td></tr>';
							$complete = false;
						}
						else{
							echo '<tr><td>Hai già impostato la tua residenza.</td></tr>';
						}
						//Immagine di default del profilo
						if($img == 'http://chilosa.altervista.org/profilo.jpg'){
							echo '<tr><td><label for="img_set"><b>Immagine del Profilo(URL):</b></label></td><td><input id="img_set" type="url" name="img" onfocusin="dimSudg()" onfocusout="disdimSudg()"></td></tr>';
							$complete = false;
						}
						else{
							echo '<tr><td>Hai già impostato la tua immagine del profilo.<br></td></tr>';
						}
						
						echo '</table>';
						if(!$complete){
							//Se ci sono dei dati ancora da inserire visualizza il button per fare il submit dei dati
							echo '<br><table align="center"><tr><td><input class="Invio" type="submit" value="Inserisci" /></td>';
							echo '<td><a class="Back_Button" href="opening.php">Back</a></td></tr></table>';
							echo "\n";
							echo '<p id="targetFunc" style="color:#FF0000"></p>';
						}
						else{
							echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="opening.php">Back</a></div>';
						}
						
						echo '</form>';
					}
				}
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
	
	function dimSudg(){
		document.getElementById("targetFunc").innerHTML = "Si consiglia di inserire un'immagine quadrata di dimensioni massime 500x500<br>In modo da non appesantire il sito.<br>La modifica sarà visualizzabile al prossimo login.";
	}
	function disdimSudg(){
		document.getElementById("targetFunc").innerHTML = "";
	}
	
</script>
	
</body>
</html>