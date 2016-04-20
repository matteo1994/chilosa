<?php
	session_start();
	$name = $_SESSION["Usr"];
	//Pattern standard per tenere traccia delle azioni dell'utente durante la navigazione	
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<!--Pagina di Benvenuto spiega il funzionamento del sito e come navigare tra le risorse-->

<link rel="stylesheet" href="../../Commons/iframe.css" />
<title>Opening</title>
</head>

<body>

<div id="Corpo">
	<div id="Intro">
		<p class="part">Benvenuto in <strong><em>Chi Lo Sa</em></strong> il miglior posto dove chiedere informazioni, consigli o curiosità!</p><br>
			Potrai inserire delle domande, rispondere o votare direttamente da questa pagina!<br>
			I tasti a lato ti permetteranno uno switch rapido tra le varie funzionalità.<br>
			Ricorda! Il pannello sottostante ti fornirà un utile supporto per utilizzare al meglio il social network!<br><br>
			<p class="part">Enjoy it!</p>
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
	document.getElementById("Intro").style.position = "relative";
	document.getElementById("Intro").style.top = ((h-introh)/2) + "px";	
	
	
</script>
	
</body>
</html>