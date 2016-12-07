<?
include('1-head.php');
?>


<?
function uprav_data(){
	get_timestamp('date_birth');
	get_timestamp('date_completing');

	
	$_POST['ID_uzivatele']=$_SESSION['usr_ID'];
}


//--vlozit zaznam
if($_POST['action']=="vlozit"):

	$_POST['datum_vlozeni']=time();
	$_POST['aktivni']=1;
	
	uprav_data();
	
	
	insert_tb('further_request');
	
	
	if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku
		message(3, "Item was successfuly inserted.", "Insert next one", "$_SERVER[PHP_SELF]");
	else:
		message(1, "Item was not inserted.", "", "");
		
		call_post();	//--zrusi akci, post promenne da do lokalnich
	endif;
		
endif;




//--upravit zaznam
if($_POST['action']=="upravit"):

	uprav_data();

	$update[0]=array("ID","datum_vlozeni","aktivni");	//--ktere sloupce vyloucit z updatu
	$update[1]=array("WHERE ID='$_GET[ID]'");	//--where
	
	update_tb('further_request',$update);	//--tabulka   |   pole vyloucenych sloupcu
	
	if($SQL):	//--pokud update neco zmenil
		message(1, "Item was successfuly edited.", "", "");
	else:
		message(1, "Item was not edited.", "", "");
	endif;
	
	unset($_POST['action']);
endif;








//-----------------------------------------formular
if(!$_POST['action']):

	//--kontrola povinnyh poli v  JS
	$povinne[0]=array("formular");	//--nazev formulare
	$povinne[1]=array("nazev");	//--nazev povinnych inputu
	$povinne[2]=array("Název firmy");	//--textove nazvy kontrolovanych poli
	$povinne[3]=array(1);	//--typ (textbox=1, checkbox=2, selectbox=3)

	get_povinne_js($povinne);
	
	
	
	//--nacteni udadu pro editaci
	$vysledek = mysql_query("SELECT * FROM further_request WHERE ID='".mysql_real_escape_string($_GET['ID'])."'");
		if(mysql_num_rows($vysledek)>0):
			$zaz = mysql_fetch_array($vysledek);
				$zaz=htmlspecialchars_array_encode($zaz);
				extract($zaz);
		endif;
	@mysql_free_result($vysledek);
	
	
	$form_name="formular";
	
	echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">

		<h1>REQUEST FOR FURTHER DNA BASED DONOR TYPING</h1>
	
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" valign=\"top\" style=\"border:0;\">
				<div style=\"float:left; margin-top:4px;\"><b>TO DONOR REGISTRY:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:350px;\" name=\"to_registry\" value=\"\"></div>
			</td>
		</tr>
		</table>
		
	
	
		<div style=\"float:left;\"><h2>PATIENT DATA:</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:260px;\" name=\"patient_name\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by patient's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"patient_id_pr\" value=\"\"></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient registry:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:250px;\" name=\"patient_registry\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by donors's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"patient_id_dn\" value=\"\"></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Diagnosis:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"diagnosis\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of birth:</b><br>(YYYY-MM-DD)</div>
				<div style=\"float:left; margin-left:5px;\"><div style=\"float:left;\"><input type=\"text\" style=\"width:250px;\" name=\"date_birth\" value=\"\"></div>";
				echo "<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
					get_calendar('date_birth',$form_name);
				echo "</div>";
				echo "</div>
			</td>
		</tr>
		</table>
		
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>PATIENT HLA: (Typing methodology used: <input type=\"text\" style=\"width:250px;\" name=\"patient_hla\" value=\"\">)</h2></div>
		
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
		</tr>
		<tr>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"a_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"b_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"c_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb1_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb3_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb4_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb5_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dqb1_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dpb1_1\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dqa1_1\" value=\"\"></td>
			<td width=\"10%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dpa1_1\" value=\"\"></td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"a_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"b_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"c_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb1_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb3_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb4_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"drb5_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dqb1_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dpb1_2\" value=\"\"></td>
			<td width=\"9%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dqa1_2\" value=\"\"></td>
			<td width=\"10%\" valign=\"top\" align=\"center\"><input type=\"text\" style=\"width:50px;\" name=\"dpa1_2\" value=\"\"></td>
		</tr>
		</table>
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>PLEASE SPECIFY THE DNA TYPING REQUESTED:</h2></div>
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border-right:0;\"></td>
			<td width=\"25%\" valign=\"top\" style=\"border-left:0; border-right:0;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"low_resolution\" id=\"low_resolution\" value=\"1\"></div>
				<div style=\"float:left; margin-left:5px; margin-top:4px;\"><label for=\"low_resolution\">Low Resolution</label></div>
			</td>
			<td width=\"55%\" valign=\"top\" style=\"border-left:0;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"inter_resolution\" id=\"inter_resolution\" value=\"1\"></div>
				<div style=\"float:left; margin-left:5px; margin-top:4px;\"><label for=\"inter_resolution\">Intermediate Resolution</label></div>
			</td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"height:5px;\">Donor ID#:</td>
			<td width=\"4%\" valign=\"top\" align=\"center\" style=\"height:5px;\">A</td>
			<td width=\"4%\" valign=\"top\" align=\"center\" style=\"height:5px;\">B</td>
			<td width=\"4%\" valign=\"top\" align=\"center\" style=\"height:5px;\">C</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB3</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB4</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB5</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DQB1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DPB1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DQA1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DPA1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
		</tr>
		<tr>
			<td width=\"20%\" valign=\"top\"><input type=\"text\" style=\"width:150px;\" name=\"donor_id_1\" value=\"\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_a_1\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_b_1\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_c_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb1_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb3_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb4_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb5_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqb1_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpb1_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqa1_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpa1_1\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
		</tr>
		<tr>
			<td width=\"20%\" valign=\"top\"><input type=\"text\" style=\"width:150px;\" name=\"donor_id_2\" value=\"\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_a_2\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_b_2\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_c_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb1_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb3_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb4_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb5_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqb1_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpb1_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqa1_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpa1_2\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
		</tr>
		<tr>
			<td width=\"20%\" valign=\"top\"><input type=\"text\" style=\"width:150px;\" name=\"donor_id_3\" value=\"\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_a_3\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_b_3\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_c_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb1_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb3_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb4_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb5_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqb1_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpb1_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqa1_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpa1_3\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"40%\" valign=\"top\" style=\"border-right:0;\"></td>
			<td width=\"70%\" valign=\"top\" style=\"border-left:0;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"high_resolution\" id=\"high_resolution\" value=\"1\"></div>
				<div style=\"float:left; margin-left:5px; margin-top:4px;\"><label for=\"high_resolution\">High Resolution</label></div>
			</td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"height:5px;\">Donor ID#:</td>
			<td width=\"4%\" valign=\"top\" align=\"center\" style=\"height:5px;\">A</td>
			<td width=\"4%\" valign=\"top\" align=\"center\" style=\"height:5px;\">B</td>
			<td width=\"4%\" valign=\"top\" align=\"center\" style=\"height:5px;\">C</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB3</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB4</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DRB5</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DQB1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DPB1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DQA1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\" style=\"height:5px;\">DPA1</td>
			<td width=\"3%\" valign=\"top\" style=\"height:5px;\"></td>
		</tr>
		<tr>
			<td width=\"20%\" valign=\"top\"><input type=\"text\" style=\"width:150px;\" name=\"donor_id_4\" value=\"\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_a_4\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_b_4\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_c_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb1_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb3_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb4_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb5_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqb1_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpb1_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqa1_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpa1_4\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
		</tr>
		<tr>
			<td width=\"20%\" valign=\"top\"><input type=\"text\" style=\"width:150px;\" name=\"donor_id_5\" value=\"\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_a_5\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_b_5\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_c_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb1_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb3_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb4_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb5_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqb1_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpb1_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqa1_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpa1_5\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"20%\" valign=\"top\"><input type=\"text\" style=\"width:150px;\" name=\"donor_id_6\" value=\"\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_a_6\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_b_6\" value=\"1\"></td>
			<td width=\"4%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_c_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb1_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb3_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb4_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_drb5_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqb1_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpb1_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"5%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dqa1_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
			<td width=\"6%\" valign=\"top\" align=\"center\"><input type=\"checkbox\" name=\"li_dpa1_6\" value=\"1\"></td>
			<td width=\"3%\" valign=\"top\"></td>
		</tr>
		</table>
		
		
		<div style=\"float:left;\"><h2>REQUESTING CENTER: (to whom report will be sent)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Hospital:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:320px;\" name=\"rc_hospital\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Contact name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"rc_contact_name\" value=\"\"></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\" rowspan=\"3\">
				<div style=\"float:left; margin-top:4px;\"><b>Address:</b></div>
				<div style=\"float:left; margin-left:5px;\"><textarea name=\"rc_address\" style=\"width:320px;\"></textarea></div>
			</td>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Phone no:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:265px;\" name=\"rc_phone\" value=\"\"></div>
			</td>
		</tr>
		<tr>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Fax no:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"rc_fax\" value=\"\"></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Email:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:285px;\" name=\"rc_email\" value=\"\"></div>
			</td>
		</tr>
		</table>
		
		
		
		<div style=\"float:left;\"><h2>INVOICE ADDRESS: (to whom request for payment will be sent)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Hospital:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:320px;\" name=\"ia_hospital\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Contact name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"ia_contact_name\" value=\"\"></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\" rowspan=\"3\">
				<div style=\"float:left; margin-top:4px;\"><b>Address:</b></div>
				<div style=\"float:left; margin-left:5px;\"><textarea name=\"ia_address\" style=\"width:320px;\"></textarea></div>
			</td>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Phone no:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:265px;\" name=\"ia_phone\" value=\"\"></div>
			</td>
		</tr>
		<tr>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Fax no:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"ia_fax\" value=\"\"></div>
			</td>
		</tr>
		<tr>

			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Email:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:285px;\" name=\"ia_email\" value=\"\"></div>
			</td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"30%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Person Completing Form:</b></div>
				<div style=\"clear:left; float:left; margin-left:5px;\"><input type=\"text\" style=\"width:270px;\" name=\"person_completing\" value=\"\"></div>
			</td>
			<td width=\"30%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Signature:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:180px;\" name=\"signature\" value=\"\"></div>
			</td>
			<td width=\"30%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date:</b><br>(YYYY-MM-DD)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:180px;\" name=\"date_completing\" value=\"".date("Y-m-d",time())."\"></div>
			</td>
		</tr>
		</table>
		
		

		
		<div style=\"float:left; width:95%; margin-top:10px; text-align:center;\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>
		";
		

		if($_GET['ID']):
			echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
		else:
			echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
		endif;
		
	echo "</form>";

endif;
?>			
			
			
			
<?
include('1-end.php');
?>