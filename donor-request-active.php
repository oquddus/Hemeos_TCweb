<?
include('1-head.php');
include('1-config.php');
?>


<?
//* =============================================================================
//	Smazani zaznamu
//============================================================================= */
if($_GET['action']=="delete_typing" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")):
	$SQL=mysql_query("DELETE FROM typing_request_donor WHERE ID_typing='".clean_high($_GET['ID'])."' AND DonorNumber='".clean_high($_GET['DonorNum'])."'");
	if(mysql_affected_rows()>0):
		message(1, "Item deleted", "", "");
	else:
		message(1, "Delete failed", "", "");
	endif;
endif;


if($_GET['action']=="delete_sample" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")):
	$SQL=mysql_query("DELETE FROM sample_request_donor WHERE ID_sample='".clean_high($_GET['ID'])."' AND DonorID='".clean_high($_GET['DonorID'])."' LIMIT 1");
	if(mysql_affected_rows()>0):
		message(1, "Item deleted", "", "");
	else:
		message(1, "Delete failed", "", "");
	endif;
endif;


if($_GET['action']=="delete_workup" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")):
	$SQL=mysql_query("DELETE FROM workup_request WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
	if(mysql_affected_rows()>0):
		message(1, "Item deleted", "", "");
	else:
		message(1, "Delete failed", "", "");
	endif;
endif;

//* =============================================================================
//	Filtrovací formuláø
//============================================================================= */
/*
echo "<form method=\"GET\" action=\"$_SERVER[PHP_SELF]\" name=\"formular_hledej\" style=\"clear:both; margin-bottom:20px;\">";

	$vysledek = mysql_query("SELECT last_name, first_name FROM search_request WHERE search_request.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' LIMIT 1");
		if(mysql_num_rows($vysledek) > 0):
			$zaz = mysql_fetch_array($vysledek);
				extract($zaz);
		endif;
	@mysql_free_result($vysledek);

	echo "<div style=\"float:left; width:100%; margin-bottom:19px; text-align:left; font-size:16px;\">Donors for patient: <b>$last_name $first_name</b>, PatientNum: <b>".$_GET['PatientNum']."</b></div>";
*/
	//--vyhledavani
	/*
	echo "<div style=\"float:left; width:100%; margin-top:-10px; margin-bottom:19px;\">
		<div class=\"hledej-text\"></div>
		<div class=\"hledej-input\">Search: &nbsp;<input type=\"text\" name=\"hledej\" style=\"width:150px; height:17px;\" value=\"".$_GET['hledej']."\">
		<input type=\"image\" src=\"img/ico/lupa.gif\" value=\"hledej\" style=\"position: relative;top:4px\"></div>
	</div>";
	echo "<input type=\"hidden\" name=\"action\" value=\"search\">";
	*/

	//$form_name="formular_hledej";

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

//echo "</form>";

//	konec filtrovaciho formulare
//=============================================================================




//* =============================================================================
//	Filtr dle voleb ve filtrovacim kalendari
//============================================================================= */
/*
if($_GET['action']=="search"):
	if($_GET['hledej']):
		$vyraz=mysql_real_escape_string(strtolower2(trim($_GET['hledej'])));
		$where_hledej="AND (LOWER(search_request.last_name) LIKE '$vyraz%' OR LOWER(search_request.first_name) LIKE '$vyraz%')";
	endif;
endif;
*/

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









//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND typing_request.RegID='".clean_high($_SESSION['usr_RegID'])."'";
	$where2.=" AND sample_request.RegID='".clean_high($_SESSION['usr_RegID'])."'";
	$where3.=" AND workup_request.RegID='".clean_high($_SESSION['usr_RegID'])."'";

	if($_SESSION['usr_InstID']):
		$where.=" AND typing_request.InstID='".clean_high($_SESSION['usr_InstID'])."'";
		$where2.=" AND sample_request.InstID='".clean_high($_SESSION['usr_InstID'])."'";
		$where3.=" AND workup_request.InstID='".clean_high($_SESSION['usr_InstID'])."'";
	endif;
endif;


//* =============================================================================
//	Stránkování
//============================================================================= */
strankovani3("SELECT 1 AS druh
	FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID)
	WHERE ID_stavu IN(0,2,1,3) $where
	GROUP BY DonorNumber, ID_typing

	UNION ALL
		(SELECT 2 AS druh
		FROM sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID)
		WHERE sample_request_donor.ID_stavu IN(0,2,1,3) $where2)

	UNION ALL
		(SELECT 3 AS druh
		FROM workup_request
		WHERE workup_request.ID_stavu IN(0,2,1,3) $where3)
	",$_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('datum_request DESC, ID DESC');





$counter=0;


//-----vypis dat
$vysledek = mysql_query("SELECT 1 AS druh, typing_request.ID, typing_request.date_completing AS datum_request, typing_request.patient_name, typing_request.PatientNum, typing_request.diagnosis, typing_request.RegID, typing_request.InstID, 0 AS smp_arr_date,
	typing_request_donor.DonorID, typing_request_donor.ID_stavu, typing_request_donor.duvod, typing_request_donor.DonorNumber, typing_request_donor.aLogMsgNum
	FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID)
	WHERE ID_stavu IN(0,2,1,3) $where
	GROUP BY DonorNumber, ID_typing

	UNION ALL
		(SELECT 2 AS druh, sample_request.ID, sample_request.date_completing AS datum_request, sample_request.patient_name, sample_request.PatientNum, 0 AS diagnosis, sample_request.RegID, sample_request.InstID, sample_request.smp_arr_date,
		sample_request_donor.DonorID, sample_request_donor.ID_stavu, sample_request_donor.duvod, sample_request_donor.DonorNumber, sample_request_donor.aLogMsgNum
		FROM sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID)
		WHERE sample_request_donor.ID_stavu IN(0,2,1,3) $where2)

	UNION ALL
		(SELECT 3 AS druh, workup_request.ID, workup_request.date_completing AS datum_request, workup_request.patient_name, workup_request.PatientNum, 0 AS diagnosis, workup_request.RegID, workup_request.InstID, 0 AS smp_arr_date,
		workup_request.DonorID, workup_request.ID_stavu, workup_request.duvod, 0 AS DonorNumber, workup_request.aLogMsgNum
		FROM workup_request
		WHERE workup_request.ID_stavu IN(0,2,1,3) $where3)

	ORDER BY ".clean_high($orderby)." LIMIT ".clean_high($_od).", ".clean_high($_na_stranku)."");

	if ($DEBUG_CFG==1)echo mysql_error();
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";

		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
			    <td width=\"9%\">".order('Req. date', 'datum_request')."</td>
				<td width=\"9%\">".order('Patient ID', 'PatientNum')."</td>
				<td width=\"10%\">".order('Patient Name', 'patient_name')."</td>
				<td width=\"8%\">".order('Donor ID', 'DonorID')."</td>
				<td width=\"21%\">Diagnosis</td>
				<td width=\"10%\">Category</td>
				<td width=\"23%\">Status</td>
				<td width=\"12%\">Action</td>
			  </tr>
			</thead>
			<tbody id=\"tb-body\">";



			while($zaz = mysql_fetch_array($vysledek)){

				extract($zaz);

				++$counter;


				if($counter%2==0):
					$styl="class=\"barva1\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva1'\"";
				else:
					$styl="class=\"barva2\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva2'\"";
				endif;


				unset($jsou_vysledky);

				if($ID_stavu==3):
					$styl="class=\"barva5\" onmouseover=\"this.className='barva5b'\" onmouseout=\"this.className='barva5'\"";
				endif;
				if($ID_stavu==2):
					$styl="class=\"barva5\" onmouseover=\"this.className='barva5b'\" onmouseout=\"this.className='barva5'\"";
				endif;
				if($ID_stavu==1):
					if($druh==1):	//Typing
						//Pokud ma odpoved na donora
						$vysledek_sub = mysql_query("SELECT ID AS ID_typing_result FROM typing_result WHERE RegID='".clean_high($RegID)."' AND aLogMsgNum='".clean_basic($aLogMsgNum)."' ORDER BY datum_vlozeni DESC LIMIT 1");
						if ($DEBUG_CFG==1)echo mysql_error();
							if(mysql_num_rows($vysledek_sub) > 0):
								$zaz_sub = mysql_fetch_array($vysledek_sub);
									extract($zaz_sub);

									$styl="class=\"barva6\" onmouseover=\"this.className='barva6b'\" onmouseout=\"this.className='barva6'\"";
									$jsou_vysledky=1;
							endif;
						@mysql_free_result($vysledek_sub);
					endif;
				endif;


				//docist diagnozu pokud sample request
				if($druh==2 || $druh==3):	//Sample, Workup
					unset($diagnosis);
					$vysledek_sub = mysql_query("SELECT nastaveni_diagnoza.diagnoza AS diagnosis FROM search_request LEFT JOIN nastaveni_diagnoza ON(search_request.diagnosis=nastaveni_diagnoza.ID)
					WHERE PatientNum='".clean_high($PatientNum)."' AND RegID='".clean_high($RegID)."' AND InstID='".clean_high($InstID)."' ORDER BY datum_vlozeni DESC LIMIT 1");
						if(mysql_num_rows($vysledek_sub) > 0):
							$zaz_sub = mysql_fetch_array($vysledek_sub);
								extract($zaz_sub);
						endif;
					@mysql_free_result($vysledek_sub);
				endif;

				//pokud je sample arrival date
				if($smp_arr_date>0):
					$styl="class=\"barva7\" onmouseover=\"this.className='barva7b'\" onmouseout=\"this.className='barva7'\"";
				endif;

				//Je response?
				$jsou_vysledky_response = mysql_result(mysql_query("SELECT COUNT(request_response.ID) FROM request_response
				WHERE request_response.RegID='".clean_high($RegID)."' AND request_response.RequestID='".clean_high($ID)."'
				AND request_response.RequestType='".mysql_real_escape_string($druh)."'
				"), 0);

				echo "<tr ".$styl.">";
					echo "<td>".date($_SESSION['date_format_php'],$datum_request)."</td>";
					echo "<td>".$RegID.$PatientNum."P</td>";
					echo "<td>$patient_name</td>";
					echo "<td>$DonorID</td>";
					echo "<td>$diagnosis</td>";
					echo "<td>";
						if($druh==1):	//Typing
							echo "Typing Req.";
						endif;
						if($druh==2):	//sample
							echo "CT Req.";
						endif;
						if($druh==3):	//worku
							echo "Workup Req.";
						endif;
					echo "</td>";
					echo "<td>";

						if($ID_stavu==0):
							echo "In Progress";
						endif;

						if($ID_stavu==2):
							echo "Denied";
							if($duvod):
								echo " - ".$duvod;
							endif;
						endif;

						if($ID_stavu==3):
							echo "Denied";
							if($duvod):
								echo " - ".$duvod;
							endif;
						endif;

						if($ID_stavu==1):
							if($jsou_vysledky):
								echo "Result";
							elseif($jsou_vysledky_response):
								echo "Response";
							else:
								echo "Complete: check email for results";
							endif;
						endif;

						if($smp_arr_date>0):
							echo ", arrival: ".date($_SESSION['date_format_php'],$smp_arr_date);
						endif;


					echo "</td>";

					echo "<td>";


					$hash=sha1($ID."SaltIsGoodForLife45");

					if($druh==1):	//Typing
						echo "<a href=\"donor-typing-request.php?ID=$ID&amp;DonorNum=$DonorNumber&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a> &nbsp;&nbsp;";
					endif;
					if($druh==2):	//sample
						echo "<a href=\"donor-sample-request.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a> &nbsp;&nbsp;";
					endif;
					if($druh==3):	//workup
						echo "<a href=\"donor-workup-request.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a> &nbsp;&nbsp;";
					endif;


					if($jsou_vysledky):
						$h=sha1($ID_typing_result."SaltIsGoodForLife45");
						echo "&nbsp;<a href=\"donor-result.php?ID=$ID_typing_result&amp;h=".$h."\" title=\"RESULT\"><img src=\"img/ico/result.png\" width=\"23\" height=\"20\" alt=\"RESULT\" style=\"margin-top:3px;\"></a>";
					endif;



					if($jsou_vysledky_response):
						$h=sha1($ID.$druh."SolitJeDobre5477");
						echo "&nbsp;<a href=\"donor-response.php?ID=".$ID."&amp;d=".$druh."&amp;h=".$h."\" title=\"RESPONSE\"><img src=\"img/ico/result.png\" width=\"23\" height=\"20\" alt=\"RESPONSE\" style=\"margin-top:3px;\"></a>";
					endif;


					if($ID_stavu==0 || $ID_stavu==2):
						if($druh==1):	//Typing
							echo "<a href=\"$_SERVER[PHP_SELF]?action=delete_typing&amp;ID=$ID&amp;DonorNum=$DonorNumber&amp;h=".$hash."\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
						endif;
						if($druh==2):	//sample
							echo "<a href=\"$_SERVER[PHP_SELF]?action=delete_sample&amp;ID=$ID&amp;DonorID=$DonorID&amp;h=".$hash."\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
						endif;
						if($druh==3):	//workup
							echo "<a href=\"$_SERVER[PHP_SELF]?action=delete_workup&amp;ID=$ID&amp;h=".$hash."\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
						endif;

					endif;

					//echo "<a href=\"donor-typing-request.php?Pn=$PatientNum&amp;Dn=$ID2\" title=\"Typing request\">T</a> &nbsp;&nbsp;";
					//echo "<a href=\"donor-sample-request.php?Pn=$PatientNum&amp;Dn=$ID2\" title=\"Sample request\">S</a>";
					//echo "&nbsp;&nbsp; <input type=\"checkbox\" name=\"cb_$ID\" value=\"$PatientNum|$ID2\">";
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

			*/

			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send\" value=\"\"></p>";


		echo "</form>";




		//* =============================================================================
		//	Stránkování
		//============================================================================= */
		strankovani2();



	else:
		message(1, "There are no records.", "", "");
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