
function swtch2(obrazek, imgname) {
    document[imgname].src = "img/zalozky/"+obrazek+".gif";
}

function swtch3(obrazek, imgname) {
    document[imgname].src = "img/menu/"+obrazek+".png";
}

function swtch4(obrazek, imgname, oblast) {



	if(oblast){

	
		//je potreba zkontrolovat, jestli je oblast, ke ktere ma zalozka vztah, neaktivni. Pokud ano, po mouseout se zalozka zneaktivnuje
		if(document.getElementById(oblast).style.display=="none"){
			document[imgname].src = "img/diskuse/"+obrazek+".png";
		}


	}else{
		document[imgname].src = "img/diskuse/"+obrazek+".png";
	}
}
