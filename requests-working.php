<?
include('1-head.php');
?>


<?
//vicet reason2 ?
$videt_reason2 = mysql_result(mysql_query("SELECT COUNT(ID) FROM transplant_centers WHERE reason2='1' AND ID='".mysql_real_escape_string($_SESSION['usr_ID_centra'])."'"), 0);

//* =============================================================================
//	Smazani zaznamu
//============================================================================= */
if($_GET['action']=="delete" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45") ):
	$SQL=mysql_query("DELETE FROM search_request_tmp WHERE ID='".mysql_real_escape_string($_GET['ID'])."' AND (PatientNum IS NULL OR PatientNum='0') LIMIT 1");
	if(mysql_affected_rows()>0):
		message(1, "Item deleted", "", "");
	else:
		message(1, "Delete failed", "", "");
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
		$where_hledej="AND (LOWER(search_request_tmp.last_name) LIKE '%$vyraz%' OR LOWER(search_request_tmp.first_name) LIKE '%$vyraz%' OR search_request_tmp.ID='".$vyraz."')";
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
//	Defaulni WHERE
//============================================================================= */
$where="search_request_tmp.ID>0";

//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND search_request_tmp.RegID='".$_SESSION['usr_RegID']."'";
	
	if($_SESSION['usr_InstID']):
		$where.=" AND search_request_tmp.InstID='".$_SESSION['usr_InstID']."'";
	endif;
endif;

//* =============================================================================
//	Stránkování
//============================================================================= */
strankovani("search_request_tmp WHERE $where $where_hledej", $_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('search_request_tmp.ID DESC');


$counter=0;

	
//-----vypis dat
$vysledek = mysql_query("SELECT search_request_tmp.ID, RegID, ID_stavu, date_request, last_name, first_name, InstID, datum_editace, date_birth, patient_id, duvod_zamitnuti, search_request_tmp.PatientNum  
FROM search_request_tmp WHERE $where $where_hledej ORDER BY $orderby LIMIT $_od, $_na_stranku");
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>";
				echo "<td width=\"5%\">".order('IntID', 'ID')."</td>";
				echo "<td width=\"12%\">".order('Date of update', 'datum_editace')."</td>
				<td width=\"19%\">".order('Last name', 'last_name')."</td>
				<td width=\"19%\">".order('First name', 'first_name')."</td>
				<td width=\"15%\">".order('Date of birth', 'date_birth')."</td>
				<td width=\"20%\">".order('Hospital patient ID', 'patient_id')."</td>
				<td width=\"8%\">Action</td>
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
					echo "<td>$ID</td>";
					echo "<td>"; if($datum_editace>0): echo date($_SESSION['date_format_php']." H:i",$datum_editace); endif; echo "</td>";
					echo "<td>$last_name</td>";
					echo "<td>$first_name</td>";
					echo "<td>"; if($date_birth<>0): echo date($_SESSION['date_format_php'],$date_birth); endif; echo "</td>";
					echo "<td>$patient_id</td>";

					echo "<td>";
					echo "<a href=\"requests-vlozit.php?ID=$ID&amp;w=1&amp;h=".$hash."\" title=\""; if($PatientNum>0): echo "VIEW"; else: echo "EDIT"; endif; echo "\">".$ico_upravit."</a>&nbsp;";
					if($PatientNum==0):
						echo "<a href=\"$_SERVER[PHP_SELF]?action=delete&amp;ID=$ID&amp;h=".$hash."\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
					endif;
					echo "</td>
					<td>";
					
					echo "</td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			
			
			
			
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
	
	
	
	$(document).ready(function(){
		
		
		$('select#operation').change(function(){
			if( $('#operation').val()=='1' ){
				$('#id_duvody').show();
				$('#id_duvody2').hide();
				$('#id_duvody2_form').hide();
			}
			if( $('#operation').val()=='2' ){
				";
				if($videt_reason2):
					echo "
					$('#id_duvody2').show();
					$('#id_duvody').hide();
					nacti_reason2_form();
					";
				else:
					echo "
					$('#id_duvody').show();
					$('#id_duvody2').hide();
					$('#id_duvody2_form').hide();
					";
				endif;
				echo "
			}
			if( $('#operation').val()=='3' ){
				$('#id_duvody').hide();
				$('#id_duvody2').hide();
				$('#id_duvody2_form').hide();
			}
		});
		
		
		$('select#duvod2').change(function(){
			nacti_reason2_form();
		});
		
		$('input#complications').live('change', function(){
			if($(this).is(':checked')){
				$('#tb-form4').show();
			}else{
				$('#tb-form4').hide();
			}
		});
		
		$(\"input[name='nodonor_transplant']\" ).live('change', function(){
			if($('#nodonor_transplant1').is(':checked')){
				$('#nodonor_comm').hide();
			}else{
				$('#nodonor_comm').show();
			}
		});
		
	});
	
	
	function nacti_reason2_form(){
	
		//nacist formular
		$.ajax({
		url: 'include/ajax-reason2-form.php?ID_reason='+$('select#duvod2').val(),
		async: true,
		}).done(function( data ){
			//console.log(data);
			$('div#id_duvody2_form').html(data);
			$('#id_duvody2_form').show();
		}).fail(function() {
		}).always(function() {
		});
	
	}
	
	
			
</SCRIPT>";
?>			
			
			
			
<?
include('1-end.php');
?>