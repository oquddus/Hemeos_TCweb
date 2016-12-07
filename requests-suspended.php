<?
include('1-head.php');
?>


<?
//* =============================================================================
//	Operace s vybranymi
//============================================================================= */
if($_POST['operation']):

	$zpracovano=0;
	$nezpracovano=0;

	foreach($_POST as $klic=>$hodnota):
		$broken=explode("_",$klic);
		if($broken[0]=="cb" && $hodnota>0):
			
			/*
			if($_POST['operation']==1):	//to sent
				$vysledek = mysql_query("UPDATE search_request SET ID_stavu='1', datum_zpracovani='0', datum_odeslani='0', duvod_zamitnuti='' WHERE ID='".mysql_real_escape_string($hodnota)."' LIMIT 1");
			
				if($vysledek):
					$zpracovano++;
				else:
					$nezpracovano++;
				endif;
			endif;
			*/
			
			if($_POST['operation']==2):	//stopped
				//$vysledek = mysql_query("UPDATE search_request SET ID_stavu='5' WHERE ID='".mysql_real_escape_string($hodnota)."' AND PatientNum='0' LIMIT 1");	//ty bez PatientNum presun rovnou
				$vysledek2 = mysql_query("UPDATE search_request SET ID_stavu='6', datum_odeslani='0', datum_zpracovani='0', duvod_zmeny_stavu='".mysql_real_escape_string($_POST['duvod'])."', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($hodnota)."' AND PatientNum IS NOT NULL LIMIT 1"); //ty s PatientNum musi zadat o presun
				
				if($vysledek || $vysledek2):
					$zpracovano++;
				endif;
				if(!$vysledek && !$vysledek2):
					$nezpracovano++;
				endif;
			endif;
			
			if($_POST['operation']==3):	//to active
				$vysledek = mysql_query("UPDATE search_request SET ID_stavu='8', datum_zpracovani='0', datum_odeslani='0', duvod_zamitnuti='', duvod_zmeny_stavu='', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($hodnota)."' AND PatientNum IS NOT NULL LIMIT 1"); //zadost o presun do active
			
				if($vysledek):
					$zpracovano++;
				else:
					$nezpracovano++;
				endif;
			endif;

		endif;
	endforeach;
	
	message(1, $zpracovano." records have been processed", "", "");
	if($nezpracovano):
		message(1, $nezpracovano." records have not been processed!", "", "");
	endif;
endif;





//* =============================================================================
//	Filtrovací formuláø
//============================================================================= */
echo "<form method=\"GET\" action=\"$_SERVER[PHP_SELF]\" name=\"formular_hledej\" style=\"clear:both; margin-bottom:20px;\">";

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







//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND search_request.RegID='".$_SESSION['usr_RegID']."'";
	
	if($_SESSION['usr_InstID']):
		$where.=" AND search_request.InstID='".$_SESSION['usr_InstID']."'";
	endif;
endif;


//* =============================================================================
//	Stránkování
//============================================================================= */
strankovani("search_request LEFT JOIN search_request_stavy ON(search_request.ID_stavu=search_request_stavy.ID) WHERE search_request.ID_stavu IN(4) $where_hledej $where", $_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('search_request.ID_stavu DESC, search_request.ID DESC');


$counter=0;

	
//-----vypis dat
$vysledek = mysql_query("SELECT search_request.ID, RegID, ID_stavu, date_request, last_name, first_name, InstID, duvod_zamitnuti, PatientNum, search_request_stavy.stav 
FROM search_request LEFT JOIN search_request_stavy ON(search_request.ID_stavu=search_request_stavy.ID) 
WHERE search_request.ID_stavu IN(4) $where_hledej $where ORDER BY $orderby LIMIT $_od, $_na_stranku");
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
				<td width=\"13%\">".order('Request date', 'date_request')."</td>
				<td width=\"15%\">".order('Last name', 'last_name')."</td>
				<td width=\"15%\">".order('First name', 'first_name')."</td>
				<td width=\"13%\">".order('Patient ID', 'PatientNum')."</td>
				<td width=\"15%\">".order('Transplant center', 'InstID')."</td>
				<td width=\"20%\">".order('Status', 'ID_stavu')."</td>
				<td width=\"5%\">Action</td>
				<td width=\"4%\"></td>
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
				
				if($ID_stavu==3):
					$styl="class=\"barva5\" onmouseover=\"this.className='barva5b'\" onmouseout=\"this.className='barva5'\"";
				endif;
			
				$hash=sha1($ID."SaltIsGoodForLife45");
				
				echo "<tr ".$styl.">";
					echo "<td>".date($_SESSION['date_format_php'],$date_request)."</td>";
					echo "<td>$last_name</td>";
					echo "<td>$first_name</td>";
					echo "<td>".$RegID.$PatientNum."P</td>";
					echo "<td>$InstID</td>";
					echo "<td>$stav</td>";

					echo "<td>";
					echo "<a href=\"requests-vlozit.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a> &nbsp;&nbsp;";
					//echo "<a href=\"$_SERVER[PHP_SELF]?action=smazat&ID=$ID\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
					echo "</td>
					<td><input type=\"checkbox\" name=\"cb_$ID\" value=\"$ID\"></td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			
			echo "<div style=\"float:right; width:56px; margin-left:7px;\"><input type=\"submit\" class=\"form-ok\" value=\"\"></div>";
			
			echo "<div style=\"float:right; width:300px; text-align:right;\">Selected: ";
			echo "<select name=\"operation\" id=\"operation\" onchange=\"zmena_zadaneho_stavu();\">";
				//echo "<option value=\"1\""; if($operation==1): echo " selected"; endif; echo ">Move to sent requests</option>";
				echo "<option value=\"2\""; if($operation==2): echo " selected"; endif; echo ">Move to stopped</option>
				<option value=\"3\""; if($operation==3): echo " selected"; endif; echo ">Move to active</option>
			</select>";
			echo "</div>";
			
			echo "<div style=\"clear:right; float:right; width:500px; text-align:right; margin-top:5px;\" id=\"id_duvody\">Reason: ";
			echo "<select name=\"duvod\" id=\"duvod\" style=\"width:400px;\">";
				
				$vysledek_sub = mysql_query("SELECT ID AS ID_duvod, duvod FROM nastaveni_duvody_prepnuti ORDER BY ID");
					if(mysql_num_rows($vysledek_sub)>0):
						while($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
							extract($zaz_sub);
							
							echo "<option value=\"$ID_duvod\""; if($_POST['duvod']==$ID_duvod): echo " selected"; endif; echo ">$duvod</option>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_sub);
				
			echo "</select>";
			echo "</div>";
			
			
			
			
			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send_en\" value=\"\"></p>";

			
		echo "</form>";
		
		
		
		
		//* =============================================================================
		//	Stránkování
		//============================================================================= */
		strankovani2();
		
		
			
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
	
	
	
	function zmena_zadaneho_stavu(){
		if(document.getElementById('operation').value==\"2\"){
			document.getElementById('id_duvody').style.display=\"block\";
		}else{
			document.getElementById('id_duvody').style.display=\"none\";
		}
	}
	
	
	
	
			
</SCRIPT>";
?>			
			
			
			
<?
include('1-end.php');
?>