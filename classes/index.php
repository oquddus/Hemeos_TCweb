<?php
//----------cast nastaveni
ini_set('session.use_only_cookies', 1); //pouzivat pouze cookies
ini_set('session.use_trans_sid', 0); //automaticky nedoplnovat PHPSESSID
ini_set('session.cookie_httponly', 1); //cookie pouze pomoci http protokolu - nelze tedy menit pomoci JS (proti XSS utokum)

//-- Moved to apache config header('X-Content-Type-Options: nosniff');

//--vytvo�en� session
session_start();


//--obrana proti SQL injection
foreach (array_keys($_GET) as $key):
	if (stristr($_GET[$key], " union ")):
		//$_GET[$key]="";
	endif;

	if (stristr($_GET[$key], " or ")):
		//$_GET[$key]="";
	endif;

	if (stristr($_GET[$key], " and ")):
		//$_GET[$key]="";
	endif;

	if (stristr($_GET[$key], " select ")):
		//$_GET[$key]="";
	endif;

endforeach;
//--konec obrany


if($_GET['lng']):
	if($_GET['lng']=="cz"): $_SESSION['jazyk']="_cz"; endif;
	if($_GET['lng']=="en"): $_SESSION['jazyk']="_en"; endif;

	//setcookie("CookieJazyk", $_SESSION['jazyk'], time()+86400*7, "/", "127.0.0.1");	//cookie pro ulozeni zvoleneho jazyka
	//setcookie("CookieJazyk", $_SESSION['jazyk'], time()+86400*7, "/", "helpdesk.steiner.cz");	//cookie pro ulozeni zvoleneho jazyka
endif;


//if(!$_SESSION['jazyk']):

	//if($_COOKIE['CookieJazyk']):
//		$_SESSION['jazyk']=$_COOKIE['CookieJazyk'];
	//else:
		$_SESSION['jazyk']="_en";
	//endif;

//endif;




require "opendb.php";
include('1-lng.php');
include('1-verze.php');

//--prihlaseni uzivatele
if($_POST['action']=="login"):

	if($_POST['login']&&$_POST['heslo']):

		$uziv_jmeno=mysql_real_escape_string(trim($_POST['login']));
		$uziv_jmeno_prepared_stmt=trim($_POST['login']);
		$uziv_heslo_tmp=trim($_POST['heslo']);

		$uziv_heslo=sha1($uziv_heslo_tmp);
		//Adding support for bcrypt via integrated PHP functions - AF
		$uziv_heslo_bcrypt=password_hash($uziv_heslo_tmp,PASSWORD_DEFAULT);

		//--kontrola, zda login nema prekroceny pocet nezdarenych prihlaseni
		$ID=0;
		//--Making lockout extreme untill a reset is found - AF
		$stmt = $mysqli->prepare("SELECT ID FROM admin WHERE login=? AND nologin>=999999");
		$stmt->bind_param("s", $uziv_jmeno_prepared_stmt);
		$stmt->execute();

		$stmt->bind_result($ID);
		$stmt->fetch();
		$stmt->close();

		if($ID>0):
			header('Location: index.php?st=block');
			exit;
		endif;
		//--konec kontroly


		$dnes=time();

		$ID=0;

		//Check password for old SHA1 or new bcrypt hash. Need to double check this - AF
		$stmt = $mysqli->prepare("SELECT ID, ID_centra, ID_registru, jazyk_vychozi, ID_role_admin FROM admin WHERE login=? AND (heslo=? or helso=?) AND (platnost_od<='".$dnes."' AND (platnost_do='0' OR platnost_do+86380>='".$dnes."')) LIMIT 1");

		$stmt->bind_param("ss", $uziv_jmeno_prepared_stmt, $uziv_heslo, $uziv_heslo_bcrypt);
		$stmt->execute();

		$stmt->bind_result($ID, $ID_centra, $ID_registru, $jazyk_vychozi, $ID_role_admin);
		$stmt->fetch();
		$stmt->close();



		if($ID>0):

			$_SESSION['usr_ID'] = mysql_real_escape_string($ID);

			$_SESSION['usr_ID_centra'] = mysql_real_escape_string($ID_centra);
			$_SESSION['usr_ID_registru'] = mysql_real_escape_string($ID_registru);

			$_SESSION['usr_ID_role'] = mysql_real_escape_string($ID_role_admin);

			$_SESSION['usr_login'] = $uziv_jmeno;
			$_SESSION['usr_heslo'] = mysql_real_escape_string($uziv_heslo);

			$_SESSION['jazyk']=mysql_real_escape_string($jazyk_vychozi);

			$_SESSION['IP']=$_SERVER['REMOTE_ADDR'];


			//dozjistit za jake TC a jaky Registr jsem
			$vysledek_p = mysql_query("SELECT InstID FROM transplant_centers WHERE ID='".$_SESSION['usr_ID_centra']."' LIMIT 1");
				if(mysql_num_rows($vysledek_p)>0):
					$zaz_p = mysql_fetch_array($vysledek_p);
						extract($zaz_p);
						$_SESSION['usr_InstID']=mysql_real_escape_string($InstID);
				endif;
			@mysql_free_result($vysledek_p);

			$vysledek_p = mysql_query("SELECT RegID, date_format FROM registers WHERE ID='".$_SESSION['usr_ID_registru']."' LIMIT 1");
				if(mysql_num_rows($vysledek_p)>0):
					$zaz_p = mysql_fetch_array($vysledek_p);
						extract($zaz_p);
						$_SESSION['usr_RegID']=mysql_real_escape_string($RegID);
				endif;
			@mysql_free_result($vysledek_p);

			//urcit format data
			if(!$date_format):
				$date_format="YYYY-MM-DD";
			endif;


			$_SESSION['date_format']=strtoupper($date_format);

			//urcit format data pro PHP fci date()
			$_kolik=count_chars($_SESSION['date_format'],1);
			$date_format_php=$_SESSION['date_format'];

			$_nahrada_d=array("0"=>"d","j","d");
			$_nahrada_m=array("0"=>"m","n","m");
			$_nahrada_y=array("0"=>"Y","Y","y","Y","Y");

			//kolik D?
			$kolik_d=$_kolik['68'];
			$date_format_php=preg_replace('#D{1,2}#i', $_nahrada_d[$kolik_d], $date_format_php);

			//kolik M?
			$kolik_m=$_kolik['77'];
			$date_format_php=preg_replace('#M{1,2}#i', $_nahrada_m[$kolik_m], $date_format_php);

			//kolik Y?
			$kolik_y=$_kolik['89'];
			$date_format_php=preg_replace('#Y{1,4}#i', $_nahrada_y[$kolik_y], $date_format_php);

			$_SESSION['date_format_php']=$date_format_php;




			//nastavit nepodarene loginy na 0
			mysql_query("UPDATE admin SET nologin='0' WHERE login='$uziv_jmeno' AND heslo='$uziv_heslo'");


			//admina a uzivatele TC smerovat na request
			if($_SESSION['usr_ID_role']==1 || $_SESSION['usr_ID_role']==2):
				header('Location: requests-vlozit.php');
			endif;

			//uzivatele registru smerovat na registr
			if($_SESSION['usr_ID_role']==3):
				header('Location: centers-vypsat.php');
			endif;

		else:
			//pri nespravnem heslu zaznamenat nezdareny pristup
			mysql_query("UPDATE admin SET nologin=(nologin+1) WHERE login='$uziv_jmeno'");

			header('Location: index.php?st=bad');
			exit;
		endif;

	else:
		header('Location: index.php?st=no');
		exit;
	endif;

endif;
//--konec prihlaseni
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<HTML><HEAD>

<TITLE>Hemeos, LLC</TITLE>
<link rel=”icon” href=”http://tc.hemeos.com/img/hemeos-favicon.ico” type=”image/icon”>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250; X-Content-Type-Options=nosniff">
<meta http-equiv="Content-Language" content="">
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
<LINK href="styl/styl.css" type="text/css"  rel="stylesheet">

<script type="text/javascript" src="script/menu.js"></script>
<script type="text/javascript" src="script/fce.js"></script>

<?
//* =============================================================================
//	Nastaven� (defaultn�ch) rozm�r� syst�mu
//============================================================================= */

		if(!$width): $width=973; endif;

		//--vypocty velikosty systemu
		$width2=$width-229;
		$width3=$width-235;
		$width4=$width-239;
		$width5=$width-235;

		//--velikost inputu a textarea
		$pulka_rozdilu=($width-973)/2;
		$width6=270+$pulka_rozdilu;
		$width7=470+$pulka_rozdilu;

		$global_vyska2=340;	//--vyska xinha boxu
		$global_sirka7=$width-460;	//--sirka xinha boxu

?>
</HEAD>
<BODY>


<div class="zaklad0">
<div class="zaklad" style="width:<? echo $width; ?>px">


<div class="logo"><img src="img/logo<? echo $_SESSION['jazyk']; ?>.gif" width="392" height="105" alt=""></div>
<div class="user-login">
<div class="warning-login"><img src="img/warning3.png" width="75" height="92" alt=""></div>
<div class="vlajky">
<?
//echo "<a href=\"".$_SERVER['PHP_SELF']."?lng=cz&amp;$query_string\"><img src=\"img/vlajky/cz.png\" width=\"23\" height=\"18\" alt=\"\" style=\"margin-right:8px;\"></a><a href=\"".$_SERVER['PHP_SELF']."?lng=en&amp;$query_string\"><img src=\"img/vlajky/en.png\" width=\"23\" height=\"18\" alt=\"\"></a>";
?>
</div>
</div>



<div style="clear:left; float:left; width:100%;">
<div class="login-img"></div>

<div class="prava-login text" style="width:450px;">
	<div class="zalozky" style="width:446px"><div class="menu-zalozky text5" style="width:400px; text-align:center;">
<?
	if($_GET['st']):

		if($_GET['st']=="no"):
			echo gtext('Zadejte pros�m p�ihla�ovac� �daje',104).".";
		endif;

		if($_GET['st']=="bad"):
			echo gtext('Zadan� �daje jsou chybn�',105).".";
		endif;

		if($_GET['st']=="block"):
			echo gtext('��et byl zablokov�n z d�vodu op�tovn�ho chybn�ho p�ihl��en�',106).".";
		endif;

		if($_GET['st']=="logged"):
			echo gtext('Pro p��stup do t�to oblasti mus�te b�t p�ihl��en',107).".";
		endif;

		if($_GET['st']=="rights"):
			echo gtext('Pro p��stup do t�to oblasti nem�te opr�vn�n�',108).".";
		endif;

	endif;
?></div></div>
	<div class="obal-stin-r"><div class="obal-stin-l">
		<div class="vnitrek-obal-login" style="width:440px; height:200px;">
		<div class="vnitrek" style="width:400px; margin-top:40px;">




<?

echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">

	<div class=\"form-lr\">
		<div class=\"form-left\" style=\"width:100px;\">".gtext('U�iv. jm�no',94).":</div>
		<div class=\"form-right\" style=\"width:230px;\"><input type=\"text\" style=\"width:220px;\" name=\"login\" value=\"$_POST[login]\"></div>
	</div>

	<div class=\"form-lr\">
		<div class=\"form-left\" style=\"width:100px;\">".gtext('Heslo',95).":</div>
		<div class=\"form-right\" style=\"width:230px;\"><input type=\"password\" style=\"width:220px;\" name=\"heslo\" value=\"\" autocomplete=\"off\"></div>
	</div>


	<div class=\"form-lr\">
		<div class=\"form-left\" style=\"width:100px;\">&nbsp;</div>
		<div class=\"form-right\" style=\"width:230px;\"><input type=\"submit\" class=\"form-send".$_SESSION['jazyk']."\" name=\"B1\" value=\"\" style=\"margin-bottom:30px;\"></div>

		<input type=\"hidden\" name=\"action\" value=\"login\">

	</div>

</form>";

?>

			echo "foo".password_hash("foo",PASSWORD_DEFAULT)."bar";





		</div>
		</div>
	</div></div>
<div class="stin-dole" style="width:444px; margin-bottom:100px;"></div>

</div>
</div>


</div>
</div>



<div class="patka"><div class="patka-svetlo">
	<div class="patka-vnitrek text4">
		<div class="patka-text1"><span class="text3 bold">Hemeos, LLC</span><br><br>Tel.: +1 (480) 251-8012<br>email: <a href="mailto:douggrant@hemeos.com" class="odkaz1">douggrant@hemeos.com</a></div>
		<div class="patka-text2"><span class="text3 bold"></div>
		<div class="patka-text3">Copyright � 2010 Steiner,s.r.o.<br><br><? echo gtext('Verze',109)." ".$_version; ?><br>www.steiner.cz</div>
	</div>
</div></div>




</BODY>
</HTML>
