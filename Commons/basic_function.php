<?php

error_reporting(0);

//Creo una funzione che dato il risultato di una query mi restituisce un array di tuple (oggetti)
	function fromQueryToArray($result){
		$row = 0;
		$array;
		$pos=0;
			
		while ($data = pg_fetch_object ($result, $row)) {
			$array[$pos] = $data;
			$pos++;
    		$row++;
		}
		
		return $array;
	}
	
//Funzione che instaura una connessione con il DB
	function createConnection(){
		return pg_connect("host=localhost port=5342 dbname=chilosa user=cls password=chilosa");
	}
	
//Funzione che trasforma l'input in un formato consono all'inserimento nel DB (with trim)
	function test_input($data) {
		$data = str_replace("'","''",$data); //Questa funzione permette di inserire nel DB anche caratteri speciali come li apici.
  		$data = trim($data);
  		$data = stripslashes($data); //Elimina gli slash
  		$data = htmlspecialchars($data); //trasforma i tag htlm in caratteri normali
  		return $data;
	}

//Funzione che trasforma l'input in un formato consono all'inserimento nel DB (without trim)
	function checkChar($string){
		$string = str_replace("'","''",$string);  
		$string = htmlspecialchars($string);
		$string = stripslashes($string);
		return $string;
	}
	
	
	
?>