<?php
	session_start();
	$name = $_SESSION["Usr"];
	require '../Commons/basic_function.php';
?>
<html>
<head>
<link rel="stylesheet" href="../Commons/Personal.css" />
<link rel="shortcut icon" href="Commons/loghetto.png" />
<meta charset="utf-8">
<?php
	print('
<title>'.$name.' lo sa</title>
	');

	//Funziona solo nel mio pc modificare porta, dbname
	//Praticamente si sostituiscano le righe commentate
		
	$conn = createConnection();
	
	//$conn = pg_connect("host=localhost port=5432 dbname=ChiLoSa user=postgres password=milano");
	
	
	//Verifica che l'utente abbia inserito le proprie categorie di interesse, non sono permesse interazioni con il sistema senza aver inserito le categorie di interesse
	$topic_query=pg_query($conn, 'SELECT "Topic" FROM "Interessi" WHERE "Utente" = \''.$name.'\'');
	$img_query=pg_query($conn, 'SELECT "Immagine" FROM "Utente" WHERE "Nome" = \''.$name.'\'');
	
	
	//Generalmente al login il target dell'iframe è una pagina di benvenuto 
	$path_target = "InnerPersonalPage/opening.php";
	
	$data = "";
	
	if(!$topic_query || !$img_query) {
   			echo "Error connecting DB!";
  	}
  	if (pg_num_rows($topic_query) == 0) {
		//Verrà eseguito sostanzialmente al primo login, impostando il target dell'iframe
		$path_target = 'InnerPersonalPage/categorie.php';
		$data = pg_fetch_object($img_query,0);
		$data = $data->Immagine;
	}
	else{
		$data = pg_fetch_object($img_query,0);
		$data = $data->Immagine;
	}
	
?>
</head>

<!--Pagina Principale di ChiLoSa, permette di accedere direttamente da questa unica pagina a tutte le funzionalità
	Viene utilizzato un'iframe per dare l'illusione di effettuare tutte le operazioni da questa pagina, ed avere un sistema più compatto-->

	
<script src="../Commons/Personal.js"></script>

<body onload="setDisplay()" onresize="setDisplay()">




<header id="testata">
	<div id="heading">
	<!--Logo-->
    	<a  href="InnerPersonalPage/opening.php" target="window_view" onmouseover="switchDescrizione('Credits')" onmouseout="statusQuo()">
			<img id="logo" src="../Commons/logo_da_scale.png" href="InnerPersonalPage/opening.php" target="window_view"/>
		</a>
	</div>
	<div id="Personal_Info">
    <!--Viene presentata l'immagine profilo dell'utente o quella generica-->
    	<div id="Info">
   			<span onmouseover="switchDescrizione('Profilo')" onmouseout="statusQuo()">
				<b>
					<?php echo '<a class="Info" href="InnerPersonalPage/profilo.php?u='.$name.'" target="window_view">'.$name.'</a>' ?>
				</b>
			</span><br>
    		<span id="Impostazioni" onmouseover="switchDescrizione('Impostazioni')" onmouseout="statusQuo()">
				<b>
					<a class="Info" href="InnerPersonalPage/impostazioni.php" target="window_view">Impostazioni</a>
				</b>
			</span>
        </div>
		<?php
		echo '<img id="immagine" src="'.$data.'" />';
		?>
	</div>		
</header>

<div id="Main_Pain">
	<div id="Content">
    <!--Qui va inserito l'Iframe per il contenuto-->
	<?php
		echo '<iframe id="Window" src="'.$path_target.'" name="window_view"></iframe>';
    ?>
	</div>
	<aside id="Button_Pain">
	<!--Qui vanno inseriti i Bottoni che permettono di usufruire delle varie funzionalità-->
    	<a class="Menu_Link" id="ChiLoSa_btn" href="InnerPersonalPage/chilosa.php" target="window_view" onmouseover="switchDescrizione('ChiLoSa')" onmouseout="statusQuo()">Chi lo Sa?</a> 
		<a class="Menu_Link" id="IoLoSo_btn" href="InnerPersonalPage/ioloso.php" target="window_view" onmouseover="switchDescrizione('IoLoSo')" onmouseout="statusQuo()">Io lo So</a>
		<a class="Menu_Link" id="Categorie_btn" href="InnerPersonalPage/categorie.php" target="window_view" onmouseover="switchDescrizione('Categorie')" onmouseout="statusQuo()">Le Mie Categorie</a>
		<a class="Menu_Link" id="LogOut_btn" href="InnerPersonalPage/logout.php" onmouseover="switchDescrizione('LogOut')" onmouseout="statusQuo()">Logout</a>
    </aside>
</div>

<footer id="Descrizione">
    <!--Qui verrà generato l'help per ogni tasto premibile nella pagina-->
	Benvenuto in <strong><em>Chi Lo Sa</em></strong> il miglior posto dove chiedere informazioni, consigli o curiosità!
</footer>

</body>
</html>