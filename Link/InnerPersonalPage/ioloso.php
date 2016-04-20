<?php
	session_start();
	$name = $_SESSION["Usr"];
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>IoLoSo</title>
</head>

<!--Questa pagina permette di selezionare una domanda aperta o un sondaggio a cui rispondere o semplicemente da visionare-->

<body>

<div id="Corpo">
	<div id="Intro">
		<?php
			$conn = createConnection();
			
			$quest = $survey = $sondaggi = $domande = "";
			
			//La prima query recupera tutte le domande che corrispondono alle categorie di interesse dell'utente e le ordina in modo cronologico decrescente
			$first_query = 'SELECT DISTINCT "ID_Domanda", "Testo", d."Utente", "Data", "Immagine", "Descrizione", "Chiuso" , to_char("Data", \'DD Mon YYYY HH24:MI:SS\') AS "printData"
							FROM "Domande_Aperte" AS d NATURAL JOIN "Appartenenza_Domande" AS a JOIN "Interessi" AS i ON a."Topic" = i."Topic"
							WHERE i."Utente" = \''.$name.'\'
							ORDER BY "ID_Domanda" DESC';
			
			//La prima query recupera tutti i sondaggi che corrispondono alle categorie di interesse dell'utente e li ordina in modo cronologico decrescente
			$second_query = 'SELECT DISTINCT "ID_Sondaggio", "Testo", s."Utente", "Data", "Immagine", "Descrizione", "Chiuso" , to_char("Data", \'DD Mon YYYY HH24:MI:SS\') AS "printData"
							FROM "Sondaggio" AS s NATURAL JOIN "Appartenenza_Sondaggio" AS a JOIN "Interessi" AS i ON a."Topic" = i."Topic"
							WHERE i."Utente" = \''.$name.'\'
							ORDER BY "ID_Sondaggio" DESC';
			
			$result_one = pg_query($conn, $first_query);
			$result_two = pg_query($conn, $second_query);
			
			//controlli
			if  (!$result_one || !$result_two) {
				echo "Error connecting DB!";
			}
			else{
				if (pg_num_rows($result_one) == 0) {
					//Se uno non ha inserito categorie di interesse sicuramente non visualizzerà alcuna domanda.
					//Oppure se non è stata ancora inserita una domanda per le categorie al quale è interessato un utente finirà in questo scope
					$quest = "Non sono presenti domande relative alle tue categorie di interesse<br>";
				}
				else{
					$domande = fromQueryToArray($result_one);
				}
				if (pg_num_rows($result_two) == 0) {
					$survey = "Non sono presenti sondaggi relativi alle tue categorie di interesse<br>";
				}
				else{
					$sondaggi = fromQueryToArray($result_two);
				}
				
				echo '<div id="ioloso">';
				echo '<p><b>Domande:</b></p>';
				echo '<em>'.$quest.'</em>';
				//Se ci sono domande le stampa
				if($domande != ""){createTable($domande, false);}
				echo '<p><b>Sondaggi:</b></p>';
				echo '<em>'.$survey.'</em>';
				//Se ci sono sondaggi li stampa
				if($sondaggi != ""){createTable($sondaggi, true);}
				echo '</div>';
			}			
		?>
	</div>
</div>

<?php

	//Funzione che dato un array di domande, formatta tutto in una tabella visualizzando
	//per ogni riga la domanda con i relativi dettagli
	function createTable($array_di_domande, $isSurvey){
		//Se è un sondaggio imposta la funzione in modo adatto a trattare i sondaggi
		($isSurvey)? print('<p>Di seguito sono elencati i sondaggi relativi ai tuoi interessi, ') : print('<p>Di seguito sono elencate le domande relative ai tuoi interessi, ');
		echo 'premi sul testo per vederne i dettagli e rispondere o sul nome dell\'Utente per visualizzarne il profilo.</p>';
		echo '<table class="ioloso" align="center">';
		echo "\n";
		foreach($array_di_domande as $value){
			echo '<tr><td>';
			//Per visualizzare le risposte, il link alla domanda passa tutti gli attributi ottenuti tramite la query
			//fatta precedentemente in modo da ridurre le interazioni al DB se non strettamente necessarie
			//sfrutta la feature standard di un browser di richiedere una pagina web tramite HTTP GET
			$target = ($isSurvey)? $value->ID_Sondaggio : $value->ID_Domanda;
			$path = 'q='.$target.'&testo='.$value->Testo.'&owner='.$value->Utente.'&img='.$value->Immagine.'&caption='.$value->Descrizione.'&date='.$value->printData.'&t='.$isSurvey.'&close='.$value->Chiuso.'';
			echo '<a class="table_ioloso" href="domanda.php?'.$path.'">'.$value->Testo.'</a><br>';
			echo 'from <a class="table_ioloso" href="profilo.php?u='.$value->Utente.'&from=ioloso">'.$value->Utente.'</a>&nbsp;&nbsp;&nbsp;&nbsp;'.$value->printData.'';
			 //A seconda della presenza o no dell'immagine o della descrizione vengono visualizzati tali dettagli
			($value->Immagine != null)? print('<br><a class="table_ioloso" href="'.$value->Immagine.'" target="_blank">'.$value->Immagine.'</a>') : print('');
			($value->Descrizione != null)? print('<br>'.$value->Descrizione) : print('');
			echo '</td></tr>';
		}
		echo '</table><br>';
	}

?>
	
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