<?php
	session_start();
	require '../Commons/basic_function.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" href="Commons/style_static.css">
<link rel="shortcut icon" href="Commons/loghetto.png" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Chi Lo Sà</title>
</head>

<!--Pagina di Handling per una richiesta di Login -->

<body onload="setDisplay()" onresize="setDisplay()">

<?php
	$name = $password = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
  		$name = test_input($_POST["login"]);
  		$password = $_POST["loginpassword"];
  		
		//Funziona solo nel mio pc modificare porta, dbname
		//Durante la prova di lab ricorda di creare l'utente.
		
		$conn = createConnection();
		
		//$conn = pg_connect("host=localhost port=5432 dbname=ChiLoSa user=postgres password=milano");
		
		//Query per verificare la correttezza dei dati di Login inseriti
		$result=pg_query($conn, 'SELECT "Password" FROM "Utente" WHERE "Nome" = \''.$name.'\';');
		
		if  (!$result) {
   			echo "Error on contact DB";
  		}
  		if (pg_num_rows($result) == 0) {			
  		}
  		else{
			//Utilizzo degli oggetti per semplificare l'accesso agli attributi
			$data = pg_fetch_object($result,0);
			$check = $data->Password;
			//Se la Password è corretta si viene identificati e si può proseguire nell'utilizzo del servizio
			if($password == $check){
				$_SESSION["Usr"] = $name;  //Utilizzo delle sessioni per mantere traccia delle attività di un utente
				header("Location: PersonalPage.php");
			}
		}
	}
  
?>

<div id="regPanel">
    	<form name="Registration" action="registrationForm.php" onsubmit="return checkPassword()"  method="POST">
    	<div id="input">
        	<input type="text" name="nickname" placeholder="Nickname " required />
            <br />
            <input type="text" name="name" placeholder="Nome " required />
            <br />
    		<input type="text" name="surname" placeholder="Cognome " required />
            <br />
    		<input type="email" name="email" placeholder="E-mail " required />
            <br />
    		<input type="password" name="password" placeholder="Password "  maxlength="16" required />
            <br />
    		<input type="password" name="password-verified" placeholder="Conferma Password "  maxlength="16" required />
            <br />
            <p id="alarm" class="alarm"></p>
    		<p id="condition">Conferma le condizioni di utilizzo:
    		<input type="checkbox" name="cond" required/>Confermo
    		</p>
        </div>        
        <div id="formButton">
   		<input type="submit" name="send" value="Registrati" />
    	<input type="button" name="delete" value="Cancella" onclick="setDisplay()"/>
        </div>
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
    <form name="Login_Panel" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" > 
    	<div id="Login">
    		<input class="alert" id="login_field1" type="text" name="login" placeholder="Nickname " required />
        	<input class="alert" id="login_field2" type="password" name="loginpassword" placeholder="Password " required />
    	</div>
    	<div id="LoginButtons">
        	<p id="LoginAlarm" style="display:inline;">Nome utente e/o password errati! </p>
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
