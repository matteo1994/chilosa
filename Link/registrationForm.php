<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="Commons/style_static.css">
<link rel="shortcut icon" href="Commons/loghetto.png" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chi Lo Sà</title>
</head>

<!--Pagina di Handling della richiesta di registrazione, gestisce eventuali errori, ma permette l opportunità di continuare la navigazione.-->

<body onload="setDisplay();putOnTop()" onresize="setDisplay();putOnTop()">

<?php
	require '../Commons/basic_function.php';

	$nickname = $name = $surname = $email = $password = "";
	$inserted = false;
	
  	if ($_SERVER["REQUEST_METHOD"] == "POST") {
  		$nickname = test_input($_POST["nickname"]);
  		$email = test_input($_POST["email"]);
		$name = test_input($_POST["name"]);
  		$surname = test_input($_POST["surname"]);
  		$password = test_input($_POST["password"]);
  		
		//Funziona solo nel mio pc modificare porta, dbname
		//Durante la prova di lab ricorda di creare l'utente.
		
		$conn = createConnection();
		
		//$conn = pg_connect("host=localhost port=5432 dbname=ChiLoSa user=postgres password=milano");
		
		
		//Verifica se il nickname inserito è già presente
		$result = pg_query($conn, 'SELECT * FROM "Utente" WHERE "Nome" = \''.$nickname.'\';');
		
		if  (!$result) {
   			echo "Error on contact DB";
  		}
  		if (pg_num_rows($result) == 0 && !empty($nickname) && !empty($password)) {
			//Esegue la registrazione
			$query = 'INSERT INTO "Utente" ("Nome", "Mail", "Password", "Nome_Vero", "Cognome_Vero")
VALUES (\''.$nickname.'\', \''.$email.'\', \''.$password.'\', \''.$name.'\', \''.$surname.'\');';
   			$inserted = pg_query($conn, $query); //i vari altri attributi
  		}
	}
  
?>

<div id="regPanel">
    	<form name="Registration" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" onsubmit="return checkPassword()"  method="POST">
        <?php
			if(!$inserted){
			if($name == ""){
				$holder = "Nickname ";
			}
			else{
				$holder = $name;
			}
			
			//Nel caso si verifichi un errore nella registrazione per incoerenza dei dati viene retificato.
			
			print( '
    			<div id="input">
        			<input class="alert" type="text" name="nickname" placeholder="'.$nickname.' " required />
            		<br />
            		<input type="text" name="name" placeholder="Nome " required />
            		<br />
    				<input type="text" name="surname" placeholder="Cognome " required />
            		<br />
    				<input type="email" name="email" placeholder="Email " required />
            		<br />
    				<input class="alert" type="password" name="password" placeholder="Password " maxlength="16" required />
            		<br />
    				<input type="password" name="password-verified" placeholder="Conferma Password " maxlength="16" required />
					<br />
            		<p id="alarm" class="alarm" style="">*Errore, nickname gi&agrave in uso! Oppure hai inserito dati non validi</p>
    				<p id="condition">Conferma le condizioni di utilizzo:
    				<input type="checkbox" name="cond" required/>Confermo
    				</p>
        		</div>        
        		<div id="formButton">
   					<input type="submit" name="send" value="Registrati" />
    				<input type="button" name="delete" value="Cancella" onclick="setDisplay()"/>
        			<input type="hidden" name="action" value="form1" />
        		</div>
				');
			}else{
				
				//La registrazione va a buon fine 
				
				print( '
					<p id=Conferma>Iscrizione avvenuta con successo!<br>Ciao '.$name.' benvenuto nella comunity!<p>
					<div id="formButton">
    					<input type="button" name="delete" value="Indietro" onclick="setDisplay()"/>
        			</div>
					');
			}
			?>
        </form> 
		
</div>

<div id="MainPanel">
	<div id="Logo">
    <img src="Commons/c_logo.png" alt="" class="small_logo" id="C"/>
    <img class="small_logo" id="L" src="Commons/l_logo.png" alt=""/>        
   	<img class="small_logo" id="S" src="Commons/s_logo.png" alt=""/> 
    <img src="Commons/hi_logo.png" id="HI" alt=""  />
    <img src="Commons/o_logo.png" id="O" alt="" />
    <img src="Commons/a_logo.png" id="A" alt=""  />
    </div>
    <form name="Login_Panel" action="loginForm.php" method="post" > 
    	<div id="Login">
    		<input id="login_field1" type="text" name="login" placeholder="Nickname " required />
        	<input id="login_field2" type="password" name="loginpassword" placeholder="Password " required />
    	</div>
    	<div id="LoginButtons">
        	<input id="Registrati" type="button" name="request_account" value="Registrati" onclick="putOnTop()" />
        	<input id="Accedi" type="submit" name="request_access" value="Accedi" />
            <br />
   	 	</div>
    </form>
    
    <section id="Descrizione">
    	Chi Lo Sà è il miglior posto dove chiedere informazioni, consigli o curiosità. <br />
        Chiunque può inserire delle domande e chiunque può rispondere, basta iscriversi per entrare a far parte della community, e tu che dici? <br /> Lo sai o no?
     </section> 
</div>

<script>

function putOnTop(){
	document.getElementById("regPanel").style.zIndex = 2;
}

//Funzione che posiziona al centro del browser il contenuto. Viene richiamata al Load della Pagina e ogni volta che avviene un Resize
function setDisplay(){
	var w = window.innerWidth
	|| document.documentElement.clientWidth
	|| document.body.clientWidth;

	var h = window.innerHeight
	|| document.documentElement.clientHeight
	|| document.body.clientHeight;
	

	document.getElementById("MainPanel").style.top = ((h-520)/2) + "px";	
	document.getElementById("MainPanel").style.left = ((w-1074)/2) + "px";

	document.getElementById("regPanel").style.top = ((h-500)/2) + "px";	
	document.getElementById("regPanel").style.left = ((w-1070)/2) + "px";
	document.getElementById("regPanel").style.zIndex = -2;
}


//Questa funzione verifica che le password inserite durante la registrazione combacino
function checkPassword(){
	var x = document.forms["Registration"]["password"].value;
	var y = document.forms["Registration"]["password-verified"].value;
    if (x != y) {
        document.getElementById("alarm").innerHTML = "*Le password devono coincidere!";
        return false;
    }
	else{
		return true;
	}
}
</script>
<footer>
</footer>
</body>
</html>
