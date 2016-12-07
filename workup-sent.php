<?
include('1-head.php');
?>


<?
//* =============================================================================
//	Smazani zaznamu
//============================================================================= */
if($_GET['action']=="delete"):
	$SQL=mysql_query("DELETE FROM workup_request WHERE ID='".mysql_real_escape_string($_GET['ID'])."' LIMIT 1");
	if($SQL):
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
		mysql_real_escape_string($vyraz)=mysql_real_escape_string(strtolower2(trim($_GET['hledej'])));
		$where_hledej="AND (LOWER(search_request.last_name) LIKE 'mysql_real_escape_string($vyraz)%' OR LOWER(search_request.first_name) LIKE 'mysql_real_escape_string($vyraz)%')";
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
$where="";
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND workup_request.RegID='".mysql_real_escape_string($_SESSION['usr_RegID'])."'";

	if($_SESSION['usr_InstID']):
		$where.=" AND workup_request.InstID='".mysql_real_escape_string($_SESSION['usr_InstID'])."'";
	endif;
endif;


//* =============================================================================
//	Stránkování
//============================================================================= */
strankovani("workup_request", $_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('date_completing DESC, ID DESC');





$counter=0;


//-----vypis dat
$vysledek = mysql_query("SELECT ID, date_completing AS datum_request, patient_name, PatientNum, RegID, InstID,
	DonorID, ID_stavu, duvod
	FROM workup_request
	WHERE ID_stavu IN(0,2,1,3) $where
	ORDER BY $orderby LIMIT $_od, $_na_stranku");

	echo mysql_error();
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";

		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
			    <td width=\"10%\">".order('Req. date', 'date_completing')."</td>
				<td width=\"12%\">".order('Patient ID', 'PatientNum')."</td>
				<td width=\"18%\">".order('Patient Name', 'patient_name')."</td>
				<td width=\"15%\">".order('Donor ID', 'DonorID')."</td>
				<td width=\"36%\">Status</td>
				<td width=\"10%\">Action</td>
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
					/*
					//Pokud ma odpoved na donora
					$vysledek_sub = mysql_query("SELECT ID AS ID_typing_result FROM typing_result WHERE RegID='".mysql_real_escape_string($RegID)."' AND aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' ORDER BY datum_vlozeni DESC LIMIT 1");
						if(mysql_num_rows($vysledek_sub) > 0):
							$zaz_sub = mysql_fetch_array($vysledek_sub);
								extract($zaz_sub);

								$styl="class=\"barva6\" onmouseover=\"this.className='barva6b'\" onmouseout=\"this.className='barva6'\"";
								$jsou_vysledky=1;
						endif;
					@mysql_free_result($vysledek_sub);
					*/
				endif;


				echo "<tr ".$styl.">";
					echo "<td>".date($_SESSION['date_format_php'],$datum_request)."</td>";
					echo "<td>".$RegID.$PatientNum."P</td>";
					echo "<td>$patient_name</td>";
					echo "<td>$DonorID</td>";
					echo "<td>";

						if($ID_stavu==0):
							echo "Waiting for send";
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
							else:
								echo "Without result";
							endif;
						endif;



					echo "</td>";

					echo "<td>";
					echo "<a href=\"workup-request.php?ID=$ID\" title=\"VIEW\">".$ico_upravit."</a> &nbsp;&nbsp;";

					if($jsou_vysledky):
						$hash=sha1($ID_typing_result."SaltIsGoodForLife45");
						echo "&nbsp;<a href=\"donor-result.php?ID=$ID_typing_result&amp;h=".$hash."\" title=\"RESULT\"><img src=\"img/ico/result.png\" width=\"23\" height=\"20\" alt=\"RESULT\" style=\"margin-top:3px;\"></a>";
					endif;


					if($ID_stavu==0 || $ID_stavu==2):
						echo "<a href=\"$_SERVER[PHP_SELF]?action=delete&amp;ID=$ID&amp;h=".$hash."\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
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