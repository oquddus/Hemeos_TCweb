<?
include('1-head.php');
include('1-config.php');
?>


<?
if(!$_GET['ID'] || ($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) ){
	
//objekt pro praci s tokeny
$Token = new Token();

$pocet_donoru=20;



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
		
		//vlozeni informaci
		for($d=1; $d<=$pocet_donoru; $d++):
		
			$input0="donor_id_".$d;
			
			if($_POST[$input0]):	//pokud je donor, jinak ne
			
				$je_donor++;
			
				insert_tb('typing_request');
		
				if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku
				
					$ok++;
		
					for($i=1; $i<=3; $i++):
					
						$input1="d".$d."_a_$i";
						$input2="d".$d."_b_$i";
						$input3="d".$d."_c_$i";
						$input4="d".$d."_drb1_$i";
						$input5="d".$d."_drb3_$i";
						$input6="d".$d."_drb4_$i";
						$input7="d".$d."_drb5_$i";
						$input8="d".$d."_dqb1_$i";
						$input9="d".$d."_dpb1_$i";
						$input10="d".$d."_dqa1_$i";
						$input11="d".$d."_dpa1_$i";
					
						if($_POST[$input0]):
						
							unset($DonorNumber);
							$vysledek_sub = mysql_query("SELECT patient_donor.DonorNumber FROM patient_donor WHERE ID2='".clean_high($_POST[$input0])."' LIMIT 1");
								if(mysql_num_rows($vysledek_sub)>0):
									$zaz_sub = mysql_fetch_array($vysledek_sub);
										$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
										extract($zaz_sub);
								endif;
							@mysql_free_result($vysledek_sub);
						
							$SQL=mysql_query("INSERT INTO typing_request_donor (ID_typing, DonorID, DonorNumber, resolution, a, b, c, drb1, drb3, drb4, drb5, dqb1, dpb1, dqa1, dpa1) VALUES
								('".clean_high($idcko)."',
								'".clean_basic($_POST[$input0])."',
								'".clean_basic($DonorNumber)."',
								'".clean_basic($i)."',
								'".clean_basic($_POST[$input1])."',
								'".clean_basic($_POST[$input2])."',
								'".clean_basic($_POST[$input3])."',
								'".clean_basic($_POST[$input4])."',
								'".clean_basic($_POST[$input5])."',
								'".clean_basic($_POST[$input6])."',
								'".clean_basic($_POST[$input7])."',
								'".clean_basic($_POST[$input8])."',
								'".clean_basic($_POST[$input9])."',
								'".clean_basic($_POST[$input10])."',
								'".clean_basic($_POST[$input11])."')");
								
							if($SQL):
							
							else:
								$_donerr[]=$DonorNumber;
							endif;
						endif;
					endfor;
					
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
	
		uprav_data();
		
		$_POST['datum_vlozeni']=time();

		$update[0]=array("ID","aktivni");	//--ktere sloupce vyloucit z updatu
		$update[1]=array("WHERE ID='".clean_high($_GET['ID'])."'");	//--where
		
		update_tb('typing_request',$update);	//--tabulka   |   pole vyloucenych sloupcu
		
		if($SQL):	//--pokud update neco zmenil

			//vlozeni informaci o donorech
			for($d=1; $d<=1; $d++):
			
				mysql_query("DELETE FROM typing_request_donor WHERE ID_typing='".clean_high($_GET['ID'])."' AND DonorNumber='".clean_high($_GET['DonorNum'])."'");
		
				for($i=1; $i<=3; $i++):
				
					//$input0="donor_id_1_old";
					$input0="donor_id_1";
					$input1="d".$d."_a_$i";
					$input2="d".$d."_b_$i";
					$input3="d".$d."_c_$i";
					$input4="d".$d."_drb1_$i";
					$input5="d".$d."_drb3_$i";
					$input6="d".$d."_drb4_$i";
					$input7="d".$d."_drb5_$i";
					$input8="d".$d."_dqb1_$i";
					$input9="d".$d."_dpb1_$i";
					$input10="d".$d."_dqa1_$i";
					$input11="d".$d."_dpa1_$i";
				
					if($_POST[$input0]):
					
						unset($DonorNumber);
						$vysledek_sub = mysql_query("SELECT patient_donor.DonorNumber FROM patient_donor WHERE ID2='".clean_high($_POST[$input0])."' LIMIT 1");
							if(mysql_num_rows($vysledek_sub)>0):
								$zaz_sub = mysql_fetch_array($vysledek_sub);
									$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
									extract($zaz_sub);
							endif;
						@mysql_free_result($vysledek_sub);
					
						mysql_query("INSERT INTO typing_request_donor (ID_typing, DonorID, DonorNumber, resolution, a, b, c, drb1, drb3, drb4, drb5, dqb1, dpb1, dqa1, dpa1) VALUES
							('".clean_high($_GET['ID'])."',
                            '".clean_basic($_POST[$input0])."',
							'".clean_basic($DonorNumber)."',
							'".clean_basic($i)."',
							'".clean_basic($_POST[$input1])."',
							'".clean_basic($_POST[$input2])."',
							'".clean_basic($_POST[$input3])."',
							'".clean_basic($_POST[$input4])."',
							'".clean_basic($_POST[$input5])."',
							'".clean_basic($_POST[$input6])."',
							'".clean_basic($_POST[$input7])."',
							'".clean_basic($_POST[$input8])."',
							'".clean_basic($_POST[$input9])."',
							'".clean_basic($_POST[$input10])."',
							'".clean_basic($_POST[$input11])."')");
							
					endif;
				endfor;
		
			endfor;
		
			message(1, "Item was successfuly edited.", "", "");
			
			presmeruj("donor-typing-request.php","ID=".$_GET['ID']."&DonorNum=".$DonorNumber."&msg=1&h=".$_GET['h']);
		else:
			message(1, "Item was not edited.", "", "");
		endif;
		
		unset($_POST['action']);
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
endif;

if($_GET['msg']==1):
	message(1, "Item was successfuly edited.", "", "");
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
	if($_GET['ID']):
		$vysledek = mysql_query("SELECT * FROM typing_request WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
			if(mysql_num_rows($vysledek)>0):
				$zaz = mysql_fetch_array($vysledek);
					$zaz=htmlspecialchars_array_encode($zaz);
					extract($zaz);
					

					$vysledek_sub= mysql_query("SELECT duvod, datum_odeslani, ID_stavu FROM typing_request_donor WHERE ID_typing='".clean_high($_GET['ID'])."' AND DonorNumber='".clean_high($_GET['DonorNum'])."'");
						if(mysql_num_rows($vysledek_sub)>0):
							$zaz_sub = mysql_fetch_array($vysledek_sub);
								$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
								extract($zaz_sub);

								if($duvod):
									message(1, $duvod, "", "");
								endif;
								
						endif;
					@mysql_free_result($vysledek_sub);
					
			endif;
		@mysql_free_result($vysledek);
	endif;
	
	
	
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
	
	else:
		message(1, "You are not a TC member, you cant fill the form.", "", "");
	endif;

		echo "<h1>REQUEST FOR FURTHER DNA BASED DONOR TYPING</h1>

		<div style=\"float:left;\"><h2>PATIENT DATA:</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"hidden\" style=\"width:260px;\" name=\"patient_name\" id=\"patient_name\" value=\"$patient_name\" readonly>
				<select name=\"patient_select\" id=\"patient_select\" style=\"width:250px;\" onchange=\"zmena_pacienta();\" tabindex=\"1\">";
				
						echo "<option value=\"\">- choose patient -</option>";
						
						$vysledek_sub = mysql_query("SELECT last_name, first_name, PatientNum AS PatientN, RegID AS rid FROM search_request WHERE (search_request.ID_stavu='2' OR PatientNum='".mysql_real_escape_string($PatientNum)."') AND PatientNum!='' $where_pomocny ORDER BY PatientNum");
							if(mysql_num_rows($vysledek_sub)>0):
								while($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
									$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
									extract($zaz_sub);
									
									echo "<option value=\"$PatientN\""; if(htmlspecialchars($_GET['Pn'])==$PatientN || $PatientNum==$PatientN): echo " selected"; endif; echo " data-reg=\"".$rid."\">$last_name $first_name, ($PatientN)</option>";
									
								endwhile;
							endif;
						@mysql_free_result($vysledek_sub);
				
					echo "</select>
				</div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by patient's registry)</div>
				<div style=\"float:left; margin-left:5px;\">
				<input type=\"text\" style=\"width:260px;\" name=\"PatientID\" id=\"PatientID\" value=\""; if($PatientID): echo $PatientID; endif; if(htmlspecialchars($_GET['Pn'])): echo htmlspecialchars($_GET['RegID']).htmlspecialchars($_GET['Pn'])."P"; endif; echo "\" class=\"bg1\" readonly>
				<input type=\"hidden\" style=\"width:260px;\" name=\"PatientNum\" id=\"PatientNum\" value=\""; if($PatientNum): echo $PatientNum; endif; if(htmlspecialchars($_GET['Pn'])): echo htmlspecialchars($_GET['Pn']); endif; echo "\" readonly></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient registry:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:250px;\" name=\"patient_registry\" id=\"patient_registry\" value=\"$patient_registry\" class=\"bg1\" readonly></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by donors's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"patient_id_dn\" id=\"patient_id_dn\" value=\"$patient_id_dn\" tabindex=\"2\"></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Diagnosis:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"diagnosis\" id=\"diagnosis\" value=\"$diagnosis\" class=\"bg1\" readonly></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of birth:</b><br>(".$_SESSION['date_format'].")</div>
				<div style=\"float:left; margin-left:5px;\"><div style=\"float:left;\"><input type=\"text\" style=\"width:250px;\" name=\"date_birth\" id=\"date_birth\" value=\""; if($date_birth): echo date($_SESSION['date_format_php'],$date_birth); endif; echo "\" class=\"bg1\" readonly></div>";
				echo "<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					//get_calendar('date_birth',$form_name);
				echo "</div>";
				echo "</div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Urgent:</b></div>
				<div style=\"float:left; margin-left:36px;\"><input type=\"radio\" name=\"search_urgent\" id=\"search_urgent1\" value=\"1\""; if($search_urgent==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:4px 0 0 4px;\"><label for=\"search_urgent1\">Yes</label></div>
				<div style=\"float:left; margin-left:9px;\"><input type=\"radio\" name=\"search_urgent\" id=\"search_urgent2\" value=\"2\""; if($search_urgent==2): echo " checked"; endif; echo "></div><div style=\"float:left; margin:4px 0 0 4px;\"><label for=\"search_urgent2\">No</label></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				
			</td>
		</tr>
		</table>
		
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>PATIENT HLA: (Typing methodology used: <input type=\"text\" style=\"width:250px;\" name=\"patient_hla\" value=\"$patient_hla\" tabindex=\"3\">)</h2></div>
		
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
		echo "<tr>
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
		</table>
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>PLEASE SPECIFY THE DNA TYPING REQUESTED:</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"height:5px;\">Donor ID#:</td>
			<td width=\"17%\" valign=\"top\" style=\"height:5px;\">Resolution</td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">A</td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">B</td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">C</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB1</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB3</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB4</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB5</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DQB1</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DPB1</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DQA1</td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DPA1</td>
		</tr>";
		
		
		$donori=array();
		$donori_id=array();
		if($_GET['ID']):
			//pri editaci nacist jednoho konkretniho donora, ktereho chci editovat. Ostatni napojene na tento typing request ne
			$citac_donoru=0;
			$sloupce=array("a","b","c","drb1","drb3","drb4","drb5","dqb1","dpb1","dqa1","dpa1");
			$vysledek_sub = mysql_query("SELECT typing_request_donor.* FROM typing_request_donor WHERE ID_typing='".clean_high($_GET['ID'])."' AND DonorNumber='".clean_high($_GET['DonorNum'])."' ORDER BY DonorNumber, resolution");
				if(mysql_num_rows($vysledek_sub)>0):
					while($zaz_sub = mysql_fetch_array($vysledek_sub)):
						$zaz_sub=htmlspecialchars_array_encode($zaz_sub);
						extract($zaz_sub);

						$ID_donora_editace=$DonorID;
						if($DonorID!=$DonorID_old):
							$citac_donoru++;
							$donori_id[$citac_donoru]=$DonorID;
							$DonorID_old=$DonorID;
						endif;
					
						foreach($sloupce as $klic=>$sloupec):
							if(${$sloupec}==1):
								$donori[$citac_donoru][$resolution][$sloupec]=1;
							endif;
						endforeach;
						reset($sloupce);

					endwhile;
				endif;
			@mysql_free_result($vysledek_sub);
		endif;

		//print_r($donori);
		
		//vypis trojbloku darce
		for($d=1; $d<=$pocet_donoru; $d++):

			echo "<tbody id=\"trojblok_$d\""; if($d>1 && $donori_id[$d]==""): echo "style=\"display:none;\""; endif; echo ">";
			$i=1;
			echo "<tr>
				<td width=\"20%\" valign=\"top\" id=\"td_donor_id_$d\" rowspan=\"3\"></td>
				<td width=\"17%\" valign=\"top\">Low</td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_a_$i\" value=\"1\""; if($donori[$d][$i]['a']==1): echo " checked"; endif; echo "></td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_b_$i\" value=\"1\""; if($donori[$d][$i]['b']==1): echo " checked"; endif; echo "></td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_c_$i\" value=\"1\""; if($donori[$d][$i]['c']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb1_$i\" value=\"1\""; if($donori[$d][$i]['drb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb3_$i\" value=\"1\""; if($donori[$d][$i]['drb3']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb4_$i\" value=\"1\""; if($donori[$d][$i]['drb4']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb5_$i\" value=\"1\""; if($donori[$d][$i]['drb5']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dqb1_$i\" value=\"1\""; if($donori[$d][$i]['dqb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dpb1_$i\" value=\"1\""; if($donori[$d][$i]['dpb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dqa1_$i\" value=\"1\""; if($donori[$d][$i]['dqa1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dpa1_$i\" value=\"1\""; if($donori[$d][$i]['dpa1']==1): echo " checked"; endif; echo "></td>
			</tr>";
			
			$i=2;
			echo "<tr>
				
				<td width=\"17%\" valign=\"top\">Intermediate</td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_a_$i\" value=\"1\""; if($donori[$d][$i]['a']==1): echo " checked"; endif; echo "></td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_b_$i\" value=\"1\""; if($donori[$d][$i]['b']==1): echo " checked"; endif; echo "></td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_c_$i\" value=\"1\""; if($donori[$d][$i]['c']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb1_$i\" value=\"1\""; if($donori[$d][$i]['drb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb3_$i\" value=\"1\""; if($donori[$d][$i]['drb3']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb4_$i\" value=\"1\""; if($donori[$d][$i]['drb4']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb5_$i\" value=\"1\""; if($donori[$d][$i]['drb5']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dqb1_$i\" value=\"1\""; if($donori[$d][$i]['dqb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dpb1_$i\" value=\"1\""; if($donori[$d][$i]['dpb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dqa1_$i\" value=\"1\""; if($donori[$d][$i]['dqa1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dpa1_$i\" value=\"1\""; if($donori[$d][$i]['dpa1']==1): echo " checked"; endif; echo "></td>
			</tr>";
			
			$i=3;
			echo "
			<tr style=\"border-bottom:1px solid #b8b8b8;\">
				
				<td width=\"17%\" valign=\"top\">High</td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_a_$i\" value=\"1\""; if($donori[$d][$i]['a']==1): echo " checked"; endif; echo "></td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_b_$i\" value=\"1\""; if($donori[$d][$i]['b']==1): echo " checked"; endif; echo "></td>
				<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_c_$i\" value=\"1\""; if($donori[$d][$i]['c']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb1_$i\" value=\"1\""; if($donori[$d][$i]['drb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb3_$i\" value=\"1\""; if($donori[$d][$i]['drb3']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb4_$i\" value=\"1\""; if($donori[$d][$i]['drb4']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_drb5_$i\" value=\"1\""; if($donori[$d][$i]['drb5']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dqb1_$i\" value=\"1\""; if($donori[$d][$i]['dqb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dpb1_$i\" value=\"1\""; if($donori[$d][$i]['dpb1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dqa1_$i\" value=\"1\""; if($donori[$d][$i]['dqa1']==1): echo " checked"; endif; echo "></td>
				<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"d".$d."_dpa1_$i\" value=\"1\""; if($donori[$d][$i]['dpa1']==1): echo " checked"; endif; echo "></td>
			</tr>";
			
			//sem se budou vypisovat hodnoty darce
			echo "
			<tr style=\"border-bottom:1px solid #b8b8b8;\">
				<td width=\"20%\" valign=\"top\">Donor's DNA:</td>
				<td width=\"80%\" valign=\"top\" colspan=\"12\" id=\"hodnoty_donor_id_".$d."\"></td>
			</tr>";
			
			echo "</tbody>";
		
		endfor;
		
		
		echo "</table>
		
		
		
		
		<div style=\"float:left; margin-top:20px; display:none;\"><h2>INVOICE ADDRESS: (to whom request for payment will be sent)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0; display:none;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Hospital:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:320px;\" name=\"ia_hospital\" value=\"$ia_hospital\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Contact name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"ia_contact_name\" value=\"$ia_contact_name\"></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\" rowspan=\"3\">
				<div style=\"float:left; margin-top:4px;\"><b>Address:</b></div>
				<div style=\"float:left; margin-left:5px;\"><textarea name=\"ia_address\" style=\"width:320px;\">$ia_address</textarea></div>
			</td>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Phone no:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:265px;\" name=\"ia_phone\" value=\"$ia_phone\"></div>
			</td>
		</tr>
		<tr>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Fax no:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"ia_fax\" value=\"$ia_fax\"></div>
			</td>
		</tr>
		<tr>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Email:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:285px;\" name=\"ia_email\" value=\"$ia_email\"></div>
			</td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; display:none;\" id=\"tb-form\">
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"30%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Person Completing Form:</b></div>
				<div style=\"clear:left; float:left; margin-left:5px;\"><input type=\"text\" style=\"width:270px;\" name=\"person_completing\" value=\"$person_completing\"></div>
			</td>
			<td width=\"30%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Signature:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:180px;\" name=\"signature\" value=\"$signature\"></div>
			</td>
			<td width=\"30%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date:</b><br>(".$_SESSION['date_format_php'].")</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:180px;\" name=\"date_completing\" value=\""; 
				if($date_completing): 
					//echo date($_SESSION['date_format_php'],$date_completing); 
					echo date($_SESSION['date_format_php'],time());	//i pri uprave dosadit datum posledniho odeslani
				else: 
					echo date($_SESSION['date_format_php'],time());
				endif; 
				echo "\"></div>
			</td>
		</tr>
		</table>
		
		
		<input type=\"hidden\" style=\"width:380px;\" name=\"InstID\" value=\""; if($InstID): echo "$InstID"; else: echo $_SESSION['usr_InstID']; endif; echo "\">
		<input type=\"hidden\" name=\"RegID\" value=\""; if(htmlspecialchars($RegID)): echo htmlspecialchars($RegID); else: echo htmlspecialchars($_SESSION['usr_RegID']); endif; echo "\" readonly>";
		
		
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
			
		if($ID_stavu==0 || $ID_stavu==2):
			echo "</form>";
		endif;
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
					document.getElementById('patient_registry').value=document.getElementById('aj_RegID').value;
					document.getElementById('diagnosis').value=document.getElementById('aj_diagnoza').value;

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
					
					
					$('#search_urgent1').attr('checked',false);
					$('#search_urgent2').attr('checked',false);
					
					if(document.getElementById('aj_search_urgent').value==\"1\"){
						$('#search_urgent1').attr('checked',true);
					}
					if(document.getElementById('aj_search_urgent').value==\"2\"){
						$('#search_urgent2').attr('checked',true);
					}
					
					
				}

				
				$.ajax({
					url: 'include/ajax-pacient2.php?id_pacienta='+document.getElementById('PatientNum').value+'&RegID='+regID+'&DonorID=".$donori_id[1]."',
					async: true,
					complete: function(XMLHTTPRequest, textStatus){
					
						";
						for($d=1;$d<=$pocet_donoru;$d++):
							echo "document.getElementById('td_donor_id_$d').innerHTML=XMLHTTPRequest.responseText.replace(/doplnit_jmeno/g,'donor_id_$d');
							document.getElementById('donor_id_$d').value='';
							";
						endfor;
						
						
						if(htmlspecialchars($_GET['donors'])):
							$citac_donor=1;
							foreach(explode(",",htmlspecialchars($_GET['donors'])) as $donor):
								echo "document.getElementById('donor_id_".$citac_donor."').value='".$donor."';";
								echo "document.getElementById('trojblok_".$citac_donor."').style.display=\"table-row-group\";";
								$citac_donor++;
							endforeach;
							
							//pri editaci dalsi radek uz neotvirat, edituju jen jednoho donora
							if(!$_GET['ID']):
								echo "document.getElementById('trojblok_".$citac_donor."').style.display=\"table-row-group\";";
							endif;
						endif;
						
						
						if($_GET['ID']):
							foreach($donori_id as $citac_donor=>$donor):
								echo "document.getElementById('donor_id_".$citac_donor."').value='".$donor."';";
							endforeach;
							$citac_donor++;
							//dalsi radek uz neotvirat, edituju jen jednoho donora
							//echo "document.getElementById('trojblok_".$citac_donor."').style.display=\"table-row-group\";";
						endif;
						
						echo "

					}
				});
				
				
				
			}
		});
		
	}
	
	
	";
	if(htmlspecialchars($_GET['Pn'])):
		
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
	

<SCRIPT language="JavaScript" type="text/javascript">	

	$(document).ready(function(){
		
		//po vyberu donora otevrit dalsi trojblok. Pokud vybiram jiz vybraneho donora, tak hlasku
		$('.selectbox_donora').live('change', function(){
			$idcko=$(this).attr('id');
			$brokenstring=$idcko.split('donor_id_');
			$new_id=(parseFloat($brokenstring[1])+1);
			
			$ID_donora=$(this).val();
			
			$do_tohoto_id=$new_id-2;
			
			for(t=1;t<=$do_tohoto_id;t++){
				if(document.getElementById('donor_id_'+t).value==document.getElementById('donor_id_'+$brokenstring[1]).value){
					alert('This donor is already selected.');
					document.getElementById('donor_id_'+$brokenstring[1]).value="";
				}
			}
			
			
			nacti_udaje_darce(''+$idcko+'', ''+$ID_donora+'');
			
			
			<?
			//pri editaci dalsi radek uz neotvirat, edituju jen jednoho donora
			if(!$_GET['ID']):
				echo "\$('#trojblok_'+\$new_id).show();";
			endif;
			?>
		});
		
		
		var jednou=0;
		function nacti_udaje_darce(idcko, ID_donora){
			//console.log(idcko+' '+ID_donora);
			regID=$('#patient_select option:selected').attr('data-reg');
			
			$.ajax({
				url: 'include/ajax-pacient3.php?id_pacienta='+document.getElementById('PatientNum').value+'&RegID='+regID+'&id_darce='+ID_donora,
				async: true,
				complete: function(XMLHTTPRequest, textStatus){
					
					document.getElementById('hodnoty_'+idcko).innerHTML=XMLHTTPRequest.responseText;
					
					<?
					if($_GET['ID']):
						echo "
						if(jednou==0){
							zmena_pacienta(1);
							jednou=1;
						}";
					endif;
					?>
				}
			});
		}
		
		
		<?
		if($_GET['ID']):
			echo "nacti_udaje_darce('donor_id_1', '".$ID_donora_editace."');";
		endif;
		
		if(htmlspecialchars($_GET['Pn'])):
			$citac=1;
			foreach(explode(",",htmlspecialchars($_GET['donors'])) as $id_donora):
				echo "nacti_udaje_darce('donor_id_".$citac."', '".$id_donora."');";
			$citac++;
			endforeach;
		endif;
		?>
	});
	
	
</SCRIPT>		
			
			
<?
include('1-end.php');
?>