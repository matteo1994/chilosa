<?php
	session_start();
	$name = $_SESSION["Usr"];
	//Pattern standard per tenere traccia delle azioni dell'utente durante la navigazione
	
	require '../../Commons/basic_function.php';
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>TopicSelection</title>
</head>

<!--Pagina adibita alla scelta delle categorie di interesse, se questa pagina non viene completata nella sua interezza non sarà possibile
	utilizzare alcun servizio di ChiLoSa-->

<body>

<?php

	$conn = createConnection();
	
	
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		
		$checked = $_POST["Categoria"];
			
			$topic_query = "";
			
			foreach($checked as $value){
				$topic_query = $topic_query.'INSERT INTO "Interessi" ("Utente","Topic") VALUES (\''.$name.'\',\''.$value.'\'); ';
			}
			
			if($topic_query != ""){
				$check_query = pg_query($conn, $topic_query);
				
				if($check_query){
					echo '<div id="Intro" class="mid">Hai inserito correttamente le categorie di tuo interesse!<br>Premi <a href="opening.php">QUI</a> per iniziare la tua esperienza in Chi Lo Sa</div>';
				}
				else{
					echo '<p>Si &egrave verificato un errore di comunicazione con la Base di Dati si prega di aggiornare la pagina dal browser e riprovare.</p>';
				}
			}
			else{
				echo '<div id="Intro" class="mid">Non hai inserito nessuna categoria!<br>Si prega di premere sul bottone a lato "Le mie Categorie".
					 <br>Non sar&agrave possibile utilizzare le altre funzionalità finchè non scegli le tue categorie di interesse!
					</div>';
			}
	}
	
	//Al primo accesso alla pagina viene eseguito questo scope
	if ($_SERVER["REQUEST_METHOD"] == "GET"){
		
		$query=pg_query($conn, 'SELECT * FROM "Interessi" WHERE "Utente" = \''.$name.'\'');
		
		//Verifica che un utente abbia già scelto o no le proprie categorie di interesse
		
		if  (!$query) {
   			echo "Error connecting DB!";
  		}
  		else{
			if (pg_num_rows($query) == 0) {
				
				//Nel caso in cui nessuna categoria sia stata ancora scelta vengono presentate tutte quelle disponibili
				
				//Prima richiedo al DB le categorie "Root"
				$result=pg_query($conn, 'SELECT * FROM "Topic" WHERE "NomePadre" IS NULL');
		
				if  (!$result) {
					echo "Error connecting DB!";
				}
				else{
					if (pg_num_rows($result) == 0) {
						echo "No such topic found."; //Caso in cui il DB non abbia Categorie
					}
					else{
						/*
							Ottenute le categorie root, le passo ad una funzione ricorsiva che per ogni root
							costruisce la gerarchia completa
						 */
						
						echo '<div id="Corpo"><p class="mid" style="padding-left: -10px; text-align: center;">
							 Benvenuto '.$name.' scegli tra le seguenti categorie di interesse.<br>
							 Sono fondamentali per poter utilizzare le funzionalit&agrave di Chi Lo Sa.<br>
							 <em>Attenzione non sarà possibile modificarle in seguito!</em></p>';
						$father = fromQueryToArray($result);
						echo '<form name="Registration" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'" method="POST">';
						
						recursiveTree($father,$conn); //chiamata alla funzione ricorsiva che prende in input un array di root e stampa le gerarchie
						
						echo '<input class="Invio" type="submit" value="Avanti">';
						echo "</form></div>";	
					}
				}
			}
			else{
				
				//Nel caso in cui l'utente abbia già inserito le proprie categorie di interesse, quest'ultime vengono ripresentate senza possibilità di modifica
				$toPrint = fromQueryToArray($query);
				echo '<div id="Intro"><p id="Alarm">Hai gi&agrave selezionato le categorie di tuo interesse, ti verranno riproposte di seguito.<br>Ma &egrave impossibile modificarle!</p>';
				foreach($toPrint as $value){
					echo '<p style="font-weight:bold;padding-left:10px;line-height:70%">'.$value->Topic.'</p>';
				}
				echo '</div>';
			}
		}		
	}
	
	
	//Funzione ricorsiva che permette di parsare una struttura gerarchica ad albero
	function recursiveTree($father, $conn){
		
		//Organizza la gerarchia in una lista per la formattazione in HTML
		
		foreach($father as $value){
			if($value->NomePadre == NULL){
				//Ogni root ha una lista separata di oggetti
				echo '<ul style="list-style-type:none">';
				echo "\n";
			}
			
			//Ottengo un array di figli della sottocategoria selezionata attualmente
			$result=pg_query($conn, 'SELECT * FROM "Topic" WHERE "NomePadre"=\''.$value->NomeTopic.'\'');
			
			if  (!$result) {
   				echo "Error connecting DB!";
  				}
  				else if (pg_num_rows($result) == 0) {
					//Se non ha figli è una semplice voce della lista
					echo '<li><input id="'.$value->NomeTopic.'" type="checkbox" name="Categoria[]" value="'.$value->NomeTopic.'" /><label for="'.$value->NomeTopic.'"><span></span>'.$value->NomeTopic.'</label></li>';
					echo "\n";
					continue;
  				}
				else{
					echo '<li><input id="'.$value->NomeTopic.'" type="checkbox" name="Categoria[]" value="'.$value->NomeTopic.'" onchange="cascadeSelection(\''.$value->NomeTopic.'\')" /><label for="'.$value->NomeTopic.'"><span></span>'.$value->NomeTopic.'</label></li>';
					echo "\n";
					//Se una categoria ha figli, a sua volta crea una sottolista
					echo '<ul id="'.$value->NomeTopic.'_sub" style="list-style-type:none">';
					echo "\n";
					$sons = fromQueryToArray($result);
					recursiveTree($sons,$conn); //Chiamata ricorsiva
				}
			echo '</ul>';
			echo "\n";
			if($value->NomePadre == NULL){
				//Chiusura ultima della lista appartente alla root
				echo '</ul>';
				echo "\n";
			}
		}
	}
	
?>


<script>

	var w = window.innerWidth
	|| document.documentElement.clientWidth
	|| document.body.clientWidth;

	var h = window.innerHeight
	|| document.documentElement.clientHeight
	|| document.body.clientHeight;
	
	if(document.getElementById("Intro") != null){
		var introh = document.getElementById("Intro").clientHeight;
		
		if(introh < h){
			document.getElementById("Intro").style.position = "relative";
			document.getElementById("Intro").style.top = ((h-introh)/2) + "px";	
		}
	}
		
	//Funzione Ricorsiva che checka e unchecka le sottocategorie
	function cascadeSelection(root) {
		/*
			Questa funzione JS percorre tutta la sottogerarchia della categoria selezionata se presente
			checka o unchecka a seconda dello stato della categoria cliccata.
			Il sistema afferisce automaticamente che un'apprezzamento ad una sovracategoria implichi
			un'apprezzamento per le categorie "figlie"
		 */
		var element = root + "_sub";
		var father = document.getElementById(element);
		var check = document.getElementById("" + root).checked;
		var sons = father.children;
		
		//Ciclo per i figli
		for(i in sons){
			if( sons[i].nodeName == "LI"){
				var tmp = sons[i].children;
				var x = document.getElementById("" + tmp[0].id);
				if(check){
					x.checked = true;
				}else{
					x.checked = false;
				}
				//Se trovo un figlio è anch'esso padre continuo per ricorsione
				if(document.getElementById(x.id + "_sub") != null){
					cascadeSelection("" + x.id);  //Chiamata ricorsiva
				}
				
			}
		}
	}
	
	
</script>
</body>
</html>