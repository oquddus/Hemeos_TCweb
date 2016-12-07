<?
include('1-head.php');
?>


<?
if($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) {
	
	//--nacteni udadu pro editaci
	$vysledek = mysql_query("SELECT * FROM typing_result WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
		if(mysql_num_rows($vysledek)>0):
			$zaz = mysql_fetch_assoc($vysledek);
				extract($zaz);

				/*
				$vysledek_sub = mysql_query("SELECT typing_request.patient_name, typing_request.PatientNum, typing_request_donor.DonorNumber 
				FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' LIMIT 1");
					if(mysql_num_rows($vysledek_sub) > 0):
						$zaz_sub = mysql_fetch_array($vysledek_sub);
							extract($zaz_sub);
					endif;
				@mysql_free_result($vysledek_sub);
				*/

				$donori=array();
				$sloupce=array("a","b","c","drb1","drb3","drb4","drb5","dqb1","dpb1","dqa1","dpa1");
				$vysledek_sub = mysql_query("SELECT typing_request.patient_name, typing_request.PatientNum, typing_request_donor.DonorNumber, ID_typing, resolution, a, b, c, drb1, drb3, drb4, drb5, dqb1, dpb1, dqa1, dpa1, ID_stavu, duvod FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) WHERE typing_request.RegID='".clean_high($RegID)."' AND typing_request_donor.aLogMsgNum='".clean_basic($aLogMsgNum)."'");
					if(mysql_num_rows($vysledek_sub)>0):
						while($zaz_sub = mysql_fetch_array($vysledek_sub)):
							extract($zaz_sub);
							
							switch($resolution):
								case 1: $resolution_text="Low"; break;
								case 2: $resolution_text="Intermediate"; break;
								case 3: $resolution_text="High"; break;
							endswitch;
							
							$donors_number[$DonorID]=$DonorNumber;
							
							foreach($sloupce as $klic=>$sloupec):
								if(${$sloupec}==1):
									$donori[$DonorID][$sloupec]=strtoupper($sloupec).":".$resolution_text;
								else:
									if($donori[$DonorID][$sloupec]==""):
										$donori[$DonorID][$sloupec]=strtoupper($sloupec).":-";
									endif;
								endif;
							endforeach;
							reset($sloupce);
							
						endwhile;
					else:
						message(1, "Donor's typing request is not in database", "", "");
					endif;
				@mysql_free_result($vysledek_sub);
				
				
				echo "<div style=\"float:left; width:100%; margin-bottom:19px; text-align:left; font-size:16px;\">Patient: <b>$patient_name</b>, Patient ID: <b>".$RegID.$PatientNum."P</b>. ";
				//echo "<a href=\"donor-typing-request.php?ID=$ID_typing&amp;DonorNum=$DonorNumber\">Typing request here</a></div>";
	
	
	
				if($ID_stavu==2):
					echo "<div style=\"float:left; width:100%; margin-top:19px;\">";
						message(1, $duvod, "", "");
					echo "</div>";
				else:
		
					echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">";
					
						$_preklad=array("DonorID"=>"Donor identification","REQ_DATE"=>"Request date",
						"REF_CODE"=>"Referent code",
						"RESOLUT"=>"Resolution",
						"D_BIRTH_DATE"=>"Donor Date of birth",
						"D_SEX"=>"Donor sex",
						"D_ABO"=>"D_ABO",
						"D_CMV"=>"Donor CMV status",
						"D_CMV_DATE"=>"Date of CMV test",
						"D_A1"=>"Donor HLA-A, 1st antigen",
						"D_A2"=>"Donor HLA-A, 2nd antigen",
						"D_B1"=>"Donor HLA-B, 1st antigen",
						"D_B2"=>"Donor HLA-B, 2nd antigen",
						"D_C1"=>"Donor HLA-C, 1st antigen",
						"D_C2"=>"Donor HLA-C, 2nd antigen",
						"D_DNA_A1"=>"Donor DNA-A, 1st allele",
						"D_DNA_A2"=>"Donor DNA-A, 2nd allele",
						"D_DNA_B1"=>"Donor DNA-B, 1st allele",
						"D_DNA_B2"=>"Donor DNA-B, 2nd allele",
						"D_DNA_C1"=>"Donor DNA-C, 1st allele",
						"D_DNA_C2"=>"Donor DNA-C, 2nd allele",
						"D_DR1"=>"Donor HLA-DR, 1st antigen",
						"D_DR2"=>"Donor HLA-DR, 2nd antigen",
						"D_DQ1"=>"Donor HLA-DQ, 1st antigen",
						"D_DQ2"=>"Donor HLA-DQ, 2nd antigen",
						"D_DRB11"=>"Donor HLA-DRB1, 1st allele",
						"D_DRB12"=>"Donor HLA-DRB1, 2nd allele",
						"D_DRB31"=>"Donor HLA-DRB3, 1st allele",
						"D_DRB32"=>"Donor HLA-DRB3, 2nd allele",
						"D_DRB41"=>"Donor HLA-DRB4, 1st allele",
						"D_DRB42"=>"Donor HLA-DRB4, 2nd allele",
						"D_DRB51"=>"Donor HLA-DRB5, 1st allele",
						"D_DRB52"=>"Donor HLA-DRB5, 2nd allele",
						"D_DQA11"=>"Donor HLA-DQA1, 1st allele",
						"D_DQA12"=>"Donor HLA-DQA1, 2nd allele",
						"D_DQB11"=>"Donor HLA-DQB1, 1st allele",
						"D_DQB12"=>"Donor HLA-DQB1, 2nd allele",
						"D_DPA11"=>"Donor HLA-DPA1, 1st allele",
						"D_DPA12"=>"Donor HLA-DPA1, 2nd allele",
						"D_DPB11"=>"Donor HLA-DPB1, 1st allele",
						"D_DPB12"=>"Donor HLA-DPB1, 2nd allele",
						"REMARK"=>"Remark",
						"HUB_SND"=>"Registry sending the message",
						"HUB_RCV"=>"Registry receiving message",
						"HLA_NOM_VER"=>"Major version of the HLA Nomenclature in use",
						"DISCREP_ORIG"=>"Was typing discrepant from the original typing reported by donor registry?",
						"CONCLUSION"=>"Conclusion",
						"REL_REASON"=>"Release donor, reason",
						"CB_SAMPLE_TYPE"=>"Type of sample of cord blood unit");
					
						$_nevypisovat=array("ID","RegID","PatientID","datum_vlozeni","PatientNum","DonorNumber","aLogMsgNum");
						foreach($zaz as $klic=>$hodnota):
							if(!in_array($klic,$_nevypisovat)):
							
								if($klic=="RecordUpdate"):
									if($hodnota):
										$hodnota=date($_SESSION['date_format_php'],$hodnota);
									endif;
								endif;
								
								if($klic=="REQ_DATE"):
									if($hodnota):
										if(date("Y",$hodnota)!=1970):
											$hodnota=date($_SESSION['date_format_php'],$hodnota);
										endif;
									endif;
								endif;
								
								if($klic=="D_BIRTH_DATE"):
									if($hodnota):
										$hodnota=date($_SESSION['date_format_php'],$hodnota);
									endif;
								endif;
								
								if($klic=="D_CMV_DATE"):
									if($hodnota):
										$hodnota=date($_SESSION['date_format_php'],$hodnota);
									endif;
								endif;
								
								if($klic=="TX_DATE"):
									if($hodnota):
										$hodnota=date($_SESSION['date_format_php'],$hodnota);
									endif;
								endif;
								
								if($klic=="RESOLUT"):
									if($donori[$DonorID]):
										$hodnota=implode(", ",$donori[$DonorID]);
									else:
										$hodnota="";
									endif;
								endif;
							
								echo "<tr>
									<td width=\"30%\" style=\"height:20px; padding:2px 0 2px 5px;\"><b>";
										if($_preklad[$klic]):
											echo $_preklad[$klic];
										else:
											echo $klic;
										endif;
									echo "</b></td>
									<td width=\"70%\" style=\"height:20px; padding:2px 0 2px 5px;\">$hodnota</td>
								</tr>";
							endif;
						endforeach;
					echo "</table>";
					
				endif;
		
		
		endif;
	@mysql_free_result($vysledek);
	
}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>		

			
<?
include('1-end.php');
?>