<?php
function clean_high($value){
    $value = preg_replace("/[^A-Za-z0-9_\-\., ]/", '', $value);
    return mysql_real_escape_string($value);
}
function clean_basic($value){
    $value = preg_replace("/[^A-Za-z0-9_\-|:\.,\'\?\!#&\[\]\$\/ ]/", '', $value);
    return mysql_real_escape_string($value);
}


function osetrit_get($_osetrit=array()){

	if(count($_osetrit)>0):
		foreach($_GET as $klic=>$hodnota):
			if(in_array($klic,$_osetrit)):
				$_GET[$klic]=(int)$hodnota;
			endif;
		endforeach;
	else:
		foreach($_GET as $klic=>$hodnota):
		echo htmlspecialchars($hodnota);
			$_GET[$klic]=(int)$hodnota;
		endforeach;
	endif;

}

 /*
Ošetøení vstupních superglobálních promìnných
*/
function htmlspecialcharsRecursive(&$val) {
/*
  Funkce pro rekurzivní ošetøení hodnot i klíèù metodou htmlspecialchars
  *&$val reference na hodnotu
*/
if(is_array($val)) {  //zjistí zda $val je pole
  $keys = array_keys($val);   //naète všechny klíèe tohoto pole
  foreach($keys As $key) {  //projde celé pole
	$value = htmlspecialcharsRecursive($val[$key]); //pokraèuje v rekurzi
	unset($val[$key]); //smaže pùvodní klíè
	$val[htmlspecialchars($key)] = $value; //vytvoøí prvek s ošetøeným klíèem i hodnotou v pùvodním poli
  }
}
else
  $val = htmlspecialchars($val);  //pokud $val není pole pouze ošetøí hodnotu

return $val; // vrací $val, nutné pro rekurzi
}

function fixSuperglobals() {
/*
  Ošetøí superglobální promìnné proti XSS
*/
$superglobals = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);  //pole vstupních superglobálních promìnných
foreach ($superglobals As &$process) {  //projde pole
  htmlspecialcharsRecursive($process);  //provede rekurzivní ošetøení
}
//odstranìní zbyteèných promìnných
unset($process);
unset($superglobals);
}


//funkce pro osetreni uzivatelskeho vstupu
function safe($promenna){
	return mysql_real_escape_string($promenna);
}


//----nahrada znaku
function do_htmlspecialchar(&$arr) {
  foreach (array_keys($arr) as $key):
    if (is_array($arr[$key])):
	 do_htmlspecialchar($arr[$key]);
    else:
	 $arr[$key] = htmlspecialchars($arr[$key]);
   endif;
   endforeach;
}


function cast_popisu($popis,$delka) {
	if(strlen($popis)>$delka):
		$popis=substr($popis,0,$delka);
		$popis=$popis."...";
	endif;
	return $popis;
}


//--strankovani
function strankovani($dotaz, $_na_stranku, $pocitej_sloupec="*"){

	global $_od;
	global $_stranek;
	$_na_stranku=(int)$_na_stranku;

	if(!$_GET['page']): $_GET['page']=1; endif;
	$_GET['page']=(int)$_GET['page'];

	//echo "SELECT COUNT($pocitej_sloupec) FROM $dotaz";

	$zaznamu=mysql_result(mysql_query("SELECT COUNT(".mysql_real_escape_string($pocitej_sloupec).") FROM $dotaz"), 0);
	$_stranek=ceil($zaznamu/$_na_stranku);

	$_od=($_GET['page']-1)*$_na_stranku;

}

//--strankovani
function strankovani3($dotaz,$_na_stranku){

	global $_od;
	global $_stranek;
	$_na_stranku=(int)$_na_stranku;

	if(!$_GET['page']): $_GET['page']=1; endif;
	$_GET['page']=(int)$_GET['page'];

	//echo "SELECT COUNT($pocitej_sloupec) FROM $dotaz";


	$vysledek = mysql_query($dotaz);

	$zaznamu = mysql_num_rows($vysledek);
	$_stranek=ceil($zaznamu/$_na_stranku);

	$_od=($_GET['page']-1)*$_na_stranku;

}


//--strankovani - cisla stran
function strankovani2(){

	global $_stranek;

	if($_stranek>1):

		echo "<div style=\"float:left; width:100%; margin-top:20px;\">
			<div style=\"float:left; width:100px; text-align:left; margin-left:12px; display:inline;\">"; if(htmlspecialchars($_stranek)): echo "Strana: htmlspecialchars($_GET[page])/htmlspecialchars($_stranek)"; endif; echo "</div>
			<div style=\"float:right; text-align:right; margin-right:12px; display:inline;\">";

			//--zjistit soucasny query string
			$query_string=get_query_string();
			//--v url se uz mozna vyskytuje cislo stranky - to vyhodit
			$query_string=preg_replace("(page=[0-9]+&)", "", $query_string);

				if($_GET['page']>1):
					$kam=$_GET['page']-1;
					echo "<a href=\"htmlspecialchars($_SERVER[PHP_SELF])?page=htmlspecialchars($kam)&amp;htmlspecialchars($query_string)\" class=\"odkaz3\"><</a> &nbsp;";
				endif;


				for($i=1;$i<=$_stranek;$i++):
						if($_GET['page']==$i):
							echo "<b><u class=\"text-orange\">htmlspecialchars($i)</u></b>";
						else:
							echo "<a href=\"htmlspecialchars($_SERVER[PHP_SELF])?page=htmlspecialchars($i)&amp;htmlspecialchars($query_string)\" class=\"odkaz3\">htmlspecialchars($i)</a>";
						endif;

					if($i<$_stranek):
						echo " | ";
					endif;
				endfor;


				if($_GET['page']<$_stranek):
					$kam=$_GET['page']+1;
					echo "&nbsp; <a href=\"htmlspecialchars($_SERVER[PHP_SELF])?page=htmlspecialchars($kam)&amp;htmlspecialchars($query_string)\" class=\"odkaz3\">></a>";
				endif;

		echo "</div>
		</div>";

	endif;

}


function get_query_string(){

	$pole=explode("?",$_SERVER['REQUEST_URI']);

	$query_string=$pole[1];

	return $query_string;
}

//--generuje odkaz pro razeni dle sloupce tabulky
function order($text, $sloupec){

	$get_name="ord_".str_replace(".","",$sloupec);
	if(!htmlspecialchars($_GET[$get_name])):
		$_GET[$get_name]="ASC";
	else:

		if(htmlspecialchars($_GET[$get_name])=="ASC"):
			$_GET[$get_name]="DESC";
		else:
			$_GET[$get_name]="ASC";
		endif;

	endif;

	/*
	//--zjistit soucasny query string
	$query_string=get_query_string();
	//--v url se uz mozna vyskytuje cislo stranky - to vyhodit
	$query_string=preg_replace("(page=[0-9]+&)", "", $query_string);

	//--v url se uz mozna vyskytuje razeni - to vyhodit
	$query_string=preg_replace("(ord_[^&]+&)", "", $query_string);
	$query_string=preg_replace("(col=[^&]+&)", "", $query_string);
	*/

	parse_str($_SERVER['QUERY_STRING'], $params);
	//vyhodit cislo stranky
	unset($params['page']);

	//vyhodit puvodni ord a osetrit gety
	foreach($params as $klic=>$hodnota){
		if (strpos($klic, "ord_") !== false || strpos($klic, "action") !== false) {
			unset($params[$klic]);
		}else{
			$params[$klic]=htmlspecialchars($hodnota,ENT_QUOTES,'ISO-8859-1');
		}
	}

	//nove razeni
	$params[$get_name]=htmlspecialchars($_GET[$get_name]);
	$params['col']=htmlspecialchars($sloupec);

	$link="?".http_build_query($params);

	$odkaz="<a href=\"$_SERVER[PHP_SELF]".$link."\" class=\"odkaz2\">$text</a>";

	return $odkaz;
}

//--generuje dle ceho radit tabulku
function razeni($primarne){

	global $orderby;

	if(htmlspecialchars($_GET['col'])):
		$orderby=clean_high(htmlspecialchars($_GET['col'])." ".htmlspecialchars($_GET['ord_'.str_replace(".","",htmlspecialchars($_GET['col']))]));
	else:
		$orderby=clean_high($primarne);
	endif;
}






function vypsat_tabulku($_poradi_default,$cast){

	//--zjistit, jestli poradi sloupcu neni customizovano
	$vysledek_p = mysql_query("SELECT ID_sloupce FROM _uzivatele_sloupce WHERE ID_uzivatele='$_SESSION[usr_ID]' ORDER BY poradi");
		if(mysql_num_rows($vysledek_p)):
			$_poradi_default=array();
			while($zaz_p = mysql_fetch_array($vysledek_p)):
				extract($zaz_p);

				$_poradi_default[]=$ID_sloupce;

			endwhile;
		endif;
		@mysql_free_result($vysledek_p);

	//--vypsat data
	foreach($_poradi_default as $id):

		$data=$cast."_".$id;
		global ${$data};
		echo ${htmlspecialchars($data)};

	endforeach;

}







function get_id_adresatu($ID_projektu,$prava,$prava_rozsirujici){

	$_pole_id_adresatu=array();

	$vysledek_p = mysql_query("SELECT ID_uzivatele AS id_adresata FROM admin_prava_projekty WHERE ID_projektu='".mysql_real_escape_string($ID_projektu)."' AND prava='".mysql_real_escape_string($prava)."' AND prava_rozsirujici='".mysql_real_escape_string($prava_rozsirujici)."'");
		if(mysql_num_rows($vysledek_p)):
			while($zaz_p = mysql_fetch_array($vysledek_p)):
				extract($zaz_p);
				$_pole_id_adresatu[]=$id_adresata;
			endwhile;
		endif;
		@mysql_free_result($vysledek_p);

	return $_pole_id_adresatu;
}


function get_id_adresatu_zadavatelu($ID_pozadavku){

	$_pole_id_adresatu=array();

	$vysledek_p = mysql_query("SELECT ID_zadavatele AS id_adresata FROM _pozadavky_zadavatele WHERE ID_pozadavku='".mysql_real_escape_string($ID_pozadavku)."'");
		if(mysql_num_rows($vysledek_p)):
			while($zaz_p = mysql_fetch_array($vysledek_p)):
				extract($zaz_p);
				$_pole_id_adresatu[]=$id_adresata;
			endwhile;
		endif;
		@mysql_free_result($vysledek_p);

	return $_pole_id_adresatu;
}



function zasli_email_adresatum($_pole_id_adresatu,$druh_zpravy,$autor_udalosti_id,$ID_pozadavku){

	//--adresatum poslat email
	if(count($_pole_id_adresatu)>0):

		//--zjistit autora udalosti
		$vysledek_p = mysql_query("SELECT CONCAT(jmeno,' ',prijmeni) AS autor, admin.email AS email_od FROM admin WHERE admin.ID='".mysql_real_escape_string($autor_udalosti_id)."'");
			if(mysql_num_rows($vysledek_p)):
				$zaz_p = mysql_fetch_array($vysledek_p);
					extract($zaz_p);
			endif;
		@mysql_free_result($vysledek_p);

		//--zjistit id projektu, nazev a pozadavek - posilam to sice v postu, ale mailuju i z jinych mist, nez po odeslani formulare pozadavku - napr. v diskusi
		$vysledek_p = mysql_query("SELECT ID_projektu, nazev AS nazev_poz, pozadavek AS poz, reakce AS reakce_poz FROM pozadavky WHERE ID='".mysql_real_escape_string($ID_pozadavku)."'");
			if(mysql_num_rows($vysledek_p)):
				$zaz_p = mysql_fetch_array($vysledek_p);
					extract($zaz_p);

					$_POST['nazev']=$nazev_poz;
					$_POST['pozadavek']=$poz;
					$_POST['reakce']=$reakce_poz;
					$_POST['ID_projektu']=$ID_projektu;
			endif;
		@mysql_free_result($vysledek_p);

		//--zjistit firmu zadavatele
		$vysledek_p = mysql_query("SELECT nazev AS nazev_firmy FROM _pozadavky_zadavatele LEFT JOIN admin ON(_pozadavky_zadavatele.ID_zadavatele=admin.ID) LEFT JOIN firmy ON(admin.ID_firmy=firmy.ID) WHERE _pozadavky_zadavatele.ID_pozadavku='".mysql_real_escape_string($ID_pozadavku)."'");
			if(mysql_num_rows($vysledek_p)):
				$zaz_p = mysql_fetch_array($vysledek_p);
					extract($zaz_p);
			endif;
		@mysql_free_result($vysledek_p);


		//--zjistit nazev projektu
		$vysledek_p = mysql_query("SELECT projekty.zkratka AS nazev_projektu FROM projekty WHERE projekty.ID='".mysql_real_escape_string($_POST['ID_projektu'])."'");
			if(mysql_num_rows($vysledek_p)):
				$zaz_p = mysql_fetch_array($vysledek_p);
					extract($zaz_p);
			endif;
		@mysql_free_result($vysledek_p);


		//--textove vyjadreni priority
		switch($_POST['priorita']):
			case 1: $priorita_cz=gtext('nízká',4,'_cz'); $priorita_en=gtext('nízká',4,'_en'); break;
			case 2: $priorita_cz=gtext('støední',5,'_cz'); $priorita_en=gtext('støední',5,'_en'); break;
			case 3: $priorita_cz=gtext('vysoká',6,'_cz'); $priorita_en=gtext('vysoká',6,'_en'); break;
		endswitch;

		//--zjistit textove hodnoceni
		$vysledek_p = mysql_query("SELECT nazev_cz AS hodnoceni_cz, nazev_en AS hodnoceni_en FROM nastaveni_hodnoceni_pozadavku  WHERE ID='".mysql_real_escape_string($_POST['ID_hodnoceni'])."'");
			if(mysql_num_rows($vysledek_p)):
				$zaz_p = mysql_fetch_array($vysledek_p);
					extract($zaz_p);
			endif;
		@mysql_free_result($vysledek_p);


			switch($druh_zpravy):
				case "pozadavek_vlozen":
						$predmet_cz="".gtext('Nový pozadavek',56,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('uživatel',57,'_cz')." <i>$autor</i> ($nazev_firmy) <u>".gtext('vložil nový požadavek',58,'_cz')."</u>.";

						$predmet_en="".gtext('Nový pozadavek',56,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('uživatel',57,'_en')." <i>$autor</i> ($nazev_firmy) <u>".gtext('vložil nový požadavek',58,'_en')."</u>.";
				break;

				case "pridelen_resitel":
						$predmet_cz="".gtext('Prideleni resitele',59,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="<u>".gtext('byl(a) jste urèen(a) jako øešitel požadavku',60,'_cz')."</u> <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i>.";

						$predmet_en="".gtext('Prideleni resitele',59,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="<u>".gtext('byl(a) jste urèen(a) jako øešitel požadavku',60,'_en')."</u> <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i>.";

						$zprava_komentar=1;
				break;

				case "zmena_textu_spravce":
						$predmet_cz="".gtext('Zmena komentare spravce',139,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl okomentován správcem',140,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku',61,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl vyøešen',63,'_en')."</u>.";

						$zprava_komentar=1;
						$zprava_reakce=1;
						$zprava_hodnoceni=1;
						$zprava_datum_vyreseni=1;
				break;

				case "pozadavek_vyresen":
						$predmet_cz="".gtext('Vyreseni pozadavku',61,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl vyøešen',63,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku',61,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl vyøešen',63,'_en')."</u>.";

						$zprava_reakce=1;
						$zprava_hodnoceni=1;
						$zprava_datum_vyreseni=1;
				break;

				case "pozadavek_vyresen_echo_spravci":
						$predmet_cz="".gtext('Vyreseni pozadavku',61,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl vyøešen',63,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku',61,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl vyøešen',63,'_en')."</u>.";

						$zprava_komentar=1;
						$zprava_reakce=1;
						$zprava_hodnoceni=1;
						$zprava_datum_vyreseni=1;
				break;

				case "pozadavek_vyresen_v_nove_verzi":
						$predmet_cz="".gtext('Vyreseni pozadavku v nové verzi',129,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('bude vyøešen v nové verzi',130,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku v nové verzi',129,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('bude vyøešen v nové verzi',130,'_en')."</u>.";

						$zprava_reakce=1;
				break;

				case "pozadavek_vyresen_schvalit":
						$predmet_cz="".gtext('Vyreseni pozadavku - zadost o souhlas',131,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('je vyøešen s prosbou o schválení zákazníkem',132,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku - zadost o souhlas',131,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('je vyøešen s prosbou o schválení zákazníkem',132,'_en')."</u>.";

						$zprava_reakce=1;
				break;

				case "pozadavek_vyresen_akceptovan":
						$predmet_cz="".gtext('Vyreseni pozadavku - odsouhlaseno zakaznikem',133,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('je vyøešen a odsouhlasen zákazníkem',134,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku - odsouhlaseno zakaznikem',133,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('je vyøešen a odsouhlasen zákazníkem',134,'_en')."</u>.";

						$zprava_reakce=1;
				break;

				case "pozadavek_vyresen_bude_nove_verzi":
						$predmet_cz="".gtext('Vyreseni pozadavku - objevi se v nove verzi',135,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('je vyøešen a objeví se v nové verzi',136,'_cz')."</u>.";

						$predmet_en="".gtext('Vyreseni pozadavku - objevi se v nove verzi',135,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('je vyøešen a objeví se v nové verzi',136,'_en')."</u>.";

						$zprava_reakce=1;
				break;


				case "pozadavek_vracen_uzivateli":
						$predmet_cz="".gtext('Zadost o doplneni pozadavku',64,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu)";
						$zprava_cz="".gtext('øešitel požadavku',65,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu)</i> <u>".gtext('žádá o doplnìní informací',82,'_cz')."</u>.";

						$predmet_en="".gtext('Zadost o doplneni pozadavku',64,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu)";
						$zprava_en="".gtext('øešitel požadavku',65,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu)</i> <u>".gtext('žádá o doplnìní informací',82,'_en')."</u>.";

						$zprava_reakce=1;
				break;

				case "pozadavek_vracen_spravci":
						$predmet_cz="".gtext('Vraceni pozadavku spravci',66,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl vrácen správci',67,'_cz')."</u>.";

						$predmet_en="".gtext('Vraceni pozadavku spravci',66,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl vrácen správci',67,'_en')."</u>.";

						$zprava_komentar=1;
						$zprava_reakce=1;
				break;

				case "pozadavek_vracen_resiteli":
						$predmet_cz="".gtext('Vraceni pozadavku resiteli',68,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl vrácen øešiteli',69,'_cz')."</u>.";

						$predmet_en="".gtext('Vraceni pozadavku resiteli',68,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl vrácen øešiteli',69,'_en')."</u>.";

						$zprava_komentar=1;
						$zprava_reakce=1;
				break;

				case "pozadavek_doplnen_uzivatelem":
						$predmet_cz="".gtext('Doplneni pozadavku uzivatelem',70,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl doplnìn uživatelem',71,'_cz')."</u>.";

						$predmet_en="".gtext('Doplneni pozadavku uzivatelem',70,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl doplnìn uživatelem',71,'_en')."</u>.";

						$zprava_reakce=1;
				break;

				case "pozadavek_upravena_reakce":
						$predmet_cz="".gtext('Doplneni reakce resitele',100,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('požadavek',62,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl upraven v reakci øešitele',101,'_cz')."</u>.";

						$predmet_en="".gtext('Doplneni reakce resitele',100,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('požadavek',62,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl upraven v reakci øešitele',101,'_en')."</u>.";

						$zprava_reakce=1;
				break;


				case "diskuse_pozadavku":
						$predmet_cz="".gtext('Diskuse k pozadavku',72,'_cz')." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('zadatel',50,'_cz')." $nazev_firmy)";
						$zprava_cz="".gtext('k požadavku',73,'_cz')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_cz')." $nazev_projektu, ".gtext('Žadatel',50,'_cz')." $nazev_firmy)</i> <u>".gtext('byl v diskusi pøidán komentáø',74,'_cz')."</u>.";

						$predmet_en="".gtext('Diskuse k pozadavku',72,'_en')." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('zadatel',50,'_en')." $nazev_firmy)";
						$zprava_en="".gtext('k požadavku',73,'_en')." <i>".$_POST['nazev']." (".gtext('Projekt',1,'_en')." $nazev_projektu, ".gtext('Žadatel',50,'_en')." $nazev_firmy)</i> <u>".gtext('byl v diskusi pøidán komentáø',74,'_en')."</u>.";

						$zprava_reakce=1;
						$zprava_diskuse=1;
				break;
			endswitch;


			//--ke vsem predmetum doplnit ID pozadavku
			$predmet_cz=$predmet_cz." ID: $ID_pozadavku";
			$predmet_en=$predmet_en." ID: $ID_pozadavku";

			//--kompletovani textu zpravy
				if($druh_zpravy=="diskuse_pozadavku"):
					$_adresa_skriptu="/pozadavky-vlozit.php";
				else:
					$_adresa_skriptu=$_SERVER['PHP_SELF'];
				endif;


				$zprava_cz.=" ".gtext('Celý požadavek mùžete zobrazit zde',75,'_cz').": <a href=\"http://".$_SERVER['SERVER_NAME']."".$_adresa_skriptu."?ID=$ID_pozadavku\" target=\"_blank\">HelpDesk</a>
				<span class=\"text-grey2\"><br><br><b>ID:</b> $ID_pozadavku<br>
				<b>".gtext('Projekt',1,'_cz').":</b> ".$nazev_projektu."<br>
				<b>".gtext('Priorita',3,'_cz').":</b> ".$priorita_cz."<br><br></span>
				<b>".gtext('Název požadavku',9,'_cz').":</b> ".$_POST['nazev']."<br><br>
				<b>".gtext('Požadavek',10,'_cz').":</b> ".nl2br($_POST['pozadavek'])."<br><br>";

				$zprava_en.=" ".gtext('Celý požadavek mùžete zobrazit zde',75,'_en').": <a href=\"http://".$_SERVER['SERVER_NAME']."".$_adresa_skriptu."?ID=$ID_pozadavku\" target=\"_blank\">HelpDesk</a>
				<span class=\"text-grey2\"><br><br><b>ID:</b> $ID_pozadavku<br>
				<b>".gtext('Projekt',1,'_en').":</b> ".$nazev_projektu."<br>
				<b>".gtext('Priorita',3,'_en').":</b> ".$priorita_en."<br><br></span>
				<b>".gtext('Název požadavku',9,'_en').":</b> ".$_POST['nazev']."<br><br>
				<b>".gtext('Požadavek',10,'_en').":</b> ".nl2br($_POST['pozadavek'])."<br><br>";

				if($zprava_komentar):
					$zprava_cz.="<b>".gtext('Komentáø správce',12,'_cz').":</b> ".nl2br($_POST['komentar'])."<br><br>";
					$zprava_en.="<b>".gtext('Komentáø správce',12,'_en').":</b> ".nl2br($_POST['komentar'])."<br><br>";
				endif;

				if($zprava_reakce):
					$zprava_cz.="<b>".gtext('Reakce øešitele',14,'_cz').":</b> ".nl2br($_POST['reakce'])."<br><br>";
					$zprava_en.="<b>".gtext('Reakce øešitele',14,'_en').":</b> ".nl2br($_POST['reakce'])."<br><br>";
				endif;

				if($zprava_hodnoceni):
					$zprava_cz.="<b>".gtext('Vyøešeno',24,'_cz').":</b> $hodnoceni_cz<br>";
					$zprava_en.="<b>".gtext('Vyøešeno',24,'_en').":</b> $hodnoceni_en<br>";
				endif;

				if($zprava_datum_vyreseni):
					$zprava_cz.="<b>".gtext('Datum vyøešení',16,'_cz').":</b> ";
					$zprava_en.="<b>".gtext('Datum vyøešení',16,'_en').":</b> ";
					if($_POST['datum_vyreseni_zadane']>0):
						$zprava_cz.=date("d.m.Y",$_POST['datum_vyreseni_zadane']);
						$zprava_en.=date("d.m.Y",$_POST['datum_vyreseni_zadane']);
					endif;
					$zprava_cz.="<br>";
					$zprava_en.="<br>";
				endif;

				if($zprava_diskuse):
					$zprava_cz.="<br><br><b>".gtext('Zadaný pøíspìvek do diskuse',76,'_cz').":</b> ".nl2br($_POST['prispevek'])."<br><br>";
					$zprava_en.="<br><br><b>".gtext('Zadaný pøíspìvek do diskuse',76,'_en').":</b> ".nl2br($_POST['prispevek'])."<br><br>";
				endif;





			//--zaslani emailu
			unset($prom);
			$_pole_id_adresatu=array_unique($_pole_id_adresatu);

			foreach($_pole_id_adresatu as $id):
				if($id>0):
					$prom.=", ".$id;
				endif;
			endforeach;

			$prom="0".$prom;


			//echo "SELECT email FROM admin WHERE ID IN ($prom)";

			$vysledek_p = mysql_query("SELECT email, jazyk_vychozi FROM admin WHERE ID IN ($prom)");
				if(mysql_num_rows($vysledek_p)):
					while($zaz_p = mysql_fetch_array($vysledek_p)):
						extract($zaz_p);

							if($jazyk_vychozi=="_cz" || !$jazyk_vychozi):
								$predmet=$predmet_cz;
								$zprava=$zprava_cz;
							endif;
							if($jazyk_vychozi=="_en"):
								$predmet=$predmet_en;
								$zprava=$zprava_en;
							endif;

							zasli_email($email_od, $email, $predmet, $zprava, $jazyk_vychozi);

					endwhile;
				endif;
			@mysql_free_result($vysledek_p);

	endif;

}



function smazat($ID,$tabulka,$hlaska){

global $del_stat;
$del_stat=0;

	$SQL=mysql_query("DELETE FROM ".mysql_real_escape_string($tabulka)." WHERE ID='".mysql_real_escape_string($ID)."'");

	if($hlaska==1):
	  if(mysql_affected_rows()>0):
		message(1,"Item was deleted.", "", "");
	  else:
		message(1,"Item was not deleted.", "", "");
	  endif;
	endif;


	if($SQL):
		$del_stat=1;
	endif;

return $del_stat;

}

//--konec smazat





//----vypsani hlasky
function message($pocet, $hlaska, $odkaz_text, $odkaz){

		for($i=1; $i<=$pocet; $i++){
			echo "<br>";
		}

		echo "<center><div class=\"bunka-message\">";

		echo htmlspecialchars($hlaska);

		echo "</div></center>";

		if($odkaz_text){
			echo "<br><br><br><a href=\"htmlspecialchars($odkaz)\">htmlspecialchars($odkaz_text)</a>";
		}
		echo "<br>";

}


//--presmerovani po akci
function presmeruj($url_smerovani,$params=""){

	if($_POST['action']=="vlozit"):
		$slovo="inserted";
	else:
		$slovo="edited";
	endif;



	if($url_smerovani):

        echo "<script type=\"text/javascript\">
		window.location.href = '".htmlspecialchars($url_smerovani); if(htmlspecialchars($params)): echo "?"; endif; echo htmlspecialchars($params)."';
		</script>";

		exit;

	else:
		message(1, "Item was successfuly $slovo", "", "");
	endif;

}


//fce na nahradu znaku za entity
function htmlspecialchars_array_encode($zaz){
	foreach($zaz as $klic=>$zaznam):
		$zaz[$klic]=str_replace("&amp;","&",htmlspecialchars($zaznam,ENT_QUOTES,'ISO-8859-1'));
	endforeach;

	return $zaz;
}


//----vlozeni do tb
function insert_tb($tabulka){

	global $idcko;

	$vysledek = mysql_query("SHOW COLUMNS FROM ".clean_high($tabulka)."");
	if(mysql_num_rows($vysledek)>0):

		$dotaz_1="INSERT INTO ".clean_high($tabulka)." VALUES (";
		$dotaz_3=")";

		while($zaz = mysql_fetch_array($vysledek)):

			$sloupec=$zaz['Field'];
			$null=$zaz['Null'];

			if (eregi("^[0-9]+,[0-9]+$", $_POST[$sloupec])):
				$_POST[$sloupec]=str_replace(",",".",$_POST[$sloupec]);
			endif;

			//--zretezeni vkladanych hodnot
			if($dotaz_2):
				if(!$_POST[$sloupec] && $null=="YES"):
					$dotaz_2=$dotaz_2.", NULL";
				else:
					$dotaz_2=$dotaz_2.", '".clean_basic($_POST[$sloupec])."'";
				endif;
			else:
				$dotaz_2="'".clean_basic($_POST[$sloupec])."'";
			endif;

		endwhile;


	endif;

	//echo htmlspecialchars($dotaz_1)."".htmlspecialchars($dotaz_2)."".htmlspecialchars($dotaz_3) ;
	mysql_query("$dotaz_1$dotaz_2$dotaz_3");
	if ($DEBUG_CFG==1)echo mysql_error();

	//--pri uspesnem vlozeni ziskej ID vlozeneho zaznamu a vrat ho
	$idcko=MySQL_Insert_Id();
	return $idcko;
}




//----vlozeni do tb
function update_tb($tabulka,$update){

	global $SQL;

	$vysledek = mysql_query("SHOW COLUMNS FROM ".mysql_real_escape_string($tabulka)."");
	if(mysql_num_rows($vysledek)>0):

		while($zaz = mysql_fetch_array($vysledek)):

			$sloupec=$zaz['Field'];
			$null=$zaz['Null'];

			//--automaticka nahrada carek za tecky v datech, ktera jsou pouze ciselna
			if (eregi("^[0-9]+,[0-9]+$", $_POST[$sloupec])):
				$_POST[$sloupec]=str_replace(",",".",$_POST[$sloupec]);
				//$_POST[$sloupec]=str_replace(" ","",$_POST[$sloupec]);
			else:
				$_POST[$sloupec]=htmlspecialchars_decode($_POST[$sloupec],ENT_QUOTES);
				//$_POST[$sloupec]=htmlspecialchars($_POST[$sloupec],ENT_QUOTES);
			endif;


			//--je sloupec povolen?
			if(!in_array($sloupec,$update[0])):
				if($dotaz_1):
					if(!$_POST[$sloupec] && $null=="YES"):
						$dotaz_1.=", $sloupec=NULL";
					else:
						$dotaz_1.=", $sloupec='".safe($_POST[$sloupec])."'";
					endif;
				else:
					$dotaz_1="$sloupec='".safe($_POST[$sloupec])."'";
				endif;
			endif;

		endwhile;

	endif;

	$SQL=mysql_query("UPDATE $tabulka SET $dotaz_1 ".$update[1][0]."");

}




function call_post(){
		//--zruseni akce, post promenne do beznych
		unset($_POST['action']);

		foreach($_POST as $key => $value) {
			global ${$key};
			${$key} = stripslashes($value);
		}


}



function get_povinne_js($povinne){

	if($povinne[0][1]):
		$nazev_fce=$povinne[0][1];
	else:
		$nazev_fce="Kontrola";
	endif;

echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
  function $nazev_fce()\n
  {\n

  var zaskrtnuto;";

  $pocet_prvku=count($povinne[1]);

  for($i=0;$i<=$pocet_prvku-1;$i++):

	switch($povinne[3][$i]):
		case 1:
			echo "if(!document.".htmlspecialchars($povinne[0][0]).".".htmlspecialchars($povinne[1][$i]).".value)\n
			{      \n
				document.".$povinne[0][0].".".$povinne[1][$i].".focus();\n
				alert (\"Please, fill ".$povinne[2][$i].".\");\n
				return false;\n
			}\n";
		break;

		case 2:
			echo "if(!document.".htmlspecialchars($povinne[0][0]).".".htmlspecialchars($povinne[1][$i]).".checked)\n
			{      \n
				document.".$povinne[0][0].".".$povinne[1][$i].".focus();\n
				alert (\"Please, choose ".htmlspecialchars($povinne[2][$i]).".\");\n
				return false;\n
			}";
		break;

		case 3:
			echo "if(!document.".htmlspecialchars($povinne[0][0]).".".htmlspecialchars($povinne[1][$i]).".value)\n
			{      \n
				document.".$povinne[0][0].".".$povinne[1][$i].".focus();\n
				alert (\"Please, fill ".htmlspecialchars($povinne[2][$i]).".\");\n
				return false;\n
			}";
		break;

		case 4:

			echo "var hodnota=document.".htmlspecialchars($povinne[0][0]).".".htmlspecialchars($povinne[1][$i]).".value;\n";
			echo "hodnota=hodnota.replace(\"|\",\"\");\n";
			echo "hodnota=hodnota.replace(\" \",\"\");\n";

			echo "if(hodnota==\"\")\n
			{      \n
				alert (\"Please, choose ".htmlspecialchars($povinne[2][$i]).".\");\n
				return false;\n
			}\n";
		break;

		case 5:

			echo "
			zaskrtnuto=0;

			for(i=0; i<document.".$povinne[0][0].".".$povinne[1][$i].".length; i++){
				if (document.".$povinne[0][0].".".$povinne[1][$i]."[i].checked == true){
					zaskrtnuto=1;
				}
			}

			if(zaskrtnuto==0){
				alert (\"Please, choose ".$povinne[2][$i].".\");\n
				return false;\n
			}
			";

		break;


	endswitch;

  endfor;


  echo "\n} \n
</SCRIPT>";

}




//--funkce pro vypsani odkazu na kalendar
function get_calendar($nazev_inputu,$form_name){

	echo "<a href=\"javascript:get_calendar('$nazev_inputu','$form_name');\"><img src=\"img/ico/ico-kalendar.png\" border=\"0\" width=\"26\" height=\"20\" alt=\"\" onclick=\"minimize('meziprostor_kalendar'); ztmaveni('meziprostor_kalendar'); getMouseYObrazovka(event,'iddivu_kalendar',70);\" align=\"absmiddle\"></a>";
}


//--fce pro ziskani casoveho razitka z dat v inputu
function get_timestamp($post_name,$hodina="0",$minuta="0"){

	if($_POST[$post_name]!=$_SESSION['date_format'] && $_POST[$post_name]):

		//odstranit mezery
		$_POST[$post_name]=str_replace(" ","",$_POST[$post_name]);

		//co je delici znak?
		preg_match_all("/[^0-9]+/", $_POST[$post_name], $matches);
		$delitko=$matches[0][0];

		$_n1=explode($delitko,$_POST[$post_name]);

		//urceni pozic
		$_pozice=explode($delitko,$_SESSION['date_format']);

		if($_pozice[0][0]=="D"): $pozice_den=0; endif;
		if($_pozice[0][0]=="M"): $pozice_mesic=0; endif;
		if($_pozice[0][0]=="Y"): $pozice_rok=0; endif;

		if($_pozice[1][0]=="D"): $pozice_den=1; endif;
		if($_pozice[1][0]=="M"): $pozice_mesic=1; endif;
		if($_pozice[1][0]=="Y"): $pozice_rok=1; endif;

		if($_pozice[2][0]=="D"): $pozice_den=2; endif;
		if($_pozice[2][0]=="M"): $pozice_mesic=2; endif;
		if($_pozice[2][0]=="Y"): $pozice_rok=2; endif;



		if(!$hodina): $hodina=0; endif;
		if(!$minuta): $minuta=0; endif;

		$_POST[$post_name]=mktime($hodina,$minuta,0,$_n1[$pozice_mesic],$_n1[$pozice_den],$_n1[$pozice_rok]);
	else:
		$_POST[$post_name]=0;
	endif;

}


//--fce pro ziskani casoveho razitka z dat v XML -> rok-mesic-den
function get_timestamp2($promenna,$hodina="0",$minuta="0"){

	if($promenna):

		$_datum=explode("-",$promenna);

		if(!$hodina): $hodina=0; endif;
		if(!$minuta): $minuta=0; endif;

		$promenna=mktime($hodina,$minuta,0,$_datum[1],$_datum[2],$_datum[0]);
	else:
		$promenna=0;
	endif;

	return $promenna;

}


//--generuje n-mistny retezec
function get_token($pocet){

	$token_return="";

	for($i=1;$i<=$pocet;$i++):

	//48-57 jsou cisla, 97-122 pismena

	$cislo=rand(48,82);

	if($cislo<=57):
		$token_return.=chr($cislo);
	else:
		$token_return.=chr($cislo-57+97);
	endif;


	endfor;

	return $token_return;

}


//--funkce pro zmenseni retezce (na hostingu neslo zmensovat velka pismena s diakritikou)
function strtolower2($vyraz) {
	$co=iconv('windows-1250','utf-8',mysql_real_escape_string($vyraz));
	$co2=mb_strtolower($co, 'UTF-8');
	$co3=iconv('utf-8','windows-1250',$co2);

	return $co3;
}


//---funkce pro generovani kodu pro input
function ajax_multibox($ajax_pole1,$ajax_pole2){

	global $nazev_form;
	global $form_name;
	global $cil_text;
	global $ajax_where;
	global $ajax_join;
	global $group_by;
	global $nadrazeny_form;
	global $podrazeny_form;
	global $ajax_ico;

	$nazev_form=$ajax_pole1[0];
	$form_name=$ajax_pole1[1];
	$cil_text=$form_name.$nazev_form;
	$ajax_where=$ajax_pole1[2];
	$ajax_join=$ajax_pole1[5];
	$group_by=$ajax_pole1[6];
	$nadrazeny_form=$ajax_pole1[3];
	$podrazeny_form=$ajax_pole1[4];

	$_GET['inc']=1;
	$_GET['tab']=$ajax_pole2[0];
	$_GET['sl']=$ajax_pole2[1];
	$_GET['zpusob']=$ajax_pole2[2];
	$_GET['multi']=$ajax_pole2[3];
	$_GET['idcka']=$ajax_pole2[4];
	$_GET['cil_form']=$form_name;
	$_GET['cil_id']=$nazev_form;
	$_GET['cil_text']=$cil_text;
	$_GET['podrazeny_form']=$podrazeny_form;


	$ajax_ico="<a href=\"javascript:showHint('1-add.php?tab=$_GET[tab]&amp;sl=$_GET[sl]&amp;multi=$_GET[multi]&amp;cil_form=$form_name&amp;cil_id=$nazev_form&amp;cil_text=$cil_text&amp;zpusob=$_GET[zpusob]&amp;ajax_where=$ajax_where&amp;ajax_join=$ajax_join&amp;group_by=$group_by&amp;nadrazeny_form=$nadrazeny_form&amp;podrazeny_form=$podrazeny_form&amp;form='";
		if($nadrazeny_form): $ajax_ico.="+document.$form_name.$nadrazeny_form.value"; endif;
		$ajax_ico.="+'&amp;id_value='+document.$form_name.$nazev_form.value,'ajax_form', '1'); minimize('ajax_div')\"><img src=\"img/ico/multi.png\" border=\"0\" width=\"17\" height=\"20\" alt=\"pøiøadit prvky\" style=\"position:relative; top:3px;\" onclick=\"getMouseYObrazovka(event,'ajax_div',150); ztmaveni('meziprostor'); show_it('meziprostor');\"></a>";

}






//--funkce pro generovani kodu pro add ikonku
function ajax_add($ID_stranky,$url_stranky,$cil_add="",$js_fce=""){

	//--Pozn.: JS_fse obsahuje nazev promenne, ktera se po vykonani showhintu preda do fce next_fce(). Pouzito napr. v partnerech

	global $ajax_add_page;
	global $pole_js;


	//--odkaz na add stranku neukazuj, pokud uz jsem v add strance
	if(!$_GET['from_ajax']):

		if(!$js_fce):
			$ajax_add_page="<br><a href=\"javascript:showHint('$url_stranky?from_ajax=1','ajax_next_page', '1'); minimize('ajax_next_page_form'); var url_action='$url_stranky'; var cil_add='$cil_add';\"><img src=\"img/black/ico/add.gif\" border=\"0\" width=\"17\" height=\"18\" alt=\"pøiøadit prvky\" style=\"position:relative; top:3px;\" onclick=\"getPos('ajax_next_page_form', event); ztmaveni('meziprostor'); show('meziprostor');\"></a>";
		else:
			$ajax_add_page="<br><a href=\"javascript:showHint2('$url_stranky?from_ajax=1','ajax_next_page', '1', '$js_fce'); minimize('ajax_next_page_form'); var url_action='$url_stranky'; var cil_add='$cil_add';\"><img src=\"img/black/ico/add.gif\" border=\"0\" width=\"17\" height=\"18\" alt=\"pøiøadit prvky\" style=\"position:relative; top:3px;\" onclick=\"getPos('ajax_next_page_form', event); ztmaveni('meziprostor'); show('meziprostor');\"></a>";
		endif;

		$pole_js[]=$ID_stranky;
	endif;
}



//-----vytvoreni formulare pro ajaxove ADD stranky
function ajax_get_form($pole_add_cilu=""){

	if(!$_GET['from_ajax']):

		echo "<div style=\"display:none; position:absolute; left:180px; top:200px; width:385px; z-index:500;\" id=\"ajax_next_page_form\">
		<div style=\"position:relative; width:30px; height:28px; top:47px; left:329px;\"><a href=\"javascript:minimize('ajax_next_page_form'); hide('meziprostor'); hide('meziprostor2');\"><img src=\"img/black/table/close.gif\" border=\"0\" width=\"21\" height=\"28\" alt=\"\"></a></div>
		<form onsubmit=\"return false;\" action=\"\" name=\"ajax_next_formular\">

		<div style=\"float:left; width:100%;\" id=\"ajax_next_page\"></div>

		<input type=\"hidden\" name=\"action\" value=\"vlozit\">

		<div class=\"selectbox5\"><input type=\"submit\" class=\"form-send\" name=\"B2\" value=\"odeslat\" onclick=\"send_post(url_action); minimize('ajax_next_page_form'); hide('meziprostor'); hide('meziprostor2');\"></div>

		</form>
		</div>



		<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

			function send_post(url_action){

				var seznam=\"\";

				 var form = document.forms['ajax_next_formular'];

					for (i = 0; i < form.elements.length; i++) {

						var hodnota=\"\";

						if(form.elements[i].type == \"checkbox\" || form.elements[i].type == \"radio\"){
							if(form.elements[i].checked){
								hodnota=form.elements[i].value;
							}
						}else{
								hodnota=form.elements[i].value;
						}


						//kdyz mam hodnotu, pridej do seznamu
						if(hodnota){
							seznam=seznam+\"&\"+form.elements[i].name+\"=\"+hodnota;
						}

					}


				showHint_post(url_action+'?from_ajax=1',seznam,'ajax_next_page', '1');
			}

		</SCRIPT>";


		//--tento JS je volan po odeslani nejake ajaxove stranky metodou post. Ukolem je ziskat ID prave vlozeneho prvku, najit mu textovou hodnotu a rovnou ukazat ve formulari, abych nemusel vlozeny prvek znovu nalistovavat.
		if($pole_add_cilu):
			echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

				function next_fce_post(){

					//obecne operace
					var id_vytvoreneho_prvku=document.getElementById('ajax_next_page').innerHTML.replace(\" \",\"\");

					//ziskani textove hodnoty dle id - nutno specifikovat tabulku a sloupce\n\r";
					foreach($pole_add_cilu as $dilci_pole):
						echo "if(cil_add==\"htmlspecialchars($dilci_pole[0])\"){";

							//--pokud je multi 1, idcka pridavej, jinak prepisuj
							if($dilci_pole[4]==1):
								echo "document.getElementById(cil_add).value=document.getElementById(cil_add).value+'|'+id_vytvoreneho_prvku;";
							else:
								echo "document.getElementById(cil_add).value=id_vytvoreneho_prvku;";
							endif;

							echo "ajax_hodnoty('$dilci_pole[1]', cil_add, '".htmlspecialchars($dilci_pole[1])."'+cil_add, '$dilci_pole[2]', '$dilci_pole[3]', 1, '');

						}";
					endforeach;



				echo "}

			</SCRIPT>";
		endif;
	endif;

}





//--vlozeni idcek multiboxu - jen pro pomocnou tabulku
function insert_multibox($idcko, $seznam_id, $tabulka_vztahu, $kam_idcko, $typ){

	//--vymazat stara prirazeni
	$sloupce_idcka=explode(", ",$kam_idcko);
	mysql_query("DELETE FROM ".mysql_real_escape_string($tabulka_vztahu)." WHERE $sloupce_idcka[0]='".mysql_real_escape_string($idcko)."' AND typ='".mysql_real_escape_string($typ)."'");

	if($seznam_id):

		$_pole_multi=explode("|",$seznam_id);

		foreach($_pole_multi as $multi_id):

			if($multi_id>0):

				mysql_query("INSERT INTO ".mysql_real_escape_string($tabulka_vztahu)." (".mysql_real_escape_string($kam_idcko).", typ) VALUES
					('".mysql_real_escape_string($idcko)."',
					'".mysql_real_escape_string($multi_id)."',
					'".mysql_real_escape_string($typ)."')");
			endif;
		endforeach;

	endif;

}



//--vlozeni idcek multiboxu - jen pro pomocnou tabulku
function insert_multibox2($idcko, $seznam_id, $tabulka_vztahu, $kam_idcko, $typ, $typ2){

	//--vymazat stara prirazeni
	$sloupce_idcka=explode(", ",$kam_idcko);
	//mysql_query("DELETE FROM $tabulka_vztahu WHERE $sloupce_idcka[0]='$idcko' AND prava='$typ' AND prava_rozsirujici='$typ2'");
	//kvuli unique sloupcum nemohu mazat zde, ale jeste pres skupinou multibox operaci v uprave


	if($seznam_id):

		$_pole_multi=explode("|",$seznam_id);

		foreach($_pole_multi as $multi_id):

			if($multi_id>0):

				mysql_query("INSERT IGNORE INTO ".mysql_real_escape_string($tabulka_vztahu)." (".mysql_real_escape_string($kam_idcko).", prava, prava_rozsirujici) VALUES
					('".mysql_real_escape_string($idcko)."',
					'".mysql_real_escape_string($multi_id)."',
					'".mysql_real_escape_string($typ)."',
					'".mysql_real_escape_string($typ2)."')");
			endif;
		endforeach;

	endif;

}








//--------vlozeni obrazku

function create_img($obrazek, $sirka_a, $vyska_a, $sirka_b, $vyska_b, $sirka_c, $vyska_c, $nazev_bd, $idcko, $citac) {


//použije se primárnì sirka, vyska je omezující nastavení, obrázek se zmenší na šíøku a zkontroluje se, zda není vyšší než zadaná výška,
//jestli ano, tak ho ještì zmenší na uvedenou výšku

//není-li uvedena ani sirka ani vyska, tak tuto velikost ignoruj
//obrazek je nazev inputu
//primarni je sirka_a - nejvìtší velikost
//nazev_bd je název, jak se má obrázek jmenovat vèetnì adresáøe, kde je uložen, napø. "reference/ref"
//citac je dodatek nazvu

global $IMG_PATH;
global $vodotisk_zdroj;
global $vodotisk_x;
global $vodotisk_y;


//hodnoty pro ostøení
$amount=100;	//obvykle 50 - 200
$radius=0.7;	//obvykle 0.5 - 1
$threshold=3;	//obvykle 0 - 5



//je ukladan obrazek?
if (is_uploaded_file($_FILES[$obrazek]['tmp_name'])):

	$typ=$_FILES[$obrazek]['type'];

	if($typ=="image/gif"): $typ_uploadu=1; endif;
	if($typ=="image/pjpeg"||$typ=="image/jpeg"): $typ_uploadu=2; endif;


   //pouze jpg nebo gif
   if(($typ_uploadu==2)||($typ_uploadu==1)){


   	//nejprve smazat dosavadni obrazek jpg a gif,aby pri vlozeni jpg nezustal napr. minuly gif
   	del_single_image($idcko,$nazev_bd,$citac); //citac je dodatek,ten odlisi ktere presne soubory smazat


	//zakladni nazev pro odliseni
   	$nazev_bd=$nazev_bd."_".$idcko;


	switch($typ_uploadu):
		case 1: $pripona=".gif";
			$vstup_funkce = ImageCreateFromGIF;
			$vystup_funkce = ImageGIF;
		break;

		case 2: $pripona=".jpg";
			$vstup_funkce = ImageCreateFromJPEG;
			$vystup_funkce = ImageJPEG;
		break;
	endswitch;





	//ulozeni pro dalsi upravy
	$nazev_docasny_obrazek=$IMG_PATH."/".$nazev_bd."_docasny".$pripona;
	move_uploaded_file($_FILES[$obrazek]['tmp_name'],$nazev_docasny_obrazek);

	list($sirka_img,$vyska_img)=GetImageSize($nazev_docasny_obrazek);	//zjištìní šíøky a výšky originálního obrázku

	//stanoveni nazvu vytvarenych obrazku
	$nazev_obrazku_a=$nazev_bd."_".$citac.$pripona;
	$nazev_obrazku_b=$nazev_bd."_".$citac."_b".$pripona;
	$nazev_obrazku_c=$nazev_bd."_".$citac."_c".$pripona;



	//existuje docasny?
	if(File_Exists ($nazev_docasny_obrazek)){


	//pro obrázek A
	if($sirka_a!=0):

		if($sirka_img>$sirka_a):	//1920,800
		   $velx_a=$sirka_a;
		   $koef_zmenseni=$sirka_img/$velx_a;
		   $vely_a=$vyska_img/$koef_zmenseni; ///urceni vysky noveho obrazku
		else:
		   $velx_a=$sirka_img;
		   $koef_zmenseni=$sirka_img/$velx_a;
		   $vely_a=$vyska_img/$koef_zmenseni;
		endif;

		   //kontrola, zda výška není vìtší než je zadáná ve fci
		   //jestli ano, tak pøepoèítej na nové hodnoty
		   if(($vely_a>$vyska_a) AND ($vyska_a!=0)):
			   $vely_a=$vyska_a;
			   $koef_zmenseni=$vyska_img/$vyska_a;
			   $velx_a=$sirka_img/$koef_zmenseni;
		   endif;
	 endif;



	//pro obrázek B
	if($sirka_b!=0):

		if($sirka_img>$sirka_b):
		   $velx_b=$sirka_b;
		   $koef_zmenseni=$sirka_img/$velx_b;
		   $vely_b=$vyska_img/$koef_zmenseni; ///urceni vysky noveho obrazku
		else:
		   $velx_b=$sirka_img;
		   $koef_zmenseni=$sirka_img/$velx_b;
		   $vely_b=$vyska_img/$koef_zmenseni;
		endif;


		   //kontrola, zda výška není vìtší než je zadáná ve fci
		   //jestli ano, tak pøepoèítej na nové hodnoty
		   if(($vely_b>$vyska_b) AND ($vyska_b!=0)):
			   $vely_b=$vyska_b;
			   $koef_zmenseni=$vyska_img/$vyska_b;
			   $velx_b=$sirka_img/$koef_zmenseni;
		   endif;
	endif;


	//pro obrázek C
	if($sirka_c!=0):

		if($sirka_img>$sirka_c):
		   $velx_c=$sirka_c;
		   $koef_zmenseni=$sirka_img/$velx_c;
		   $vely_c=$vyska_img/$koef_zmenseni; ///urceni vysky noveho obrazku
		else:
		   $velx_c=$sirka_img;
		   $koef_zmenseni=$sirka_img/$velx_c;
		   $vely_c=$vyska_img/$koef_zmenseni;
		endif;


		   //kontrola, zda výška není vìtší než je zadáná ve fci
		   //jestli ano, tak pøepoèítej na nové hodnoty
		   if(($vely_c>$vyska_c) AND ($vyska_c!=0)):
		       $vely_c=$vyska_c;
			   $koef_zmenseni=$vyska_img/$vyska_c;
			   $velx_c=$sirka_img/$koef_zmenseni;
		   endif;
	endif;




	   $img_zdroj = $vstup_funkce($nazev_docasny_obrazek); // nacteme obrazek ze souboru


	if($sirka_a>0):
		$img_cil_a = imagecreatetruecolor($velx_a,$vely_a); // vytvorime prostor pro cilovy obrazek


		ImageCopyResampled($img_cil_a,$img_zdroj,0,0,0,0,$velx_a,$vely_a,$sirka_img,$vyska_img); // zmensime obrazek
		$vodotisk_zdroj_out=ImageCreateFromGIF($vodotisk_zdroj);

		//--aplikace vodoznaku do obrazku
		$souradnice_x=$velx_a-$vodotisk_x-20;
		$souradnice_y=$vely_a-$vodotisk_y-20;

		imagecopymerge($img_cil_a,$vodotisk_zdroj_out,$souradnice_x,$souradnice_y,0,0,$vodotisk_x,$vodotisk_y,20);
		$img_cil_a=UnsharpMask($img_cil_a, $amount, $radius, $threshold);  //fce pro ostøení
		$vystup_funkce($img_cil_a,"$IMG_PATH/$nazev_obrazku_a",95); // zapiseme novy obrazek do souboru
		ImageDestroy($img_cil_a);
	endif;


	if($sirka_b>0):
		$img_cil_b = imagecreatetruecolor($velx_b,$vely_b); // vytvorime prostor pro cilovy obrazek
		ImageCopyResampled($img_cil_b,$img_zdroj,0,0,0,0,$velx_b,$vely_b,$sirka_img,$vyska_img); // zmensime obrazek
		$img_cil_b=UnsharpMask($img_cil_b, $amount, $radius, $threshold);	//fce pro ostøení
		$vystup_funkce($img_cil_b,"$IMG_PATH/$nazev_obrazku_b",95); // zapiseme novy obrazek do souboru
		ImageDestroy($img_cil_b);
	endif;


	if($sirka_c>0):
		$img_cil_c = imagecreatetruecolor($velx_c,$vely_c); // vytvorime prostor pro cilovy obrazek
		ImageCopyResampled($img_cil_c,$img_zdroj,0,0,0,0,$velx_c,$vely_c,$sirka_img,$vyska_img); // zmensime obrazek
		$img_cil_c=UnsharpMask($img_cil_c, $amount, $radius, $threshold);	//fce pro ostøení
		$vystup_funkce($img_cil_c,"$IMG_PATH/$nazev_obrazku_c",95); // zapiseme novy obrazek do souboru
		ImageDestroy($img_cil_c);
	endif;


	ImageDestroy($img_zdroj); // uvolnime pamet zdrojoveho obrazku



	if(File_Exists ($nazev_docasny_obrazek)):
		unlink($nazev_docasny_obrazek);
	endif;


	}	//existuje doèasny?


   } else {

	echo "<script language=\"JavaScript\" type=\"text/javascript\">
	alert(\"Obrázek není ve formátu JPG nebo GIF ! Fotografie lze vkládat pouze typu JPG nebo GIF.\");
	</SCRIPT>";

   }


endif;

}
//-konec obrazku





//-----smazat obrázek - pro jeden obrazek s pripadnym charakteristickym doplnenim nazvu
function del_single_image($ID,$nazev,$citac){

global $IMG_PATH;

$pole=array(".jpg",".gif");

 foreach($pole as $pripona):

	$obrazek_a=$IMG_PATH."/".$nazev."_".$ID."_".$citac.$pripona;
	$obrazek_b=$IMG_PATH."/".$nazev."_".$ID."_".$citac."_b".$pripona;
	$obrazek_c=$IMG_PATH."/".$nazev."_".$ID."_".$citac."_c".$pripona;

	if(File_Exists ($obrazek_a)):
	   unlink($obrazek_a);
	endif;

	if(File_Exists ($obrazek_b)):
	   unlink($obrazek_b);
	endif;

	if(File_Exists ($obrazek_c)):
	   unlink($obrazek_c);
	endif;


 endforeach;

}
//--konec smazat



//--funkce k obrazku
function image_fce($ID,$obrazek,$imd){ //imd je rozliseni obrazku, pokud jich je k obsahu vice

global $ico_lupa2;
global $ico_smazat2;


$pole=array(".jpg",".gif");

 foreach($pole as $pripona):

	$obrazek1=$obrazek.$pripona;

	if(File_Exists ($obrazek1)):
		echo "&nbsp;&nbsp;&nbsp;<a href=\"javascript: void window.open('detail_img.php?obrazek_det=$obrazek1','detail','height=50, width=50, scrollbars=no')\" title=\"\">".htmlspecialchars($ico_lupa2)."</a>&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href=\"htmlspecialchars($_SERVER[PHP_SELF])?del_img=ok&amp;ID=htmlspecialchars($ID)&amp;imd=htmlspecialchars($imd)\" onclick=\"return potvrd('Opravdu si pøejete smazat obrázek?\\nNeuložená data budou ztracena.')\">".htmlspecialchars($ico_smazat2)."</a>";
	endif;

 endforeach;

}
//--konec funkce k obrazku




//--funkce k obrazku
function ImageVypis($ID_zbozi){ //imd je rozliseni obrazku, pokud jich je k obsahu vice

global $IMG_PATH;
global $ico_obrazek3;

$counter=0;

for($i=1;$i<6;++$i) {

	$pole=array(".jpg",".gif");

	foreach($pole as $pripona){

		$obrazek1=$IMG_PATH."/film_".$ID_zbozi."_".$i.$pripona;

		if(File_Exists ($obrazek1)):
			++$counter;
		endif;

	}

}

if($counter>0):
	return "<span style=\"font-size:9px\">".$counter."x</span> ".$ico_obrazek3;
endif;
}
//--konec funkce k obrazku




function UnsharpMask($img, $amount, $radius, $threshold)    {

////////////////////////////////////////////////////////////////////////////////////////////////
////
////                  Unsharp Mask for PHP - version 2.1.1
//// 				  From: http://vikjavev.no/computing/ump.php
///////////////////////////////////////////////////////////////////////////////////////////////

    // $img is an image that is already created within php using
    // imgcreatetruecolor. No url! $img must be a truecolor image.

    // Attempt to calibrate the parameters to Photoshop:
    if ($amount > 500)    $amount = 500;
    $amount = $amount * 0.016;
    if ($radius > 50)    $radius = 50;
    $radius = $radius * 2;
    if ($threshold > 255)    $threshold = 255;

    $radius = abs(round($radius));     // Only integers make sense.
    if ($radius == 0) {
        return $img; imagedestroy($img); break;        }
    $w = imagesx($img); $h = imagesy($img);
    $imgCanvas = imagecreatetruecolor($w, $h);
    $imgBlur = imagecreatetruecolor($w, $h);


    // Gaussian blur matrix:
    //
    //    1    2    1
    //    2    4    2
    //    1    2    1
    //
    //////////////////////////////////////////////////


    if (function_exists('imageconvolution')) { // PHP >= 5.1
            $matrix = array(
            array( 1, 2, 1 ),
            array( 2, 4, 2 ),
            array( 1, 2, 1 )
        );
        imagecopy ($imgBlur, $img, 0, 0, 0, 0, $w, $h);
        imageconvolution($imgBlur, $matrix, 16, 0);
    }
    else {

    // Move copies of the image around one pixel at the time and merge them with weight
    // according to the matrix. The same matrix is simply repeated for higher radii.
        for ($i = 0; $i < $radius; $i++)    {
            imagecopy ($imgBlur, $img, 0, 0, 1, 0, $w - 1, $h); // left
            imagecopymerge ($imgBlur, $img, 1, 0, 0, 0, $w, $h, 50); // right
            imagecopymerge ($imgBlur, $img, 0, 0, 0, 0, $w, $h, 50); // center
            imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);

            imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 33.33333 ); // up
            imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 25); // down
        }
    }

    if($threshold>0){
        // Calculate the difference between the blurred pixels and the original
        // and set the pixels
        for ($x = 0; $x < $w-1; $x++)    { // each row
            for ($y = 0; $y < $h; $y++)    { // each pixel

                $rgbOrig = ImageColorAt($img, $x, $y);
                $rOrig = (($rgbOrig >> 16) & 0xFF);
                $gOrig = (($rgbOrig >> 8) & 0xFF);
                $bOrig = ($rgbOrig & 0xFF);

                $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                $rBlur = (($rgbBlur >> 16) & 0xFF);
                $gBlur = (($rgbBlur >> 8) & 0xFF);
                $bBlur = ($rgbBlur & 0xFF);

                // When the masked pixels differ less from the original
                // than the threshold specifies, they are set to their original value.
                $rNew = (abs($rOrig - $rBlur) >= $threshold)
                    ? max(0, min(255, ($amount * ($rOrig - $rBlur)) + $rOrig))
                    : $rOrig;
                $gNew = (abs($gOrig - $gBlur) >= $threshold)
                    ? max(0, min(255, ($amount * ($gOrig - $gBlur)) + $gOrig))
                    : $gOrig;
                $bNew = (abs($bOrig - $bBlur) >= $threshold)
                    ? max(0, min(255, ($amount * ($bOrig - $bBlur)) + $bOrig))
                    : $bOrig;



                if (($rOrig != $rNew) || ($gOrig != $gNew) || ($bOrig != $bNew)) {
                        $pixCol = ImageColorAllocate($img, $rNew, $gNew, $bNew);
                        ImageSetPixel($img, $x, $y, $pixCol);
                    }
            }
        }
    }
    else{
        for ($x = 0; $x < $w; $x++)    { // each row
            for ($y = 0; $y < $h; $y++)    { // each pixel
                $rgbOrig = ImageColorAt($img, $x, $y);
                $rOrig = (($rgbOrig >> 16) & 0xFF);
                $gOrig = (($rgbOrig >> 8) & 0xFF);
                $bOrig = ($rgbOrig & 0xFF);

                $rgbBlur = ImageColorAt($imgBlur, $x, $y);

                $rBlur = (($rgbBlur >> 16) & 0xFF);
                $gBlur = (($rgbBlur >> 8) & 0xFF);
                $bBlur = ($rgbBlur & 0xFF);

                $rNew = ($amount * ($rOrig - $rBlur)) + $rOrig;
                    if($rNew>255){$rNew=255;}
                    elseif($rNew<0){$rNew=0;}
                $gNew = ($amount * ($gOrig - $gBlur)) + $gOrig;
                    if($gNew>255){$gNew=255;}
                    elseif($gNew<0){$gNew=0;}
                $bNew = ($amount * ($bOrig - $bBlur)) + $bOrig;
                    if($bNew>255){$bNew=255;}
                    elseif($bNew<0){$bNew=0;}
                $rgbNew = ($rNew << 16) + ($gNew <<8) + $bNew;
                    ImageSetPixel($img, $x, $y, $rgbNew);
            }
        }
    }
    imagedestroy($imgCanvas);
    imagedestroy($imgBlur);

    return $img;

}








//* =============================================================================
//		Soubory
//============================================================================= */

//--obecna fce pro ulozeni souboru
function uloz_soubor($form_pocet,$typ,$akce,$idcko,$dnes){

	global $soubor_name;

	//--vklada se
	if($akce==1):
		$nahodny_nazev=time().get_token(4);
		$nazev_adresare=md5($nahodny_nazev);

			for($k=1;$k<=$form_pocet;$k++):
				create_file('soubor'.$k, $nazev_adresare, '');

				if($soubor_name):
					mysql_query("INSERT INTO _soubory VALUES
						('".mysql_real_escape_string($idcko)."',
						'".mysql_real_escape_string($nazev_adresare)."',
						'".mysql_real_escape_string($soubor_name)."',
						'".mysql_real_escape_string($k)."',
						'".mysql_real_escape_string($typ)."',
						'".mysql_real_escape_string($dnes)."')");
				endif;
			endfor;
	endif;


	//--editace
	if($akce==2):
		$stare_soubory=array();

		$vysledek_fil = mysql_query("SELECT nazev_adresare,nazev_souboru AS stary_soubor,poradove_cislo FROM _soubory WHERE ID_dokumentu='".mysql_real_escape_string($idcko)."' AND typ='".mysql_real_escape_string($typ)."' ORDER BY poradove_cislo");

		if(mysql_num_rows($vysledek_fil)>0):

			while($zaz_fil = mysql_fetch_array($vysledek_fil)):

				extract($zaz_fil);

				$stare_soubory[$poradove_cislo]=$stary_soubor;

			endwhile;

		else:
			$nahodny_nazev=time().get_token(4);
			$nazev_adresare=md5($nahodny_nazev);
		endif;


		for($k=1;$k<=$form_pocet;$k++):
			create_file('soubor'.$k, $nazev_adresare, $stare_soubory[$k]);

			if($soubor_name):

				mysql_query("DELETE FROM _soubory WHERE ID_dokumentu='".mysql_real_escape_string($idcko)."' AND poradove_cislo='".mysql_real_escape_string($k)."' AND typ='".mysql_real_escape_string($typ)."'");

				mysql_query("INSERT INTO _soubory VALUES
					('".mysql_real_escape_string($idcko)."',
					'".mysql_real_escape_string($nazev_adresare)."',
					'".mysql_real_escape_string($soubor_name)."',
					'".mysql_real_escape_string($k)."',
					'".mysql_real_escape_string($typ)."',
					'".mysql_real_escape_string($dnes)."')");

			endif;
		endfor;
	endif;

}


//--------vlozeni souboru
function create_file($file,$nazev_adresare, $stary_soubor){ //nazev inputu, id, stary nazev pro smazani

global $FILE_PATH;
global $soubor_name;

$soubor_name="";

//je ukladan obrazek?
if (is_uploaded_file($_FILES[$file]['tmp_name'])):

	$soubor_name=StrTr($_FILES[$file]['name'], "áäèïéìëíòóöøš?úùüýžÁÄÈÏÉÌËÍÒÓÖØŠ?ÚÙÜÝŽ:/;°,´ ", "aacdeeeinoorstuuuyzAACDEEEINOORSTUUUYZ-------");


	$pole_file=explode(".",$soubor_name);
	$prvku = count($pole_file)-1;
	$pripona=$pole_file[$prvku];

	if($pripona!="php"&&$pripona!="asp"&&$pripona!="php3"&&$pripona!="php4"):


	  if($stary_soubor):
		//smazani stareho
		$soubor_smazat=$FILE_PATH."/".$nazev_adresare."/".$stary_soubor;
		if (File_Exists ($soubor_smazat)):
			unlink($soubor_smazat);
		endif;

	  else:

		if(!is_dir($FILE_PATH."/".$nazev_adresare)):
			mkdir($FILE_PATH."/".$nazev_adresare, 0700);
		endif;

	  endif;


		//ulozeni
		$nazev_souboru=$FILE_PATH."/".$nazev_adresare."/".$soubor_name;
		move_uploaded_file($_FILES[$file]['tmp_name'],$nazev_souboru);

	else:

		$soubor_name="";

		echo "<script language=\"JavaScript\" type=\"text/javascript\">
		alert(\"Soubory .$pripona nelze nahrát!\");
		</SCRIPT>";

	endif;

else:

	unset($soubor_name);

endif;


//return $soubor_name;


}



function file_fce_del($ID,$soubor,$poradi,$soubor_typ){ //imd je rozliseni obrazku, pokud jich je k obsahu vice | barva 1 = svetlejsi radek, barva 2 tmavsi

	if (File_Exists ($soubor)):
		echo "<a href=\"htmlspecialchars($_SERVER[PHP_SELF])?del_file=ok&amp;ID=htmlspecialchars($ID)&amp;poradi=htmlspecialchars($poradi)&amp;soubor_typ=htmlspecialchars($soubor_typ)\" title=\"smazat\" onclick=\"return potvrd('Opravdu si pøejete smazat soubor?')\"><img src=\"img/ico/smazat.png\" border=\"0\" width=\"17\" height=\"20\" alt=\"smazat\" style=\"position: relative; top: 4px\"></a>";
	endif;

}



function del_file($ID,$nazev_souboru,$nazev_adresare,$poradi,$soubor_typ){ //imd je rozliseni obrazku, pokud jich je k obsahu vice | barva 1 = svetlejsi radek, barva 2 tmavsi

global $FILE_PATH;

	$soubor_del=$FILE_PATH."/".$nazev_adresare."/".$nazev_souboru;
	$adresar_del=$FILE_PATH."/".$nazev_adresare;


	if (File_Exists ($soubor_del)):
		unlink($soubor_del);

		mysql_query("DELETE FROM _soubory WHERE ID_dokumentu='".mysql_real_escape_string($ID)."' AND poradove_cislo='".mysql_real_escape_string($poradi)."' AND typ='".mysql_real_escape_string($soubor_typ)."'");

		$vysledek_fil = mysql_query("SELECT nazev_adresare FROM _soubory WHERE ID_dokumentu='".mysql_real_escape_string($ID)."' AND typ='".mysql_real_escape_string($soubor_typ)."'");

			if(mysql_num_rows($vysledek_fil)==0):
				rmdir($adresar_del);
			endif;
	endif;

}


function file_fce_show($nazev_adresare,$nazev_souboru,$style){

global $FILE_PATH;

	$soubor=$FILE_PATH."/".$nazev_adresare."/".$nazev_souboru;

	if (File_Exists ($soubor)):
		echo "<div style=\"float:left; width:100%; $style\">";
		echo "<a href=\"$soubor\" target=\"_blank\">htmlspecialchars($nazev_souboru)</a>";
		echo "</div>";
	endif;


}

function file_fce_show2($nazev_adresare,$nazev_souboru){

global $FILE_PATH;

	$soubor=$FILE_PATH."/".$nazev_adresare."/".$nazev_souboru;

	if (File_Exists ($soubor)):
		echo "<a href=\"htmlspecialchars($soubor)\" target=\"_blank\">$nazev_souboru</a>";
	endif;


}

function file_fce_ico($soubor,$top,$alt){ //imd je rozliseni obrazku, pokud jich je k obsahu vice | barva 1 = svetlejsi radek, barva 2 tmavsi

	if (File_Exists ($soubor)):

		if(!$alt):
			$alt="otevøít dokument";
		endif;

		$pole_soubor=explode(".",$soubor);

		switch($pole_soubor[count($pole_soubor)-1]):
			case "doc": $ico_file="doc"; break;
			case "docx": $ico_file="docx"; break;
			case "mdb": $ico_file="access"; break;
			case "avi": $ico_file="avi"; break;
			case "mpg": $ico_file="avi"; break;
			case "csv": $ico_file="xls"; break;
			case "xls": $ico_file="xls"; break;
			case "xlsx": $ico_file="xls"; break;
			case "jpg": $ico_file="jpg"; break;
			case "gif": $ico_file="jpg"; break;
			case "png": $ico_file="jpg"; break;
			case "pdf": $ico_file="pdf"; break;
			case "ppt": $ico_file="ppt"; break;

			default: $ico_file="dokument"; break;
		endswitch;

		echo "<a href=\"htmlspecialchars($soubor)\" target=\"_blank\" title=\"htmlspecialchars($alt)\"><img src=\"img/ico/$ico_file.png\" border=\"0\" width=\"17\" height=\"18\" alt=\"$alt\" style=\"position: relative; top: ".$top."px\"></a>";
	endif;

}



?>