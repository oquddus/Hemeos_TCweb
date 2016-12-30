<?php
//----------cast nastaveni
ini_set('session.use_only_cookies', 1); //pouzivat pouze cookies
ini_set('session.use_trans_sid', 0); //automaticky nedoplnovat PHPSESSID
ini_set('session.cookie_httponly', 1); //cookie pouze pomoci http protokolu - nelze tedy menit pomoci JS (proti XSS utokum)

header('X-Content-Type-Options: nosniff');

//--vytvoï¿½enï¿½ session
session_start();


//--jazyky
if($_GET['lng']):
	if($_GET['lng']=="cz"): $_SESSION['jazyk']="_cz"; endif;
	if($_GET['lng']=="en"): $_SESSION['jazyk']="_en"; endif;

	//setcookie("CookieJazyk", $_SESSION['jazyk'], time()+86400*7, "/", "127.0.0.1");	//cookie pro ulozeni zvoleneho jazyka
	//setcookie("CookieJazyk", $_SESSION['jazyk'], time()+86400*7, "/", "helpdesk.steiner.cz");	//cookie pro ulozeni zvoleneho jazyka
endif;

if(!$_SESSION['jazyk']):
	//if($_COOKIE['CookieJazyk']):
	//	$_SESSION['jazyk']=$_COOKIE['CookieJazyk'];
	//else:
		$_SESSION['jazyk']="_en";
	//endif;
endif;



require "opendb.php";
require_once('1-function-mail.php');
require_once('1-function.php');
include('1-kontrola.php');	//kontrola prihlaseni

include('1-config.php');

require_once('classes/token.php');


//Osetreni vstupu
$_osetrit=array("ID","PatientNum");
osetrit_get($_osetrit);

//--detekce potencialniho problemu v sql
foreach (array_keys($_GET) as $key):
	if (stristr($_GET[$key], " union ")):
		$_GET[$key]="";
	endif;

	if (stristr($_GET[$key], " or ")):
		$_GET[$key]="";
	endif;

	if (stristr($_GET[$key], " and ")):
		$_GET[$key]="";
	endif;

	if (stristr($_GET[$key], " select ")):
		$_GET[$key]="";
	endif;

endforeach;
//--konec


include('1-lng.php');
include('1-verze.php');

include('1-menu.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<HTML><HEAD>

<TITLE>Hemeos, LLC</TITLE>
<link rel=icon href=http://tc.hemeos.com/img/hemeos-favicon.ico type=image/icon>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<meta http-equiv="Content-Language" content="">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="robots" content="index,follow">
<meta name="rating" content="general">
<meta name="expires" content="never">
<meta name="language" content="">
<meta name="revisit-after" content="3 Days">
<meta name="resource-type" content="document">
<meta name="distribution" content="global">
<LINK href="styl/styl4.css?v=2" type="text/css"  rel="stylesheet">

<LINK href="styl/sweetTitles.css" type="text/css"  rel="stylesheet">
<LINK href="styl/dragtable.css" type="text/css"  rel="stylesheet">

<script type="text/javascript" src="script/menu.js"></script>
<script type="text/javascript" src="script/fce2.js"></script>
<script type="text/javascript" src="script/ajax.js"></script>
<script type="text/javascript" src="script/sweetTitles.js"></script>

<script type="text/javascript" src="script/jquery1.7.1.js"></script>
<script type="text/javascript" src="script/jquery-ui.js"></script>
<script type="text/javascript" src="script/jquery-skripty.js?v=2"></script>


<?
if($ID_stranky==444):	//statistiky
echo "<script language=\"javascript\" type=\"text/javascript\" src=\"script/excanvas.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jquery.jqplot.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.pieRenderer.js\"></script>

<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.barRenderer.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.categoryAxisRenderer.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.dateAxisRenderer.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.pointLabels.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.highlighter.js\"></script>
<script language=\"javascript\" type=\"text/javascript\" src=\"script/jqplot.dateAxisRenderer.js\"></script>

<link rel=\"stylesheet\" type=\"text/css\" href=\"styl/jquery.jqplot.css\">";
endif;
?>

<?
if($_SESSION['jazyk']=="en"):
	//echo "<script type=\"text/javascript\" src=\"script/jquery-skripty-preklad.js\"></script>";
endif;
?>

<script type="text/javascript" src="script/jquery.translate.js"></script>
<script type="text/javascript" src="script/jquery.dragtable.js"></script>

<?
//* =============================================================================
//	Nastavenï¿½ (defaultnï¿½ch) rozmï¿½rï¿½ systï¿½mu
//	Xinha box
//============================================================================= */

	//--nastaveni rozmeru, zjisteni primarniho razeni pozadavku
	$vysledek = mysql_query("SELECT width, razeni AS razeni_pozadavku FROM admin WHERE login='".clean_high($_SESSION[usr_login])."' AND heslo='".mysql_real_escape_string($_SESSION[usr_heslo])."'");
		if(mysql_num_rows($vysledek)>0):
			$zaz = mysql_fetch_array($vysledek);
			extract($zaz);
		endif;

		if($width_vynucena):
			$width=$width_vynucena;
		endif;

		if(!$width):
			$width=1200;
		endif;

		if($width<1200):
			$width=1200;
		endif;

		//--vypocty velikosty systemu
		$width2=$width-229;
		$width3=$width-235;
		$width4=$width-239;
		$width5=$width-235;
		$width8=$width-279;
		$width9=$width8-140-15;

		//--velikost inputu a textarea
		$pulka_rozdilu=($width-973)/2;
		$width6=270+$pulka_rozdilu;
		$width7=470+$pulka_rozdilu;

		$global_vyska2=340;	//--vyska xinha boxu
		$global_sirka7=$width-460;	//--sirka xinha boxu


if($xinha):

	echo "<script type=\"text/javascript\">
	var _editor_url  = \"xinha/\";
	var _editor_lang = \"cz\";
	var xinha_sirka=$global_sirka7;
	var xinha_vyska=$global_vyska2;
	var xinha=$xinha;
	</script>

	<!-- Load up the actual editor core -->
	<script type=\"text/javascript\" src=\"xinha/htmlarea.js\"></script>";


	echo "<script type=\"text/javascript\" src=\"xinha/my-config3.js\"></script>";


	echo "<link type=\"text/css\" rel=\"stylesheet\" title=\"blue-look\" href=\"xinha/skins/blue-look/skin.css\">";

endif;
?>


<?
/*echo "<script language=\"JavaScript\" type=\"text/javascript\">

function addOnloadEvent(fnc){
  if ( typeof window.addEventListener != \"undefined\" )
    window.addEventListener( \"load\", fnc, false );
  else if ( typeof window.attachEvent != \"undefined\" ) {
    window.attachEvent( \"onload\", fnc );
  }
  else {
    if ( window.onload != null ) {
      var oldOnload = window.onload;
      window.onload = function ( e ) {
        oldOnload( e );
        window[fnc]();
      };
    }
    else
      window.onload = fnc;
  }
}

</script>";
*/
?>


<!-- Hotjar Tracking Code for www.hemeos.com -->
<script>
    (function(h,o,t,j,a,r){
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
        h._hjSettings={hjid:319654,hjsv:5};
        a=o.getElementsByTagName('head')[0];
        r=o.createElement('script');r.async=1;
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
        a.appendChild(r);
    })(window,document,'//static.hotjar.com/c/hotjar-','.js?sv=');
</script>

</HEAD>
<BODY>

<?





//pro zatmnï¿½nï¿½ obrazovky
	echo "<div id=\"meziprostor_kalendar\" style=\"display:none;height:10px;\" onclick=\"minimize('iddivu_kalendar'); minimize('meziprostor_kalendar');\">&nbsp;</div>";
	echo "<div id=\"meziprostor\" style=\"display:none;\" onclick=\"minimize('ajax_div'); minimize('meziprostor');\">&nbsp;</div>";

	//pro zobrazenï¿½ kalednï¿½ï¿½e
	echo "<div id=\"iddivu_kalendar\" style=\"display:none;\">";
	echo "<form class=\"formik\" onsubmit=\"return false;\" action=\"\" name=\"ajax_cal_formular\">";
	echo "<div id=\"crm_kalendar\" style=\"float:left; width:100%;\"></div>";
	echo "</form>";
	echo "</div>";
?>

<div class="zaklad0">
<div class="zaklad" style="width:<? echo $width; ?>px">

<?
//--jazyky
//--zjistit soucasny query string
$query_string=get_query_string();
//--v url se uz mozna vyskytuje cislo stranky - to vyhodit
$query_string=preg_replace("(lng=[a-z]+&)", "", $query_string);
?>
<div class="logo"><img src="img/logo_en.gif" width="250" height="105" alt=""></div>
<div class="odhlasit"><a href="logout.php" id="log-out"><img src="img/odhlasit<? echo $_SESSION['jazyk']; ?>.gif" width="144" height="30" alt=""></a></div>
<div class="vlajky">
<?
//echo "<a href=\"".$_SERVER['PHP_SELF']."?lng=cz&amp;$query_string\"><img src=\"img/vlajky/cz.png\" width=\"23\" height=\"18\" alt=\"\" style=\"margin-right:8px;\"></a><a href=\"".$_SERVER['PHP_SELF']."?lng=en&amp;$query_string\"><img src=\"img/vlajky/en.png\" width=\"23\" height=\"18\" alt=\"\"></a>";
?>
</div>
<div class="user">
	<div class="user-ico"><img src="img/user.gif" width="50" height="50" alt=""></div>
	<div class="user-logged text1"><? echo gtext('pï¿½ihlï¿½ï¿½enï¿½ uï¿½ivatel',91)?>: <span class="text2"><? echo $_SESSION['usr_login']; ?></span></div>
</div>

<div class="menu" style="margin-bottom:20px;">
<?
//* =============================================================================
//	Hlavnï¿½ menu
//============================================================================= */
	//--zjisteni stranky
	$_stranka_cela=str_replace("/","",strrchr($_SERVER['PHP_SELF'],"/"));
	$_stranka_bez=explode(".",$_stranka_cela);
	$_stranka=explode("-",$_stranka_bez[0]);


	//--vypis menu
	if(count($_pole_menu)>0):
		foreach($_pole_menu as $klic=>$hodnota):

			//--kontrola opravneni
			$vysledek_c = mysql_query("SELECT ID_admin FROM admin_prava WHERE ID_admin='".clean_high($_SESSION[usr_ID])."' AND modul='".mysql_real_escape_string($klic)."'");

				if(mysql_num_rows($vysledek_c)==1):

					$klic=$klic.$_SESSION['jazyk'];

					if(File_Exists ("img/menu/".$klic.".png")):

						list($sirka_menu,$vyska_menu)=GetImageSize("img/menu/".$klic.".png");

						echo "<a href=\"$hodnota[0].php\" onmouseover=\"swtch3('".$klic."2','$klic')\" onmouseout=\"swtch3('$klic"; if($_stranka[0]==$klic): echo "2"; endif; echo "','$klic')\"><img src=\"img/menu/$klic"; if($_stranka[0]==$klic): echo "2"; endif; echo ".png\" width=\"$sirka_menu\" height=\"$vyska_menu\" alt=\"\" name=\"$klic\"></a>";
					endif;

				endif;

		endforeach;
	endif;
?>
</div>

<div class="prava text" style="width:<? echo $width2; ?>px">
	<div class="zalozky" style="width:<? echo $width3; ?>px"><div class="menu-zalozky"><?
	//* =============================================================================
	//	Zalozky
	//============================================================================= */
		$citac=1;
		if(count($_pole_menu[$_stranka[0]])>0):
			foreach($_pole_menu[$_stranka[0]] as $hodnota):

				$zobrazit_zalozku=1;

				//zjiï¿½tï¿½nï¿½ ï¿½ï¿½ï¿½ky obrï¿½zku
				$obrazek="img/zalozky/".$_stranka[0]."0".$citac.""; if($_stranka_bez[0]==$hodnota&&!$_GET[ID]) $obrazek=$obrazek."a";  $obrazek=$obrazek.$_SESSION['jazyk'].".gif";
				list($sirka_img,$vyska_img)=GetImageSize($obrazek);

				//zjistit, jestli ma uzivatel pravo vkladat do novinek
				if($_stranka[0]=="novinky" && $citac==3):
					$vysledek_check = mysql_result(mysql_query("SELECT COUNT(*) FROM admin_prava_rozsirujici WHERE ID_admin='".clean_high($_SESSION[usr_ID])."' AND modul='novinky' AND rozsireni='1'"), 0);
					if($vysledek_check==0):
						$zobrazit_zalozku=0;
					endif;
				endif;

				if($zobrazit_zalozku):
					echo "<a href=\"$hodnota.php\" onmouseover=\"swtch2('".$_stranka[0]."0".$citac."a".$_SESSION['jazyk']."','f".$citac."')\" onmouseout=\"swtch2('".$_stranka[0]."0".$citac.""; if($_stranka_bez[0]==$hodnota&&!$_GET[ID]): echo "a"; endif; echo $_SESSION['jazyk']."','f".$citac."')\"><img src=\"img/zalozky/".$_stranka[0]."0".$citac.""; if($_stranka_bez[0]==$hodnota&&!$_GET[ID]): echo "a"; endif; echo $_SESSION['jazyk'].".gif\" width=\"".$sirka_img."\" height=\"41\" alt=\"\" name=\"f".$citac."\" style=\"margin-right:6px;\"></a>";

					if($citac<count($_pole_menu[$_stranka[0]])):
						echo "<img src=\"img/zalozky/predel.gif\" width=\"3\" height=\"41\" alt=\"\" style=\"margin-right:6px;\">";
					endif;

					$citac++;
				endif;
			endforeach;
		endif;
	?></div></div>
	<div class="obal-stin-r"><div class="obal-stin-l">
		<div class="vnitrek-obal" style="width:<? echo $width4; ?>px">
		<div class="vnitrek" style="width:<? echo $width8; ?>px;">
