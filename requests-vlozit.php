<?
include('1-head.php');
include('1-config.php');
?>


<?
if(!$_GET['ID'] || ($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) ){

//objekt pro praci s tokeny
$Token = new Token();


//pravo na dalsi policka
$extra_inputs = mysql_result(mysql_query("SELECT COUNT(*) FROM transplant_centers WHERE ID='".clean_high($_SESSION['usr_ID_centra'])."' AND extra_inputs='1'"), 0);

function get_label($txt,$key){
	global $_hodnoty;

	if(!empty($_hodnoty[$key]['l'])):
		$txt=$_hodnoty[$key]['l'];
	endif;
	return $txt;

}

function uprav_data(){
	get_timestamp('date_request');
	get_timestamp('date_birth');
	get_timestamp('date_diagnosis');

	get_timestamp('cmv_date');
	get_timestamp('temp_transpl_date');

	$_POST['weight']=str_replace(" ","",$_POST['weight']);
	$_POST['weight']=str_replace(",",".",$_POST['weight']);

	if(!$_POST['mismatches']):
		$_POST['mismatches']=2;	//nastavit jakoze ne (puvodne to byl radiobutton)
		unset($_POST['mm_hla_a'],$_POST['mm_hla_b'],$_POST['mm_hla_c'],$_POST['mm_hla_dr'],$_POST['mm_hla_dq']);
	endif;

}

//pokud jsem v editaci pracovni verze a uz neni ukladani to tmp, musim vlozit novy zaznam
$smazat_tmp=0;
if($_POST['action'] && $_GET['w']==1 && $_POST['temp_data']!=1):
	$_POST['action']="vlozit";
	$smazat_tmp=1;
endif;


//--vlozit zaznam
if($_POST['action']=="vlozit"):
	if($Token->useToken($_POST['token'])):

		if($_POST['temp_data']==1):
			$table="search_request_tmp";
		else:
			$table="search_request";
		endif;


		$_POST['datum_vlozeni']=time();
		$_POST['datum_editace']=time();
		$_POST['ID_stavu']=1;

		uprav_data();

		if($_POST['RegID'] && $_POST['InstID']):
			insert_tb($table);
		else:
			message(1, "Transplant center and register identification missed", "", "");
		endif;

		if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku

			if($smazat_tmp==1):
				mysql_query("DELETE FROM search_request_tmp WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
			endif;

			presmeruj('requests-result.php','status=1');
		else:
			message(1, "Item was not inserted.", "", "");

			call_post();	//--zrusi akci, post promenne da do lokalnich
		endif;
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
endif;




//--upravit zaznam
if($_POST['action']=="upravit"):
	if($Token->useToken($_POST['token'])):

		if($_POST['temp_data']==1):
			$table="search_request_tmp";
		else:
			$table="search_request";
		endif;

		uprav_data();

		$_POST['datum_editace']=time();

		//$update[0]=array("ID","datum_vlozeni","InstID","RegID","ID_stavu","PatientNum","datum_zpracovani","datum_nacteno");	//--ktere sloupce vyloucit z updatu
		$update[0]=array("ID","datum_vlozeni","RegID","ID_stavu","PatientNum","datum_zpracovani","datum_odeslani","duvod_zmeny_stavu");	//--ktere sloupce vyloucit z updatu
		$update[1]=array("WHERE ID='".clean_high($_GET['ID'])."'");	//--where

		update_tb($table,$update);	//--tabulka   |   pole vyloucenych sloupcu

		if($SQL):	//--pokud update neco zmenil

			if($_POST['temp_data']!=1):
				//--zjistit stav
				$vysledek = mysql_query("SELECT ID_stavu FROM search_request WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
					if(mysql_num_rows($vysledek)>0):
						$zaz = mysql_fetch_array($vysledek);
							extract($zaz);

							//pokud upravuji a stav je 3 (zamitnuto), zmenit stav opet na 1(k odeslani)
							if($ID_stavu==3):
								mysql_query("UPDATE search_request SET ID_stavu='1', datum_zpracovani='0', datum_odeslani='0' WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
							endif;

					endif;
				@mysql_free_result($vysledek);
			endif;

			message(1, "Item was successfuly edited.", "", "");
		else:
			message(1, "Item was not edited.", "", "");
		endif;

		unset($_POST['action']);
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
endif;








//-----------------------------------------formular
if(!$_POST['action']):

	//Generuj token
	$token=$Token->getToken();

	/*
	//--kontrola povinnyh poli v  JS
	$povinne[0]=array("formular");	//--nazev formulare
	$povinne[1]=array("last_name","date_birth","gender","diagnosis","date_diagnosis","InstID");	//--nazev povinnych inputu
	$povinne[2]=array("Last name","Date of Birth","Gender","Diagnosis","Date of Diagnosis","Transplant Center");	//--textove nazvy kontrolovanych poli
	$povinne[3]=array(1,1,5,3,1,1);	//--typ (textbox=1, checkbox=2, selectbox=3, multibox=4, radio=5)

	get_povinne_js($povinne);
	*/




	//--nacteni udadu pro editaci
	if($_GET['ID']):

		if($_GET['w']==1):	//pracovni verze
			$table="search_request_tmp";
		else:
			$table="search_request";
		endif;

		$vysledek = mysql_query("SELECT * FROM ".$table." WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
			if(mysql_num_rows($vysledek)>0):
				$zaz = mysql_fetch_array($vysledek);
					$zaz=htmlspecialchars_array_encode($zaz);
					extract($zaz);

			endif;
		@mysql_free_result($vysledek);
	endif;

	//volitelna pole, popisky...
	$_hodnoty=array();

		//default hodnoty
		$vysledek_if = mysql_query("SELECT ID AS ID_item, title, visible, required, change_v, change_r, change_l FROM setting_preliminary");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);

					$_hodnoty[$ID_item]['v']=$visible;
					$_hodnoty[$ID_item]['r']=$required;
					$_hodnoty[$ID_item]['l']=$title;

					if($change_v):	//mohu menit nastaveni
						$vysledek_sub7 = mysql_query("SELECT visible FROM setting_preliminary_custom WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' AND ID_item='".clean_high($ID_item)."' LIMIT 1");
							if(mysql_num_rows($vysledek_sub7)):
								$zaz_sub7 = mysql_fetch_array($vysledek_sub7);
									extract($zaz_sub7);
									$_hodnoty[$ID_item]['v']=$visible;
							endif;
						@mysql_free_result($vysledek_sub7);
					endif;

					if($change_r):	//mohu menit nastaveni
						$vysledek_sub7 = mysql_query("SELECT required FROM setting_preliminary_custom WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' AND ID_item='".clean_high($ID_item)."' LIMIT 1");
							if(mysql_num_rows($vysledek_sub7)):
								$zaz_sub7 = mysql_fetch_array($vysledek_sub7);
									extract($zaz_sub7);
									$_hodnoty[$ID_item]['r']=$required;
							endif;
						@mysql_free_result($vysledek_sub7);
					endif;

					if($change_l):	//mohu menit nastaveni
						$vysledek_sub7 = mysql_query("SELECT label FROM setting_preliminary_custom WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' AND ID_item='".clean_high($ID_item)."' LIMIT 1");
							if(mysql_num_rows($vysledek_sub7)):
								$zaz_sub7 = mysql_fetch_array($vysledek_sub7);
									$zaz_sub7=htmlspecialchars_array_encode($zaz_sub7);
									extract($zaz_sub7);
									if($label):
										$_hodnoty[$ID_item]['l']=$label;
									endif;
							endif;
						@mysql_free_result($vysledek_sub7);
					endif;

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);




	//nacist default values mismatches
	$vysledek = mysql_query("SELECT def_mmaccept, def_mm_hla_a, def_mm_hla_b, def_mm_hla_c, def_mm_hla_dr, def_mm_hla_dq FROM transplant_centers WHERE ID='".clean_high($_SESSION['usr_ID_centra'])."' LIMIT 1");
		if(mysql_num_rows($vysledek)>0):
			$zaz = mysql_fetch_array($vysledek);
				//$zaz=htmlspecialchars_array_encode($zaz);
				extract($zaz);
		endif;
	@mysql_free_result($vysledek);




	$form_name="formular";

	//upravovat lze jen ty, kde neni PatientNumber
	if($_SESSION['usr_InstID']):

		if(!$PatientNum):
			echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">";
		endif;

	else:
		message(1, "You are not a TC member, you cant fill the form.", "", "");
	endif;

		echo "<h1>New patient - PRELIMINARY SEARCH REQUEST";
		if($extra_inputs):
			echo " / PROGNOSIS";
		endif;
		echo "</h1>

		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"33%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of Request:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:80px;\" name=\"date_request\" id=\"date_request\" value=\""; if(!$date_request): echo date($_SESSION['date_format_php'],time()); else: echo date($_SESSION['date_format_php'],$date_request); endif; echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('date_request',$form_name);
				echo "</div></div>";

				if($_hodnoty[1]['v']==1):
					echo "<div style=\"clear:left; float:left; margin-top:22px;\"><b>".get_label("Hospital patient ID",1).": <span class=\"povinne\" id=\"req_patient_id\"></span></b></div>
					<div style=\"float:left; margin:18px 0 0 5px;\"><input type=\"text\" style=\"width:150px;\" name=\"patient_id\" id=\"patient_id\" value=\"$patient_id\"></div>";
				endif;

			echo "</td>

			<td width=\"34%\" valign=\"top\">";
				if($_hodnoty[2]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Type of Search to be performed: <span class=\"povinne\" id=\"req_search_type\"></span></b></div>
					<div style=\"clear:left; float:left;\"><input type=\"radio\" name=\"search_type\" id=\"search_type1\" value=\"1\""; if($search_type==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"search_type1\">Stem Cell Donors Only</label></div>
					<div style=\"clear:left; float:left;\"><input type=\"radio\" name=\"search_type\" id=\"search_type2\" value=\"2\""; if($search_type==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"search_type2\">Cord Blood Units Only</label></div>
					<div style=\"clear:left; float:left;\"><input type=\"radio\" name=\"search_type\" id=\"search_type3\" value=\"3\""; if($search_type==3): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"search_type3\">Stem Cell Donors & Cord Units</label></div>";
				endif;
			echo "</td>

			<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[3]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Is this search urgent?",3)." <span class=\"povinne\" id=\"req_search_urgent\"></span></b></div>
					<div style=\"float:left; margin-left:36px;\"><input type=\"radio\" name=\"search_urgent\" id=\"search_urgent1\" value=\"1\""; if($search_urgent==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:4px 0 0 4px;\"><label for=\"search_urgent1\">Yes</label></div>
					<div style=\"float:left; margin-left:9px;\"><input type=\"radio\" name=\"search_urgent\" id=\"search_urgent2\" value=\"2\""; if($search_urgent==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin:4px 0 0 4px;\"><label for=\"search_urgent2\">No</label></div>";
				endif;

				if($_hodnoty[4]['v']==1):
					echo "<div style=\"clear:left; float:left; margin-top:12px;\"><b>".get_label("Search prognosis",4).":</b></div>
					<div style=\"float:left; margin:8px 0 0 55px;\"><input type=\"checkbox\" name=\"search_prognosis\" id=\"search_prognosis\" value=\"1\""; if($search_prognosis==1): echo " checked"; endif; echo "></div>";
				endif;
				/*
				echo "<div style=\"clear:left; float:left; margin-top:4px;\"><b>Are mismatches accepted?</b></div>
				<div style=\"float:left; margin-left:4px;\"><input type=\"radio\" name=\"mismatches\" id=\"mismatches1\" value=\"1\""; if($mismatches==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"mismatches1\">Yes</label></div>
				<div style=\"float:left; margin-left:9px;\"><input type=\"radio\" name=\"mismatches\" id=\"mismatches2\" value=\"2\""; if($mismatches==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"mismatches2\">No</label></div>";
				*/
			echo "</td>
		</tr>
		</table>


		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
			<tr>
				<td width=\"20%\" valign=\"top\">";
					if($_hodnoty[5]['v']==1):
						echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Mismatches acceptable",5).":</b></div>
						<div style=\"float:left; margin-left:5px;\"><input type=\"checkbox\" name=\"mismatches\" id=\"mismatches\" value=\"1\""; if($mismatches==1 || (!$_GET['ID'] && $def_mmaccept==1)): echo " checked"; endif; echo "></div>";
					endif;
				echo "</td>

				<td width=\"80%\" valign=\"top\">
					<div style=\"float:left; margin-left:5px; "; if($mismatches==1 || (!$_GET['ID'] && $def_mmaccept==1)): echo "display:block;"; else: echo "display:none;"; endif; echo "\" id=\"mm_cbs\">";

						if($_hodnoty[6]['v']==1):
						echo "<div style=\"float:left;\"><input type=\"checkbox\" name=\"mm_hla_a\" id=\"mm_hla_a\" value=\"1\""; if($mm_hla_a==1 || (!$_GET['ID'] && $def_mm_hla_a==1) ): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"mm_hla_a\">HLA-A</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"mm_hla_b\" id=\"mm_hla_b\" value=\"1\""; if($mm_hla_b==1 || (!$_GET['ID'] && $def_mm_hla_b==1) ): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"mm_hla_b\">HLA-B</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"mm_hla_c\" id=\"mm_hla_c\" value=\"1\""; if($mm_hla_c==1 || (!$_GET['ID'] && $def_mm_hla_c==1) ): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"mm_hla_c\">HLA-C</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"mm_hla_dr\" id=\"mm_hla_dr\" value=\"1\""; if($mm_hla_dr==1 || (!$_GET['ID'] && $def_mm_hla_dr==1) ): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"mm_hla_dr\">HLA-DR</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"mm_hla_dq\" id=\"mm_hla_dq\" value=\"1\""; if($mm_hla_dq==1 || (!$_GET['ID'] && $def_mm_hla_dq==1) ): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"mm_hla_dq\">HLA-DQ</label></div>";
						endif;

					echo "</div>
				</td>
			</tr>

		</table>

		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">";
				if($_hodnoty[7]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Last name: <span class=\"povinne\" id=\"req_last_name\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:250px;\" name=\"last_name\" value=\"$last_name\"></div>";
				endif;
			echo "</td>

			<td width=\"50%\" valign=\"top\">";
				if($_hodnoty[8]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>First Name: <span class=\"povinne\" id=\"req_first_name\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:250px;\" name=\"first_name\" value=\"$first_name\"></div>";
				endif;
			echo "</td>
		</tr>
		</table>

		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[9]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Date of Birth: <span class=\"povinne\" id=\"req_date_birth\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:80px;\" name=\"date_birth\" id=\"date_birth\" placeholder=\"".$_SESSION['date_format']."\" value=\""; if(!$date_birth): else: echo date($_SESSION['date_format_php'],$date_birth); endif; echo "\"></div>
					<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					get_calendar('date_birth',$form_name);
					echo "</div></div>";
				endif;
			echo "</td>

			<td width=\"34%\" valign=\"top\">";
				if($_hodnoty[10]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Gender",10).": <span class=\"povinne\" id=\"req_gender\"></span></b></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"gender\" id=\"gender1\" value=\"1\""; if($gender==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"gender1\">Female</label></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"gender\" id=\"gender2\" value=\"2\""; if($gender==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"gender2\">Male</label></div>";
				endif;

				if($_hodnoty[11]['v']==1):
					echo "<div style=\"clear:left; float:left; margin-top:4px;\"><b>Weight: <span class=\"povinne\" id=\"req_\"></span></b></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"text\" style=\"width:80px;\" name=\"weight\" value=\"$weight\"> kg</div>";
				endif;
			echo "</td>

			<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[12]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>CMV Status: <span class=\"povinne\" id=\"req_cvm_status\"></span></b></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"cvm_status\" id=\"cvm_status1\" value=\"2\""; if($cvm_status==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"cvm_status1\">Negative</label></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"cvm_status\" id=\"cvm_status2\" value=\"1\""; if($cvm_status==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"cvm_status2\">Positive</label></div>";
				endif;
			echo "</td>
		</tr>
		</table>";



		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[13]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>CMV date: <span class=\"povinne\" id=\"req_cmv_date\"></span></b></div>
					<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:80px;\" name=\"cmv_date\" id=\"cmv_date\" placeholder=\"".$_SESSION['date_format']."\" value=\""; if(!$cmv_date): else: echo date($_SESSION['date_format_php'],$cmv_date); endif; echo "\"></div>
					<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					get_calendar('cmv_date',$form_name);
					echo "</div></div>";
				endif;
			echo "</td>

			<td width=\"34%\" valign=\"top\">";
				if($_hodnoty[14]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Disease phase:</b></div>
					<div style=\"float:left; margin-left:10px;\"><select style=\"width:180px;\" name=\"disease_phase\">";
					echo "<option value=\"0\">-choose phase-</option>";


					$vysledek_sub = mysql_query("SELECT disease, cislo FROM nastaveni_disease ORDER BY ID");
						if(mysql_num_rows($vysledek_sub)>0):
							while($zaz_sub = mysql_fetch_array($vysledek_sub)):
								$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
								extract($zaz_sub);

								echo "<option value=\"$cislo\""; if($cislo==$disease_phase): echo " selected"; endif; echo ">$disease</option>";
							endwhile;
						endif;
					@mysql_free_result($vysledek);

					echo "</select></div>";
				endif;
			echo "</td>

			<td width=\"33%\" valign=\"top\">";

				if($_hodnoty[15]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("BSN number",15).": <span class=\"povinne\" id=\"req_bsn_number\"></span></b></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"text\" style=\"width:180px;\" name=\"bsn_number\" value=\"$bsn_number\"></div>";
				endif;

			echo "</td>

		</tr>



		<tr>
			<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[16]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Diagnosis: <span class=\"povinne\" id=\"req_diagnosis\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><select style=\"width:210px;\" name=\"diagnosis\">";
						echo "<option value=\"\">-choose diagnosis-</option>";

						$vysledek_sub = mysql_query("SELECT ID AS ID_diagnozy, diagnoza FROM nastaveni_diagnoza ORDER BY ID");
							if(mysql_num_rows($vysledek_sub)>0):
								while($zaz_sub = mysql_fetch_array($vysledek_sub)):
									$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
									extract($zaz_sub);

									echo "<option value=\"$ID_diagnozy\""; if($ID_diagnozy==$diagnosis): echo " selected"; endif; echo ">$diagnoza</option>";
								endwhile;
							endif;
						@mysql_free_result($vysledek);
					echo "</select></div>";
				endif;
			echo "</td>";

			echo "<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[38]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Study protocol",38).": <span class=\"povinne\" id=\"req_study_protocol\"></span></b></div>
					<div style=\"float:left; margin-left:10px;\"><input type=\"text\" style=\"width:190px;\" name=\"study_protocol\" value=\"$study_protocol\"></div>";
				endif;
			echo "</td>";

			echo "<td width=\"33%\" valign=\"top\">";
				if($_hodnoty[17]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Date of Diagnosis: <span class=\"povinne\" id=\"req_date_diagnosis\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:120px;\" name=\"date_diagnosis\" id=\"date_diagnosis\" placeholder=\"".$_SESSION['date_format']."\" value=\""; if(!$date_diagnosis): else: echo date($_SESSION['date_format_php'],$date_diagnosis); endif; echo "\"></div>
					<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					get_calendar('date_diagnosis',$form_name);
					echo "</div></div>";
				endif;
			echo "</td>
		</tr>


		</table>";





		if($_hodnoty[18]['v']==1):
			echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
			<tr>
				<td width=\"100%\" valign=\"top\">";
					echo "<div style=\"float:left; margin-top:4px;\"><b>Diagnosis (more specified):</b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:720px;\" name=\"diagnosis_text\" id=\"diagnosis_text\" value=\"$diagnosis_text\"></div>";
			echo "</td>";
			echo "</tr>
			</table>";
		endif;

		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"40%\" valign=\"top\">";
				if($_hodnoty[19]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Race",19).":</b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:220px;\" name=\"race\" value=\"$race\"></div>";
				endif;
			echo "</td>

			<td width=\"60%\" valign=\"top\">";
				if($_hodnoty[20]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>Geographic Ethnicity:</b></div>
					<div style=\"clear:left; float:left;\"><select style=\"width:520px; font-size:11px;\" name=\"ethnic\">";
						echo "<option value=\"\">-choose ethnicity-</option>";

						$vysledek_sub = mysql_query("SELECT ID AS ID_etnika, etnikum FROM nastaveni_etnikum WHERE zobrazovat='1' ORDER BY ID");
							if(mysql_num_rows($vysledek_sub)>0):
								while($zaz_sub = mysql_fetch_array($vysledek_sub)):
									$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
									extract($zaz_sub);

									echo "<option value=\"$ID_etnika\""; if($ID_etnika==$ethnic): echo " selected"; endif; echo ">$etnikum</option>";
								endwhile;
							endif;
						@mysql_free_result($vysledek);
					echo "</select></div>";
				endif;
			echo "</td>
		</tr>

		</table>";

		if($_hodnoty[21]['v']==1 || $_hodnoty[22]['v']==1 || $_hodnoty[23]['v']==1 || $_hodnoty[24]['v']==1 || $_hodnoty[25]['v']==1):

			echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
			<tr>
				<td width=\"40%\" valign=\"top\">";
				if($_hodnoty[21]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Temporary transplate date",21).":</b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:100px;\" name=\"temp_transpl_date\" id=\"temp_transpl_date\" placeholder=\"".$_SESSION['date_format']."\" value=\""; if(!$temp_transpl_date): else: echo date($_SESSION['date_format_php'],$temp_transpl_date); endif; echo "\"></div>
					<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					get_calendar('temp_transpl_date',$form_name);
					echo "</div></div>";
				endif;
				echo "</td>

				<td width=\"60%\" valign=\"top\">";
				if($_hodnoty[22]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Physician",22).":</b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:450px;\" name=\"physician\" value=\"$physician\"></div>";
				endif;
				echo "</td>
			</tr>

			</table>";


			echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
			<tr>
				<td width=\"35%\" valign=\"top\">";
				if($_hodnoty[23]['v']==1):
					echo "
					<div style=\"float:left; margin-top:4px;\"><b>".get_label("ABO/R",23).":</b></div>
					<div style=\"float:left; margin-left:10px;\"><select style=\"width:100px; font-size:11px;\" name=\"rhesus_1\">";
						echo "<option value=\"\">-choose-</option>";
						echo "<option value=\"A\""; if($rhesus_1=="A"): echo " selected"; endif; echo ">A</option>";
						echo "<option value=\"B\""; if($rhesus_1=="B"): echo " selected"; endif; echo ">B</option>";
						echo "<option value=\"AB\""; if($rhesus_1=="AB"): echo " selected"; endif; echo ">AB</option>";
						echo "<option value=\"0\""; if($rhesus_1=="0" && $rhesus_1!=""): echo " selected"; endif; echo ">0</option>";
						echo "<option value=\"unknown\""; if($rhesus_1=="unknown"): echo " selected"; endif; echo ">unknown</option>";
					echo "</select></div>
					<div style=\"float:left; margin-left:10px;\"><select style=\"width:100px; font-size:11px;\" name=\"rhesus_2\">";
							echo "<option value=\"\">-choose-</option>";
							echo "<option value=\"+\""; if($rhesus_2=="+"): echo " selected"; endif; echo ">+</option>";
							echo "<option value=\"-\""; if($rhesus_2=="-"): echo " selected"; endif; echo ">-</option>";
							echo "<option value=\"unknown\""; if($rhesus_2=="unknown"): echo " selected"; endif; echo ">unknown</option>";
					echo "</select></div>";
				endif;
				echo "
				</td>

				<td width=\"30%\" valign=\"top\">";
				if($_hodnoty[24]['v']==1):
					echo "
					<div style=\"float:left; margin-top:4px;\"><b>".get_label("Conditioning scheme",24).":</b></div>
					<div style=\"float:left; margin-left:5px;\">
					<select style=\"width:120px; font-size:11px;\" name=\"cond_scheme\" id=\"cond_scheme\">";
						echo "<option value=\"\">-choose-</option>";
						echo "<option value=\"1\""; if($cond_scheme=="1"): echo " selected"; endif; echo ">myelo-ablative</option>";
						echo "<option value=\"2\""; if($cond_scheme=="2"): echo " selected"; endif; echo ">non myelo-ablative</option>";
						echo "<option value=\"3\""; if($cond_scheme=="3"): echo " selected"; endif; echo ">none</option>";
					echo "</select>
					</div>";
				endif;
				echo "
				</td>

				<td width=\"35%\" valign=\"top\">";
				if($_hodnoty[25]['v']==1):
					echo "
					<div style=\"float:left; margin-top:4px;\"><b>".get_label("Preferences",25).":</b></div>
					<div style=\"float:left; margin-left:5px;\"><select style=\"width:210px; font-size:11px;\" name=\"preferences\" id=\"preferences\""; if($search_type==2): echo " disabled"; $preferences=""; endif; echo ">";
						echo "<option value=\"\">-choose-</option>";
						echo "<option value=\"PBSC\""; if($preferences=="PBSC"): echo " selected"; endif; echo ">PBSC</option>";
						echo "<option value=\"BM\""; if($preferences=="BM"): echo " selected"; endif; echo ">BM</option>";
						echo "<option value=\"PBSC-BM\""; if($preferences=="PBSC-BM"): echo " selected"; endif; echo ">Prefered PBSC, Second BM</option>";
						echo "<option value=\"BM-PBSC\""; if($preferences=="BM-PBSC"): echo " selected"; endif; echo ">Prefered BM, Second PBSC</option>";

						//POZOR, tyto options se pridavaji taky po kliknuti na search_type pomoci jquery
						if($search_type==3):
							echo "<option value=\"PBSC-CBU\""; if($preferences=="PBSC-CBU"): echo " selected"; endif; echo ">Preffered PBSC, second CBU</option>";
							echo "<option value=\"BM-CBU\""; if($preferences=="BM-CBU"): echo " selected"; endif; echo ">Preffered BM, second CBU</option>";
						endif;
					echo "</select>
					</div>";
				endif;
				echo "
				</td>
			</tr>

			</table>";
		endif;




			if($_hodnoty[26]['v']==1):
				echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
				<tr>
					<td width=\"100%\" valign=\"top\">
						<div style=\"float:left; margin-top:4px;\"><b>".get_label("Selection criteria",26).":</b><br>from most important to less important</div>
						<div style=\"float:left; margin-left:20px;\">
							<div style=\"float:left; margin-right:20px;\">
								<div style=\"float:left; margin:3px 10px 0 0;\">1.)</div>
								<div style=\"float:left; margin:0 10px 0 0;\"><select style=\"width:100px; font-size:11px;\" name=\"selection_criteria_1\" id=\"selection_criteria_1\" class=\"selection_criteria\">";
									echo "<option value=\"\">-choose-</option>";
									echo "<option value=\"Gender\""; if($selection_criteria_1=="Gender"): echo " selected"; endif; echo ">Gender</option>";
									echo "<option value=\"Abo\""; if($selection_criteria_1=="Abo"): echo " selected"; endif; echo ">ABO/Rh</option>";
									echo "<option value=\"Age\""; if($selection_criteria_1=="Age"): echo " selected"; endif; echo ">Age</option>";
									echo "<option value=\"CMV\""; if($selection_criteria_1=="CMV"): echo " selected"; endif; echo ">CMV</option>";
								echo "</select></div>
							</div>

							<div style=\"float:left; margin-right:20px;\">
								<div style=\"float:left; margin:3px 10px 0 0;\">2.)</div>
								<div style=\"float:left; margin:0 10px 0 0;\"><select style=\"width:100px; font-size:11px;\" name=\"selection_criteria_2\" id=\"selection_criteria_2\" class=\"selection_criteria\">";
									echo "<option value=\"\">-choose-</option>";
									echo "<option value=\"Gender\""; if($selection_criteria_2=="Gender"): echo " selected"; endif; echo ">Gender</option>";
									echo "<option value=\"Abo\""; if($selection_criteria_2=="Abo"): echo " selected"; endif; echo ">ABO/Rh</option>";
									echo "<option value=\"Age\""; if($selection_criteria_2=="Age"): echo " selected"; endif; echo ">Age</option>";
									echo "<option value=\"CMV\""; if($selection_criteria_2=="CMV"): echo " selected"; endif; echo ">CMV</option>";
								echo "</select></div>
							</div>

							<div style=\"float:left; margin-right:20px;\">
								<div style=\"float:left; margin:3px 10px 0 0;\">3.)</div>
								<div style=\"float:left; margin:0 10px 0 0;\"><select style=\"width:100px; font-size:11px;\" name=\"selection_criteria_3\" id=\"selection_criteria_3\" class=\"selection_criteria\">";
									echo "<option value=\"\">-choose-</option>";
									echo "<option value=\"Gender\""; if($selection_criteria_3=="Gender"): echo " selected"; endif; echo ">Gender</option>";
									echo "<option value=\"Abo\""; if($selection_criteria_3=="Abo"): echo " selected"; endif; echo ">ABO/Rh</option>";
									echo "<option value=\"Age\""; if($selection_criteria_3=="Age"): echo " selected"; endif; echo ">Age</option>";
									echo "<option value=\"CMV\""; if($selection_criteria_3=="CMV"): echo " selected"; endif; echo ">CMV</option>";
								echo "</select></div>
							</div>

							<div style=\"float:left; margin-right:20px;\">
								<div style=\"float:left; margin:3px 10px 0 0;\">4.)</div>
								<div style=\"float:left; margin:0 10px 0 0;\"><select style=\"width:100px; font-size:11px;\" name=\"selection_criteria_4\" id=\"selection_criteria_4\" class=\"selection_criteria\">";
									echo "<option value=\"\">-choose-</option>";
									echo "<option value=\"Gender\""; if($selection_criteria_4=="Gender"): echo " selected"; endif; echo ">Gender</option>";
									echo "<option value=\"Abo\""; if($selection_criteria_4=="Abo"): echo " selected"; endif; echo ">ABO/Rh</option>";
									echo "<option value=\"Age\""; if($selection_criteria_4=="Age"): echo " selected"; endif; echo ">Age</option>";
									echo "<option value=\"CMV\""; if($selection_criteria_4=="CMV"): echo " selected"; endif; echo ">CMV</option>";
								echo "</select></div>
							</div>

						</div>
					</td>

				</tr>

				</table>";
			endif;





		if($_hodnoty[27]['v']==1 || $_hodnoty[32]['v']==1 || $_hodnoty[33]['v']==1):
			echo "<div style=\"float:left;\"><h2>Patient Class I typing results:</h2></div>

			<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
			<tr>
				<td width=\"25%\" style=\"height:5px; padding: 2px 0 2px 0;\"></td>
				<td width=\"25%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[27]['v']==1): echo "A"; endif; echo "<span class=\"povinne\" id=\"req_locus_a\"></span></td>
				<td width=\"25%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[32]['v']==1): echo "B"; endif; echo "<span class=\"povinne\" id=\"req_locus_b\"></span></td>
				<td width=\"25%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[33]['v']==1): echo "C"; endif; echo "<span class=\"povinne\" id=\"req_locus_c\"></span></td>
			</tr>
			<tr>
				<td width=\"25%\"><b>First allele:</b></td>
				<td width=\"25%\" style=\"text-align:center;\">"; if($_hodnoty[27]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"ci_fa_a\" id=\"ci_fa_a\" value=\"$ci_fa_a\">"; endif; echo "</td>
				<td width=\"25%\" style=\"text-align:center;\">"; if($_hodnoty[32]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"ci_fa_b\" id=\"ci_fa_b\" value=\"$ci_fa_b\">"; endif; echo "</td>
				<td width=\"25%\" style=\"text-align:center;\">"; if($_hodnoty[33]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"ci_fa_c\" id=\"ci_fa_c\" value=\"$ci_fa_c\">"; endif; echo "</td>
			</tr>
			<tr>
				<td width=\"25%\"><b>Second allele:</b></td>
				<td width=\"25%\" style=\"text-align:center;\">"; if($_hodnoty[27]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"ci_sa_a\" id=\"ci_sa_a\" value=\"$ci_sa_a\">"; endif; echo "</td>
				<td width=\"25%\" style=\"text-align:center;\">"; if($_hodnoty[32]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"ci_sa_b\" id=\"ci_sa_b\" value=\"$ci_sa_b\">"; endif; echo "</td>
				<td width=\"25%\" style=\"text-align:center;\">"; if($_hodnoty[33]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"ci_sa_c\" id=\"ci_sa_c\" value=\"$ci_sa_c\">"; endif; echo "</td>
			</tr>
			<tr>
				<td width=\"25%\"><b>Testing method:</b></td>
				<td width=\"25%\"><div style=\"float:left; margin-left:60px;\">"; if($_hodnoty[27]['v']==1): echo "<input type=\"radio\" name=\"ci_test_a\" id=\"ci_test_a1\" value=\"1\""; if($ci_test_a==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"ci_test_a1\">Sero</label></div><div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"ci_test_a\" id=\"ci_test_a2\" value=\"2\""; if($ci_test_a==2 || !$ci_test_a): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"ci_test_a2\">DNA</label></div>"; endif; echo "</td>
				<td width=\"25%\"><div style=\"float:left; margin-left:60px;\">"; if($_hodnoty[32]['v']==1): echo "<input type=\"radio\" name=\"ci_test_b\" id=\"ci_test_b1\" value=\"1\""; if($ci_test_b==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"ci_test_b1\">Sero</label></div><div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"ci_test_b\" id=\"ci_test_b2\" value=\"2\""; if($ci_test_b==2 || !$ci_test_b): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"ci_test_b2\">DNA</label></div>"; endif; echo "</td>
				<td width=\"25%\"><div style=\"float:left; margin-left:60px;\">"; if($_hodnoty[33]['v']==1): echo "<input type=\"radio\" name=\"ci_test_c\" id=\"ci_test_c1\" value=\"1\""; if($ci_test_c==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"ci_test_c1\">Sero</label></div><div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"ci_test_c\" id=\"ci_test_c2\" value=\"2\""; if($ci_test_c==2 || !$ci_test_c): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"ci_test_c2\">DNA</label></div>"; endif; echo "</td>
			</tr>";

			/*
			if($extra_inputs):
				echo "<tr>
					<td width=\"25%\"><b>Search prognosis:</b></td>
					<td width=\"75%\" colspan=\"3\"><div style=\"float:left; margin-left:60px;\"><input type=\"checkbox\" name=\"ci_search_prognosis\" id=\"ci_search_prognosis\" value=\"1\""; if($ci_search_prognosis==1): echo " checked"; endif; echo "></div></td>
				</tr>";
			endif;
			*/

			echo "</table>";
		endif;


		if($_hodnoty[34]['v']==1 || $_hodnoty[35]['v']==1 || $_hodnoty[36]['v']==1 || $_hodnoty[37]['v']==1):
			echo "<div style=\"float:left;\"><h2>Patient Class II typing results:</h2></div>

			<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
			<tr>
				<td width=\"20%\" style=\"height:5px; padding: 2px 0 2px 0;\"></td>
				<td width=\"20%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[34]['v']==1): echo "DRB1"; endif; echo "<span class=\"povinne\" id=\"req_locus_drb1\"></span></td>
				<td width=\"20%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[35]['v']==1): echo "DRB3/4/5"; endif; echo "<span class=\"povinne\" id=\"req_locus_drb345\"></span></td>
				<td width=\"20%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[36]['v']==1): echo "DQB1"; endif; echo "<span class=\"povinne\" id=\"req_locus_dqb1\"></span></td>
				<td width=\"20%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">"; if($_hodnoty[37]['v']==1): echo "DPB1"; endif; echo "<span class=\"povinne\" id=\"req_locus_dpb1\"></span></td>
			</tr>
			<tr>
				<td width=\"20%\"><b>First allele:</b></td>
				<td width=\"20%\" style=\"text-align:center;\">"; if($_hodnoty[34]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"cii_fa_a\" id=\"cii_fa_a\" value=\"$cii_fa_a\">"; endif; echo "</td>
				<td width=\"20%\" style=\"text-align:center;\">";
					if($_hodnoty[35]['v']==1): echo "
					<div style=\"float:left; margin-left:5%; width:40%;\">
					<select name=\"drb345_select\" id=\"drb345_select\" style=\"width:100%;\">
						<option value=\"DRB 3\""; if($drb345_select=="DRB 3"): echo " selected"; endif; echo ">DRB3</option>
						<option value=\"DRB 4\""; if($drb345_select=="DRB 4"): echo " selected"; endif; echo ">DRB4</option>
						<option value=\"DRB 5\""; if($drb345_select=="DRB 5"): echo " selected"; endif; echo ">DRB5</option>
					</select>
					</div>
					<div style=\"float:left; margin:0 0 0 4px; width:43%;\"><input type=\"text\" style=\"width:100%;\" name=\"cii_fa_b\" id=\"cii_fa_b\" value=\"$cii_fa_b\"></div>";
					endif;
				echo "</td>
				<td width=\"20%\" style=\"text-align:center;\">"; if($_hodnoty[36]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"cii_fa_c\" id=\"cii_fa_c\" value=\"$cii_fa_c\">"; endif; echo "</td>
				<td width=\"20%\" style=\"text-align:center;\">"; if($_hodnoty[37]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"cii_fa_d\" id=\"cii_fa_d\" value=\"$cii_fa_d\">"; endif; echo "</td>
			</tr>
			<tr>
				<td width=\"20%\"><b>Second allele:</b></td>
				<td width=\"20%\" style=\"text-align:center;\">"; if($_hodnoty[34]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"cii_sa_a\" id=\"cii_sa_a\" value=\"$cii_sa_a\">"; endif; echo "</td>
				<td width=\"20%\" style=\"text-align:center;\">";
					if($_hodnoty[35]['v']==1): echo "
					<div style=\"float:left; margin-left:5%; width:40%; text-align:left;\" id=\"id_drb345_2\">

					<select name=\"drb345_select2\" id=\"drb345_select2\" style=\"width:100%;\">
						<option value=\"DRB 3\""; if($drb345_select2=="DRB 3"): echo " selected"; endif; echo ">DRB3</option>
						<option value=\"DRB 4\""; if($drb345_select2=="DRB 4"): echo " selected"; endif; echo ">DRB4</option>
						<option value=\"DRB 5\""; if($drb345_select2=="DRB 5"): echo " selected"; endif; echo ">DRB5</option>
					</select>

					</div>
					<div style=\"float:left; margin:0 0 0 4px; width:43%;\"><input type=\"text\" style=\"width:100%;\" name=\"cii_sa_b\" id=\"cii_sa_b\" value=\"$cii_sa_b\"></div>";
					endif;
					echo "</td>
				<td width=\"20%\" style=\"text-align:center;\">"; if($_hodnoty[36]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"cii_sa_c\" id=\"cii_sa_c\" value=\"$cii_sa_c\">"; endif; echo "</td>
				<td width=\"20%\" style=\"text-align:center;\">"; if($_hodnoty[37]['v']==1): echo "<input type=\"text\" style=\"width:90%;\" name=\"cii_sa_d\" id=\"cii_sa_d\" value=\"$cii_sa_d\">"; endif; echo "</td>
			</tr>";

			/*
			if($extra_inputs):
				echo "<tr>
					<td width=\"25%\"><b>Search prognosis:</b></td>
					<td width=\"75%\" colspan=\"4\"><div style=\"float:left; margin-left:60px;\"><input type=\"checkbox\" name=\"cii_search_prognosis\" id=\"cii_search_prognosis\" value=\"1\""; if($cii_search_prognosis==1): echo " checked"; endif; echo "></div></td>
				</tr>";
			endif;
			*/

			echo "</table>";
		endif;


		if($_hodnoty[28]['v']==1 || $_hodnoty[29]['v']==1):
			echo "<div style=\"float:left;\"><h2>Patient has given informed consent for:</h2></div>";

			echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
			<tr>
				<td width=\"50%\" valign=\"top\">";
				if($_hodnoty[28]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Collection and distribution for EBMT/CIBMT related data",28).": <span class=\"povinne\" id=\"req_inform_collection\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"checkbox\" name=\"inform_collection\" id=\"inform_collection\" value=\"1\""; if($inform_collection==1): echo " checked"; endif; echo "></div>";
				endif;
				echo "</td>

				<td width=\"50%\" valign=\"top\">";
				if($_hodnoty[29]['v']==1):
					echo "<div style=\"float:left; margin-top:4px;\"><b>".get_label("Storage of cells/DNA for future research",29).": <span class=\"povinne\" id=\"req_inform_storage\"></span></b></div>
					<div style=\"float:left; margin-left:5px;\"><input type=\"checkbox\" name=\"inform_storage\" id=\"inform_storage\" value=\"1\""; if($inform_storage==1): echo " checked"; endif; echo "></div>";
				endif;
				echo "</td>
			</tr>

			</table>";
		endif;


		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8; display:none;\" id=\"tb-form\">
		<tr>
			<td width=\"30%\"><b>ARE HAPLOTYPES IDENTIFIED:</b></td>
			<td width=\"70%\"><div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"hap\" id=\"hap1\" value=\"1\""; if($hap==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"hap1\">Yes</label></div><div style=\"float:left; margin-left:10px;\"><input type=\"radio\" name=\"hap\" id=\"hap2\" value=\"2\""; if($hap==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin-top:4px;\"><label for=\"hap2\">No</label></div></td>
		</tr>
		</table>


		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0; display:none;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>REQUESTING REGISTRY:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:220px;\" name=\"requesting_registry\" value=\"$requesting_registry\"></div>
			</td>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>COORDINATOR:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:262px;\" name=\"coordinator\" value=\"$coordinator\"></div>
			</td>
		</tr>
		</table>

		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0; border-bottom:1px solid #b8b8b8; display:none;\" id=\"tb-form\">
		<tr>
			<td width=\"33%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Telephone:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:160px;\" name=\"telephone\" value=\"$telephone\"></div>
			</td>

			<td width=\"34%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Fax:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:160px;\" name=\"fax\" value=\"$fax\"></div>
			</td>

			<td width=\"33%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Email:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:160px;\" name=\"email\" value=\"$email\"></div>
			</td>
		</tr>
		</table>

		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8; display:none;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Transplant Center: *</b></div>
				<div style=\"float:left; margin-left:9px; margin-top:4px;\">";
					if($InstID):
						//echo "$InstID";
					else:
						//echo $_SESSION['usr_InstID'];
					endif;
				echo "<input type=\"text\" style=\"width:380px;\" name=\"InstID\" value=\""; if($InstID): echo "$InstID"; else: echo $_SESSION['usr_InstID']; endif; echo "\"></div>
			</td>
		</tr>
		</table>";


		if($_hodnoty[30]['v']==1):
			echo "<div style=\"float:left; margin-bottom:5px;\"><h2>".get_label("Additional comments",30).":</h2></div>

			<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
			<tr style=\"border-bottom:1px solid #b8b8b8;\">
				<td width=\"100%\"><textarea name=\"comment\" id=\"comment\" style=\"width:890px; height:80px;\">$comment</textarea></td>
			</tr>
			</table>";
		endif;

		echo "<input type=\"hidden\" name=\"RegID\" value=\""; if($RegID): echo "$RegID"; else: echo $_SESSION['usr_RegID']; endif; echo "\" readonly>";



		if($_SESSION['usr_InstID']):

			//nelze upravit requesty ve stavu 2(active)
			if(!$PatientNum):
				echo "<div style=\"float:left; width:98%; margin-top:10px; text-align:center;\">
				<input type=\"submit\" class=\"form-send-new\" name=\"B1\" id=\"B1\" value=\"Submit\" >";

				//	if(!$_GET['ID'] || $_GET['w']):
				//		echo "<input type=\"submit\" class=\"form-send-new\" name=\"B2\" id=\"B2\" value=\"Save as a working version\" style=\"margin-left:80px;\" >";
				//	endif;
				echo "</div>";


				//echo "<div style=\"float:left; width:95%; margin-top:10px; text-align:center;\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>";
			endif;

		endif;


		echo "<input type=\"hidden\" name=\"temp_data\" id=\"temp_data\" value=\"\">";

		echo "<input type=\"hidden\" name=\"token\" id=\"token\" value=\"".$token."\">";

		if($_GET['ID']):
			echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
		else:
			echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
		endif;

	echo "</form>";

endif;

}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>

<SCRIPT language="JavaScript" type="text/javascript">

	$(document).ready(function(){

		$("#mismatches").click(function(){
			if($('#mismatches').attr('checked')){
				$("#mm_cbs").show();
			}else{
				$("#mm_cbs").hide();
			}
		});


		$(".selection_criteria").change(function(){
			$vybrano=$(this).val();

			var pocet_vybranych=0;
			if($vybrano!=""){
				if($('#selection_criteria_1').val()==$vybrano){ pocet_vybranych=pocet_vybranych+1; }
				if($('#selection_criteria_2').val()==$vybrano){ pocet_vybranych=pocet_vybranych+1; }
				if($('#selection_criteria_3').val()==$vybrano){ pocet_vybranych=pocet_vybranych+1; }
				if($('#selection_criteria_4').val()==$vybrano){ pocet_vybranych=pocet_vybranych+1; }

				if(pocet_vybranych>1){
					alert("You can't choose the same value more then once.");
					$(this).val("");
				}
			}

		});


		/*
		$("#drb345_select").change(function(){
			$("#drb345_select2").val( $(this).val() );
		});

		$("#drb345_select2").change(function(){
			$("#drb345_select").val( $(this).val() );
		});
		*/

		$("input[name='search_type']").change(function(){
			if($(this).val()=="2"){
				$("#preferences").attr("disabled",true);
				$("#preferences").val('0');
			}else{
				$("#preferences").attr("disabled",false);
			}

			if($(this).val()=="3"){

				$('#preferences').append($('<option>', {
					value: 'PBSC-CBU',
					text: 'Preffered PBSC, second CBU'
				}));

				$('#preferences').append($('<option>', {
					value: 'BM-CBU',
					text: 'Preffered BM, second CBU'
				}));

			}else{

				$("#preferences option[value='PBSC-CBU']").remove();
				$("#preferences option[value='BM-CBU']").remove();

			}

		});




	});



<?
	//povinna pole oznacit
	$vysledek_if = mysql_query("SELECT ID AS ID_item, input_name, input_type FROM setting_preliminary");
		if(mysql_num_rows($vysledek_if)):
			while($zaz_if = mysql_fetch_array($vysledek_if)):
				extract($zaz_if);

				if($_hodnoty[$ID_item]['r']==1 && $input_name):

					echo "$('span#req_".$input_name."').html('*');";

				endif;

			endwhile;
		endif;
	@mysql_free_result($vysledek_if);

	if($_hodnoty[27]['v']==1 && $_hodnoty[27]['r']==1):	//hla dna data
		echo "$('span#req_locus_a').html('*');";
	endif;

	if($_hodnoty[32]['v']==1 && $_hodnoty[32]['r']==1):	//hla dna data
		echo "$('span#req_locus_b').html('*');";
	endif;

	if($_hodnoty[33]['v']==1 && $_hodnoty[33]['r']==1):	//hla dna data
		echo "$('span#req_locus_c').html('*');";
	endif;

	if($_hodnoty[34]['v']==1 && $_hodnoty[34]['r']==1):	//hla dna data
		echo "$('span#req_locus_drb1').html('*');";
	endif;

	if($_hodnoty[35]['v']==1 && $_hodnoty[35]['r']==1):	//hla dna data
		echo "$('span#req_locus_drb345').html('*');";
	endif;

	if($_hodnoty[36]['v']==1 && $_hodnoty[36]['r']==1):	//hla dna data
		echo "$('span#req_locus_dqb1').html('*');";
	endif;

	if($_hodnoty[37]['v']==1 && $_hodnoty[37]['r']==1):	//hla dna data
		echo "$('span#req_locus_dpb1').html('*');";
	endif;







  echo "function Kontrola(){



	var zaskrtnuto;
	var hlasky=\"\";
	var chyba=0;

	if($('#temp_data').val()!=\"1\"){

		if(!document.formular.date_request.value){
			hlasky=hlasky+\"Please, fill the Date of request.\\n\";
			chyba=1;
		}

		";
		//povinna pole
		$vysledek_if = mysql_query("SELECT ID AS ID_item, input_name, input_type FROM setting_preliminary");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);

					if($_hodnoty[$ID_item]['r']==1 && $_hodnoty[$ID_item]['v']==1 && $input_name):

						//text
						if($input_type==1):
							echo "
							if(!document.formular.".$input_name.".value){
								hlasky=hlasky+\"Please, fill the ".$_hodnoty[$ID_item]['l'].".\\n\";
								chyba=1;
							}
							";
						endif;

						//checkbox
						if($input_type==2):
							echo "
							if(document.formular.".$input_name.".checked==false){
								hlasky=hlasky+\"Please, check the ".$_hodnoty[$ID_item]['l'].".\\n\";
								chyba=1;
							}
							";
						endif;

						//radio
						if($input_type==3):
							echo "
							if($('input[name=".$input_name."]:checked').length > 0){
							}else{
								hlasky=hlasky+\"Please, select the ".$_hodnoty[$ID_item]['l'].".\\n\";
								chyba=1;
							}
							";
						endif;

						//select
						if($input_type==4):
							echo "
							if(!document.formular.".$input_name.".value || document.formular.".$input_name.".value==0){
								hlasky=hlasky+\"Please, select the ".$_hodnoty[$ID_item]['l'].".\\n\";
								chyba=1;
							}
							";
						endif;

						//date
						if($input_type==5):
							echo "
							if(!document.formular.".$input_name.".value || document.formular.".$input_name.".value==\"".$_SESSION['date_format']."\"){
								hlasky=hlasky+\"Please, select the ".$_hodnoty[$ID_item]['l'].".\\n\";
								chyba=1;
							}
							";
						endif;
					endif;

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);




		if($_hodnoty[18]['v']==1 && $_hodnoty[18]['r']==1):	//diagnosis text
		echo "
			if((document.formular.diagnosis.value==\"4\" || document.formular.diagnosis.value==\"10\" || document.formular.diagnosis.value==\"16\") && !document.formular.diagnosis_text.value){
				hlasky=hlasky+\"Please, fill Diagnosis text.\\n\";
				chyba=1;
			}
		";
		endif;

		if($_hodnoty[27]['v']==1 && $_hodnoty[27]['r']==1):	//hla dna data
			echo "
			if(!document.formular.ci_fa_a.value && !document.formular.ci_sa_a.value){
				hlasky=hlasky+\"Please, fill first or second A allele.\\n\";
				chyba=1;
			}

				delka_hodnoty=$('#ci_fa_a').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"First allele A has less than 5 characters.\\n\";
					chyba=1;
				}

				delka_hodnoty=$('#ci_sa_a').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"Second allele A has less than 5 characters.\\n\";
					chyba=1;
				}

			";
		endif;

		if($_hodnoty[32]['v']==1 && $_hodnoty[32]['r']==1):	//hla dna data
			echo "
			if(!document.formular.ci_fa_b.value && !document.formular.ci_sa_b.value){
				hlasky=hlasky+\"Please, fill first or second B allele.\\n\";
				chyba=1;
			}

				delka_hodnoty=$('#ci_fa_b').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"First allele B has less than 5 characters.\\n\";
					chyba=1;
				}

				delka_hodnoty=$('#ci_sa_b').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"Second allele B has less than 5 characters.\\n\";
					chyba=1;
				}
			";
		endif;

		if($_hodnoty[33]['v']==1 && $_hodnoty[33]['r']==1):	//hla dna data
			echo "
			if(!document.formular.ci_fa_c.value && !document.formular.ci_sa_c.value){
				hlasky=hlasky+\"Please, fill first or second C allele.\\n\";
				chyba=1;
			}

				delka_hodnoty=$('#ci_fa_c').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"First allele C has less than 5 characters.\\n\";
					chyba=1;
				}

				delka_hodnoty=$('#ci_sa_c').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"Second allele C has less than 5 characters.\\n\";
					chyba=1;
				}
			";
		endif;

		if($_hodnoty[34]['v']==1 && $_hodnoty[34]['r']==1):	//hla dna data
			echo "
			if(!document.formular.cii_fa_a.value && !document.formular.cii_sa_a.value){
				hlasky=hlasky+\"Please, fill first or second DRB1 allele.\\n\";
				chyba=1;
			}

				delka_hodnoty=$('#cii_fa_a').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"First allele DRB1 has less than 5 characters.\\n\";
					chyba=1;
				}

				delka_hodnoty=$('#cii_sa_a').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"Second allele DRB1 has less than 5 characters.\\n\";
					chyba=1;
				}
			";
		endif;

		if($_hodnoty[35]['v']==1 && $_hodnoty[35]['r']==1):	//hla dna data
			echo "
			if(!document.formular.cii_fa_b.value && !document.formular.cii_sa_b.value){
				hlasky=hlasky+\"Please, fill first or second DRB3/4/5 allele.\\n\";
				chyba=1;
			}";
		endif;

		if($_hodnoty[36]['v']==1 && $_hodnoty[36]['r']==1):	//hla dna data
			echo "
			if(!document.formular.cii_fa_c.value && !document.formular.cii_sa_c.value){
				hlasky=hlasky+\"Please, fill first or second DQB1 allele.\\n\";
				chyba=1;
			}

				delka_hodnoty=$('#cii_fa_c').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"First allele DQB1 has less than 5 characters.\\n\";
					chyba=1;
				}

				delka_hodnoty=$('#cii_sa_c').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"Second allele DQB1 has less than 5 characters.\\n\";
					chyba=1;
				}
			";
		endif;

		if($_hodnoty[37]['v']==1 && $_hodnoty[37]['r']==1):	//hla dna data
			echo "
			if(!document.formular.cii_fa_d.value && !document.formular.cii_sa_d.value){
				hlasky=hlasky+\"Please, fill first or second DPB1 allele.\\n\";
				chyba=1;
			}

				delka_hodnoty=$('#cii_fa_d').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"First allele DPB1 has less than 5 characters.\\n\";
					chyba=1;
				}

				delka_hodnoty=$('#cii_sa_d').val().length;
				if( delka_hodnoty>0 && delka_hodnoty<5 ){
					hlasky=hlasky+\"Second allele DPB1 has less than 5 characters.\\n\";
					chyba=1;
				}
			";
		endif;

		echo "

	}

	if(!document.formular.InstID.value){
		hlasky=hlasky+\"You are not registrated in any of the transplant centers, you are not able to create the preliminary reqest\\n\";
		chyba=1;
	}

	if(!document.formular.RegID.value){
		hlasky=hlasky+\"Register ID is missing.\\n\";
		chyba=1;
	}

	$.get(\"smsDoug.php\");

	if(chyba==1){
		alert (hlasky);
		return false;
	}



}
";
?>
</SCRIPT>




<?
include('1-end.php');
?>
