<?
//$width_vynucena=1400;
include('1-head.php');
?>


<?
//* =============================================================================
//	Operace s vybranymi
//============================================================================= */
/*
if($_POST['operation']):

	$_donori=array();
	foreach($_POST as $klic=>$hodnota):
		$broken=explode("_",$klic);
		if($broken[0]=="cb" && $hodnota>0):
		
			$data=explode("|",$hodnota);
			$_donori[]=$data[1];
			
		endif;
	endforeach;
	
	//JEN PN nestaci, musi byt take RegID, PN neni unikatni!!!!!!
	if($_POST['operation']==1):	//typing
		echo "<script type=\"text/javascript\">
			window.location.href = 'donor-typing-request.php?Pn=".$data[0]."&donors=".implode(",",$_donori)."';
		</script>";
	endif;
	
	if($_POST['operation']==2):	//sample
		echo "<script type=\"text/javascript\">
			window.location.href = 'donor-sample-request.php?Pn=".$data[0]."&donors=".implode(",",$_donori)."';
		</script>";
	endif;
	
	if($_POST['operation']==3):	//workup
		echo "<script type=\"text/javascript\">
			window.location.href = 'donor-workup-request.php?Pn=".$data[0]."&donors=".implode(",",$_donori)."';
		</script>";
	endif;
	
endif;
*/




//* =============================================================================
//	Filtrovac� formul��
//============================================================================= */
echo "<form method=\"GET\" action=\"$_SERVER[PHP_SELF]\" name=\"formular_hledej\" style=\"clear:both; margin-bottom:20px;\">";

	//--vyhledavani
	//--vyhledavani
	echo "<div style=\"float:left; width:100%; margin-top:-10px; margin-bottom:19px;\">
		<div class=\"hledej-text\"></div>
		<div class=\"hledej-input\">Search: &nbsp;<input type=\"text\" name=\"hledej\" style=\"width:150px; height:17px;\" value=\"".$_GET['hledej']."\">
		<input type=\"image\" src=\"img/ico/lupa.gif\" value=\"hledej\" style=\"position: relative;top:4px\"></div>
	</div>";
	echo "<input type=\"hidden\" name=\"action\" value=\"search\">";


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
//	Str�nkov�n�
//============================================================================= */
//strankovani("patient_donor WHERE search_request.ID_stavu='2' $where_hledej", $_na_stranku);



//* =============================================================================
//	Defaultn� �azen�
//============================================================================= */
razeni('patient_donor.RecordUpdate DESC');


$counter=0;

$where="";
//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND search_request.RegID='".$_SESSION['usr_RegID']."'";
	
	if($_SESSION['usr_InstID']):
		$where.=" AND search_request.InstID='".$_SESSION['usr_InstID']."'";
	endif;
endif;
	
	
	
//-----vypis dat
$vysledek = mysql_query("SELECT patient_donor.ID, patient_donor.RegID, patient_donor.RecordUpdate, patient_donor.PatientNum, patient_donor.Hub, patient_donor.Status, patient_donor.Type, patient_donor.Sex, patient_donor.BirthDate, patient_donor.MatchGrade, patient_donor.MatchGradeInternal, patient_donor.PhenotypeQuality, patient_donor.DonorNumber, patient_donor.datum_vlozeni, patient_donor.ID2, search_request.last_name, search_request.first_name 
FROM patient_donor 
LEFT JOIN search_request ON(patient_donor.PatientNum=search_request.PatientNum) WHERE patient_donor.ID>0 $where 
ORDER BY datum_vlozeni DESC, MatchGradeInt ASC, PhenotypeQuality DESC, ID2 ASC");
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";

			while($zaz = mysql_fetch_array($vysledek)){

				extract($zaz);
			
				++$counter;
				
				if($datum_vlozeni!=$datum_vlozeni_old):
					if($counter>1):
						echo "</tbody></table>";
					endif;
					
					$hash=sha1($RegID.$PatientNum."SaltIsGoodForLife45");
					
					echo "<div style=\"float:left; width:100%; border-bottom:1px solid #c2c2c2; text-align:left; padding-bottom:2px;\"><b>Imported: "; if($datum_vlozeni>0): echo date("d.m.Y H:i",$datum_vlozeni); endif; echo "</b>
					&nbsp;&nbsp;&nbsp; <span style=\"font-size:14px;\"><a href=\"results-donors.php?Rid=".$RegID."&amp;PatientNum=$PatientNum&amp;h=".$hash."\">Patient: $first_name $last_name, Patient ID: ".$RegID.$PatientNum."P</a></span></div>";
					echo "<table cellspacing=\"0\" width=\"100%\" style=\"clear:left;\">
					<thead id=\"tb-head\">
					  <tr>
						<td width=\"12%\">Donor ID</td>
						<td width=\"14%\">Record Update</td>
						<td width=\"5%\">Hub</td>
						<td width=\"5%\">Status</td>
						<td width=\"5%\">Type</td>
						<td width=\"5%\">Sex</td>
						<td width=\"10%\">BirthDate</td>
						<td width=\"16%\">MatchGrade ABDR</td>
						<td width=\"14%\">MatchGrade</td>
						<td width=\"16%\">Action</td>
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
					echo "<td>$ID2</td>";
					echo "<td>"; if($RecordUpdate>0): if(date("Y",$RecordUpdate)!=1970): echo date($_SESSION['date_format_php'],$RecordUpdate); endif; endif; echo "</td>";
					echo "<td>$Hub</td>";
					echo "<td>$Status</td>";
					echo "<td>$Type</td>";
					echo "<td>$Sex</td>";
					echo "<td>$BirthDate</td>";
					echo "<td>$MatchGrade</td>";
					echo "<td>$MatchGradeInternal</td>";
					
					
					
					echo "<td>";
					echo "<a href=\"results-donor.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a>";
					echo "&nbsp;<a href=\"donor-typing-request.php?Pn=".$PatientNum."&amp;RegID=".$RegID."&amp;donors=$ID2\" title=\"NEW TYPING REQUEST\"><img src=\"img/ico/typing.png\" width=\"20\" height=\"20\" alt=\"NEW TYPING REQUEST\" style=\"margin-top:3px;\"></a>";
					echo "&nbsp;<a href=\"donor-sample-request.php?Pn=".$PatientNum."&amp;RegID=".$RegID."&amp;donors=$ID2\" title=\"NEW SAMPLE REQUEST\"><img src=\"img/ico/typing.png\" width=\"20\" height=\"20\" alt=\"NEW SAMPLE REQUEST\" style=\"margin-top:3px;\"></a>";
					echo "&nbsp;<a href=\"donor-workup-request.php?Pn=".$PatientNum."&amp;RegID=".$RegID."&amp;donors=$ID2\" title=\"NEW WORKUP REQUEST\"><img src=\"img/ico/typing.png\" width=\"20\" height=\"20\" alt=\"NEW WORKUP REQUEST\" style=\"margin-top:3px;\"></a>";
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
		//	Str�nkov�n�
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
			document.getElementById(ID_span).innerHTML=\"skr�t\";
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