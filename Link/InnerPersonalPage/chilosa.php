<?php
	session_start();
	$name = $_SESSION["Usr"];
	$_SESSION["alert"] = (isset($_SESSION["alert"]))? $_SESSION["alert"] : false;   //Se la variabile non è già stata settata la imposta a false
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>ChiLoSa</title>
</head>

<!--Questa pagina permette di proporre o una domanda aperta o un sondaggio proponendo dei form ad hoc per queste funzionalità-->

<body>

<div id="Corpo">
	<div id="Intro">
		<?php
			
			//Flusso di esecuzione secondario, dopo la scelta della tipologia di domanda
			if ($_SERVER["REQUEST_METHOD"] == "POST"){
				
				$conn = createConnection();
				
				//$conn = pg_connect("host=localhost port=5432 dbname=ChiLoSa user=postgres password=milano");
				
				$alert_topic = false;
				$max_ans = 5;
				
				//Alla scelta di una domanda Aperta o Sondaggio il flusso di esecuzione si sposta all'interno di questo Scope
				if($_POST["choice"] == "Open" || $_POST["choice"] == "Survey"){
					//Vengono selezionati gli interessi dell'utente in modo da far scegliere fra questi le categorie di pertinenza della domanda
					$query=pg_query($conn, 'SELECT * FROM "Interessi" WHERE "Utente" = \''.$name.'\'');
					if  (!$query) {
						echo "Error connecting DB!";
					}
					if (pg_num_rows($query) == 0) {
						//Controllo sul fatto che un utente abbia già scelto o no gli interessi
						echo "L'utente non ha ancora selezionato delle categorie a cui &egrave interessato";
					}
					else{
						$topic = fromQueryToArray($query); //fetch delle categorie in un array di oggetti
						
						//Stampa a video del form per l'inserimento del testo della domanda, scelta delle categorie, immagine, e descrizione
						echo '<form class="init" name="quest-template" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
						echo "\n";
						echo '<p>Seleziona tra le categorie di tuo interesse quelle che riguardano la tua domanda</p>';
						echo "\n";
						echo '<table class="allineamento" align="center">';
						//Stampa Categorie
						foreach($topic as $value){
							echo '<tr><td><input id="'.$value->Topic.'" type="checkbox" name="Categoria[]" value="'.$value->Topic.'" /><label for="'.$value->Topic.'"><span></span>'.$value->Topic.'</label></td></tr>';
							echo "\n";
						}
						echo '</table>';
						
						//Check sulla selezione delle categorie, ogni domanda deve avere almeno una categoria di appartenenza
						//Questo statement deriva da un flusso di esecuzione successivo al tentativo di pubblicare una domanda
						if($_SESSION["alert"]){ //Utilizzo di una variabile globale per l'handling dell'errore
							unset($_SESSION["alert"]);
							echo '<em id="alert" style="color:#F00">*Devi selezionare almeno una categoria per la domanda</em>';
							echo "\n";
						}
						
						echo '<div class="allineamento"><label for="text">Testo:</label><br><textarea id="Text" placeholder="Inserire qui il testo della domanda." name="Testo" required></textarea>';
						echo "\n";
						echo '<br><label for="Img">Immagine(Non obbligatoria):</label><br><input type="url" id="Img" placeholder="www.example.com/img.jpg" name="img"><br>
								<textarea id="Img_desc" placeholder="Inserire sopra l\'url dell\'immagine e qui una sua breve descrizione." name="descrizione"></textarea></div>';
						
						
						//Se si tratta di un sondaggio viene stampato anche il form per inserire le risposte relative al sondaggio in questione
						if($_POST["choice"] == "Survey"){
							$survey = true;
							echo '<p>Qui di seguito potrai inserire minimo 2 e al massimo '.$max_ans.' risposte predefinite per il tuo sondaggio</p>';
							echo "\n";
							$ans_id = "ans_";
							//Due risposte sono obbligatorie fino ad un massimo di 5 opzionali
							for($x = 1; $x <= $max_ans; $x++){
								$ans_id = $ans_id + $x;
								$required = ($x <= 2)? "required" : "";
								echo '<textarea id="'.$ans_id.'" placeholder="Risposta '.$x.'" name="answer[]" '.$required.'></textarea>';
								echo "\n";
								$ans_id = "ans_";
							}
							echo '<br><table align="center"><tr><td><input class="Invio" id="Survey_send" type="submit" value="Proponi Sondaggio" /></td>';
							echo "\n";
							echo '<td><a class="Back_Button" href="chilosa.php">Back</a></td></tr></table>';
							//Ancora utilizzo di hidden per l'handling dell'ultimo flusso di esecuzione su questa pagina
							echo '<input type="hidden" value="send" name="surv" />';
							echo "\n";
							//Fine form pubblicazione Sondaggio
						}
						else{
							echo '<br><table align="center"><tr><td><input class="Invio" id="Open_send" type="submit" value="Pubblica Domanda" /></td>';
							echo "\n";
							echo '<td><a class="Back_Button" href="chilosa.php">Back</a></td></tr></table>';
							echo '<input type="hidden" value="send" name="quest" />';
							echo "\n";
							//Fine form pubblicazione domanda aperta
						}
						
						$_POST["choice"] = "";  //pulisce la variabile in modo che non rientri in questo statement al submit
					}
				}
				
				//Terzo flusso di esecuzione, successivo alla richiesta di pubblicazione della domanda, formula le query relative al DB e da un risponso
				//sul successo dell'operazione, vengono divisi in 2 statement, uno per le domande aperte, l'altro per i sondaggi.
				if($_POST["quest"] == "send"){
					$categorie = $_POST["Categoria"];
					//Controllo di coerenza sui dati inseriti
					if(count($categorie) == 0){
						//Catch dell'errore e relativo redirect per la gestione.
						$_SESSION["alert"] = true;
						$_POST["choice"] = "Open";
						$_POST["quest"] = "";
						echo '<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
						echo '<input id="older" type="submit" value="Back" style="display:none">';
						echo '<input type="hidden" value="Open" name="choice" />';
						echo '</form>';
						//Trick in JS per il redirect, svariati modi per risolvere questo passaggio
						echo '<script>document.getElementById("older").click();</script>';
					}else{
						//Se non ci sono errori di coerenza si passa alla formulazione della query
						
						$testo = checkChar($_POST["Testo"]);  //Questa funzione permette di inserire nel DB anche caratteri speciali come li apici.
						$tag_value = '';
						$value_opt = '';
						//controllo dei dati inseriti
						if(isset($_POST["img"]) && isset($_POST["descrizione"])){
							$img = checkChar($_POST["img"]);
							$desc = checkChar($_POST["descrizione"]);
							$tag_value = '"Immagine","Descrizione",';
							$value_opt = '\''.$img.'\',\''.$desc.'\',';
						}
						else{ 
							if(isset($_POST["img"]) && $_POST["img"] != ""){
								$img = checkChar($_POST["img"]);
								$tag_value = '"Immagine",';
								$value_opt = '\''.$img.'\',';
							}
							else{ 
								if(isset($_POST["descrizione"]) && $_POST["descrizione"] != ""){
									$desc = checkChar($_POST["descrizione"]);
									$tag_value = '"Descrizione",';
									$value_opt = '\''.$desc.'\',';
								}
							}	
						}
						//query che ritorna l'identificativo della domanda da usare poi nella query successiva
						$query = 'INSERT INTO "Domande_Aperte"("Utente","Testo",'.$tag_value.'"Data") VALUES (\''.$name.'\',\''.$testo.'\','.$value_opt.' NOW()) RETURNING "ID_Domanda"';
						$target_quest = pg_query($conn,$query);
						
						if(!$target_quest){
							echo 'Error connecting on DB!';
						}
						else{
							//Unset della variabile di ambiente per sicurezza nella navigazione nelle altre pagine.
							unset($_SESSION["alert"]);
							$target_quest = fromQueryToArray($target_quest);
							$topic_query = '';
							//Secondo Insert nel DB per le categorie di Pertinenza delle domande.
							//Unico punto "debole" del codice, perchè l'operazione non viene effettuata in modo atomico, e potrebbe generare problemi di consistenza
							//nei dati nel caso in cui ci fosse un crash del server proprio in questo instante, poco realistico ovviamente però c'è l'eventualità
							foreach($categorie as $value){
								$topic_query = $topic_query.' INSERT INTO "Appartenenza_Domande" ("ID_Domanda","Topic") VALUES (\''.$target_quest[0]->ID_Domanda.'\',\''.$value.'\');';
							}
							//Verifica che tutte le categorie siano state inserite con successo.
							$check_topic = pg_query($conn, $topic_query);
							($check_topic)? print('<p>Domanda inserita con successo!</p>') : print ('<p>Inserimento domanda non riuscito, si prega di riprovare.</p>');
						}
					}
					
					$_POST["quest"] = ""; //pulisce la variabile in modo che non rientri in questo statement al submit
					
				}
				
				//Secondo possibile statement del terzo flusso di esecuzione, gestione dell'inserimento dei sondaggi
				if($_POST["surv"] == "send"){
				
					$categorie = $_POST["Categoria"];
					//Controllo di coerenza sui dati inseriti
					if(count($categorie) == 0){
						//Catch dell'errore e relativo redirect per la gestione.
						$_SESSION["alert"] = true;
						$_POST["choice"] = "Survey";
						$_POST["surv"] = "";
						echo '<form action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
						echo '<input id="older" type="submit" value="Back" style="display:none">';
						echo '<input type="hidden" value="Survey" name="choice" />';
						echo '</form>';
						//Trick in JS per il redirect, svariati modi per risolvere questo passaggio
						echo '<script>document.getElementById("older").click();</script>';
					}else{	
						//Se non ci sono errori di coerenza si passa alla formulazione della query
						$testo = checkChar($_POST["Testo"]);  //Questa funzione permette di inserire nel DB anche caratteri speciali come li apici.
						$tag_value = '';
						$value_opt = '';
						//controllo dei dati inseriti
						if(isset($_POST["img"]) && isset($_POST["desc"])){
							$img = checkChar($_POST["img"]);
							$desc = checkChar($_POST["descrizione"]);
							$tag_value = '"Immagine","Descrizione",';
							$value_opt = '\''.$img.'\',\''.$desc.'\',';
						}
						else{ 
							if(isset($_POST["img"]) && $_POST["img"] != ""){
								$img = checkChar($_POST["img"]);
								$tag_value = '"Immagine",';
								$value_opt = '\''.$img.'\',';
							}
							else{ 
								if(isset($_POST["desc"]) && $_POST["desc"] != ""){
									$desc = checkChar($_POST["descrizione"]);
									$tag_value = '"Descrizione",';
									$value_opt = '\''.$desc.'\',';
								}
							}	
						}
						//query che ritorna l'identificativo del sondaggio da usare poi nella query successiva
						$query = 'INSERT INTO "Sondaggio"("Utente","Testo",'.$tag_value.'"Data") VALUES (\''.$name.'\',\''.$testo.'\','.$value_opt.' NOW()) RETURNING "ID_Sondaggio"';
						$target_survey = pg_query($conn,$query);
						if(!$target_survey){
							echo 'Error connecting on DB!';
						}
						else{
							//Unset della variabile di ambiente per sicurezza nella navigazione nelle altre pagine.
							unset($_SESSION["alert"]);
							$target_survey = fromQueryToArray($target_survey);
							$topic_query = "";
							
							//Secondo Insert nel DB per le categorie di Pertinenza dei sondaggi.
							//Punto "debole" del codice, perchè l'operazione non viene effettuata in modo atomico insieme all'inserimento di una domanda e delle risposte,
							//e potrebbe generare problemi di consistenza nei dati nel caso in cui ci fosse un crash del server proprio in questo instante,
							//poco realistico ovviamente però c'è l'eventualità
							foreach($categorie as $value){
								$topic_query = $topic_query.' INSERT INTO "Appartenenza_Sondaggio" ("ID_Sondaggio","Topic") VALUES (\''.$target_survey[0]->ID_Sondaggio.'\',\''.$value.'\');';
							}
							
							$check_topic = pg_query($conn, $topic_query);
							
							$risposte = $_POST["answer"];
							$risposte_query = "";
							foreach($risposte as $value){
								if($value == null){
									continue;
								}
								$value = checkChar($value);
								$risposte_query = $risposte_query.' INSERT INTO "Risposte_Sondaggio" ("ID_Sondaggio","Testo") VALUES (\''.$target_survey[0]->ID_Sondaggio.'\',\''.$value.'\');';
							}
							$check_ans = pg_query($conn, $risposte_query);
							//Verifica che tutte le categorie e le risposte siano state inserite con successo.
							($check_topic && $check_ans)? print('<p>Sondaggio inserito con successo!</p>'): print('<p>Inserimento sondaggio non riuscito, si prega di riprovare.</p>');
						}
					}
					
					$_POST["surv"] = ""; //pulisce la variabile in modo che non rientri in questo statement al submit
				}
				
			}
			
			//Flusso di esecuzione iniziale, viene proposta la scelta della tipologia di domanda
			else{
				//Unset della variabile d'ambiente relativa all'handling degli errori
				unset($_SESSION["alert"]);
				echo '<p class="part">Questa sezione ti permetterà di fare una domanda agli altri utenti, scegli tra le tipologie seguenti:</p><br>';
				echo "\n";
				//Utilizzo un form e la self redirection in modo da non avere troppe funzionalità sparse tra le pagine
				echo	'<form class="init" name="openquest" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
				echo "\n";
				echo '<input class="Invio" id="Open" type="submit" value="Domanda Aperta" />';
				echo "\n";
				//Utilizzo di un hidden per differenziare i due form e fare un handling diverso della risorsa in base a quale è stata selezionata
				echo '<input type="hidden" value="Open" name="choice" />';
				echo "\n";
				echo	'</form>';
				echo "\n";
				echo	'<form class="init" name="survey" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
				echo "\n";
				echo		'<input class="Invio" id="Survey" type="submit" value="Sondaggio" />';
				echo "\n";
				echo		'<input type="hidden" value="Survey" name="choice" />';
				echo "\n";
				echo '</form>';
			}
		?>
	</div>
</div>
	
<script>
	
	//Piccolo script che imposta il contenuto al centro della pagina
	var w = window.innerWidth
	|| document.documentElement.clientWidth
	|| document.body.clientWidth;

	var h = window.innerHeight
	|| document.documentElement.clientHeight
	|| document.body.clientHeight;
	
	var introh = document.getElementById("Intro").clientHeight;
	if(introh < h){ //Evita tagli del contenuto se più grande dell'iframe
	document.getElementById("Intro").style.position = "relative";
	document.getElementById("Intro").style.top = ((h-introh)/2) + "px";	
	}
	
</script>
	
</body>
</html>