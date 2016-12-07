<?
header('Content-type: text/html; charset=windows-1250'); 

//--vytvoøení session
session_start(); 

if($_SESSION['usr_ID']):

//* =============================================================================
//	Formulare pro reason2
//============================================================================= */
require "../opendb.php";
require "../1-function.php";

	$form_name="formular";
	
	//nacteni custom hodnot
	$_hodnoty=array();
	$vysledek_if = mysql_query("SELECT ID_item, label FROM setting_reasons2_custom WHERE ID_center='".mysql_real_escape_string($_SESSION['usr_ID_centra'])."'");
		if(mysql_num_rows($vysledek_if)):
			while($zaz_if = mysql_fetch_array($vysledek_if)):
				extract($zaz_if);
				
				$_hodnoty[$ID_item]=$label;
				
			endwhile;
		endif;
	@mysql_free_result($vysledek_if);
	
	
	function get_label($txt,$key){
		global $_hodnoty;
		
		if(!empty($_hodnoty[$key])):
			$txt=$_hodnoty[$key];
		endif;
		return $txt;

	}
	
	
	if($_GET['ID_reason']==20):	//transplanted
	
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #e0e0e0; margin-bottom:10px;\" id=\"tb-form\">
		
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0;\">Product:</td>
			<td width=\"80%\" colspan=\"4\" valign=\"top\" style=\"border:0; border-bottom:1px solid #e0e0e0; padding-bottom:10px;\">
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"product_bm\" id=\"product_bm\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"product_bm\">BM</label></div>
				
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"product_pbsc\" id=\"product_pbsc\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"product_pbsc\">PBSC</label></div>
				
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"product_dli\" id=\"product_dli\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"product_dli\">DLI</label></div>
				
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"product_double\" id=\"product_double\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"product_double\">Double CB</label></div>
				
				<div style=\"float:left;\"><input type=\"checkbox\" name=\"product_single\" id=\"product_single\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"product_single\">Single CB</label></div>
			</td>
		</tr>
		
		<tr>
			<td width=\"100%\" colspan=\"5\" valign=\"top\" style=\"border:0; padding:0; height:2px; border-top:1px solid #e0e0e0; padding-bottom:10px;\"></td>
		</tr>
		
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-bottom:18px;\">".get_label("Donor ID",1).":</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0;\"><input type=\"text\" style=\"width:120px;\" name=\"trans_donor_id\" id=\"trans_donor_id\" value=\"\"></td>
			
			<td width=\"10%\" valign=\"top\" style=\"border:0;\">&nbsp;</td>
			
			<td width=\"20%\" valign=\"top\" style=\"border:0;\">".get_label("Datum infusie",2).":</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0;\"><div style=\"float:left;\"><input type=\"text\" style=\"width:120px;\" name=\"trans_date0\" id=\"trans_date0\" value=\"\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('trans_date0',$form_name);
				echo "</div></div></td>
		</tr>";
		
		echo "<tr>
			<td width=\"100%\" colspan=\"5\" valign=\"top\" style=\"border:0; padding:0; height:2px; border-top:1px solid #e0e0e0; padding-bottom:10px;\"></td>
		</tr>";
		
		//echo "</table>";
		
		//echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #e0e0e0; margin-bottom:10px;\" id=\"tb-form2\">
		echo "<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0;\">".get_label("CB ID 1",3).":</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0;\"><input type=\"text\" style=\"width:120px;\" name=\"trans_cbid1\" id=\"trans_cbid1\" value=\"\"></td>
			
			<td width=\"10%\" valign=\"top\" style=\"border:0;\">&nbsp;</td>
			
			<td width=\"20%\" valign=\"top\" style=\"border:0;\">".get_label("CB ID 2",4).":</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0;\"><input type=\"text\" style=\"width:120px;\" name=\"trans_cbid2\" id=\"trans_cbid2\" value=\"\"></td>
		</tr>
		<tr>
			<td valign=\"top\" style=\"border:0;\">".get_label("Datum infusie",5).":</td>
			<td valign=\"top\" style=\"border:0;\">
				<div style=\"float:left;\"><input type=\"text\" style=\"width:120px;\" name=\"trans_date1\" id=\"trans_date1\" value=\"\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('trans_date1',$form_name);
				echo "</div></div>
			</td>
			
			<td valign=\"top\" style=\"border:0;\">&nbsp;</td>
			
			<td valign=\"top\" style=\"border:0;\">".get_label("Datum infusie",6).":</td>
			<td valign=\"top\" style=\"border:0;\">
				<div style=\"float:left;\"><input type=\"text\" style=\"width:120px;\" name=\"trans_date2\" id=\"trans_date2\" value=\"\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('trans_date2',$form_name);
				echo "</div></div>
			</td>
			
		</tr>
		<tr>
			<td valign=\"top\" style=\"border:0; padding-bottom:15px;\">".get_label("Zijn er problemen geweest bij ontdooien?",7)."</td>
			<td valign=\"top\" style=\"border:0;\"><input type=\"text\" name=\"trans_problem1\" id=\"trans_problem1\" value=\"\"></td>
			
			<td valign=\"top\" style=\"border:0;\">&nbsp;</td>
			
			<td valign=\"top\" style=\"border:0;\">".get_label("Zijn er problemen geweest bij ontdooien?",8)."</td>
			<td valign=\"top\" style=\"border:0;\"><input type=\"text\" name=\"trans_problem2\" id=\"trans_problem2\" value=\"\"></td>
		</tr>
		
		</table>";
		
		
		
		
		
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #e0e0e0; margin-bottom:10px;\" id=\"tb-form\">
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-bottom:10px; padding-top:10px;\">".get_label("Complicaties?",9)."</td>
			<td width=\"80%\" valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"complications\" id=\"complications\" value=\"1\"></td>
		</tr>
		</table>";
		
		
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0; display:none;\" id=\"tb-form4\">
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-top:5px; padding-bottom:10px;\">Gemeld aan IGZ/TRIP:</td>
			<td width=\"80%\" colspan=\"3\" valign=\"top\" style=\"border:0; padding-top:3px;\"><input type=\"checkbox\" name=\"reported_igz\" id=\"reported_igz\" value=\"1\"></td>
		</tr>
		
		<tr>
			<td width=\"100%\" colspan=\"4\" valign=\"top\" style=\"border:0; padding:0; height:1px; border-top:1px solid #e0e0e0; padding-bottom:10px;\"></td>
		</tr>
		
		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-top:5px; padding-bottom:10px;\">Waren deze onverwacht?</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"unexpected\" id=\"unexpected1\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"unexpected1\">ja</label></div>
			</td>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"unexpected\" id=\"unexpected2\" value=\"2\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"unexpected2\">nee</label></div>
			</td>
			<td width=\"35%\" valign=\"top\" style=\"border:0; padding-top:3px;\"></td>
		</tr>
		
		<tr>
			<td width=\"100%\" colspan=\"4\" valign=\"top\" style=\"border:0; padding:0; height:1px; border-top:1px solid #e0e0e0; padding-bottom:10px;\"></td>
		</tr>

		<tr>
			<td width=\"20%\" rowspan=\"2\" valign=\"top\" style=\"border:0; padding-top:5px; padding-bottom:6px;\">Wat was de ernst</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"gravity\" id=\"gravity1\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"gravity1\">mild</label></div>
			</td>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"gravity\" id=\"gravity2\" value=\"2\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"gravity2\">matig</label></div>
			</td>
			<td width=\"35%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"gravity\" id=\"gravity3\" value=\"3\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"gravity3\">levensbedreigend</label></div>
			</td>
		</tr>
		
		<tr>
			<td width=\"20%\" colspan=\"3\" valign=\"top\" style=\"border:0; padding-top:3px; padding-bottom:15px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"gravity\" id=\"gravity4\" value=\"4\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"gravity4\">patiënt is overleden als gevolg van complicatie</label></div>
			</td>
		</tr>
		
		<tr>
			<td width=\"100%\" colspan=\"4\" valign=\"top\" style=\"border:0; padding:0; height:1px; border-top:1px solid #e0e0e0; padding-bottom:10px;\"></td>
		</tr>
		
		<tr>
			<td width=\"20%\" rowspan=\"2\" valign=\"top\" style=\"border:0; padding-top:5px; padding-bottom:6px;\">Product of donor gerelateerd?</td>
			<td width=\"25%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"don_product\" id=\"don_product1\" value=\"1\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"don_product1\">Uitgesloten</label></div>
			</td>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"don_product\" id=\"don_product2\" value=\"2\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"don_product2\">onwaarschijnlijk</label></div>
			</td>
			<td width=\"35%\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"don_product\" id=\"don_product3\" value=\"3\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"don_product3\">mogelijk</label></div>
			</td>
		</tr>

		<tr>
			<td width=\"20%\" valign=\"top\" style=\"border:0; padding-top:3px; padding-bottom:15px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"don_product\" id=\"don_product4\" value=\"4\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"don_product4\">waarschijnlijk</label></div>
			</td>
			<td colspan=\"2\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"don_product\" id=\"don_product5\" value=\"5\"></div>
				<div style=\"float:left; margin:3px 25px 0 6px;\"><label for=\"don_product5\">zeker</label></div>
			</td>
		</tr>
		
		<tr>
			<td width=\"100%\" colspan=\"4\" valign=\"top\" style=\"border:0; padding:0; height:1px; border-top:1px solid #e0e0e0; padding-bottom:10px;\"></td>
		</tr>
		
		<tr>
			<td colspan=\"4\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				Hier graag omschrijven: de aard van de complicaties, hoe de patiënt is behandeld en of de patiënt (restloos) is hersteld en wat de veronderstelde relatie is met het product.
			</td>
		</tr>
		<tr>
			<td colspan=\"4\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<textarea name=\"complicaties_text\" id=\"complicaties_text\" style=\"width:100%; height:40px;\"></textarea>
			</td>
		</tr>

		
		<tr>
			<td colspan=\"4\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				Welke informatie van het donor - of afname centrum is gewenst?:
			</td>
		</tr>
		<tr>
			<td colspan=\"4\" valign=\"top\" style=\"border:0; padding-top:3px;\">
				<textarea name=\"donor_info\" id=\"donor_info\" style=\"width:100%; height:40px;\"></textarea>
			</td>
		</tr>
		
		</table>";
		/*
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0; display:none;\" id=\"tb-form4\">
		<tr>
			<td width=\"5%\" valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp1\" id=\"trans_comp1\" value=\"1\"></td>
			<td width=\"28%\" valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp1\">".get_label("Bradycardie",10)."</label></td>
			
			<td width=\"5%\" valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp2\" id=\"trans_comp2\" value=\"1\"></td>
			<td width=\"28%\" valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp2\">".get_label("Hemolytische reactie",11)."</label></td>
			
			<td width=\"5%\" valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp3\" id=\"trans_comp3\" value=\"1\"></td>
			<td width=\"29%\" valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp3\">".get_label("Dyspnoe, respiratoire decompensatie",12)."</label></td>
		</tr>
		<tr>
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp4\" id=\"trans_comp4\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp4\">".get_label("Hypotensie",13)."</label></td>
			
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp5\" id=\"trans_comp5\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp5\">".get_label("Cardio-respiratoir arrest",14)."</label></td>
			
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp6\" id=\"trans_comp6\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp6\">".get_label("Bacteremie tgv contaminatie",15)."</label></td>
		</tr>
		<tr>
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp7\" id=\"trans_comp7\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp7\">".get_label("Hypertensie",16)."</label></td>
			
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp8\" id=\"trans_comp8\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp8\">".get_label("Transfer to ICU",17)."</label></td>
			
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp9\" id=\"trans_comp9\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp9\">".get_label("Overlijden binnen 48 hr na infusie",18)."</label></td>
		</tr>
		<tr>
			<td valign=\"top\" style=\"border:0;\"><input type=\"checkbox\" name=\"trans_comp10\" id=\"trans_comp10\" value=\"1\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"><label for=\"trans_comp10\">".get_label("Anders, nl",19)."</label></td>
			
			<td valign=\"top\" style=\"border:0;\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"></td>
			
			<td valign=\"top\" style=\"border:0;\"></td>
			<td valign=\"top\" style=\"border:0; padding-top:3px;\"></td>
		</tr>
		</table>";
		*/

	endif;
	
	
	if($_GET['ID_reason']==21):	//died
	
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" valign=\"top\" style=\"border:0;\">
				<div style=\"float:left; margin-top:4px;\"><b>".get_label("Date of die",20).":</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"text\" style=\"width:120px;\" name=\"die_date\" id=\"die_date\" value=\"\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
				get_calendar('die_date',$form_name);
				echo "</div></div>
			
				<div style=\"float:left; margin:4px 0 0 40px;\"><b>".get_label("Reason",21).":</b></div>
				<div style=\"float:left; margin-left:5px;\">";
				
				echo "<input type=\"text\" style=\"width:400px;\" name=\"die_reason\" id=\"die_reason\" value=\"\">";
				/*
				echo "<select name=\"die_reason\" id=\"die_reason\" style=\"width:300px;\">";
				
					$vysledek_sub = mysql_query("SELECT ID AS ID_duvod_umrti, duvod_umrti FROM nastaveni_duvody_umrti ORDER BY poradi");
						if(mysql_num_rows($vysledek_sub)>0):
							while($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
								extract($zaz_sub);
								
								echo "<option value=\"".$ID_duvod_umrti."\">".$duvod_umrti."</option>";
								
							endwhile;
						endif;
					@mysql_free_result($vysledek_sub);
				
				echo "</select>";
				*/
				
				echo "</div>
			</td>
		</tr>
		</table>";

	endif;
	
	if($_GET['ID_reason']==22):	//respod
	
		//nic

	endif;
	
	if($_GET['ID_reason']==23):	//no donor
	
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" valign=\"top\" style=\"border:0;\">
				<div style=\"float:left; margin-top:4px;\"><b><label for=\"nodonor_transplant1\">".get_label("Geen transplantatie",22).":</label></b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"radio\" name=\"nodonor_transplant\" id=\"nodonor_transplant1\" value=\"1\"></div>
				
				<div style=\"float:left; margin:4px 0 0 40px;\"><b><label for=\"nodonor_transplant2\">".get_label("Haplo identieke transplantatie",23).":</label></b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"radio\" name=\"nodonor_transplant\" id=\"nodonor_transplant2\" value=\"2\"></div>				
			
				<div id=\"nodonor_comm\" style=\"display:none;\">
				<div style=\"float:left; margin:4px 0 0 40px;\"><b>".get_label("Text",24).":</b></div>
				<div style=\"float:left; margin-left:5px;\"><textarea name=\"nodonor_text\" id=\"nodonor_text\" style=\"width:400px; height:22px;\"></textarea></div>
			</td>
		</tr>
		</table>";

	endif;
	
	if($_GET['ID_reason']==24):	//other
	
		echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" valign=\"top\" style=\"border:0;\">
				<div style=\"float:left; margin:4px 0 0 0;\"><b>".get_label("Text",25).":</b></div>
				<div style=\"float:left; margin-left:5px;\"><textarea name=\"other_text\" id=\"other_text\" style=\"width:600px; height:22px;\"></textarea></div>
			</td>
		</tr>
		</table>";

	endif;
	
endif;
?>

