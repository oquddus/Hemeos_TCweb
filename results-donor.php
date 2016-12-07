<?
include('1-head.php');
?>


<?
if($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) {

	//--nacteni udadu pro editaci
	$vysledek = mysql_query("SELECT * FROM patient_donor WHERE ID='".mysql_real_escape_string($_GET['ID'])."' LIMIT 1");
		if(mysql_num_rows($vysledek)>0):
			$zaz = mysql_fetch_assoc($vysledek);
				extract($zaz);

		
				$vysledek_sub = mysql_query("SELECT last_name, first_name, RegID FROM search_request WHERE search_request.PatientNum='".mysql_real_escape_string($PatientNum)."' AND search_request.RegID='".mysql_real_escape_string($RegID)."' LIMIT 1");
					if(mysql_num_rows($vysledek_sub) > 0):
						$zaz_sub = mysql_fetch_array($vysledek_sub);
							extract($zaz_sub);
					endif;
				@mysql_free_result($vysledek_sub);
				
				echo "<div style=\"float:left; width:100%; margin-bottom:19px; text-align:left; font-size:16px;\">Donor for patient: <b>$last_name $first_name</b>, Patient ID: <b>".$RegID.$PatientNum."P</b></div>";
	
	
		
				echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">";
				
					$_nevypisovat=array("ID","RegID","ID2","PatientNum","datum_vlozeni");
					foreach($zaz as $klic=>$hodnota):
						if(!in_array($klic,$_nevypisovat)):
						
							if($klic=="RecordUpdate"):
								if($hodnota>0 && date("Y",$hodnota)!=1970):
									$hodnota=date($_SESSION['date_format_php'],$hodnota);
								else:
									$hodnota="";
								endif;
							endif;
						
							if($klic=="ProbMM0"):
								$hodnota=round($hodnota)."%";
							endif;
							if($klic=="ProbMM1"):
								$hodnota=round($hodnota)."%";
							endif;
							if($klic=="PROBA"):
								$hodnota=round($hodnota)."%";
							endif;
							if($klic=="PROBB"):
								$hodnota=round($hodnota)."%";
							endif;
							if($klic=="PROBC"):
								$hodnota=round($hodnota)."%";
							endif;
							if($klic=="PROBDR"):
								$hodnota=round($hodnota)."%";
							endif;
							if($klic=="PROBDQ"):
								$hodnota=round($hodnota)."%";
							endif;
							
							switch($klic):
								case "PROBA": $klic="P(A)"; break;
								case "PROBB": $klic="P(B)"; break;
								case "PROBC": $klic="P(C)"; break;
								case "PROBDR": $klic="P(DR)"; break;
								case "PROBDQ": $klic="P(DQ)"; break;
								
								case "ProbMM0": $klic="P(10/10)"; break;
								case "ProbMM1": $klic="P(9/10)"; break;
							endswitch;
							
							
						
							echo "<tr>
								<td width=\"25%\"><b>$klic</b></td>
								<td width=\"75%\">$hodnota</td>
							</tr>";
						endif;
					endforeach;
				echo "</table>";
		
		
		endif;
	@mysql_free_result($vysledek);
	
}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>		

			
<?
include('1-end.php');
?>