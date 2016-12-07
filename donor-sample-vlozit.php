<?
include('1-head.php');
?>


<?
//--vlozit zaznam
if($_POST['action']=="vlozit"):

	$_POST['datum_vlozeni']=time();

	insert_tb('requests');
	
	
	if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku
		message(3, "Item was successfuly inserted.", "Insert next one", "$_SERVER[PHP_SELF]");
	else:
		message(1, "Item was not inserted.", "", "");
		
		call_post();	//--zrusi akci, post promenne da do lokalnich
	endif;
		
endif;




//--upravit zaznam
if($_POST['action']=="upravit"):


	$update[0]=array("ID","datum_vlozeni");	//--ktere sloupce vyloucit z updatu
	$update[1]=array("WHERE ID='$_GET[ID]'");	//--where
	
	update_tb('firmy',$update);	//--tabulka   |   pole vyloucenych sloupcu
	
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
	$povinne[2]=array("N�zev firmy");	//--textove nazvy kontrolovanych poli
	$povinne[3]=array(1);	//--typ (textbox=1, checkbox=2, selectbox=3)

	get_povinne_js($povinne);
	
	
	
	//--nacteni udadu pro editaci
	$vysledek = mysql_query("SELECT * FROM firmy WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");

	if(mysql_num_rows($vysledek)>0):

			$zaz = mysql_fetch_array($vysledek);

				extract($zaz);
	endif;
	
	
	$form_name="formular";
	
	echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">

		<h1>BLOOD SAMPLE REQUEST FOR CONFIRMATORY TYPING</h1>
	
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"last_name\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by patient's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"patient_id_pr\" value=\"\"></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Transplant center:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:255px;\" name=\"transplant_center\" value=\"\"></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by donors's registry)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:240px;\" name=\"patient_id_dn\" value=\"\"></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of birth:</b><br>(YYYY-MM-DD)</div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:280px;\" name=\"date_birth\" value=\"\"></div>
			</td>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Gender:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"radio\" name=\"gender\" id=\"gender1\" value=\"F\"></div><div style=\"float:left; margin-top:4px;\"><label for=\"gender1\">F</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"radio\" name=\"gender\" id=\"gender2\" value=\"M\"></div><div style=\"float:left; margin-top:4px;\"><label for=\"gender2\">M</label></div>
			</td>
		</tr>
		</table>
		
		
		
		
		<div style=\"float:left;\"><h2>PATIENT HLA TYPING RESULTS:</h2></div>
		
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
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_fa_a\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_fa_b\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_fa_c\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_fa_drb1\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_fa_dqb1\" value=\"\"></td>
		</tr>
		<tr>
			<td width=\"25%\"><b>Second antigen/allele:</b></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_sa_a\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_sa_b\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_sa_c\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_sa_drb1\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"phla_sa_dqb1\" value=\"\"></td>
		</tr>
		</table>
		
		
		<div style=\"float:left;\"><h2>DONOR ID(s):</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"30%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:270px;\" name=\"donor_id_1\" value=\"\"></td>
			<td width=\"30%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:270px;\" name=\"donor_id_2\" value=\"\"></td>
			<td width=\"30%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:270px;\" name=\"donor_id_3\" value=\"\"></td>
		</tr>
		<tr>
			<td width=\"30%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:270px;\" name=\"donor_id_4\" value=\"\"></td>
			<td width=\"30%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:270px;\" name=\"donor_id_5\" value=\"\"></td>
			<td width=\"30%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:270px;\" name=\"donor_id_6\" value=\"\"></td>
		</tr>
		</table>
		
		
		<div style=\"float:left;\"><h2>BLOOD SAMPLE REQUIREMENTS <span style=\"font-weight:normal;\">(recommended maximum - 50 ml � please provide clinical reasons for greater volumes)</span></h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"mls_edta\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\">MLS EDTA</td>
			<td width=\"70%\" style=\"text-align:left;\">Acceptable days of the week to receive samples: (check all that apply)</td>
		</tr>
		<tr>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"mls_heparin\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\">mls Heparin</td>
			<td width=\"70%\" style=\"text-align:left;\" valign=\"top\" rowspan=\"4\">
				
				<div style=\"float:left; height:40px; margin-top:30px;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"monday\" id=\"monday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"monday\">Monday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"tuesday\" id=\"tuesday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"tuesday\">Tuesday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"wednesday\" id=\"wednesday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"wednesday\">Wednesday</label></div>
				</div>
				<div style=\"clear:left;float:left;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"thursday\" id=\"thursday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"thursday\">Thursday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"friday\" id=\"friday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"friday\">Friday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"saturday\" id=\"saturday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"saturday\">Saturday</label></div>
				<div style=\"float:left; margin-left:15px;\"><input type=\"checkbox\" name=\"sunday\" id=\"sunday\" value=\"1\"></div><div style=\"float:left; margin-left:3px; margin-top:4px;\"><label for=\"sunday\">Sunday</label></div>
				</div>
			</td>
		</tr>
		<tr>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"mls_acd\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\">mls ACD</td>
			
		</tr>
		<tr>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"mls_clotted\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\">mls Clotted</td>
			
		</tr>
		<tr>
			<td width=\"15%\" style=\"text-align:center;\"><input type=\"text\" style=\"width:100px;\" name=\"mls\" value=\"\"></td>
			<td width=\"15%\" style=\"text-align:center;\">mls <input type=\"text\" style=\"width:90px;\" name=\"mls_ceho\" value=\"\"></td>
			
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
			<td width=\"100%\" style=\"text-align:justify; padding-right:5px; border-top:0;\">Preferred courier service: <input type=\"text\" style=\"width:500px;\" name=\"preferred_courier\" value=\"\"></td>
		</tr>
		</table>
		
		
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" style=\"border-bottom:0;\"><b>Samples</b> to be shipped to:</td>
			<td width=\"50%\" style=\"border-bottom:0;\"><b>Invoice(s)</b> to be sent to:</td>
		</tr>
		<tr>
			<td width=\"50%\" style=\"border-bottom:0; border-top:0;\">Institution: <input type=\"text\" style=\"width:350px;\" name=\"sample_to_institution\" value=\"\"></td>
			<td width=\"50%\" style=\"border-bottom:0; border-top:0;\">Institution: <input type=\"text\" style=\"width:350px;\" name=\"invoice_to_institution\" value=\"\"></td>
		</tr>
		<tr>
			<td width=\"50%\" style=\"border-bottom:0; border-top:0;\">Address:&nbsp;&nbsp; <textarea style=\"width:350px;\" name=\"sample_address\"></textarea></td>
			<td width=\"50%\" style=\"border-bottom:0; border-top:0;\">Address:&nbsp;&nbsp; <textarea style=\"width:350px;\" name=\"invoice_address\"></textarea></td>
		</tr>
		<tr>
			<td width=\"50%\" style=\"border-top:0;\">Attention:&nbsp;&nbsp; <input type=\"text\" style=\"width:350px;\" name=\"sample_attention\" value=\"\"></td>
			<td width=\"50%\" style=\"border-top:0;\">Attention:&nbsp;&nbsp; <input type=\"text\" style=\"width:350px;\" name=\"invoice_attention\" value=\"\"></td>
		</tr>
		<tr>
			<td width=\"50%\">Phone no: <input type=\"text\" style=\"width:350px;\" name=\"sample_phone\" value=\"\"></td>
			<td width=\"50%\">Phone no: <input type=\"text\" style=\"width:350px;\" name=\"invoice_phone\" value=\"\"></td>
		</tr>
		<tr>
			<td width=\"50%\">Fax no: <input type=\"text\" style=\"width:365px;\" name=\"sample_fax\" value=\"\"></td>
			<td width=\"50%\">Fax no: <input type=\"text\" style=\"width:365px;\" name=\"invoice_fax\" value=\"\"></td>
		</tr>
		<tr>
			<td width=\"50%\">Email: <input type=\"text\" style=\"width:370px;\" name=\"sample_email\" value=\"\"></td>
			<td width=\"50%\">Email: <input type=\"text\" style=\"width:370px;\" name=\"invoice_email\" value=\"\"></td>
		</tr>
		</table>
		
		
		
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
		<tr>
			<td width=\"35%\">Transplant center representative:<br> <input type=\"text\" style=\"width:280px;\" name=\"transplant_repre\" value=\"\"></td>
			<td width=\"35%\">Signature:<br><input type=\"text\" style=\"width:280px;\" name=\"signature\" value=\"\"></td>
			<td width=\"30%\">Date: (YYYY-MM-DD)<br><input type=\"text\" style=\"width:230px;\" name=\"date_signature\" value=\"".date("Y-m-d",time())."\"></td>
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