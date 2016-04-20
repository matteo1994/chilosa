<?php
	session_start();
	$name = $_SESSION["Usr"];
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>Detail</title>
</head>

<!--Questa pagina permette di vedere in dettaglio le rispsote ad una domanda aperta o un sondaggio, proporne di nuove o votare quelle gia esistenti
	oppure partecipare ad un sondaggio-->

<body>

<div id="Corpo">
	<div id="Intro">
		<?php
			$conn = createConnection();
				
				//Primo flusso di esecuzione dovuto all'indirizzamento dalla pagina IoLoSo
				if(isset($_GET["q"]) && isset($_GET["t"])){
					
						//Recupero delle informazioni sulla domanda ottenute dalla pagina ioloso
						$id = $_GET["q"];
						$testo = $_GET["testo"];
						$img = $_GET["img"];
						$cap = $_GET["caption"];
						$owner = $_GET["owner"];
						$date = $_GET["date"];
						$sondaggio = $_GET["t"];
						$close = ($_GET["close"] == "t")? true : false;
						
						//Flusso di esecuzione che viene eseguito solo in caso il proprietario della domanda tenta di chiudere la domanda o il sondaggio.
						if(isset($_GET["action"]) && $_GET["action"]=="chiudi" && $owner==$name){
							if(!$sondaggio){
								$update_close = 'UPDATE "Domande_Aperte" SET "Chiuso" = \'True\' WHERE "ID_Domanda"= \''.$id.'\'';
							}
							else{
								$update_close = 'UPDATE "Sondaggio" SET "Chiuso" = \'True\' WHERE "ID_Sondaggio"= \''.$id.'\'';
							}
							if(!pg_query($conn, $update_close)){
								echo 'Error connecting DB!';
							}
						}
						
						$toclose = ($close)? 't' : 'f';
						
						//Preparo il link per mettere un like nel caso l utente sia vip
						$likos = $dislikos = $tmp = '<a href="domanda.php?q='.$id.'&testo='.$testo.'&owner='.$owner.'&img='.$img.'&caption='.$cap.'&date='.$date.'&t='.$sondaggio.'&close='.$toclose.'';
						
					//Flusso di esecuzione che viene eseguito solo in caso si provi ad inserire una risposta o a completare un sondaggio
					//Sostanzialmente la pagina questa pagina php funziona in modo ricorsivo e divide l'esecuzione a seconda delle operazioni effettuate
					//in precedenza
					if(isset($_GET["Risposta"])){
						$risposta = $_GET["Risposta"];
						if(!$sondaggio){
							//Inserimento della risposta aperta
							$rispondi = pg_query($conn, 'INSERT INTO "Risposte_Aperte"("ID_Domanda","Testo", "Utente") VALUES(\''.$id.'\', \''.checkChar($risposta).'\', \''.$name.'\')');
							}
						else{
							//Inserimento della risposta al sondaggio
							//se selezionato il "flag" anonimo, questa variabile viene impostata
							$anonimo = (isset($_GET["Anonimo"]) && $_GET["Anonimo"]== 'Anonimo')? '\'true\',' : '\'false\',';
							$rispondi = pg_query($conn, 'INSERT INTO "Votanti"("ID_Risposta","Anonimo", "ID_Sondaggio", "Utente") VALUES(\''.$risposta.'\','.$anonimo.'\''.$id.'\',\''.$name.'\')');
						}
						if(!$rispondi){
							//Handling degli errori
							if(!$sondaggio){
								
								echo '<em id="alert" style="color:#F00">Errore nell\'inserimento della risposta!</em>';
								echo "\n";
							}
							else{
								echo '<em id="alert" style="color:#F00">Errore nell\'inserimento del voto!</em>';
								echo "\n";
							}
						}
					}
					
					//Flusso di esecuzione dovuto ad un richiesta di mettere un "mi piace"/"non mi piace" ad una domanda
					if(!$sondaggio){
						//controllo se impostati correttamente i parametri necessari per votare
						if(isset($_GET["l"]) && isset($_GET["d"]) && isset($_GET["tag"]) && isset($_GET["vip"])) {
							$lik = $_GET["l"];
							$dislik = $_GET["d"];
							$target = $_GET["tag"];
							
							//inserimento del voto
							$vota = pg_query($conn, 'INSERT INTO  "Voto" ("Utente", "ID_Risposta", "Like", "Dislike")
									VALUES(\''.$name.'\',\''.$target.'\',\''.$lik.'\',\''.$dislik.'\')');
							if(!$vota){
								echo '<em id="alert" style="color:#F00">Errore nell\'inserimento del voto!</em>';
								echo "\n";
							}
						}
					}
					
					
					//Verifica che untente sia Vip oppure no, controllo effettuato dopo il possibile flusso di inserimento di un mi piace
					//in modo da valutare il caso in cui uno metta mi piace alle proprie risposte.
					$vip = pg_query($conn, 'SELECT "Numero_Risposte","Dislikes","Likes","Vip" FROM "Utente_Stats" LEFT JOIN "Utente" ON "Utente" = "Nome" WHERE "Utente"=\''.$name.'\'');
					if(!$vip){
						echo 'Error connecting DB!';
					}
					else{
						if(pg_num_rows($vip) == 0){
							$vip = false;
						}
						else{
							$vip = $data = pg_fetch_object($vip,0);
							//verifica delle condizioni sugli attributi	
							if($vip->Numero_Risposte > 5  && ($vip->Dislikes == 0 || (($vip->Likes - $vip->Dislikes) >= 0 ))){
								if($vip->Vip == 't'){
									$vip = true;
								}
								else{
									$vip = true;
									if(!pg_query($conn, 'UPDATE "Utente" SET "Vip" = \'True\' WHERE "Nome"= \''.$name.'\'')){
										echo 'Error connecting DB!';
									}
								}
								
							}
							else{
								if($vip->Vip == 't'){
									$vip = false;
									if(!pg_query($conn, 'UPDATE "Utente" SET "Vip" = \'False\' WHERE "Nome"= \''.$name.'\'')){
										echo 'Error connecting DB!';
									}
								}
								else{
									$vip = false;
								}
							}
						}
						
						//Recupero delle categorie della domanda/sondaggio
						$tabella = ($sondaggio)? 'SELECT "Topic" FROM "Appartenenza_Sondaggio" WHERE "ID_Sondaggio" = \''.$id.'\'' : 'SELECT "Topic" FROM "Appartenenza_Domande" WHERE "ID_Domanda" = \''.$id.'\'';
						
						$result = pg_query($conn, $tabella);	
						
						if(!$result){
							echo 'Error connecting DB!';
						}
						else{
							//Stampa delle categorie
							$result = fromQueryToArray($result);
							$topics = '';
							foreach($result as $value){
								$topics = $topics.''.$value->Topic.'  ';
							}
							echo '<span><em>'.$topics.'</em></span>';
							
							//Stampa informazioni domande
							echo '<p id="Main_Field">'.$testo.'</p>';
							echo 'from <a class="lightbg" href="profilo.php?u='.$owner.'&from=ioloso">'.$owner.'</a>&nbsp;&nbsp;&nbsp;&nbsp;'.$date.'<br>';
							($img != null)? print('<br><a class="lightbg" href="'.$img.'" target="_blank">'.$img.'</a>') : print('');
							($cap != null)? print('<br>'.$cap) : print('');
							
							//Casualità della chiusura della domanda
							echo '<br><br>';
							if($close){
								echo '<span>La domanda è chiusa!</span>';
							}
							else{
								if($name == $owner){
									$type = (!$sondaggio)? 'Chiudi Domanda' : 'Chiudi Sondaggio';
									$target_path = 'domanda.php?q='.$id.'&testo='.$testo.'&owner='.$owner.'&img='.$img.'&caption='.$cap.'&date='.$date.'&t='.$sondaggio.'&close=t&action=chiudi';
									echo '<div style="display:block;text-align:center"><a class="Back_Button" href="'.$target_path.'">'.$type.'</a></div><br>';
									
								}
							}
							
							//Se non è un sondaggio stampa delle risposte
							if(!$sondaggio){
								
								//Query che permette di ottenere dal DB con una sola interrogazione tutte le risposte date ad una domanda ordinate in ordine cronologico
								//con l'aggiunta del numero dei likes e dei dislikes e un attributo che specifica se l'attuale utente a già dato un mi piace o no
								//per quella determinata risposta. Permette di risparmiare interazioni con il DB e riduce la possibilità di errori di comunicazione.
								$get_ans = 'SELECT r."ID_Risposta", "Testo", r."Utente",COALESCE("Likes", 0) AS "Likes", COALESCE("Dislikes", 0) AS "Dislikes",
											to_char("Data", \'DD Mon YYYY HH24:MI:SS\') AS "printData", s."Utente" AS "Votato"
											FROM "Risposte_Aperte" AS r NATURAL LEFT JOIN "Risposte_Aperte_Stats" 
											LEFT JOIN (SELECT "ID_Risposta", "Utente" FROM "Voto" WHERE "Utente"=\''.$name.'\') AS s
											ON r."ID_Risposta"=s."ID_Risposta" 
											WHERE "ID_Domanda"=\''.$id.'\' ORDER BY "printData" DESC';
											
								$query = pg_query($conn, $get_ans );
								//Controlli
								if(!$query){
									echo "Error connecting DB!";
								}
								else{	
									if (pg_num_rows($query) == 0) {
										echo '<p>Non sono ancora state inserite risposte</p>';
									}
									else{
										//Stampa delle risposte formattate in una tabella
										$risposte = fromQueryToArray($query);
										echo '<table class="ioloso" align="center">';
										echo "\n";
										foreach($risposte as $value){
											echo '<tr><td>';
											echo '<p>'.$value->Testo.'</p>';
											echo 'from <a href="profilo.php?u='.$value->Utente.'&from=ioloso">'.$value->Utente.'</a>  '.$value->printData.'</p>';
											if($vip && $value->Votato == null){
												//Nel caso in cui si è vip e non si ha ancora espresso un voto per questa risposta sarà attivo il link per il voto!
												$likos = $likos.'&vip=t&l=1&d=0&tag='.$value->ID_Risposta.'"> Likes';
												$dislikos = $dislikos.'&vip=t&l=0&d=1&tag='.$value->ID_Risposta.'"> Dislikes'; 
												print('<p style="text-align:right;">'.$value->Likes.''.$likos.'</a>'.$value->Dislikes.''.$dislikos.'</a></p>');	
											}else{							
												//Scope standard di esecuzione se non si è vip
												print('<p style="text-align:right;">'.$value->Likes.' Likes '.$value->Dislikes.' Dislikes</p>');
											}
											$likos = $dislikos = $tmp;
											echo '</td></tr>';
										}
										echo '</table><br>';						
									}
									if(!$close){
										//Se la domanda non è chiusa sarà disponibile un piccolo form per aggiungere una risposta
										echo '<form class="init" name="quest-template" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="GET">';
										echo "\n";
										echo '<div class="allineamento"><label for="text">Testo:</label><br><textarea id="Text" placeholder="Inserire qui il testo della risposta" name="Risposta" required></textarea>';
										echo "\n";
										//Utilizzo degli hidden per ripassare alla pagina gli stessi dati sulla domanda e non dover fare una query
										echo '<input type="hidden" value="'.$id.'" name="q" />';
										echo '<input type="hidden" value="'.$testo.'" name="testo" />';
										echo '<input type="hidden" value="'.$img.'" name="img" />';
										echo '<input type="hidden" value="'.$cap.'" name="caption" />';
										echo '<input type="hidden" value="'.$owner.'" name="owner" />';
										echo '<input type="hidden" value="'.$date.'" name="date" />';	
										echo '<input type="hidden" value="'.$sondaggio.'" name="t" />';
										echo '<input type="hidden" value="'.$close.'" name="close" />';
										echo '<br><table align="center"><tr><td><input class="Invio" id="Open_send" type="submit" value="Rispondi" /></td>';
										echo '<td><a class="Back_Button" href="ioloso.php">Back</a></td></tr></table>';
										echo "\n";
										echo '</form>';
									}else{
										echo '<br><table align="center"><tr><td><a class="Back_Button" href="ioloso.php">Back</a></td></tr></table>';
									}
								}
							}
							else{
								//Caso in cui sia un sondaggio
								
								//Query per selezionare tutte le risposte e i relativi voti in ordine di Numero di Voti
								$query = pg_query($conn, 'SELECT "ID_Risposta", "Testo", COALESCE("Numero_Votanti", 0) AS "Numero_Votanti"
														  FROM "Risposte_Sondaggio" NATURAL LEFT JOIN "Risposte_Sondaggio_Stats"
														  WHERE "ID_Sondaggio"=\''.$id.'\' ORDER BY "Numero_Votanti" DESC');
								if(!$query){
									echo "Error connecting DB!";
								}
								else{
									//Verifica se un utente ha già risposto o no al sondaggio, in quel caso gli permette solo di guardare i risultati
									$risposte = fromQueryToArray($query);
									$giaRisposto = pg_query($conn, 'SELECT * FROM "Votanti" WHERE "Utente"=\''.$name.'\' AND "ID_Sondaggio"=\''.$id.'\'');
									if(!$giaRisposto){
										echo "Error connecting DB!";
									}
									else{
										$giaRisposto = (pg_num_rows($giaRisposto) == 0)? false : true;
										//Controllo coerenza
										if(!($close || $giaRisposto)){   //DeMorgan Power
											echo '<form class="init" name="quest-template" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="GET">';
											echo "\n";
											
											$voti = "";
											echo '<table class="ioloso" align="center">';
											for($x = 0; $x < count($risposte); $x++){
												$nVot = $risposte[$x]->Numero_Votanti;
												//Se la risposta ha 0 voti il link per vedere i nominativi dei votanti è disattivato
												$voti =($nVot > 0)? '<a href="elencoVoti.php?a='.$risposte[$x]->ID_Risposta.'">'.$nVot.'</a>' : $nVot ;
												echo '<tr>';
												if($x == 0){
													echo '<td>'.$voti.'</td><td><input type="radio" name="Risposta" value="'.$risposte[$x]->ID_Risposta.'" checked>'.$risposte[$x]->Testo.'</td>';
												}
												else{
													echo '<td>'.$voti.'</td><td><input type="radio" name="Risposta" value="'.$risposte[$x]->ID_Risposta.' ">'.$risposte[$x]->Testo.'</td>';
												}
												echo '</tr>';
											}	
											echo '</table>';
											//Possibilità di inserire un voto in formato anonimo o no
											//Utilizzo degli hidden per ripassare alla pagina gli stessi dati sulla domanda e non dover fare una query
											echo '<input type="hidden" value="'.$id.'" name="q" />';
											echo '<input type="hidden" value="'.$testo.'" name="testo" />';
											echo '<input type="hidden" value="'.$img.'" name="img" />';
											echo '<input type="hidden" value="'.$cap.'" name="caption" />';
											echo '<input type="hidden" value="'.$owner.'" name="owner" />';
											echo '<input type="hidden" value="'.$date.'" name="date" />';
											echo '<input type="hidden" value="'.$sondaggio.'" name="t" />';
											echo '<input type="hidden" value="'.$close.'" name="close" />';
											echo '<input id="isAnonimo" type="checkbox" name="Anonimo" value="Anonimo" /><label for="isAnonimo"><span></span>Anonimo?</label>';
											echo '<br><table align="center"><tr><td><input class="Invio" id="Survey_send" type="submit" value="Rispondi" /></td>';
											echo '<td><a class="Back_Button" href="ioloso.php">Back</a></td></tr></table>';
											echo "\n";
											echo '</form>';
										}
										//Stampa comunque i risultati del sondaggio
										else{
											$voti = "";
											echo '<table class="ioloso" align="center">';
											foreach($risposte as $value){
												$nVot = $value->Numero_Votanti;
												$voti =($nVot > 0)? '<a href="elencoVoti.php?a='.$value->ID_Risposta.'">'.$nVot.'</a>' : $nVot ;
												echo '<tr>';
												echo '<td>'.$voti.'</td><td>'.$value->Testo.'</td>';
												echo '</tr>';
											}
											echo '</table>';
											echo '<br><div style="display:block;text-align:center"><a class="Back_Button"  href="ioloso.php">Back</a></div>';
										}
									}	
								}
							}
						}
					}
				}
				//Principalmente statement di debug e di verifica per eventuali sniffer del sito, a questa pagina si può accedere solo tramite IoLoSo
				else{
					echo 'Sei stato spedito qui in qualche modo strano.<br>Tutte le funzioni verranno disabilitate per preservare gli altri utenti.';
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