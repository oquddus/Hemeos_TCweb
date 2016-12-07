<?
include('1-head.php');
?>


<?
//vicet reason2 ?
$videt_reason2 = mysql_result(mysql_query("SELECT COUNT(ID) FROM transplant_centers WHERE reason2='1' AND ID='".clean_high($_SESSION['usr_ID_centra'])."'"), 0);

//* =============================================================================
//	Smazani zaznamu
//============================================================================= */
if($_GET['action']=="delete" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45") ):
	$SQL=mysql_query("DELETE FROM search_request WHERE ID='".clean_high($_GET['ID'])."' AND PatientNum IS NULL LIMIT 1");
	if(mysql_affected_rows()>0):
		message(1, "Item deleted", "", "");
	else:
		message(1, "Delete failed", "", "");
	endif;
endif;



//* =============================================================================
//	Operace s vybranymi
//============================================================================= */
if($_POST['operation']):

	$zpracovano=0;
	$nezpracovano=0;

	$citac_f=1;
	foreach($_POST as $klic=>$hodnota):
		$broken=explode("_",$klic);
		if($broken[0]=="cb" && $hodnota>0):
			
			if($_POST['operation']==1):	//suspended
				//$vysledek = mysql_query("UPDATE search_request SET ID_stavu='4' WHERE ID='".mysql_real_escape_string($hodnota)."' AND PatientNum='0' LIMIT 1"); //ty bez PatientNum presun rovnou
				$vysledek2 = mysql_query("UPDATE search_request SET ID_stavu='7', datum_odeslani='0', datum_zpracovani='0', duvod_zmeny_stavu='".clean_high($_POST['duvod'])."' WHERE ID='".clean_high($hodnota)."' AND PatientNum IS NOT NULL LIMIT 1"); //ty s PatientNum musi zadat o presun

				if($vysledek || $vysledek2):
					$zpracovano++;
				endif;
				if(!$vysledek && !$vysledek2):
					$nezpracovano++;
				endif;
			endif;

			if($_POST['operation']==2):	//stopped
				if($videt_reason2):
					$duvod_name="duvod2";
				else:
					$duvod_name="duvod";
				endif;
				//$vysledek = mysql_query("UPDATE search_request SET ID_stavu='5' WHERE ID='".mysql_real_escape_string($hodnota)."' AND PatientNum='0' LIMIT 1");	//ty bez PatientNum presun rovnou
				$vysledek2 = mysql_query("UPDATE search_request SET ID_stavu='6', datum_odeslani='0', datum_zpracovani='0', duvod_zmeny_stavu='".clean_high($_POST[$duvod_name])."' WHERE ID='".clean_high($hodnota)."' AND PatientNum IS NOT NULL LIMIT 1"); //ty s PatientNum musi zadat o presun


				//ulozit reasony
				mysql_query("DELETE FROM reasons_transplanted WHERE ID_request='".clean_high($hodnota)."' LIMIT 1");
				mysql_query("DELETE FROM reasons_died WHERE ID_request='".clean_high($hodnota)."' LIMIT 1");
				mysql_query("DELETE FROM reasons_nodonor WHERE ID_request='".clean_high($hodnota)."' LIMIT 1");
				mysql_query("DELETE FROM reasons_other WHERE ID_request='".clean_high($hodnota)."' LIMIT 1");

				if($videt_reason2):
					if($_POST['duvod2']==20):	//transplanted

						if($citac_f==1):
							get_timestamp('trans_date0');
							get_timestamp('trans_date1');
							get_timestamp('trans_date2');
						endif;

						if(!$_POST['complications']):
							unset($_POST['reported_igz'], $_POST['unexpected'], $_POST['gravity'], $_POST['don_product'], $_POST['complicaties_text']);
							unset($_POST['donor_info']);
						endif;

						mysql_query("INSERT INTO reasons_transplanted (ID_request,
						product_bm, product_pbsc, product_dli, product_double, product_single,
						trans_donor_id, trans_date0, trans_cbid1, trans_cbid2, trans_date1, trans_date2, trans_problem1, trans_problem2, complications,
						reported_igz, unexpected, gravity, don_product, complicaties_text, donor_info
						) VALUES
						('".clean_basic($hodnota)."',
						'".clean_basic($_POST['product_bm'])."',
						'".clean_basic($_POST['product_pbsc'])."',
						'".clean_basic($_POST['product_dli'])."',
						'".clean_basic($_POST['product_double'])."',
						'".clean_basic($_POST['product_single'])."',
						'".clean_basic($_POST['trans_donor_id'])."',
						'".clean_basic($_POST['trans_date0'])."',
						'".clean_basic($_POST['trans_cbid1'])."',
						'".clean_basic($_POST['trans_cbid2'])."',
						'".clean_basic($_POST['trans_date1'])."',
						'".clean_basic($_POST['trans_date2'])."',
						'".clean_basic($_POST['trans_problem1'])."',
						'".clean_basic($_POST['trans_problem2'])."',
						'".clean_basic($_POST['complications'])."',
						'".clean_basic($_POST['reported_igz'])."',
						'".clean_basic($_POST['unexpected'])."',
						'".clean_basic($_POST['gravity'])."',
						'".clean_basic($_POST['don_product'])."',
						'".clean_basic($_POST['complicaties_text'])."',
						'".clean_basic($_POST['donor_info'])."'
						)
						ON DUPLICATE KEY UPDATE
							product_bm='".clean_basic($_POST['product_bm'])."',
							product_pbsc='".clean_basic($_POST['product_pbsc'])."',
							product_dli='".clean_basic($_POST['product_dli'])."',
							product_double='".clean_basic($_POST['product_double'])."',
							product_single='".clean_basic($_POST['product_single'])."',
							trans_donor_id='".clean_basic($_POST['trans_donor_id'])."',
							trans_date0='".clean_basic($_POST['trans_date0'])."',
							trans_cbid1='".clean_basic($_POST['trans_cbid1'])."',
							trans_cbid2='".clean_basic($_POST['trans_cbid2'])."',
							trans_date1='".clean_basic($_POST['trans_date1'])."',
							trans_date2='".clean_basic($_POST['trans_date2'])."',
							trans_problem1='".clean_basic($_POST['trans_problem1'])."',
							trans_problem2='".clean_basic($_POST['trans_problem2'])."',
							complications='".clean_basic($_POST['complications'])."',
							reported_igz='".clean_basic($_POST['reported_igz'])."',
							unexpected='".clean_basic($_POST['unexpected'])."',
							gravity='".clean_basic($_POST['gravity'])."',
							don_product='".clean_basic($_POST['don_product'])."',
							complicaties_text='".clean_basic($_POST['complicaties_text'])."',
							donor_info='".clean_basic($_POST['donor_info'])."'
						");

					endif;

					if($_POST['duvod2']==21):	//died

						if($citac_f==1):
							get_timestamp('die_date');
						endif;

						mysql_query("INSERT INTO reasons_died (ID_request, die_date, die_reason) VALUES
						('".clean_basic($hodnota)."',
						'".clean_basic($_POST['die_date'])."',
						'".clean_basic($_POST['die_reason'])."'
						)
						ON DUPLICATE KEY UPDATE
							die_date='".clean_basic($_POST['die_date'])."',
							die_reason='".clean_basic($_POST['die_reason'])."'
						");
					endif;

					if($_POST['duvod2']==23):	//no donor

						if($_POST['nodonor_transplant']==1):
							unset($_POST['nodonor_text']);
						endif;

						mysql_query("INSERT INTO reasons_nodonor (ID_request, nodonor_transplant, nodonor_text) VALUES
						('".clean_basic($hodnota)."',
						'".clean_basic($_POST['nodonor_transplant'])."',
						'".clean_basic($_POST['nodonor_text'])."'
						)
						ON DUPLICATE KEY UPDATE
							nodonor_transplant='".clean_basic($_POST['nodonor_transplant'])."',
							nodonor_text='".clean_basic($_POST['nodonor_text'])."'
						");
					endif;

					if($_POST['duvod2']==24):	//other

						mysql_query("INSERT INTO reasons_other (ID_request, other_text) VALUES
						('".clean_basic($hodnota)."',
						'".clean_basic($_POST['other_text'])."'
						)
						ON DUPLICATE KEY UPDATE
							other_text='".clean_basic($_POST['other_text'])."'
						");
					endif;

				endif;



				if($vysledek || $vysledek2):
					$zpracovano++;
				endif;
				if(!$vysledek && !$vysledek2):
					$nezpracovano++;
				endif;
			endif;

			if($_POST['operation']==3):	//to active
				$vysledek = mysql_query("UPDATE search_request SET ID_stavu='8', datum_zpracovani='0', datum_odeslani='0', duvod_zamitnuti='', duvod_zmeny_stavu='' WHERE ID='".clean_high($hodnota)."' AND PatientNum IS NOT NULL LIMIT 1"); //zadost o presun do active

				if($vysledek):
					$zpracovano++;
				else:
					$nezpracovano++;
				endif;
			endif;



		endif;

		$citac_f++;
	endforeach;

	message(1, $zpracovano." records have been processed", "", "");
	if($nezpracovano):
		message(1, $nezpracovano." records have not been processed!", "", "");
	endif;
endif;





//* =============================================================================
//	Filtrovac� formul��
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
		$vyraz=clean_basic(strtolower2(trim($_GET['hledej'])));
		$where_hledej="AND (LOWER(search_request.last_name) LIKE '%$vyraz%' OR LOWER(search_request.first_name) LIKE '%$vyraz%' OR search_request.ID='".$vyraz."')";
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
$where="search_request.ID_stavu IN(1,3,6,7,8)";

//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
if($_SESSION['usr_ID_role']!=1):
	$where.=" AND search_request.RegID='".clean_high($_SESSION['usr_RegID'])."'";
	
	if($_SESSION['usr_InstID']):
		$where.=" AND search_request.InstID='".clean_high($_SESSION['usr_InstID'])."'";
	endif;
endif;

//* =============================================================================
//	Str�nkov�n�
//============================================================================= */
strankovani("search_request LEFT JOIN search_request_stavy ON(search_request.ID_stavu=search_request_stavy.ID) WHERE $where $where_hledej", $_na_stranku);



//* =============================================================================
//	Defaultn� �azen�
//============================================================================= */
razeni('search_request.ID_stavu DESC, search_request.ID DESC');


$counter=0;

	
//-----vypis dat

$vysledek = mysql_query("SELECT search_request.ID, RegID, ID_stavu, date_request, last_name, first_name, InstID, duvod_zamitnuti, search_request.PatientNum, search_request_stavy.stav, search_request_stavy.upresneni FROM search_request LEFT JOIN search_request_stavy ON(search_request.ID_stavu=search_request_stavy.ID) WHERE $where $where_hledej ORDER BY $orderby LIMIT $_od, $_na_stranku");
//echo("SELECT search_request.ID, RegID, ID_stavu, date_request, last_name, first_name, InstID, duvod_zamitnuti, search_request.PatientNum, search_request_stavy.stav, search_request_stavy.upresneni FROM search_request LEFT JOIN search_request_stavy ON(search_request.ID_stavu=search_request_stavy.ID) WHERE $where $where_hledej ORDER BY $orderby LIMIT $_od, $_na_stranku");
if ($DEBUG_CFG==1)echo mysql_error();
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>";
				echo "<td width=\"5%\">".order('IntID', 'ID')."</td>";
				echo "<td width=\"12%\">".order('Request date', 'date_request')."</td>
				<td width=\"14%\">".order('Last name', 'last_name')."</td>
				<td width=\"14%\">".order('First name', 'first_name')."</td>
				<td width=\"10%\">".order('Patient ID', 'PatientNum')."</td>
				<td width=\"15%\">".order('Transplant center', 'InstID')."</td>
				<td width=\"20%\">".order('Status', 'ID_stavu')."</td>
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
					echo "<td>"; if($date_request>0): echo date($_SESSION['date_format_php'],$date_request); endif; echo "</td>";
					echo "<td>$last_name</td>";
					echo "<td>$first_name</td>";
					echo "<td>"; if($PatientNum>0): echo $RegID.$PatientNum."P"; else: echo "N/A"; endif; echo "</td>";
					echo "<td>$InstID</td>";
					echo "<td>$stav";
						
						if($ID_stavu==3): echo ": ".$duvod_zamitnuti; endif;
						if($upresneni): echo ", ".$upresneni; endif;
						
					echo "</td>";

					echo "<td>";
					echo "<a href=\"requests-vlozit.php?ID=$ID&amp;h=".$hash."\" title=\""; if($PatientNum>0): echo "VIEW"; else: echo "EDIT"; endif; echo "\">".$ico_upravit."</a>&nbsp;";
					if($PatientNum==0):
						echo "<a href=\"$_SERVER[PHP_SELF]?action=delete&amp;ID=$ID&amp;h=".$hash."\" title=\"DELETE\" onclick=\"return potvrd('Do you really remove this item?')\">".$ico_smazat."</a>";
					endif;
					echo "</td>
					<td>";
					if($PatientNum>0):
						echo "<input type=\"checkbox\" name=\"cb_$ID\" value=\"$ID\">";
					else:
						echo "<input type=\"checkbox\" name=\"cb_$ID\" value=\"\" disabled>";
					endif;
					echo "</td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			
			echo "<div style=\"float:right; width:56px; margin-left:7px;\"><input type=\"submit\" class=\"form-ok\" value=\"\"></div>";
			
			echo "<div style=\"float:right; width:600px; text-align:right;\">Selected: ";
			echo "<select name=\"operation\" id=\"operation\">
				<option value=\"1\""; if($operation==1): echo " selected"; endif; echo ">Move to suspended</option>
				<option value=\"2\""; if($operation==2): echo " selected"; endif; echo ">Move to stopped</option>
				<option value=\"3\""; if($operation==3): echo " selected"; endif; echo ">Move to active</option>
			</select>";
			echo "</div>";
			
			echo "<div style=\"clear:right; float:right; width:500px; text-align:right; margin-top:5px;\" id=\"id_duvody\">Reason: ";
			echo "<select name=\"duvod\" id=\"duvod\" style=\"width:400px;\">";
				
				$vysledek_sub = mysql_query("SELECT ID AS ID_duvod, duvod FROM nastaveni_duvody_prepnuti WHERE ID<='19' ORDER BY ID");
					if(mysql_num_rows($vysledek_sub)>0):
						while($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
							extract($zaz_sub);
							
							echo "<option value=\"$ID_duvod\""; if($_POST['duvod']==$ID_duvod): echo " selected"; endif; echo ">$duvod</option>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_sub);
				
			echo "</select>";
			echo "</div>";
			
			
			echo "<div style=\"clear:right; float:right; width:500px; text-align:right; margin-top:5px; display:none;\" id=\"id_duvody2\">Reason: ";
			echo "<select name=\"duvod2\" id=\"duvod2\" style=\"width:400px;\">";
				
				$vysledek_sub = mysql_query("SELECT ID AS ID_duvod, duvod FROM nastaveni_duvody_prepnuti WHERE ID>=20 AND ID<=29 ORDER BY poradi");
					if(mysql_num_rows($vysledek_sub)>0):
						while($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
							extract($zaz_sub);
							
							echo "<option value=\"$ID_duvod\""; if($_POST['duvod2']==$ID_duvod): echo " selected"; endif; echo ">$duvod</option>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_sub);
				
			echo "</select>";
			echo "</div>";
			
			
			echo "<div style=\"clear:right; float:right; width:100%; text-align:left; margin-top:5px; display:none;\" id=\"id_duvody2_form\">";
			echo "</div>";
			
			
			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send_en\" value=\"\"></p>";

			
		echo "</form>";
		
		
		
		
		//* =============================================================================
		//	Str�nkov�n�
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
			document.getElementById(ID_span).innerHTML=\"skr�t\";
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