<?
include('1-head.php');
?>


<?

$h=sha1($_GET['ID'].$_GET['d']."SolitJeDobre5477");
if($h==$_GET['h']):

		//info o pacientovi
		if($_GET['d']==1):	//typing
			$vysledek_sub = mysql_query("SELECT typing_request.patient_name, typing_request.PatientID, typing_request_donor.DonorID, typing_request_donor.DonorNumber 
			FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) 
			WHERE typing_request.ID='".clean_high($_GET['ID'])."' LIMIT 1");
		endif;
		
		if($_GET['d']==2):	//sample
			$vysledek_sub = mysql_query("SELECT sample_request.patient_name, sample_request.PatientID, sample_request_donor.DonorID, sample_request_donor.DonorNumber 
			FROM sample_request LEFT JOIN sample_request_donor ON(sample_request.ID=sample_request_donor.ID_sample) 
			WHERE sample_request.ID='".clean_high($_GET['ID'])."' LIMIT 1");
		endif;
		
		if($_GET['d']==3):	//workup
			$vysledek_sub = mysql_query("SELECT workup_request.patient_name, workup_request.PatientID, workup_request.DonorID 
			FROM workup_request 
			WHERE workup_request.ID='".clean_high($_GET['ID'])."' LIMIT 1");
		endif;
		
			if(mysql_num_rows($vysledek_sub)>0):
				$zaz_sub = mysql_fetch_array($vysledek_sub);
					extract($zaz_sub);
			endif;
		@mysql_free_result($vysledek_sub);
				
				
		echo "<div style=\"float:left; width:100%; margin-bottom:19px; text-align:left; font-size:16px;\">Patient: <b>$patient_name</b>, Patient ID: <b>".$PatientID."</b>, Donor ID: <b>".$DonorID."</b>. ";
	
	
		$patient_name="";
		$PatientID="";
		$DonorID="";
		$DonorNumber="";
	
		$vysledek_sub = mysql_query("SELECT * 
		FROM request_response 
		WHERE request_response.RequestID='".clean_high($_GET['ID'])."'
		AND request_response.RequestType='".clean_high($_GET['d'])."'");
			if(mysql_num_rows($vysledek_sub) > 0):
				while($zaz_sub = mysql_fetch_array($vysledek_sub)):
					extract($zaz_sub);
					
					echo "<table cellspacing=\"0\" width=\"100%\" style=\"border:0; border-bottom:1px solid #b8b8b8; margin-top:20px;\" id=\"tb-form\">";
					
						echo "<tr>
							<td width=\"20%\" style=\"height:20px; padding:2px 0 2px 5px;\">Date of response</td>
							<td width=\"80%\" style=\"height:20px; padding:2px 0 2px 5px;\">".date($_SESSION['date_format_php'],$datum_vlozeni)."</td>
						</tr>";
						
						echo "<tr>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">Patient Number</td>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">$PatientNum</td>
						</tr>";
						
						echo "<tr>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">Donor Number</td>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">$DonorNum</td>
						</tr>";
						
						echo "<tr>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">Response type</td>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">";
								
								if($ResponseID==1):
									echo "NO_RES (No result)";
								endif;
								
								if($ResponseID==2):
									echo "REQ_CAN (request cancelation)";
								endif;
								
								if($ResponseID==3):
									echo "MSG_DEN (message denial)";
								endif;
							
							echo "</td>
						</tr>";
						
						echo "<tr>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">Reason</td>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">";
							
								$reason="";
								$vysledek_sub2 = mysql_query("SELECT setting_response.reason FROM setting_response WHERE setting_response.ID='".clean_high($ReasonID)."' LIMIT 1");
									if(mysql_num_rows($vysledek_sub2) > 0):
										$zaz_sub2 = mysql_fetch_array($vysledek_sub2);
											extract($zaz_sub2);
											
											echo $reason;
											
									endif;
								@mysql_free_result($vysledek_sub2);
								
							echo "</td>
						</tr>";
						
						echo "<tr>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">Message</td>
							<td style=\"height:20px; padding:2px 0 2px 5px;\">$Msg</td>
						</tr>";

					echo "</table>";
					
				endwhile;
			endif;
		@mysql_free_result($vysledek_sub);
	
				
	
endif;
?>		

			
<?
include('1-end.php');
?>