<!--
function minimize(idcko)
{

	co=document.getElementById(idcko);
	
	if(co.style.display!="none"){
		co.style.display="none";
	}else{
		co.style.display="block";
	}

} 



function show_it(item) {

	co=document.getElementById(item);

	co.style.display="block";

}



function hide_it(item) {

	co=document.getElementById(item);

	co.style.display="none";

}




function potvrd(hlaska){
    	return confirm(hlaska);
}





function change_img(idcko,img1,img2)
{

	var brokenstring=document.getElementById(idcko).src.split("/");
	delka=brokenstring.length;
	var aktualni_obrazek=brokenstring[delka-1]



	if(aktualni_obrazek==img2){
		document.getElementById(idcko).src="img/"+img1;
	}else{
		document.getElementById(idcko).src="img/"+img2;
	}
} 





function zaskrtni_vse(){
	
	//zde zadat jmeno formulare
	var jmeno_formulare="formular";
		
	
	var seznam_elementu=document.forms[jmeno_formulare].elements;
		
	for(var i=0; i<seznam_elementu.length;i++ ){ 
		
			var jmeno_elementu=seznam_elementu[i].name;

			if(document.forms[jmeno_formulare].elements[jmeno_elementu].type=="checkbox"){
			
				if(document.forms[jmeno_formulare].elements['vse'].checked!=true){
					document.forms[jmeno_formulare].elements[jmeno_elementu].checked=false;
				}else{
					document.forms[jmeno_formulare].elements[jmeno_elementu].checked=true;
				}
			
				
			}
				
	}
	
}







//pro zatmìní
function ztmaveni(idcko){
   
   var xScroll, yScroll;
   
   if (window.innerHeight && window.scrollMaxY) {   
      xScroll = document.body.scrollWidth;
      yScroll = window.innerHeight + window.scrollMaxY;
   } else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
      xScroll = document.body.scrollWidth;
      yScroll = document.body.scrollHeight;
   } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
      xScroll = document.body.offsetWidth;
      yScroll = document.body.offsetHeight;
   }
   
   var windowWidth, windowHeight;
   if (self.innerHeight) {   // all except Explorer
      windowWidth = self.innerWidth;
      windowHeight = self.innerHeight;
   } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
      windowWidth = document.documentElement.clientWidth;
      windowHeight = document.documentElement.clientHeight;
   } else if (document.body) { // other Explorers
      windowWidth = document.body.clientWidth;
      windowHeight = document.body.clientHeight;
   }   
   
   // for small pages with total height less then height of the viewport
   if(yScroll < windowHeight){
      pageHeight = windowHeight;
   } else { 
      pageHeight = yScroll;
   }

   // for small pages with total width less then width of the viewport
   if(xScroll < windowWidth){   
      pageWidth = windowWidth;
   } else {
      pageWidth = xScroll;
   }

	co=document.getElementById(idcko);
	co.style.height=pageHeight+'px';
	co.style.width=pageWidth+'px';
}







function getMouseXY(e,iddivu,vlevo,nahoru) {

var IE = document.all?true:false;
var tempX = 0;
var tempY = 0;

if (IE) { // grab the x-y pos.s if browser is IE
tempX = event.clientX + document.body.scrollLeft;
tempY = event.clientY + document.body.scrollTop;
}
else {  // grab the x-y pos.s if browser is NS
tempX = e.pageX;
tempY = e.pageY;
}
if (tempX < 0){tempX = 0;}
if (tempY < 0){tempY = 0;}
document.getElementById(iddivu).style.left = tempX - vlevo+'px';
document.getElementById(iddivu).style.top  = tempY - 300-nahoru+'px';
return true;
}





function getMouseYObrazovka(e,iddivu,nahoru) {

	var tempY = 0;
	
	tempY = e.clientY + document.documentElement.scrollTop;

	document.getElementById(iddivu).style.top  = tempY-nahoru+'px';

return true;
}










     
// -->