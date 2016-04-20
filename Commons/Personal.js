function setDisplay(){
	w = window.innerWidth
	|| document.documentElement.clientWidth
	|| document.body.clientWidth;

	h = window.innerHeight
	|| document.documentElement.clientHeight
	|| document.body.clientHeight;
	

	if(!(w <= 900 || h <= 600)){
		document.getElementById("Personal_Info").style.width = (w-(w/1.2814)) + "px";	
		if(h <= 643){
			document.getElementById("Personal_Info").style.height = (h/4.018) + "px";
		}
		document.getElementById("Personal_Info").style.left = (w/1.2814) + "px";
		document.getElementById("Personal_Info").style.fontSize = (w/85.375) + "px";
		if(h <= 643){
			document.getElementById("heading").style.height = (h/10.046) + "px";
		}
		document.getElementById("heading").style.width = (w/1.2814) + "px";
		if(h <= 643){
			document.getElementById("testata").style.height = (h/3.993) + "px";
		}		
		document.getElementById("testata").style.width = w + "px";
		document.getElementById("immagine").style.height = (w/9.757) + "px";
		document.getElementById("immagine").style.width = (w/9.757) + "px";
		document.getElementById("immagine").style.right = (w/56.916) + "px";
		if(h <= 643){
			document.getElementById("immagine").style.top = (h/80.375) + "px";
		}
		document.getElementById("Info").style.paddingTop = (h/9.185) + "px";
		document.getElementById("Info").style.paddingLeft = (w/68.3) + "px";
		document.getElementById("logo").style.left = (w/136.6) + "px";
		document.getElementById("logo").style.top = (h/64.3) + "px";
		document.getElementById("logo").style.height = (h/14.613) + "px";
		document.getElementById("logo").style.width = (w/18.459) + "px";
		document.getElementById("Content").style.height = (h/1.108) + "px";
		document.getElementById("Window").style.top = (h/8.037) + "px";
		document.getElementById("Window").style.left = (w/97.571) + "px";
		document.getElementById("Window").style.width = (w/1.321) + "px";
		document.getElementById("Window").style.height = (h/1.451) + "px";
		document.getElementById("Button_Pain").style.left = (w/1.2814) + "px";
		document.getElementById("Button_Pain").style.top = (h/4.018) + "px";
		var height = 483;

		if(h <= 643){
			document.getElementById("Button_Pain").style.height = (h/1.3312) + "px";
			height = document.getElementById("Button_Pain").style.height;
		}
		var wid = 300;
		document.getElementById("Button_Pain").style.width = (w/4.55333) + "px";
		wid = document.getElementById("Button_Pain").style.width;
		document.getElementById("Descrizione").style.width = (w/1.321) + "px";
		document.getElementById("Descrizione").style.height = (h/6.43) + "px";
		document.getElementById("Descrizione").style.left = (w/83.375) + "px";
		document.getElementById("Descrizione").style.lineHeight = (h/6.43) + "px";
		document.getElementById("Descrizione").style.fontSize = (w/85.375) + "px";
		
		document.getElementById("ChiLoSa_btn").style.top = (height/3.5514) + "px";
		document.getElementById("ChiLoSa_btn").style.left = (w/27.32) + "px";
		document.getElementById("ChiLoSa_btn").style.width = (w/6.83) + "px";
		document.getElementById("ChiLoSa_btn").style.height = (height/12.075) + "px";
		document.getElementById("ChiLoSa_btn").style.lineHeight = (height/12.075) + "px";
		document.getElementById("ChiLoSa_btn").style.fontSize = (w/68.3) + "px";
		document.getElementById("IoLoSo_btn").style.top = (height/3) + "px";
		document.getElementById("IoLoSo_btn").style.left = (w/27.32) + "px";
		document.getElementById("IoLoSo_btn").style.width = (w/6.83) + "px";
		document.getElementById("IoLoSo_btn").style.height = (height/12.075) + "px";
		document.getElementById("IoLoSo_btn").style.lineHeight = (height/12.075) + "px";
		document.getElementById("IoLoSo_btn").style.fontSize = (w/68.3) + "px";
		document.getElementById("Categorie_btn").style.top = (height/2.5967) + "px";
		document.getElementById("Categorie_btn").style.left = (w/27.32) + "px";
		document.getElementById("Categorie_btn").style.width = (w/6.83) + "px";
		document.getElementById("Categorie_btn").style.height = (height/12.075) + "px";
		document.getElementById("Categorie_btn").style.lineHeight = (height/12.075) + "px";
		document.getElementById("Categorie_btn").style.fontSize = (w/68.3) + "px";
		document.getElementById("LogOut_btn").style.top = (height/1.9634) + "px";
		document.getElementById("LogOut_btn").style.left = (w/27.32) + "px";
		document.getElementById("LogOut_btn").style.width = (w/6.83) + "px";
		document.getElementById("LogOut_btn").style.height = (height/12.075) + "px";
		document.getElementById("LogOut_btn").style.lineHeight = (height/12.075) + "px";
		document.getElementById("LogOut_btn").style.fontSize = (w/68.3) + "px";
		
	}
}

function switchDescrizione(target){
	
	switch(target){
		case "Impostazioni": 	document.getElementById("Descrizione").innerHTML = "Arricchisci il tuo profilo inserendo nuovi dati. Ti farai riconoscere più facilmente dagli altri utenti!";
								break;
		case "ChiLoSa":	document.getElementById("Descrizione").innerHTML = "Qui potrai proporre in pochi click una domanda o un sondaggio. Semplice no? Prova.";
						break;
		case "IoLoSo": 	document.getElementById("Descrizione").innerHTML = "Se vuoi rispondere ad una domanda o sondaggio, o semplicemente visualizzarle questa è la sezione apposta per te.";
						break;
		case "Categorie":	document.getElementById("Descrizione").innerHTML = "Qui potrai visualizzare le tue categorie di interesse o inserirle se ancora non hai effettuato questa operazione.";
							break;
		case "Profilo":	document.getElementById("Descrizione").innerHTML = "Visualizza il tuo profilo per vedere cosa gli altri possono conoscere su di te.";
						break;
		case "Credits":	document.getElementById("Descrizione").innerHTML = "Authors: Matteo Marras & Davide Crespellani Porcella. Università degli Studi di Milano";
						break;
		case "LogOut":	document.getElementById("Descrizione").innerHTML = "Da qui sarà possibile disconnettersi da ChiLoSa e si verrà rediretti al login.";
						break;
	}
	
}


function statusQuo(){
	document.getElementById("Descrizione").innerHTML = 'Benvenuto in <strong><em>Chi Lo Sa</em></strong> il miglior posto dove chiedere informazioni, consigli o curiosità!';
}



