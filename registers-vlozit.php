<?
include ('1-head.php');
?>


<?
if (! $_GET ['ID'] || ($_GET ['ID'] && $_GET ['h'] == sha1 ( $_GET ['ID'] . "SaltIsGoodForLife45" ))) {
	
	// --vlozit zaznam
	if ($_POST ['action'] == "vlozit") :
		
		$_POST ['datum_vlozeni'] = time ();
		
		insert_tb ( 'registers' );
		
		if ($idcko) : // --pokud vse vlozeno, vim id vlozeneho radku
			
			$_POST ['ID_registru'] = $idcko;
			
			// vlozeni do tabulky admin
			$_POST ['heslo'] = sha1 ( $_POST ['heslo'] );
			if (! $_POST ['width'] || $_POST ['width'] <= 0) :
				$_POST ['width'] = 1200;
			 endif;
			
			get_timestamp ( 'platnost_od' );
			get_timestamp ( 'platnost_do' );
			
			insert_tb ( 'admin' );
			
			if ($idcko) : // --pokud vse vlozeno, vim id vlozeneho radku
			            // --OPRAVNENI k modulum
				if (count ( $_pole_menu ) > 0) :
					foreach ( $_pole_menu as $klic => $hodnota ) :
						
						if ($_POST [$klic] == 1) :
							mysql_query ( "INSERT INTO admin_prava VALUES
					('$idcko',
					'" . mysql_real_escape_string ( $klic ) . "')" );
						
				endif;
					endforeach
					;
				
			endif;
			
		endif;
			
			message ( 3, "Record was inserted.", "Insert next record", "$_SERVER[PHP_SELF]" );
		 else :
			message ( 1, "Record was not inserted.", "", "" );
			
			call_post ();
		 // --zrusi akci, post promenne da do lokalnich
endif;
	
		
endif;
	
	// --upravit zaznam
	if ($_POST ['action'] == "upravit") :
		
		$update [0] = array (
				"ID",
				"datum_vlozeni" 
		); // --ktere sloupce vyloucit z updatu
		$update [1] = array (
				"WHERE ID='" . mysql_real_escape_string ( $_GET ['ID'] ) . "'" 
		); // --where
		
		update_tb ( 'registers', $update ); // --tabulka | pole vyloucenych sloupcu
		
		if ($SQL) : // --pokud update neco zmenil
		          
			// uprava v tabulce admin
			get_timestamp ( 'platnost_od' );
			get_timestamp ( 'platnost_do' );
			
			$SQL = mysql_query ( "UPDATE admin SET login='" . mysql_real_escape_string ( $_POST ['login'] ) . "', platnost_od='" . mysql_real_escape_string ( $_POST ['platnost_od'] ) . "', platnost_do='" . mysql_real_escape_string ( $_POST ['platnost_do'] ) . "' WHERE ID='" . mysql_real_escape_string ( $_POST ['ID_uctu'] ) . "' LIMIT 1" );
			
			if ($_POST ['heslo']) :
				$_POST ['heslo'] = sha1 ( $_POST ['heslo'] );
				mysql_query ( "UPDATE admin SET heslo='" . mysql_real_escape_string ( $_POST ['heslo'] ) . "' WHERE ID='" . mysql_real_escape_string ( $_POST ['ID_uctu'] ) . "' LIMIT 1" );
			
		endif;
			
			if ($SQL) : // --pokud update neco zmenil
			          
				// --OPRAVNENI k modulum
				mysql_query ( "DELETE FROM admin_prava WHERE ID_admin='" . mysql_real_escape_string ( $_POST ['ID_uctu'] ) . "'" );
				
				if (count ( $_pole_menu ) > 0) :
					foreach ( $_pole_menu as $klic => $hodnota ) :
						
						if ($_POST [$klic] == 1) :
							mysql_query ( "INSERT INTO admin_prava VALUES
					('" . mysql_real_escape_string ( $_POST ['ID_uctu'] ) . "',
					'" . mysql_real_escape_string ( $klic ) . "')" );
						
				endif;
					endforeach
					;
				
			endif;
			

		endif;
			
			message ( 1, "Record was edited.", "", "" );
		 else :
			message ( 1, "Record was not edited.", "", "" );
		endif;
		
		unset ( $_POST ['action'] );
	
endif;
	
	// -----------------------------------------formular
	if (! $_POST ['action']) :
		
		// --nacteni udadu pro editaci
		$_pole_opravneni = array ();
		$_pole_opravneni_rozsirujici = array ();
		
		$vysledek = mysql_query ( "SELECT * FROM registers WHERE ID='" . mysql_real_escape_string ( $_GET ['ID'] ) . "' LIMIT 1" );
		if (mysql_num_rows ( $vysledek ) > 0) :
			$zaz = mysql_fetch_array ( $vysledek );
			$zaz = htmlspecialchars_array_encode ( $zaz );
			extract ( $zaz );
		

	endif;
		
		// zjisteni ID uctu a prav
		$vysledek_s = mysql_query ( "SELECT ID AS ID_uctu, login, platnost_od, platnost_do FROM admin WHERE ID_registru='" . mysql_real_escape_string ( $_GET ['ID'] ) . "' AND ID_role_admin='3' LIMIT 1" );
		if (mysql_num_rows ( $vysledek_s ) > 0) :
			$zaz_s = mysql_fetch_array ( $vysledek_s );
			$zaz_s = htmlspecialchars_array_encode ( $zaz_s );
			extract ( $zaz_s );
			
			// --nacteni opravneni uzivatele
			$vysledek_p = mysql_query ( "SELECT modul FROM admin_prava WHERE ID_admin='" . mysql_real_escape_string ( $ID_uctu ) . "'" );
			if (mysql_num_rows ( $vysledek_p ) > 0) :
				while ( $zaz_p = mysql_fetch_array ( $vysledek_p ) ) :
					extract ( $zaz_p );
					$_pole_opravneni [] = $modul;
				endwhile
				;
			
				endif;
			@mysql_free_result ( $vysledek_p );
		

	endif;
		
		$form_name = "formular";
		
		echo "<form onsubmit=\"return Kontrola";
		if ($_GET ['ID']) :
			echo "2";
		 endif;
		echo "();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Register name: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:" . $width6 . "px;\" name=\"registr\" value=\"$registr\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">HUB code: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:" . $width6 . "px;\" name=\"RegID\" value=\"$RegID\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Email:</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:" . $width6 . "px;\" name=\"email\" value=\"$email\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Phone:</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:" . $width6 . "px;\" name=\"telefon\" value=\"$telefon\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Date format:</div>
			<div class=\"form-right\"><select style=\"width:150px;\" name=\"date_format\" id=\"date_format\">
				<option value=\"YYYY-MM-DD\"";
		if ($date_format == "YYYY-MM-DD") :
			echo " selected";
		 endif;
		echo ">YYYY-MM-DD</option>
				<option value=\"M-D-YYYY\"";
		if ($date_format == "M-D-YYYY") :
			echo " selected";
		 endif;
		echo ">M-D-YYYY</option>
				<option value=\"D-M-YYYY\"";
		if ($date_format == "D-M-YYYY") :
			echo " selected";
		 endif;
		echo ">D-M-YYYY</option>
				<option value=\"M.D.YYYY\"";
		if ($date_format == "M.D.YYYY") :
			echo " selected";
		 endif;
		echo ">M.D.YYYY</option>
				<option value=\"D.M.YYYY\"";
		if ($date_format == "D.M.YYYY") :
			echo " selected";
		 endif;
		echo ">D.M.YYYY</option>
			</select></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Active:</div>
			<div class=\"form-right\"><input type=\"checkbox\" name=\"aktivni\" value=\"1\"";
		if ($aktivni || ! $_GET ['ID']) :
			echo " checked";
		 endif;
		echo "></div>
		</div>";
		
		// * =============================================================================
		// Ucet pro registr
		// ============================================================================= */
		echo "<div class=\"form-lr\" style=\"margin-top:25px;\">
			<div class=\"form-left\">Login: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:" . $width6 . "px;\" name=\"login\" value=\"$login\"></div>
		</div>

		<div class=\"form-lr\">
			<div class=\"form-left\">Password: ";
		if (! $_GET ['ID']) :
			echo "<span class=\"povinne\">*</span>";
		 endif;
		echo "</div>
			<div class=\"form-right\"><input type=\"password\" style=\"width:" . $width6 . "px;\" name=\"heslo\" value=\"\" autocomplete=\"off\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Password again: ";
		if (! $_GET ['ID']) :
			echo "<span class=\"povinne\">*</span>";
		 endif;
		echo "</div>
			<div class=\"form-right\"><input type=\"password\" style=\"width:" . $width6 . "px;\" name=\"heslo2\" value=\"\" autocomplete=\"off\"></div>
		</div>
		
		
		<div class=\"form-lr\">
			<div class=\"form-left\" style=\"margin-top:4px;\">Valid:</div>
			<div class=\"form-right\">
				<div style=\"float:left;\">from:<span class=\"povinne\">*</span> <input type=\"text\" style=\"width:80px;\" name=\"platnost_od\" id=\"platnost_od\" value=\"";
		if (! $platnost_od) :
			echo date ( "Y-m-d", time () );
		 else :
			echo date ( "Y-m-d", $platnost_od );
		endif;
		echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
		get_calendar ( 'platnost_od', $form_name );
		echo "</div>
				
				<div style=\"float:left; margin-left:20px; display:inline;\">to: <input type=\"text\" style=\"width:80px;\" name=\"platnost_do\" id=\"platnost_do\" value=\"";
		if (! $platnost_do) :
			echo "YYYY-MM-DD";
		 else :
			echo date ( "Y-m-d", $platnost_do );
		endif;
		echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
		get_calendar ( 'platnost_do', $form_name );
		echo "</div>
			</div>
		</div>

		
		
		<div class=\"form-lr\" style=\"margin-top:20px; display:none;\">
			<div class=\"form-left\">Application width:</div>
			<div class=\"form-right\"><select name=\"width\">";
		
		// echo "<OPTION class=\"form\" value=\"\">dle rozlišení obrazovky</option>";
		echo "<OPTION class=\"form\" value=\"973\"";
		if ($width == "973") :
			echo " selected";
		 endif;
		echo ">1024x768</option>";
		echo "<OPTION class=\"form\" value=\"1100\"";
		if ($width == "1100") :
			echo " selected";
		 endif;
		echo ">1152x864</option>";
		echo "<OPTION class=\"form\" value=\"1200\"";
		if ($width == "1200" || ! $_GET ['ID']) :
			echo " selected";
		 endif;
		echo ">1280x800 (default setting)</option>";
		echo "<OPTION class=\"form\" value=\"1300\"";
		if ($width == "1300") :
			echo " selected";
		 endif;
		echo ">1360x768</option>";
		echo "<OPTION class=\"form\" value=\"1300\"";
		if ($width == "1300") :
			echo " selected";
		 endif;
		echo ">1440x900</option>";
		echo "<OPTION class=\"form\" value=\"1400\"";
		if ($width == "1400") :
			echo " selected";
		 endif;
		echo ">1600x900</option>";
		echo "<OPTION class=\"form\" value=\"1500\"";
		if ($width == "1500") :
			echo " selected";
		 endif;
		echo ">1680x1050</option>";
		echo "<OPTION class=\"form\" value=\"1600\"";
		if ($width == "1600") :
			echo " selected";
		 endif;
		echo ">1920x1080</option>";
		
		echo "</select></div>
		</div>";
		
		echo "<input type=\"hidden\" name=\"ID_uctu\" value=\"$ID_uctu\">";
		
		// * =============================================================================
		// Oprávnìní k modulùm
		// ============================================================================= */
		echo "<div class=\"form-lr\" style=\"margin-top:25px;\" id=\"opravneni_moduly\">
			<div class=\"form-left\">Modul access:</div>
			<div class=\"form-right\">";
		
		echo "<div style=\"float:left; margin-bottom:12px; display:none;\"><select name=\"ID_role_admin\" id=\"ID_role_admin\" onchange=\"nastav_moduly();\">";
		
		echo "<OPTION class=\"form\" value=\"3\">administrator of registr</option>";
		
		echo "</select></div>";
		
		foreach ( $_pole_menu as $klic => $hodnota ) :
			
			if ($klic == "centers" || $klic == "osobni" || $klic == "user" || $klic == "registers") :
				echo "<div style=\"float:left; width:100%; margin-top:4px; margin-bottom:4px;\">
							<div style=\"float:left; margin-top:-4px; margin-left:20px; display:inline; width:25px;\"><input type=\"checkbox\" name=\"$klic\" id=\"$klic\" value=\"1\"";
				if (in_array ( $klic, $_pole_opravneni ) || ! $_GET ['ID']) :
					echo " checked";
				 endif;
				echo "></div>
							<div style=\"float:left;\"><label for=\"$klic\">$klic</label></div>";
				echo "</div>";
			
					endif;
		endforeach
		;
		
		echo "</div>
		</div>";
		// Konec oprávnìní k projektùm
		// ============================================================================= */
		
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">&nbsp;</div>
			<div class=\"form-right\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>
		</div>";
		
		if ($_GET ['ID']) :
			echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
		 else :
			echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
		endif;
		
		echo "</form>";
	

	
	
	
	
	
endif;
	
	echo "<SCRIPT LANGUAGE=\"JavaScript\">
  <!--
  function Kontrola(){
  
 		if(!document.formular.registr.value){      
			document.formular.registr.focus();
			alert (\"Please, fill the register name.\");
			return false;
		}
		
		if(!document.formular.RegID.value){      
			document.formular.RegID.focus();
			alert (\"Please, fill the register ID.\");
			return false;
		}
		
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
		
  }   
  
  
  
  
  function Kontrola2(){
  
 		if(!document.formular.registr.value){      
			document.formular.registr.focus();
			alert (\"Please, fill the register name.\");
			return false;
		}
		
		if(!document.formular.RegID.value){      
			document.formular.RegID.focus();
			alert (\"Please, fill the register ID.\");
			return false;
		}
		
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
		
  }
  
  
 
// -->
</SCRIPT>";
} else {
	message ( 1, "You can not access this page.", "", "", 2 );
}
?>

<?
include ('1-end.php');
?>
