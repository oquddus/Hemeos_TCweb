<?
include('1-head.php');
?>


<?
if(!$_GET['ID'] || ($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) ){


//--vlozit zaznam
if($_POST['action']=="vlozit"):

	$_POST['heslo']=sha1($_POST['heslo']);
	if(!$_POST['width'] || $_POST['width']<=0): $_POST['width']=1200; endif;
	
	get_timestamp('platnost_od');
	get_timestamp('platnost_do');
	
	
	insert_tb('admin');
	
	
	if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku
	
		//--OPRAVNENI k modulum
		if(count($_pole_menu)>0):
			foreach($_pole_menu as $klic=>$hodnota):

			if($_POST[$klic]==1):
				mysql_query("INSERT INTO admin_prava VALUES
				('$idcko',
				'".mysql_real_escape_string($klic)."')");
			endif;
										
			endforeach;
		endif;

	
		message(3, "Item was successfuly inserted.", "Insert next one", "$_SERVER[PHP_SELF]");
	else:
		message(1, "Item was not inserted.", "", "");
		
		call_post();	//--zrusi akci, post promenne da do lokalnich
	endif;
		
endif;




//--upravit zaznam
if($_POST['action']=="upravit"):

	if($_POST['heslo']):
		$_POST['heslo']=sha1($_POST['heslo']);
		$update[0]=array("ID");	//--ktere sloupce vyloucit z updatu
	else:
		$update[0]=array("ID","heslo");	//--ktere sloupce vyloucit z updatu
	endif;
	
	$update[1]=array("WHERE ID='".mysql_real_escape_string($_GET['ID'])."'");	//--where
	
	
	get_timestamp('platnost_od');
	get_timestamp('platnost_do');
	
	
	update_tb('admin',$update);	//--tabulka   |   pole vyloucenych sloupcu
	
	if($SQL):	//--pokud update neco zmenil
	
		//--kdyz upravuju sebe, potreba zmenit udaje
		if($_GET['ID']==$_SESSION['usr_ID']):
			$_SESSION['usr_login']=$_POST['login'];
			
			if($_POST['heslo']):
				$_SESSION['usr_heslo']=$_POST['heslo'];
			endif;
		endif;
		

		//--OPRAVNENI k modulum
		mysql_query("DELETE FROM admin_prava WHERE ID_admin='".mysql_real_escape_string($_GET['ID'])."'");
		
		if(count($_pole_menu)>0):
			foreach($_pole_menu as $klic=>$hodnota):

			if($_POST[$klic]==1):
				mysql_query("INSERT INTO admin_prava VALUES
				('".mysql_real_escape_string($_GET['ID'])."',
				'".mysql_real_escape_string($klic)."')");
			endif;
									
			endforeach;
		endif;
				
			
		message(1, "Item was successfuly edited.", "", "");
	else:
		message(1, "Item was not edited.", "", "");
	endif;
	
	unset($_POST['action']);
endif;








//-----------------------------------------formular
if(!$_POST['action']):
	
	
	//--nacteni udadu pro editaci
	$_pole_opravneni=array();
	$_pole_opravneni_rozsirujici=array();
	
	
	$vysledek = mysql_query("SELECT * FROM admin WHERE ID='".mysql_real_escape_string($_GET['ID'])."' LIMIT 1");
	if(mysql_num_rows($vysledek)>0):
		$zaz = mysql_fetch_array($vysledek);
			$zaz=htmlspecialchars_array_encode($zaz);
			extract($zaz);

			//--nacteni opravneni uzivatele
			$vysledek_p = mysql_query("SELECT modul FROM admin_prava WHERE ID_admin='".mysql_real_escape_string($_GET['ID'])."'");
				if(mysql_num_rows($vysledek_p)>0):
					while($zaz_p = mysql_fetch_array($vysledek_p)):
						extract($zaz_p);
						$_pole_opravneni[]=$modul;
					endwhile;
				endif;
			@mysql_free_result($vysledek_p);

	endif;
	
	
	$form_name="formular";
	
	echo "<form onsubmit=\"return Kontrola"; if($_GET['ID']): echo "2"; endif; echo "();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">

		<div class=\"form-lr\">
			<div class=\"form-left\">Login: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"login\" value=\"$login\"></div>
		</div>

		<div class=\"form-lr\">
			<div class=\"form-left\">Password: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"password\" style=\"width:".$width6."px;\" name=\"heslo\" value=\"\" autocomplete=\"off\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Password again: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"password\" style=\"width:".$width6."px;\" name=\"heslo2\" value=\"\" autocomplete=\"off\"></div>
		</div>
		
		
		<div class=\"form-lr\">
			<div class=\"form-left\" style=\"margin-top:4px;\">Valid:</div>
			<div class=\"form-right\">
				<div style=\"float:left;\">from:<span class=\"povinne\">*</span> <input type=\"text\" style=\"width:80px;\" name=\"platnost_od\" id=\"platnost_od\" value=\""; if(!$platnost_od): echo date("Y-m-d",time()); else: echo date("Y-m-d",$platnost_od); endif; echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('platnost_od',$form_name);
				echo "</div>
				
				<div style=\"float:left; margin-left:20px; display:inline;\">to: <input type=\"text\" style=\"width:80px;\" name=\"platnost_do\" id=\"platnost_do\" value=\""; if(!$platnost_do): echo "YYYY-MM-DD"; else: echo date("Y-m-d",$platnost_do); endif; echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('platnost_do',$form_name);
				echo "</div>
			</div>
		</div>
		
		
		<div class=\"form-lr\" style=\"margin-top:20px;\">
			<div class=\"form-left\">First name: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"jmeno\" value=\"$jmeno\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Surname: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"prijmeni\" value=\"$prijmeni\"></div>
		</div>";
		
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">Register: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><select name=\"ID_registru\" id=\"ID_registru\" style=\"width:".$width6."px;\" onchange=\"zobraz_tc();\">";

					echo "<OPTION class=\"form\" value=\"\">-choose the Register-</option>";

					//admin vidi vsechny registry, ostatni vidi jen svuj registr
					$where_registry="";
					if($_SESSION['usr_ID_role']!=1):
						$where_registry="AND registers.ID='".$_SESSION['usr_ID_registru']."'";
					endif;
					
					$vysledek_if = mysql_query("SELECT ID AS IDregistru, registr FROM registers WHERE aktivni='1' $where_registry ORDER BY registr");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							echo "<OPTION class=\"form\" value=\"$IDregistru\""; if($ID_registru==$IDregistru): echo "selected"; endif; echo ">$registr</option>";
							
						endwhile;
					endif;
					@mysql_free_result($vysledek_if);

			echo "</select></div>
		</div>";
		
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">Transplant centre: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\" id=\"prostor_centrum\"></div>
		</div>";
		
		
		
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">Email:</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"email\" value=\"$email\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Telefon:</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"telefon\" value=\"$telefon\"></div>
		</div>
		
		<div class=\"form-lr\" style=\"margin-top:20px;\">
			<div class=\"form-left\">Application width:</div>
			<div class=\"form-right\"><select name=\"width\">";

					//echo "<OPTION class=\"form\" value=\"\">dle rozlišení obrazovky</option>";
					echo "<OPTION class=\"form\" value=\"973\""; if($width=="973"): echo " selected"; endif; echo ">1024x768</option>";
					echo "<OPTION class=\"form\" value=\"1100\""; if($width=="1100"): echo " selected"; endif; echo ">1152x864</option>";
					echo "<OPTION class=\"form\" value=\"1200\""; if($width=="1200" || !$_GET['ID']): echo " selected"; endif; echo ">1280x800 (default setting)</option>";
					echo "<OPTION class=\"form\" value=\"1300\""; if($width=="1300"): echo " selected"; endif; echo ">1360x768</option>";
					echo "<OPTION class=\"form\" value=\"1300\""; if($width=="1300"): echo " selected"; endif; echo ">1440x900</option>";
					echo "<OPTION class=\"form\" value=\"1400\""; if($width=="1400"): echo " selected"; endif; echo ">1600x900</option>";
					echo "<OPTION class=\"form\" value=\"1500\""; if($width=="1500"): echo " selected"; endif; echo ">1680x1050</option>";
					echo "<OPTION class=\"form\" value=\"1600\""; if($width=="1600"): echo " selected"; endif; echo ">1920x1080</option>";


			echo "</select></div>
		</div>";
		
		/*
		echo "<div class=\"form-lr\" style=\"margin-top:20px;\">
			<div class=\"form-left\">Požadavky øadit dle:</div>
			<div class=\"form-right\"><select name=\"razeni\">";

					echo "<OPTION class=\"form\" value=\"ID\""; if($razeni=="ID"): echo " selected"; endif; echo ">data vložení (výchozí nastavení)</option>";
					echo "<OPTION class=\"form\" value=\"stav\""; if($razeni=="stav"): echo " selected"; endif; echo ">stavu</option>";
					echo "<OPTION class=\"form\" value=\"priorita\""; if($razeni=="priorita"): echo " selected"; endif; echo ">priority</option>";
					echo "<OPTION class=\"form\" value=\"zkratka\""; if($razeni=="zkratka"): echo " selected"; endif; echo ">projektu</option>";


			echo "</select></div>
		</div>";
		*/
		
		/*
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">Výchozí jazyk:</div>
			<div class=\"form-right\"><select name=\"jazyk_vychozi\">";

					echo "<OPTION class=\"form\" value=\"_cz\""; if($jazyk_vychozi=="_cz"): echo " selected"; endif; echo ">èeský</option>";
					echo "<OPTION class=\"form\" value=\"_en\""; if($jazyk_vychozi=="_en"): echo " selected"; endif; echo ">anglický</option>";
					
			echo "</select></div>
		</div>";
		*/
		
		
		
		
		//* =============================================================================
		//	Oprávnìní k modulùm
		//============================================================================= */
		echo "<div class=\"form-lr\" style=\"margin-top:35px;\" id=\"opravneni_moduly\">
			<div class=\"form-left\">Modul access:</div>
			<div class=\"form-right\">";
			
				echo "<div style=\"float:left; margin-bottom:12px; display:none;\"><select name=\"ID_role_admin\" id=\"ID_role_admin\" onchange=\"nastav_moduly();\">";

					//echo "<OPTION class=\"form\" value=\"\">-role pattern-</option>";
					//echo "<OPTION class=\"form\" value=\"1\""; if(count($_pole_opravneni)==count($_pole_menu)): echo " selected"; endif; echo ">admin</option>";
					echo "<OPTION class=\"form\" value=\"2\""; if($_GET['ID'] && count($_pole_opravneni)!=count($_pole_menu)): echo " selected"; endif; echo ">user</option>";

				echo "</select></div>";
				
				
			
				foreach($_pole_menu as $klic=>$hodnota):
							
					if($klic!="admin" && $klic!="centers" && $klic!="registers" && $klic!="user"):
						echo "<div style=\"float:left; width:100%; margin-top:4px; margin-bottom:4px;\">
							<div style=\"float:left; margin-top:-4px; margin-left:20px; display:inline; width:25px;\"><input type=\"checkbox\" name=\"$klic\" id=\"$klic\" value=\"1\""; if(in_array($klic,$_pole_opravneni) || !$_GET['ID']): echo " checked"; endif; echo "></div>
							<div style=\"float:left;\"><label for=\"$klic\">$klic</label></div>";
						echo "</div>";
					endif;

				endforeach;

			echo "</div>
		</div>";
		//	Konec oprávnìní k projektùm
		//============================================================================= */
		

		
		
		
		

		echo "<div class=\"form-lr\">
			<div class=\"form-left\">&nbsp;</div>
			<div class=\"form-right\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>
		</div>";
		

		if($_GET['ID']):
			echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
		else:
			echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
		endif;
		
	echo "</form>";

	
	
	
	
	
	
//* =============================================================================
//	JS
//============================================================================= */
echo "<SCRIPT LANGUAGE=\"JavaScript\">

	function nastav_prava(){
	
		var div_zajmu = document.getElementById('opravneni_projekty');
		var selecty = div_zajmu.getElementsByTagName(\"select\");
		
			for (i = 0; i < selecty.length; i++) {
			
				var ID_projektu=selecty[i].name.replace(\"prava_\",\"\");
				var hodnota=document.getElementById('ID_role').value;
				
				
				if(ID_projektu>0 && hodnota!=99 && hodnota>0){
					selecty[i].value=hodnota;
					rozsirujici_prava(ID_projektu);
				}
			}

	}

	function rozsirujici_prava(ID_projektu){
	
		var prava=document.getElementById('prava_'+ID_projektu).value;
	

		document.getElementById('prava_rozsirujici_'+ID_projektu).style.display=\"none\";
		document.getElementById('prava_rozsirujici_zadavatel_'+ID_projektu).style.display=\"none\";

		
		if(prava==5){
			document.getElementById('prava_rozsirujici_'+ID_projektu).style.display=\"block\";
		}
		
		if(prava==2){
			document.getElementById('prava_rozsirujici_zadavatel_'+ID_projektu).style.display=\"block\";
		}
	
	}
	
	
	function dle_tabulky(){
		document.getElementById('ID_role').value=99;
	}
	
	
	
	
	function nastav_moduly(){
	
		var div_zajmu = document.getElementById('opravneni_moduly');
		var checkboxy = div_zajmu.getElementsByTagName(\"input\");
		
			for (i = 0; i < checkboxy.length; i++) {
				
				if(document.getElementById('ID_role_admin').value==1){
					checkboxy[i].checked=true;
				}
				
				if(document.getElementById('ID_role_admin').value==2){
					checkboxy[i].checked=false;
					
					if(checkboxy[i].name==\"pozadavky\"){
						checkboxy[i].checked=true;
					}
					
					if(checkboxy[i].name==\"osobni\"){
						checkboxy[i].checked=true;
					}
				}
			}

	}
  

</SCRIPT>";
	
	
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	
	

		function zobraz_tc(){
		
			$.ajax({
				url: 'include/ajax-user1.php?ID_registru='+document.getElementById('ID_registru').value+'&ID_centra=$ID_centra',
				async: true,
				complete: function(XMLHTTPRequest, textStatus){
					
					document.getElementById('prostor_centrum').innerHTML=XMLHTTPRequest.responseText;
					
				}
			});
			
		}
		
		
		
		zobraz_tc();
		
	
	
	</SCRIPT>";	
	
	
	
	
endif;

}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>			
	
<?	
echo "<SCRIPT LANGUAGE=\"JavaScript\">

	function Kontrola (){
  
		if(!document.formular.login.value)
  		{      
			document.formular.login.focus();
			alert (\"Please, fill login.\");
			return false;
		}

  		if(!document.formular.heslo.value)
  		{      
			document.formular.heslo.focus();
			alert (\"Please, fill password.\");
			return false;
		}
		
  		if(document.formular.heslo.value!=document.formular.heslo2.value)
  		{      
			document.formular.heslo2.focus();
			alert (\"Passwords must be identical.\");
			return false;
		}
		
		if(!document.formular.platnost_od.value || document.formular.platnost_od.value==\"YYYY-MM-DD\")
  		{      
			document.formular.platnost_od.focus();
			alert (\"Please, fill valid from.\");
			return false;
		}
		
		if(!document.formular.jmeno.value)
  		{      
			document.formular.jmeno.focus();
			alert (\"Please, fill first name.\");
			return false;
		}
		
		if(!document.formular.prijmeni.value)
  		{      
			document.formular.prijmeni.focus();
			alert (\"Please, fill surname.\");
			return false;
		}
		
		if(!document.formular.ID_registru.value)
  		{      
			document.formular.ID_registru.focus();
			alert (\"Please, choose Register.\");
			return false;
		}
		
		if(!document.formular.ID_centra.value)
  		{      
			document.formular.ID_centra.focus();
			alert (\"Please, choose Transplant centre.\");
			return false;
		}

		
	}   
  
  
  function Kontrola2 (){
   
		if(!document.formular.login.value)
  		{      
			document.formular.login.focus();
			alert (\"Please, fill login.\");
			return false;
		}
		
  		if(document.formular.heslo.value!=document.formular.heslo2.value)
  		{      
			document.formular.heslo2.focus();
			alert (\"Passwords must be identical.\");
			return false;
		}
		
		if(!document.formular.platnost_od.value || document.formular.platnost_od.value==\"YYYY-MM-DD\")
  		{      
			document.formular.platnost_od.focus();
			alert (\"Please, fill valid from.\");
			return false;
		}
		
		if(!document.formular.jmeno.value)
  		{      
			document.formular.jmeno.focus();
			alert (\"Please, fill first name.\");
			return false;
		}
		
		if(!document.formular.prijmeni.value)
  		{      
			document.formular.prijmeni.focus();
			alert (\"Please, fill surname.\");
			return false;
		}
		
		if(!document.formular.ID_registru.value)
  		{      
			document.formular.ID_registru.focus();
			alert (\"Please, choose Register.\");
			return false;
		}
		
		if(!document.formular.ID_centra.value)
  		{      
			document.formular.ID_centra.focus();
			alert (\"Please, choose Transplant centre.\");
			return false;
		}

  } 			

</SCRIPT> ";
?>
<?
include('1-end.php');
?>
