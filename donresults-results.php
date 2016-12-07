<?
include('1-head.php');
?>


<?
//* =============================================================================
//	Filtrovací formuláø
//============================================================================= */
echo "<form method=\"GET\" action=\"$_SERVER[PHP_SELF]\" name=\"formular_hledej\" style=\"clear:both; margin-bottom:20px;\">";

	//--vyhledavani
	//--vyhledavani
	/*
	echo "<div style=\"float:left; width:100%; margin-top:-10px; margin-bottom:19px;\">
		<div class=\"hledej-text\"></div>
		<div class=\"hledej-input\">Search: &nbsp;<input type=\"text\" name=\"hledej\" style=\"width:150px; height:17px;\" value=\"".$_GET['hledej']."\">
		<input type=\"image\" src=\"img/ico/lupa.gif\" value=\"hledej\" style=\"position: relative;top:4px\"></div>
	</div>";
	echo "<input type=\"hidden\" name=\"action\" value=\"search\">";
	*/


	$form_name="formular_hledej";			
					
	/*
	echo "<table border=\"0\" width=\"100%\" cellspacing=\"4\" class=\"tb-abeceda\" $skryt_filtrovaci_tabulku>
		<tr>
			<td>";
			
				unset($IDcka_prirazena);
				if($_SESSION['ID_firem_filtr'] && !$_GET['ID_firem_filtr']):
					$IDcka_prirazena=$_SESSION['ID_firem_filtr'];
					$_GET['ID_firem_filtr']=$_SESSION['ID_firem_filtr'];	//--kvuli vyhodnocovani podminek je potreba ulozit to i do getu
				endif;
				
				if($_GET['ID_firem_filtr']):
					$IDcka_prirazena=$_GET['ID_firem_filtr'];
					$_SESSION['ID_firem_filtr']=$_GET['ID_firem_filtr'];
				endif;
				
				$ajax_pole1=array("ID_firem_filtr","$form_name","","","","");		//nazev_form, form_name, ajax_where, nadrazeny_form, podrazeny_form, ajax_join
				$ajax_pole2=array("firmy","nazev","1","1","$IDcka_prirazena");		//tab, sl, zpusob, multi, idcka
				ajax_multibox($ajax_pole1,$ajax_pole2);
				
				echo "<div style=\"text-align:left; float:left; width:100%; height:auto;\"><div style=\"height:29px; float:left; margin-top:2px;\">&nbsp;&nbsp;<b>Filtrovat podle firem</b>: &nbsp;&nbsp;$ajax_ico</div>";

					echo "<input type=\"hidden\" name=\"$nazev_form\" class=\"form\" size=\"50\" value=\"$IDcka_prirazena\" id=\"$nazev_form\">";
					echo "<div id=\"$cil_text\" style=\"float:left; margin-left:8px; display:inline; margin-top:9px;\">";
						//--pri editaci nacti textove hodnoty
							include('1-add2.php');
						//--konec hodnot pri editaci
					echo "</div>";

				echo "</div>";

			echo "</td>
		</tr>
	</table>";
	*/

echo "</form>";
//	konec filtrovaciho formulare
//=============================================================================




//* =============================================================================
//	Filtr dle voleb ve filtrovacim kalendari
//============================================================================= */
if($_GET['action']=="search"):
	if($_GET['hledej']):
		$vyraz=mysql_real_escape_string(strtolower2(trim($_GET['hledej'])));
		$where_hledej="AND (LOWER(search_request.last_name) LIKE '$vyraz%' OR LOWER(search_request.first_name) LIKE '$vyraz%')";
	endif;
endif;

/*
//--ziskani id firem
if($_GET['ID_firem_filtr']):
	unset($seznam_id_firem);
	
	$pole_id_firem=explode("|",$_GET['ID_firem_filtr']);
	foreach($pole_id_firem as $id_firmy):
		if($id_firmy>0):
			$seznam_id_firem.=",".$id_firmy;
		endif;
	endforeach;
	
	if($seznam_id_firem):
		$seznam_id_firem="AND firmy.ID IN (0".$seznam_id_firem.")";
	endif;
endif;
*/

//--konec vytvarenych filtru
//=============================================================================











//* =============================================================================
//	Stránkování
//============================================================================= */
//strankovani("patient_donor WHERE search_request.ID_stavu='2' $where_hledej", $_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('typing_result.datum_vlozeni DESC');


$counter=0;


//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND typing_result.RegID='".$_SESSION['usr_RegID']."'";
	
	if($_SESSION['usr_InstID']):
		$where.=" AND typing_request.InstID='".$_SESSION['usr_InstID']."'";
	endif;
endif;

	
//-----vypis dat
$vysledek = mysql_query("SELECT typing_result.ID, typing_result.PatientID, typing_result.DonorID, typing_result.datum_vlozeni, typing_result.aLogMsgNum, typing_result.RegID, 
	typing_result.D_SEX, typing_result.REF_CODE, typing_result.RESOLUT,typing_result.D_ABO, typing_result.HUB_SND, typing_result.REQ_DATE    
	FROM typing_result 
	LEFT JOIN typing_request_donor ON(typing_result.aLogMsgNum=typing_request_donor.aLogMsgNum) LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID)
	WHERE typing_result.ID>0 $where 
	GROUP BY typing_result.ID
	ORDER BY typing_result.datum_vlozeni DESC");
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";

			while($zaz = mysql_fetch_array($vysledek)){

				extract($zaz);
				
				//zjistit udaje pacienta
				$patient_name="";
				$PatientNum="";
				$ID_stavu=0;
				$vysledek_sub = mysql_query("SELECT patient_name, PatientNum, typing_request_donor.ID_stavu FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' LIMIT 1");
					if(mysql_num_rows($vysledek_sub) > 0):
						$zaz_sub = mysql_fetch_array($vysledek_sub);
							extract($zaz_sub);
					endif;
				@mysql_free_result($vysledek_sub);
				
			
				++$counter;
				
				if($datum_vlozeni!=$datum_vlozeni_old):
					if($counter>1):
						echo "</tbody></table>";
					endif;
					echo "<div style=\"float:left; width:100%; border-bottom:1px solid #c2c2c2; text-align:left; padding-bottom:2px;\"><b>Imported: ".date($_SESSION['date_format_php']." H:i",$datum_vlozeni)."</b>
					&nbsp;&nbsp;&nbsp; <span style=\"font-size:14px;\">Patient: $patient_name, Patient ID: ".$PatientID."</span></div>";
					echo "<table cellspacing=\"0\" width=\"100%\" style=\"clear:left;\">
					<thead id=\"tb-head\">
					  <tr>
						<td width=\"25%\">Patient name</td>
						<td width=\"10%\">HUB</td>
						<td width=\"15%\">Date</td>
						<td width=\"20%\">DonorID</td>
						<td width=\"20%\">Category</td>
						<td width=\"10%\">Action</td>
					  </tr>
					</thead>
					<tbody id=\"tb-body\">";
					
					$datum_vlozeni_old=$datum_vlozeni;
				endif;
			
			
				if($counter%2==0):
					$styl="class=\"barva1\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva1'\"";
				else:
					$styl="class=\"barva2\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva2'\"";
				endif;
				
				
				if($pocet_donoru>0):
					$styl="class=\"barva6\" onmouseover=\"this.className='barva6b'\" onmouseout=\"this.className='barva6'\"";
				endif;
				
				
				$hash=sha1($ID."SaltIsGoodForLife45");
				
				echo "<tr ".$styl.">";
					echo "<td>$patient_name</td>";
					echo "<td>$HUB_SND</td>";
					echo "<td>"; if($REQ_DATE): if(date("Y",$REQ_DATE)!=1970): echo date($_SESSION['date_format_php'],$REQ_DATE); endif; endif; echo "</td>";
					echo "<td>$DonorID</td>";
					echo "<td>";

					/*
					$vysledek_sub = mysql_query("SELECT typing_request_donor.ID_stavu FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) WHERE typing_request.PatientNum='".mysql_real_escape_string($PatientNum)."' AND typing_request_donor.DonorNumber='".mysql_real_escape_string($DonorNumber)."'");
						if(mysql_num_rows($vysledek_sub)>0):
							$zaz_sub = mysql_fetch_array($vysledek_sub);
								extract($zaz_sub);
								
								if($ID_stavu==2):
									$category_text="Other message";
								endif;
						else:
							$category_text="No typing request";
						endif;
					@mysql_free_result($vysledek_sub);
					
					if(!$category_text):
						$category_text="Result of typing";
					endif;
					*/
					
						if($ID_stavu==2):
							$category_text="Other message";
						endif;
						
						if(!$category_text):
							$category_text="Result of typing";
						endif;
					
						echo $category_text;
					echo "</td>";
				
					echo "<td>";
					echo "<a href=\"donor-result.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a>";
					//echo "&nbsp;&nbsp;<input type=\"checkbox\" name=\"cb_$ID\" value=\"$PatientNum|$ID2\">";
					echo "</td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			
			/*
			echo "<div style=\"float:right; width:56px; margin-left:7px;\"><input type=\"submit\" class=\"form-ok\" value=\"\"></div>";
			
			echo "<div style=\"float:right; width:300px; text-align:right;\">For selected: ";
			echo "<select name=\"operation\" id=\"operation\">
				<option value=\"1\""; if($operation==1): echo " selected"; endif; echo ">send typing request</option>
				<option value=\"2\""; if($operation==2): echo " selected"; endif; echo ">send sample request</option>
			</select>";
			
			echo "<input type=\"hidden\" name=\"action\" value=\"request\">";
			echo "</div>";
			
			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send\" value=\"\"></p>";
			*/

			
		echo "</form>";
		
		
		
		
		//* =============================================================================
		//	Stránkování
		//============================================================================= */
		//strankovani2();
		
		
			
	else:
		message(1, "There are no items.", "", "");
	endif;
	
	
//* =============================================================================
//	JS
//============================================================================= */
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

	function change_text(ID_span){
		if(document.getElementById(ID_span).innerHTML==\"zobrazit\"){
			document.getElementById(ID_span).innerHTML=\"skrýt\";
			var operace=\"zobrazit\";
		}else{
			document.getElementById(ID_span).innerHTML=\"zobrazit\";
			var operace=\"skryt\";
		}
		
		
		//pomoci ajaxu do sessions zapsat zobrazeni filtru
		showHint('ajax-filtry.php?druh_filtru='+ID_span+'&operace='+operace,'ajax_pomocny','1');
	}
	
	
	
	
			
</SCRIPT>";
?>			
			
			
			
<?
include('1-end.php');
?>