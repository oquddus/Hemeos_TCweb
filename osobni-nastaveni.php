<?
include('1-head.php');
?>

<?
//objekt pro praci s tokeny
$Token = new Token();


//--upravit zaznam
if($_POST['action']=="upravit"):
	if($Token->useToken($_POST['token'])):

		if($_POST['heslo']):
			$_POST['heslo']=sha1($_POST['heslo']);
		endif;

		
		$SQL=mysql_query("UPDATE admin SET telefon='".mysql_real_escape_string($_POST['telefon'])."', 
		width='".mysql_real_escape_string($_POST['width'])."', 
		razeni='".mysql_real_escape_string($_POST['razeni'])."', 
		jazyk_vychozi='".mysql_real_escape_string($_POST['jazyk_vychozi'])."' 
		WHERE ID='$_SESSION[usr_ID]'");
		
		if($SQL):	//--pokud update neco zmenil
		
			//--kdyz upravuju sebe, potreba zmenit udaje
				if($_POST['heslo']):
					mysql_query("UPDATE admin SET heslo='".mysql_real_escape_string($_POST['heslo'])."' WHERE ID='$_SESSION[usr_ID]'");
					$_SESSION['usr_heslo']=$_POST['heslo'];
				endif;
			
		
			message(1, gtext('Záznam byl úspìšnì upraven',31), "", "");
		else:
			message(1, gtext('Záznam nemohl být upraven',32), "", "");
		endif;
		
		unset($_POST['action']);
		
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
endif;







//-----------------------------------------formular
if(!$_POST['action']):
	
	
	//--nacteni udadu pro editaci
	$_pole_opravneni=array();
	
	
	$vysledek = mysql_query("SELECT * FROM admin WHERE ID='$_SESSION[usr_ID]' LIMIT 1");

	if(mysql_num_rows($vysledek)>0):

			$zaz = mysql_fetch_array($vysledek);

				extract($zaz);

	endif;
	
	
	$form_name="formular";
	
	echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">

		<div class=\"form-lr\">
			<div class=\"form-left\">Login:</div>
			<div class=\"form-right\" style=\"margin-top:4px;\">$login</div>
		</div>

		<div class=\"form-lr\">
			<div class=\"form-left\">".gtext('Nové heslo',84).": <span class=\"povinne\"></span></div>
			<div class=\"form-right\"><input type=\"password\" style=\"width:".$width6."px;\" name=\"heslo\" value=\"\" autocomplete=\"off\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">".gtext('Heslo znovu',85).": <span class=\"povinne\"></span></div>
			<div class=\"form-right\"><input type=\"password\" style=\"width:".$width6."px;\" name=\"heslo2\" value=\"\" autocomplete=\"off\"></div>
		</div>";

		
		$vysledek_sub = mysql_query("SELECT registr, RegID FROM registers WHERE ID='".mysql_real_escape_string($ID_registru)."' LIMIT 1");
			if(mysql_num_rows($vysledek_sub) > 0):
				$zaz_sub = mysql_fetch_array($vysledek_sub);
					extract($zaz_sub);
			endif;
		@mysql_free_result($vysledek_sub);
		
		$vysledek_sub = mysql_query("SELECT centrum, InstID FROM transplant_centers WHERE ID='".mysql_real_escape_string($ID_centra)."' LIMIT 1");
			if(mysql_num_rows($vysledek_sub) > 0):
				$zaz_sub = mysql_fetch_array($vysledek_sub);
					extract($zaz_sub);
			endif;
		@mysql_free_result($vysledek_sub);
		

		echo "<div class=\"form-lr\" style=\"margin-top:20px;\">
			<div class=\"form-left\">Register:</div>
			<div class=\"form-right\" style=\"margin-top:4px;\">$registr ($RegID)</div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Transplant center:</div>
			<div class=\"form-right\" style=\"margin-top:4px;\">$centrum ($InstID)</div>
		</div>
		
		
		<div class=\"form-lr\" style=\"margin-top:20px;\">
			<div class=\"form-left\">".gtext('Jméno',86).":</div>
			<div class=\"form-right\" style=\"margin-top:4px;\">$jmeno</div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">".gtext('Pøíjmení',87).":</div>
			<div class=\"form-right\" style=\"margin-top:4px;\">$prijmeni</div>
		</div>
		
				
		<div class=\"form-lr\">
			<div class=\"form-left\">Email:</div>
			<div class=\"form-right\" style=\"margin-top:4px;\">$email</div>
		</div>
		
		
		<div class=\"form-lr\">
			<div class=\"form-left\">".gtext('Telefon',89).":</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"telefon\" value=\"$telefon\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">".gtext('Pøizpùsobit šíøku aplikace rozlišení obrazovky',93).":</div>
			<div class=\"form-right\"><select name=\"width\">";

					//echo "<OPTION class=\"form\" value=\"\">dle rozlišení obrazovky</option>";
					//echo "<OPTION class=\"form\" value=\"973\""; if($width=="973"): echo " selected"; endif; echo ">1024x768 (výchozí nastavení)</option>";
					//echo "<OPTION class=\"form\" value=\"1100\""; if($width=="1100"): echo " selected"; endif; echo ">1152x864</option>";
					echo "<OPTION class=\"form\" value=\"1200\""; if($width=="1200"): echo " selected"; endif; echo ">1280x800 (default)</option>";
					echo "<OPTION class=\"form\" value=\"1300\""; if($width=="1300"): echo " selected"; endif; echo ">1360x768</option>";
					echo "<OPTION class=\"form\" value=\"1300\""; if($width=="1300"): echo " selected"; endif; echo ">1440x900</option>";
					echo "<OPTION class=\"form\" value=\"1400\""; if($width=="1400"): echo " selected"; endif; echo ">1600x900</option>";
					echo "<OPTION class=\"form\" value=\"1500\""; if($width=="1500"): echo " selected"; endif; echo ">1680x1050</option>";
					echo "<OPTION class=\"form\" value=\"1600\""; if($width=="1600"): echo " selected"; endif; echo ">1920x1080</option>";


			echo "</select></div>
		</div>
		
		";
		
	
		
		

		echo "<div class=\"form-lr\">
			<div class=\"form-left\">&nbsp;</div>
			<div class=\"form-right\"><input type=\"submit\" class=\"form-send".$_SESSION['jazyk']."\" name=\"B1\" value=\"\"></div>
		</div>";
		
			//Generuj token
			$token=$Token->getToken();
			echo "<input type=\"hidden\" name=\"token\" id=\"token\" value=\"".$token."\">";

			echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
		
	echo "</form>";

	
	
	
endif;
?>			
			
<SCRIPT LANGUAGE="JavaScript">
  <!--
  function Kontrola ()
  {
  
		
  		if(document.formular.heslo.value!=document.formular.heslo2.value)
  		{      
			document.formular.heslo2.focus();
			alert ("Passwords must be identical.");
			return false;
		}
		
		
  }   
			

 
// -->
</SCRIPT>  
<?
include('1-end.php');
?>
