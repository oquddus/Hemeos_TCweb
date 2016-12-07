var xmlhttp

function showHint(url,cil,zpusob){

	xmlhttp=GetXmlHttpObject();
	
	if (xmlhttp==null){
	   alert ("V� prohl�e� nepodporuje XMLHTTP!");
	   return;
	}




	xmlhttp.onreadystatechange=function(){

			if (xmlhttp.readyState==4){

				if(zpusob==1){
					document.getElementById(cil).innerHTML=xmlhttp.responseText;
				}

				if(zpusob==2){
					document.getElementById(cil).value=xmlhttp.responseText;
				}
			

	  		}
		}


	url=url+"&amp;sid="+Math.random();
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}








function showHint2(url,cil,zpusob,promenna){

	xmlhttp=GetXmlHttpObject();
	
	if (xmlhttp==null){
	   alert ("V� prohl�e� nepodporuje XMLHTTP!");
	   return;
	}




	xmlhttp.onreadystatechange=function(){

			if (xmlhttp.readyState==4){

				if(zpusob==1){
					document.getElementById(cil).innerHTML=xmlhttp.responseText;
				}

				if(zpusob==2){

					document.getElementById(cil).value=xmlhttp.responseText;
				}

				
				if(typeof next_fce=='function'){
					next_fce(promenna);
				}
	  		}
		}


	url=url+"&amp;sid="+Math.random();
	xmlhttp.open("GET",url,true);
	xmlhttp.send(null);
}







function showHint_post(url,params,cil,zpusob){

	xmlhttp=GetXmlHttpObject();
	
	if (xmlhttp==null){
	   alert ("V� prohl�e� nepodporuje XMLHTTP!");
	   return;
	}




	xmlhttp.onreadystatechange=function(){

			if (xmlhttp.readyState==4){

				if(zpusob==1){
					document.getElementById(cil).innerHTML=xmlhttp.responseText;
				}

				if(zpusob==2){
					document.getElementById(cil).value=xmlhttp.responseText;
				}
			
				
				if(typeof next_fce_post=='function'){
					next_fce_post();
				}

	  		}
		}


	url=url+"&amp;sid="+Math.random();
	xmlhttp.open("POST",url,true);

	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.setRequestHeader("Content-Encoding", "multipart/form-data");
	xmlhttp.setRequestHeader("Content-length", params.length);
	xmlhttp.setRequestHeader("Connection", "close");

	xmlhttp.send(params);
}








function GetXmlHttpObject(){
	if (window.XMLHttpRequest){
	   // code for IE7+, Firefox, Chrome, Opera, Safari
	   return new XMLHttpRequest();
	}

	if (window.ActiveXObject){
	   // code for IE6, IE5
	   return new ActiveXObject("Microsoft.XMLHTTP");
	}

	return null;
}
