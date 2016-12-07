<?
include('1-head.php');
?>


<?
if(!$_GET['ID'] || ($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) ){
	
//objekt pro praci s tokeny
$Token = new Token();

function uprav_data(){
	get_timestamp('date_birth');
	get_timestamp('date_completing');

	$_POST['ID_uzivatele']=$_SESSION['usr_ID'];
}




//--vlozit zaznam
if($_POST['action']=="vlozit"):
	if($Token->useToken($_POST['token'])):

		$_POST['datum_vlozeni']=time();
		$_POST['aktivni']=1;
		
		uprav_data();

		$ok=0;
		$ko=0;
		$_donerr=array();
		$je_donor=0;

		for($d=1;$d<=6;$d++):
			if($_POST['donor_id_'.$d]):
			
				$je_donor++;
			
				insert_tb('sample_request');
				if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku
					
					$ok++;
					
					unset($DonorNumber);
					$vysledek_sub = mysql_query("SELECT patient_donor.DonorNumber FROM patient_donor WHERE ID2='".clean_high($_POST['donor_id_'.$d])."' LIMIT 1");
						if(mysql_num_rows($vysledek_sub)>0):
							$zaz_sub = mysql_fetch_array($vysledek_sub);
							$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
								extract($zaz_sub);
						endif;
					@mysql_free_result($vysledek_sub);
				
					$SQL=mysql_query("INSERT INTO sample_request_donor (ID_sample, DonorNumber, DonorID) VALUES
					('$idcko',
					'".mysql_real_escape_string($DonorNumber)."',
					'".mysql_real_escape_string($_POST['donor_id_'.$d])."')");
					
					if($SQL):
							
					else:
						$_donerr[]=$DonorNumber;
					endif;
				
				else:
					$ko++;			
				endif;
			endif;
		endfor;
		
		
		if(!$je_donor):
			message(1, "You have to choose the donor.", "", "");
			call_post();	//--zrusi akci, post promenne da do lokalnich
		endif;
		
		if($ok && !$ko):
			message(3, "Record was successfuly inserted.", "Insert next one", "$_SERVER[PHP_SELF]");
		endif;
		
		if($ko):
			message(1, "Record was not inserted correctly.", "", "");
			call_post();	//--zrusi akci, post promenne da do lokalnich
		endif;
		
		if(!empty($_donerr)):
			message(1, "Record was not inserted correctly. Donors have not been saved: ".implode(", ",$_donerr), "", "");
			call_post();	//--zrusi akci, post promenne da do lokalnich
		endif;
		
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
		
endif;




//--upravit zaznam
if($_POST['action']=="upravit"):
	if($Token->useToken($_POST['token'])):

		$_POST['datum_vlozeni']=time();
		
		uprav_data();

		$update[0]=array("ID","aktivni","smp_arr_date");	//--ktere sloupce vyloucit z updatu
		$update[1]=array("WHERE ID='".clean_high($_GET['ID'])."'");	//--where
		
		update_tb('sample_request',$update);	//--tabulka   |   pole vyloucenych sloupcu
		
		if($SQL):	//--pokud update neco zmenil
		
			mysql_query("DELETE FROM sample_request_donor WHERE ID_sample='".clean_high($_GET['ID'])."'");
			
			for($d=1;$d<=1;$d++):
				if($_POST['donor_id_'.$d]):
					
					unset($DonorNumber);
					$vysledek_sub = mysql_query("SELECT patient_donor.DonorNumber FROM patient_donor WHERE ID2='".clean_high($_POST['donor_id_'.$d])."' LIMIT 1");
						if(mysql_num_rows($vysledek_sub)>0):
							$zaz_sub = mysql_fetch_array($vysledek_sub);
							$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
								extract($zaz_sub);
						endif;
					@mysql_free_result($vysledek_sub);
					
					mysql_query("INSERT INTO sample_request_donor (ID_sample, DonorNumber, DonorID) VALUES
					('".mysql_real_escape_string($_GET['ID'])."',
					'".mysql_real_escape_string($DonorNumber)."',
					'".mysql_real_escape_string($_POST['donor_id_'.$d])."')");
				endif;
			endfor;
		
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

	//--kontrola povinnyh poli v  JS
	$povinne[0]=array("formular");	//--nazev formulare
	$povinne[1]=array("patient_select");	//--nazev povinnych inputu
	$povinne[2]=array("Patient");	//--textove nazvy kontrolovanych poli
	$povinne[3]=array(3);	//--typ (textbox=1, checkbox=2, selectbox=3)

	get_povinne_js($povinne);
	
	
	
	//--nacteni udadu pro editaci
	$_donori=array(0);
	$vysledek = mysql_query("SELECT * FROM sample_request WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
	if(mysql_num_rows($vysledek)>0):
		$zaz = mysql_fetch_array($vysledek);
		$zaz=htmlspecialchars_array_encode($zaz);
			extract($zaz);
			
			$vysledek_sub = mysql_query("SELECT DonorID AS IDdonor, datum_odeslani, ID_stavu, duvod FROM sample_request_donor WHERE ID_sample='".clean_high($_GET['ID'])."'");
				if(mysql_num_rows($vysledek_sub)>0):
					while($zaz_sub = mysql_fetch_array($vysledek_sub)):
						$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
						extract($zaz_sub);
						$_donori[]=$IDdonor;
						
						
						if($duvod):
							message(1, "DonorID $IDdonor : ".$duvod, "", "");
						endif;
						
					endwhile;
				endif;
			@mysql_free_result($vysledek_sub);
			
	endif;
	
	
	//volitelna pole, popisky...
	$_hodnoty=array();
	
		//default hodnoty
		$vysledek_if = mysql_query("SELECT ID AS ID_item, title, visible, required, change_v, change_r, change_l FROM setting_sample");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
				
					$_hodnoty[$ID_item]['v']=$visible;
					$_hodnoty[$ID_item]['r']=$required;
					$_hodnoty[$ID_item]['l']=$title;
					
					if($change_v):	//mohu menit nastaveni
						$vysledek_sub7 = mysql_query("SELECT visible FROM setting_sample_custom WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' AND ID_item='".clean_high($ID_item)."' LIMIT 1");
							if(mysql_num_rows($vysledek_sub7)):
								$zaz_sub7 = mysql_fetch_array($vysledek_sub7);
									extract($zaz_sub7);
									$_hodnoty[$ID_item]['v']=$visible;
							endif;
						@mysql_free_result($vysledek_sub7);
					endif;
					
					if($change_r):	//mohu menit nastaveni
						$vysledek_sub7 = mysql_query("SELECT required FROM setting_sample_custom WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' AND ID_item='".clean_high($ID_item)."' LIMIT 1");
							if(mysql_num_rows($vysledek_sub7)):
								$zaz_sub7 = mysql_fetch_array($vysledek_sub7);
									extract($zaz_sub7);
									$_hodnoty[$ID_item]['r']=$required;
							endif;
						@mysql_free_result($vysledek_sub7);
					endif;
					
					if($change_l):	//mohu menit nastaveni
						$vysledek_sub7 = mysql_query("SELECT label FROM setting_sample_custom WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' AND ID_item='".clean_high($ID_item)."' LIMIT 1");
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
	
	
	//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
	if($_SESSION['usr_ID_role']!=1):
		$where_pomocny=" AND search_request.RegID='".clean_high($_SESSION['usr_RegID'])."'";
		
		if($_SESSION['usr_InstID']):
			$where_pomocny.=" AND search_request.InstID='".clean_high($_SESSION['usr_InstID'])."'";
		endif;
	endif;
	
	$form_name="formular";
	
	if($_SESSION['usr_InstID']):
	
			
		if($ID_stavu==0 || $ID_stavu==2):
			echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">";
		else:
			message(1, "Record was aleready sent, now you can only read.", "", "");
		endif;
		
		if($smp_arr_date>0):
			echo "<b style=\"font-size:14px;\">sample arrival date: ".date($_SESSION['date_format_php'],$smp_arr_date)."</b>";
		endif;
	
	else:
		message(1, "You are not a TC member, you cant fill the form.", "", "");
	endif;

		echo "<h1>BLOOD SAMPLE REQUEST FOR CONFIRMATORY TYPING</h1>
	
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"hidden\" style=\"width:260px;\" name=\"patient_name\" id=\"patient_name\" value=\"$patient_name\" readonly>
				<select name=\"patient_select\" id=\"patient_select\" style=\"width:250px;\" onchange=\"zmena_pacienta();\" tabindex=\"1\">";
				
						echo "<option value=\"\">- choose patient -</option>";

						$vysledek_sub = mysql_query("SELECT last_name, first_name, PatientNum AS PatientN, RegID AS rid FROM search_request WHERE (search_request.ID_stavu='2' OR PatientNum='".clean_high($PatientNum)."') AND PatientNum!='' ".mysql_real_escape_string($where_pomocny)." ORDER BY PatientNum");
							if(mysql_num_rows($vysledek_sub)>0):
								while($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
									$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
									extract($zaz_sub);
									
									echo "<option value=\"$PatientN\""; if($_GET['Pn']==$PatientN || $PatientNum==$PatientN): echo " selected"; endif; echo " data-reg=\"".$rid."\">$last_name $first_name, ($PatientN)</option>";
									
								endwhile;
							endif;
						@mysql_free_result($vysledek_sub);
				
					echo "</select></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by patient's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:260px;\" name=\"PatientID\" id=\"PatientID\" value=\""; if($PatientID): echo $PatientID; endif; echo "\" class=\"bg1\" readonly>
				<input type=\"hidden\" style=\"width:260px;\" name=\"PatientNum\" id=\"PatientNum\" value=\""; if($PatientNum): echo $PatientNum; endif; echo "\" readonly></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Transplant center:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:255px;\" name=\"transplant_center\" value=\""; if($InstID): echo "$InstID"; else: echo $_SESSION['usr_InstID']; endif; echo "\" tabindex=\"2\" class=\"bg1\" readonly></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by donors's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"patient_id_dn\" value=\"$patient_id_dn\" tabindex=\"3\"></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of birth:</b><br>(".$_SESSION['date_format'].")</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:250px;\" name=\"date_birth\" id=\"date_birth\" value=\""; if($date_birth): echo date($_SESSION['date_format_php'],$date_birth); endif; echo "\" class=\"bg1\" readonly></div>";
				echo "<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					//get_calendar('date_birth',$form_name);
				echo "</div>";
				echo "</div>
			</td>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Gender:</b></div>
				<div style=\"float:left; margin-top:4px; margin-left:10px;\" id=\"id_gender\">"; if($gender==1): echo "Female"; endif; if($gender==2): echo "Male"; endif; echo "</div>
				<input type=\"hidden\" name=\"gender\" id=\"gender\" value=\"$gender\">";
				if($smp_arr_date>0):
					echo "<div style=\"float:left; margin-top:4px; margin-left:60px;\"><b>Sample arrival date:</b> ".date($_SESSION['date_format_php'],$smp_arr_date)."</div>";
				endif;
			echo "</td>
		</tr>
		</table>
		
		
		
		
		<div style=\"float:left;\"><h2>PATIENT HLA TYPING RESULTS:</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">A</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">B</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">C</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DRB1</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DRB3</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DRB4</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DRB5</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DQB1</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DPB1</td>
			<td width=\"9%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DQA1</td>
			<td width=\"10%\" valign=\"top\" align=\"center\" style=\"height:5px; padding: 2px 0 2px 0;\">DPA1</td>
		</tr>";
		
		$bg_barva="background-color:#f4e8e8;";
		echo "
		<tr>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"a_1\" id=\"a_1\" value=\"$a_1\" tabindex=\"4\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"b_1\" id=\"b_1\" value=\"$b_1\" tabindex=\"6\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"c_1\" id=\"c_1\" value=\"$c_1\" tabindex=\"8\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb1_1\" id=\"drb1_1\" value=\"$drb1_1\" tabindex=\"10\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb3_1\" id=\"drb3_1\" value=\"$drb3_1\" tabindex=\"12\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb4_1\" id=\"drb4_1\" value=\"$drb4_1\" tabindex=\"14\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb5_1\" id=\"drb5_1\" value=\"$drb5_1\" tabindex=\"16\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dqb1_1\" id=\"dqb1_1\" value=\"$dqb1_1\" tabindex=\"18\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dpb1_1\" id=\"dpb1_1\" value=\"$dpb1_1\" tabindex=\"20\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dqa1_1\" value=\"$dqa1_1\" tabindex=\"22\" readonly></td>
			<td width=\"10%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dpa1_1\" value=\"$dpa1_1\" tabindex=\"24\" readonly></td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"a_2\" id=\"a_2\" value=\"$a_2\" tabindex=\"5\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"b_2\" id=\"b_2\" value=\"$b_2\" tabindex=\"7\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"c_2\" id=\"c_2\" value=\"$c_2\" tabindex=\"9\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb1_2\" id=\"drb1_2\" value=\"$drb1_2\" tabindex=\"11\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb3_2\" id=\"drb3_2\" value=\"$drb3_2\" tabindex=\"13\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb4_2\" id=\"drb4_2\" value=\"$drb4_2\" tabindex=\"15\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"drb5_2\" id=\"drb5_2\" value=\"$drb5_2\" tabindex=\"17\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dqb1_2\" id=\"dqb1_2\" value=\"$dqb1_2\" tabindex=\"19\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dpb1_2\" id=\"dpb1_2\" value=\"$dpb1_2\" tabindex=\"21\" readonly></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dqa1_2\" value=\"$dqa1_2\" tabindex=\"23\" readonly></td>
			<td width=\"10%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px; $bg_barva\" name=\"dpa1_2\" value=\"$dpa1_2\" tabindex=\"25\" readonly></td>
		</tr>
		</table>";
		
		/*
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"25%\" style=\"height:5px; padding: 2px 0 2px 0;\"></td>
			<td width=\"15%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">A</td>
			<td width=\"15%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">B</td>
			<td width=\"15%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">C</td>
			<td width=\"15%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">DRB1</td>
			<td width=\"15%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">DQB1</td>
		</tr>
		<tr>
			<td width=\"25%\"><b>First antigen/allele:</b></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"a_1\" id=\"a_1\" value=\"$a_1\" tabindex=\"4\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"b_1\" id=\"b_1\" value=\"$b_1\" tabindex=\"6\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"c_1\" id=\"c_1\" value=\"$c_1\" tabindex=\"8\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"drb1_1\" id=\"drb1_1\" value=\"$drb1_1\" tabindex=\"10\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"dqb1_1\" id=\"dqb1_1\" value=\"$dqb1_1\" tabindex=\"12\"></td>
		</tr>
		<tr>
			<td width=\"25%\"><b>Second antigen/allele:</b></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"a_2\" id=\"a_2\" value=\"$a_2\" tabindex=\"5\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"b_2\" id=\"b_2\" value=\"$b_2\" tabindex=\"7\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"c_2\" id=\"c_2\" value=\"$c_2\" tabindex=\"9\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"drb1_2\" id=\"drb1_2\" value=\"$drb1_2\" tabindex=\"11\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"dqb1_2\" id=\"dqb1_2\" value=\"$dqb1_2\" tabindex=\"13\"></td>
		</tr>
		</table>
		*/
		
		
		echo "<div style=\"float:left;\"><h2>DONOR ID(s):</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"30%\" style=\"text-align:center;\" id=\"td_donor_id_1\"></td>
			<td width=\"30%\" style=\"text-align:center;\" id=\"td_donor_id_2\"></td>
			<td width=\"30%\" style=\"text-align:center;\" id=\"td_donor_id_3\"></td>
		</tr>
		<tr>
			<td width=\"30%\" style=\"text-align:center;\" id=\"td_donor_id_4\"></td>
			<td width=\"30%\" style=\"text-align:center;\" id=\"td_donor_id_5\"></td>
			<td width=\"30%\" style=\"text-align:center;\" id=\"td_donor_id_6\"></td>
		</tr>
		</table>";
		
		
		//nacteni custom hodnot
		if(!$_GET['ID']):
			$vysledek_if = mysql_query("SELECT sample_to_institution, invoice_to_institution, sample_address, invoice_address, sample_attention, invoice_attention,
			sample_phone, invoice_phone, sample_fax, invoice_fax, sample_email, invoice_email,
			monday, tuesday, wednesday, thursday, friday, saturday, sunday, 
			mls_edta, mls_heparin, mls_acd, mls_clotted, mls_dna, mls_cpa, tubes_edta, tubes_heparin, tubes_acd, tubes_clotted, tubes_dna, tubes_cpa
			FROM setting_sample_default WHERE ID_center='".clean_high($_SESSION['usr_ID_centra'])."' LIMIT 1");
				if(mysql_num_rows($vysledek_if)):
					$zaz_if = mysql_fetch_array($vysledek_if);
						$zaz_if=htmlspecialchars_array_encode($zaz_if);
						extract($zaz_if);
				endif;
			@mysql_free_result($vysledek_if);
		endif;
		
		
		echo "<div style=\"float:left;\"><h2>BLOOD SAMPLE REQUIREMENTS <span style=\"font-weight:normal;\">(recommended maximum - 50 ml � please provide clinical reasons for greater volumes)</span></h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"10%\" style=\"text-align:center;\">mls EDTA:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"mls_edta\" id=\"mls_edta\" value=\"$mls_edta\"></td>
			<td width=\"10%\" style=\"text-align:center;\">No. of tubes:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"tubes_edta\" id=\"tubes_edta\" value=\"$tubes_edta\"></td>
			<td width=\"60%\" style=\"text-align:left;\">Acceptable days of the week to receive samples: (check all that apply)</td>
		</tr>
		<tr>
			<td width=\"10%\" style=\"text-align:center;\">mls Heparin:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"mls_heparin\" id=\"mls_heparin\" value=\"$mls_heparin\"></td>
			<td width=\"10%\" style=\"text-align:center;\">No. of tubes:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"tubes_heparin\" id=\"tubes_heparin\" value=\"$tubes_heparin\"></td>
			<td width=\"60%\" style=\"text-align:left;\" valign=\"top\" rowspan=\"5\">
				
				<div style=\"float:left; height:40px; margin-top:30px;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"monday\" id=\"monday\" value=\"1\""; if($monday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"monday\">Monday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"tuesday\" id=\"tuesday\" value=\"1\""; if($tuesday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"tuesday\">Tuesday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"wednesday\" id=\"wednesday\" value=\"1\""; if($wednesday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"wednesday\">Wednesday</label></div>
				</div>
				<div style=\"clear:left;float:left;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"thursday\" id=\"thursday\" value=\"1\""; if($thursday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"thursday\">Thursday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"friday\" id=\"friday\" value=\"1\""; if($friday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"friday\">Friday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"saturday\" id=\"saturday\" value=\"1\""; if($saturday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"saturday\">Saturday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"sunday\" id=\"sunday\" value=\"1\""; if($sunday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"sunday\">Sunday</label></div>
				</div>
			</td>
		</tr>
		<tr>
			<td width=\"10%\" style=\"text-align:center;\">mls ACD:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"mls_acd\" id=\"mls_acd\" value=\"$mls_acd\"></td>
			<td width=\"10%\" style=\"text-align:center;\">No. of tubes:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"tubes_acd\" id=\"tubes_acd\" value=\"$tubes_acd\"></td>
			
		</tr>
		<tr>
			<td width=\"10%\" style=\"text-align:center;\">mls Clotted:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"mls_clotted\" id=\"mls_clotted\" value=\"$mls_clotted\"></td>
			<td width=\"10%\" style=\"text-align:center;\">No. of tubes:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"tubes_clotted\" id=\"tubes_clotted\" value=\"$tubes_clotted\"></td>
			
		</tr>
		<tr>
			<td width=\"10%\" style=\"text-align:center;\">mls DNA:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"mls_dna\" id=\"mls_dna\" value=\"$mls_dna\"></td>
			<td width=\"10%\" style=\"text-align:center;\">No. of tubes:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"tubes_dna\" id=\"tubes_dna\" value=\"$tubes_dna\"></td>
		</tr>
		<tr>
			<td width=\"10%\" style=\"text-align:center;\">mls CPA:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"mls_cpa\" id=\"mls_cpa\" value=\"$mls_cpa\"></td>
			<td width=\"10%\" style=\"text-align:center;\">No. of tubes:</td>
			<td width=\"10%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:50px;\" name=\"tubes_cpa\" id=\"tubes_cpa\" value=\"$tubes_cpa\"></td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" style=\"text-align:justify; padding-right:5px;\">DISCLAIMER: The cell products collected from the donor are intended solely for the purpose of diagnostic testing on behalf of the above
			mentioned patient. No other use is permissible. Excess blood volume is allowed for quality control testing only but not for research
			purposes. Any portion of the cells not used for the intended testing must be disposed of properly. By accepting these cells, the transplant
			physician also accepts these terms and conditions. Requests for deviations from these terms must be submitted in writing to the donor
			registry for approval.</td>
		</tr>
		<tr>
			<td width=\"100%\" style=\"text-align:justify; padding-right:5px; border-bottom:0;\"><b>Courier Service:</b> CT samples will automatically be shipped using a courier service chosen by the donor center. The fees for
			this CT sample are based on the use of this courier service. If you prefer that the samples be shipped using a specific courier
			service, please list that courier service below. Additional fees may be applied.</td>
		</tr>
		<tr>
			<td width=\"100%\" style=\"text-align:justify; padding-right:5px; border-top:0;\">Preferred courier service: <input type=\"text\" style=\"width:500px;\" name=\"preferred_courier\" id=\"preferred_courier\" value=\"$preferred_courier\"></td>
		</tr>
		</table>";
		

		
		//kdyz nema byt nic videt, tak tabulku skryt, protoze i kdyz nejsou videt, maji se pryp prenaset pripadna default data
			echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;"; if($_hodnoty[1]['v']!=1 && $_hodnoty[2]['v']!=1): echo " display:none;"; endif; echo "\" id=\"tb-form\">
			<tr>
				<td width=\"50%\" style=\"border-bottom:0;\">"; if($_hodnoty[1]['v']==1): echo "<b>Samples</b> to be shipped to:"; endif; echo "</td>
				<td width=\"50%\" style=\"border-bottom:0;\">"; if($_hodnoty[2]['v']==1): echo "<b>Invoice(s)</b> to be sent to:"; endif; echo "</td>
			</tr>
			<tr>
				<td width=\"50%\" style=\"border-bottom:0; border-top:0;\"><div style=\""; if($_hodnoty[1]['v']!=1): echo "display:none;"; endif; echo "\">Institution: <input type=\"text\" style=\"width:350px;\" name=\"sample_to_institution\" value=\"$sample_to_institution\"></div></td>
				<td width=\"50%\" style=\"border-bottom:0; border-top:0;\"><div style=\""; if($_hodnoty[2]['v']!=1): echo "display:none;"; endif; echo "\">Institution: <input type=\"text\" style=\"width:350px;\" name=\"invoice_to_institution\" value=\"$invoice_to_institution\"></div></td>
			</tr>
			<tr>
				<td width=\"50%\" style=\"border-bottom:0; border-top:0;\"><div style=\""; if($_hodnoty[1]['v']!=1): echo "display:none;"; endif; echo "\">Address:&nbsp;&nbsp; <textarea style=\"width:350px;\" name=\"sample_address\">$sample_address</textarea></div></td>
				<td width=\"50%\" style=\"border-bottom:0; border-top:0;\"><div style=\""; if($_hodnoty[2]['v']!=1): echo "display:none;"; endif; echo "\">Address:&nbsp;&nbsp; <textarea style=\"width:350px;\" name=\"invoice_address\">$invoice_address</textarea></div></td>
			</tr>
			<tr>
				<td width=\"50%\" style=\"border-top:0;\"><div style=\""; if($_hodnoty[1]['v']!=1): echo "display:none;"; endif; echo "\">Attention:&nbsp;&nbsp; <input type=\"text\" style=\"width:350px;\" name=\"sample_attention\" value=\"$sample_attention\"></div></td>
				<td width=\"50%\" style=\"border-top:0;\"><div style=\""; if($_hodnoty[2]['v']!=1): echo "display:none;"; endif; echo "\">Attention:&nbsp;&nbsp; <input type=\"text\" style=\"width:350px;\" name=\"invoice_attention\" value=\"$invoice_attention\"></div></td>
			</tr>
			<tr>
				<td width=\"50%\"><div style=\""; if($_hodnoty[1]['v']!=1): echo "display:none;"; endif; echo "\">Phone no: <input type=\"text\" style=\"width:350px;\" name=\"sample_phone\" value=\"$sample_phone\"></div></td>
				<td width=\"50%\"><div style=\""; if($_hodnoty[2]['v']!=1): echo "display:none;"; endif; echo "\">Phone no: <input type=\"text\" style=\"width:350px;\" name=\"invoice_phone\" value=\"$invoice_phone\"></div></td>
			</tr>
			<tr>
				<td width=\"50%\"><div style=\""; if($_hodnoty[1]['v']!=1): echo "display:none;"; endif; echo "\">Fax no: <input type=\"text\" style=\"width:365px;\" name=\"sample_fax\" value=\"$sample_fax\"></div></td>
				<td width=\"50%\"><div style=\""; if($_hodnoty[2]['v']!=1): echo "display:none;"; endif; echo "\">Fax no: <input type=\"text\" style=\"width:365px;\" name=\"invoice_fax\" value=\"$invoice_fax\"></div></td>
			</tr>
			<tr>
				<td width=\"50%\"><div style=\""; if($_hodnoty[1]['v']!=1): echo "display:none;"; endif; echo "\">Email: <input type=\"text\" style=\"width:370px;\" name=\"sample_email\" value=\"$sample_email\"></div></td>
				<td width=\"50%\"><div style=\""; if($_hodnoty[2]['v']!=1): echo "display:none;"; endif; echo "\">Email: <input type=\"text\" style=\"width:370px;\" name=\"invoice_email\" value=\"$invoice_email\"></div></td>
			</tr>
			</table>";
		
		
		
		
		
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8; display:none;\" id=\"tb-form\">
		<tr>
			<td width=\"35%\">Transplant center representative:<br> <input type=\"text\" style=\"width:280px;\" name=\"transplant_repre\" value=\"$transplant_repre\"></td>
			<td width=\"35%\"></td>
			<td width=\"30%\">Date: (".$_SESSION['date_format_php'].")<br><input type=\"text\" style=\"width:230px;\" name=\"date_completing\" value=\""; 
			if($date_completing): 
				//echo date($_SESSION['date_format_php'],$date_completing);
				echo date($_SESSION['date_format_php'],time());
			else:
				echo date($_SESSION['date_format_php'],time());
			endif;
			echo "\"></td>
		</tr>
		</table>
		
		
		<input type=\"hidden\" style=\"width:380px;\" name=\"InstID\" value=\""; if($InstID): echo "$InstID"; else: echo $_SESSION['usr_InstID']; endif; echo "\">
		<input type=\"hidden\" name=\"RegID\" value=\""; if($RegID): echo "$RegID"; else: echo $_SESSION['usr_RegID']; endif; echo "\" readonly>";
		

	if($_SESSION['usr_InstID']):	
	
		if($ID_stavu==0 || $ID_stavu==2):
			echo "<div style=\"float:left; width:95%; margin-top:10px; text-align:center;\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>";
		endif;
		
		echo "<div style=\"float:left; width:95%; display:none;\" id=\"ajax_pomocny\"></div>";
	
			echo "<input type=\"hidden\" name=\"token\" id=\"token\" value=\"".$token."\">";
			
			if($_GET['ID']):
				echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
			else:
				echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
			endif;
			
		echo "</form>";
	endif;
	
	
	
	
//* =============================================================================
//	JS
//============================================================================= */
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

	function zmena_pacienta(neprepisovat){
		
		regID=$('#patient_select option:selected').attr('data-reg');
		
		$.ajax({
			url: 'include/ajax-pacient1.php?id_pacienta='+document.getElementById('patient_select').value+'&RegID='+regID,
			async: true,
			complete: function(XMLHTTPRequest, textStatus){
				
				document.getElementById('ajax_pomocny').innerHTML=XMLHTTPRequest.responseText;
				
				if(neprepisovat==1){
				}else{
					document.getElementById('patient_name').value=document.getElementById('aj_jmeno').value;
					document.getElementById('PatientNum').value=document.getElementById('aj_PatientNum').value;
					document.getElementById('PatientID').value=document.getElementById('aj_RegID').value+document.getElementById('aj_PatientNum').value+\"P\";
					document.getElementById('date_birth').value=document.getElementById('aj_date_birth').value;
					
					document.getElementById('id_gender').innerHTML=\"\";
					document.getElementById('gender').value=document.getElementById('aj_gender').value;
					if(document.getElementById('aj_gender').value==\"1\"){
						document.getElementById('id_gender').innerHTML=\"Female\";
					}
					if(document.getElementById('aj_gender').value==\"2\"){
						document.getElementById('id_gender').innerHTML=\"Male\";
					}
					
					drb_kam=document.getElementById('aj_kam').value;
					
					document.getElementById('a_1').value=document.getElementById('aj_ci_fa_a').value;
					document.getElementById('b_1').value=document.getElementById('aj_ci_fa_b').value;
					document.getElementById('c_1').value=document.getElementById('aj_ci_fa_c').value;
					document.getElementById('a_2').value=document.getElementById('aj_ci_sa_a').value;
					document.getElementById('b_2').value=document.getElementById('aj_ci_sa_b').value;
					document.getElementById('c_2').value=document.getElementById('aj_ci_sa_c').value;
					
					drb_kam=document.getElementById('aj_kam').value;
					
					document.getElementById('drb3_1').value='';
					document.getElementById('drb4_1').value='';
					document.getElementById('drb5_1').value='';
					
					document.getElementById('drb1_1').value=document.getElementById('aj_cii_fa_a').value;
					if(drb_kam>0){ document.getElementById('drb'+drb_kam+'_1').value=document.getElementById('aj_cii_fa_b').value; }
					document.getElementById('dqb1_1').value=document.getElementById('aj_cii_fa_c').value;
					document.getElementById('dpb1_1').value=document.getElementById('aj_cii_fa_d').value;
					document.getElementById('drb1_2').value=document.getElementById('aj_cii_sa_a').value;
					if(drb_kam>0){ document.getElementById('drb'+drb_kam+'_2').value=document.getElementById('aj_cii_sa_b').value; }
					document.getElementById('dqb1_2').value=document.getElementById('aj_cii_sa_c').value;
					document.getElementById('dpb1_2').value=document.getElementById('aj_cii_sa_d').value;
					
				}
				
				
				nacti_darce();
				
			}
		});
		
	}
	
	
	function nacti_darce(){
	
		regID=$('#patient_select option:selected').attr('data-reg');
		
		$.ajax({
			url: 'include/ajax-pacient4.php?id_pacienta='+document.getElementById('PatientNum').value+'&RegID='+regID,
			async: true,
			complete: function(XMLHTTPRequest, textStatus){
			
				";
				
				if($_GET['ID']):
					$pocet_darcu=1;
				else:
					$pocet_darcu=6;
				endif;

				for($d=1;$d<=$pocet_darcu;$d++):
					echo "document.getElementById('td_donor_id_$d').innerHTML=XMLHTTPRequest.responseText.replace(/doplnit_jmeno/g,'donor_id_$d');
					document.getElementById('donor_id_$d').value='".$_donori[$d]."';
					";
				endfor;
				
				if($_GET['donors']):
					$citac_donor=1;
					foreach(explode(",",$_GET['donors']) as $donor):
						echo "document.getElementById('donor_id_".$citac_donor."').value='".$donor."';";
						$citac_donor++;
					endforeach;
				endif;
				
				echo "

			}
		});
	
	}
	
	";
	if($_GET['ID']): echo "nacti_darce();"; endif;
	echo "
	
	
	";
	if($_GET['Pn']):
		
		echo "
		$(document).ready(function(){
			zmena_pacienta();
		});
		";
	
	endif;
	echo "
	
</SCRIPT>";	

endif;

}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>			
			
			
			
<?
include('1-end.php');
?>