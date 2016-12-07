<?
include('1-head.php');
?>


<?
//vicet reason2 ?
$videt_reason2 = mysql_result(mysql_query("SELECT COUNT(ID) FROM transplant_centers WHERE reason2='1' AND ID='".mysql_real_escape_string($_SESSION['usr_ID_centra'])."'"), 0);

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
				$vysledek = mysql_query("UPDATE search_request SET ID_stavu='7', datum_odeslani='0', datum_zpracovani='0', duvod_zmeny_stavu='".mysql_real_escape_string($_POST['duvod'])."', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($hodnota)."' LIMIT 1"); //ty z active musi zadat o presun
			endif;
			
			if($_POST['operation']==2):	//stopped
				if($videt_reason2):
					$duvod_name="duvod2";
				else:
					$duvod_name="duvod";
				endif;
				$vysledek = mysql_query("UPDATE search_request SET ID_stavu='6', datum_odeslani='0', datum_zpracovani='0', duvod_zmeny_stavu='".mysql_real_escape_string($_POST[$duvod_name])."', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($hodnota)."' LIMIT 1"); //ty z active musi zadat o presun
				
				//ulozit reasony
				mysql_query("DELETE FROM reasons_transplanted WHERE ID_request='".mysql_real_escape_string($hodnota)."' LIMIT 1");
				mysql_query("DELETE FROM reasons_died WHERE ID_request='".mysql_real_escape_string($hodnota)."' LIMIT 1");
				mysql_query("DELETE FROM reasons_nodonor WHERE ID_request='".mysql_real_escape_string($hodnota)."' LIMIT 1");
				mysql_query("DELETE FROM reasons_other WHERE ID_request='".mysql_real_escape_string($hodnota)."' LIMIT 1");

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
						('".mysql_real_escape_string($hodnota)."',
						'".mysql_real_escape_string($_POST['product_bm'])."',
						'".mysql_real_escape_string($_POST['product_pbsc'])."',
						'".mysql_real_escape_string($_POST['product_dli'])."',
						'".mysql_real_escape_string($_POST['product_double'])."',
						'".mysql_real_escape_string($_POST['product_single'])."',
						'".mysql_real_escape_string($_POST['trans_donor_id'])."',
						'".mysql_real_escape_string($_POST['trans_date0'])."',
						'".mysql_real_escape_string($_POST['trans_cbid1'])."',
						'".mysql_real_escape_string($_POST['trans_cbid2'])."',
						'".mysql_real_escape_string($_POST['trans_date1'])."',
						'".mysql_real_escape_string($_POST['trans_date2'])."',
						'".mysql_real_escape_string($_POST['trans_problem1'])."',
						'".mysql_real_escape_string($_POST['trans_problem2'])."',
						'".mysql_real_escape_string($_POST['complications'])."',
						'".mysql_real_escape_string($_POST['reported_igz'])."',
						'".mysql_real_escape_string($_POST['unexpected'])."',
						'".mysql_real_escape_string($_POST['gravity'])."',
						'".mysql_real_escape_string($_POST['don_product'])."',
						'".mysql_real_escape_string($_POST['complicaties_text'])."',
						'".mysql_real_escape_string($_POST['donor_info'])."'
						)
						ON DUPLICATE KEY UPDATE 
							product_bm='".mysql_real_escape_string($_POST['product_bm'])."',
							product_pbsc='".mysql_real_escape_string($_POST['product_pbsc'])."',
							product_dli='".mysql_real_escape_string($_POST['product_dli'])."',
							product_double='".mysql_real_escape_string($_POST['product_double'])."',
							product_single='".mysql_real_escape_string($_POST['product_single'])."',
							trans_donor_id='".mysql_real_escape_string($_POST['trans_donor_id'])."',
							trans_date0='".mysql_real_escape_string($_POST['trans_date0'])."',
							trans_cbid1='".mysql_real_escape_string($_POST['trans_cbid1'])."',
							trans_cbid2='".mysql_real_escape_string($_POST['trans_cbid2'])."',
							trans_date1='".mysql_real_escape_string($_POST['trans_date1'])."',
							trans_date2='".mysql_real_escape_string($_POST['trans_date2'])."',
							trans_problem1='".mysql_real_escape_string($_POST['trans_problem1'])."',
							trans_problem2='".mysql_real_escape_string($_POST['trans_problem2'])."',
							complications='".mysql_real_escape_string($_POST['complications'])."',
							reported_igz='".mysql_real_escape_string($_POST['reported_igz'])."',
							unexpected='".mysql_real_escape_string($_POST['unexpected'])."',
							gravity='".mysql_real_escape_string($_POST['gravity'])."',
							don_product='".mysql_real_escape_string($_POST['don_product'])."',
							complicaties_text='".mysql_real_escape_string($_POST['complicaties_text'])."',
							donor_info='".mysql_real_escape_string($_POST['donor_info'])."'
						");
				
					endif;
					
					
					if($_POST['duvod2']==21):	//died
						
						if($citac_f==1):
							get_timestamp('die_date');
						endif;
						
						mysql_query("INSERT INTO reasons_died (ID_request, die_date, die_reason) VALUES
						('".mysql_real_escape_string($hodnota)."',
						'".mysql_real_escape_string($_POST['die_date'])."',
						'".mysql_real_escape_string($_POST['die_reason'])."'
						)
						ON DUPLICATE KEY UPDATE 
							die_date='".mysql_real_escape_string($_POST['die_date'])."',
							die_reason='".mysql_real_escape_string($_POST['die_reason'])."'
						");
					endif;
					
					
					if($_POST['duvod2']==23):	//no donor
						
						if($_POST['nodonor_transplant']==1):
							unset($_POST['nodonor_text']);
						endif;
						
						mysql_query("INSERT INTO reasons_nodonor (ID_request, nodonor_transplant, nodonor_text) VALUES
						('".mysql_real_escape_string($hodnota)."',
						'".mysql_real_escape_string($_POST['nodonor_transplant'])."',
						'".mysql_real_escape_string($_POST['nodonor_text'])."'
						)
						ON DUPLICATE KEY UPDATE 
							nodonor_transplant='".mysql_real_escape_string($_POST['nodonor_transplant'])."',
							nodonor_text='".mysql_real_escape_string($_POST['nodonor_text'])."'
						");
					endif;
					
					
					if($_POST['duvod2']==24):	//other
						
						mysql_query("INSERT INTO reasons_other (ID_request, other_text) VALUES
						('".mysql_real_escape_string($hodnota)."',
						'".mysql_real_escape_string($_POST['other_text'])."'
						)
						ON DUPLICATE KEY UPDATE 
							other_text='".mysql_real_escape_string($_POST['other_text'])."'
						");
					endif;
					
				endif;
				
				
			endif;
			
			if($vysledek):
				$zpracovano++;
			else:
				$nezpracovano++;
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
//	Filtrovací formuláø
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
		$where_hledej="AND (LOWER(search_request.last_name) LIKE '%$vyraz%' OR LOWER(search_request.first_name) LIKE '%$vyraz%' OR search_request.ID='".$vyraz."'
		OR CONCAT(search_request.RegID,search_request.PatientNum,'P')='".$vyraz."')";
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
strankovani("search_request WHERE search_request.ID_stavu='2' $where_hledej $where", $_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('search_request.ID DESC');


$counter=0;

	
//-----vypis dat
$vysledek = mysql_query("SELECT ID, InstID, RegID, date_request, last_name, first_name, PatientNum, status, duvod_zamitnuti, datum_zpracovani FROM search_request 
WHERE search_request.ID_stavu='2' $where_hledej $where ORDER BY $orderby LIMIT $_od, $_na_stranku");
	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
				<td width=\"13%\">".order('Request date', 'date_request')."</td>
				<td width=\"17%\">".order('Last name', 'last_name')."</td>
				<td width=\"14%\">".order('First name', 'first_name')."</td>
				<td width=\"13%\">".order('Patient ID', 'PatientNum')."</td>
				<td width=\"14%\">".order('Recieved', 'datum_zpracovani')."</td>
				<td width=\"15%\">".order('Search status', 'status')."</td>
				<td width=\"10%\">Action</td>
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
				
				$pocet_donoru = mysql_result(mysql_query("SELECT COUNT(ID) FROM patient_donor WHERE PatientNum='$PatientNum'"), 0);
				if($pocet_donoru>0):
					$styl="class=\"barva6\" onmouseover=\"this.className='barva6b'\" onmouseout=\"this.className='barva6'\"";
				endif;
					
				
				$status_text="";
				if($status==1): $status_text="Preliminary"; endif;
				if($status==2): $status_text="Active"; endif;
				if($status==3): $status_text="Stopped"; endif;
				if(!$status || $status>=4): $status_text="Not active"; endif;
				
				
				$hash=sha1($ID."SaltIsGoodForLife45");
				
				echo "<tr ".$styl.">";
					echo "<td>"; if($date_request>0): echo date($_SESSION['date_format_php'],$date_request); endif; echo "</td>";
					echo "<td>$last_name</td>";
					echo "<td>$first_name</td>";
					echo "<td>".$RegID.$PatientNum."P</td>";
					echo "<td>"; if($datum_zpracovani>0): echo date($_SESSION['date_format_php'],$datum_zpracovani); endif; echo "</td>";
					echo "<td>$status_text</td>";
					
					echo "<td>";
					echo "<a href=\"requests-vlozit.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a> &nbsp;&nbsp;";
					
					
					if($pocet_donoru>0):
						$hash=sha1($RegID.$PatientNum."SaltIsGoodForLife45");
						echo "<a href=\"results-donors.php?Rid=".$RegID."&amp;PatientNum=$PatientNum&amp;h=".$hash."\" title=\"DONORS\">".$ico_donor."</a>";
					else:
						echo $ico_donor_no;
					endif;
						
					
					//moznost presunout
					$disable="";
					
					if($status==1 || !$status || $status>=4): $disable=1; endif;
						
					echo "</td>
					<td>";
						if(!$disable):
							echo "<input type=\"checkbox\" name=\"cb_$ID\" value=\"$ID\">";
						endif;
					echo "</td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			
			echo "<div style=\"float:right; width:56px; margin-left:7px;\"><input type=\"submit\" class=\"form-ok\" value=\"\"></div>";
			
			echo "<div style=\"float:right; width:300px; text-align:right;\">Selected: ";
			echo "<select name=\"operation\" id=\"operation\">
				<option value=\"1\""; if($operation==1): echo " selected"; endif; echo ">Move to suspended</option>
				<option value=\"2\""; if($operation==2): echo " selected"; endif; echo ">Move to stopped</option>
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
			//echo "<input type=\"submit\" class=\"form-send\" value=\"\"></p>";

			
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
		
		";
		if($videt_reason2):
		echo "
		$('select#operation').change(function(){
			if( $('#operation').val()=='1' ){
				$('#id_duvody').show();
				$('#id_duvody2').hide();
				$('#id_duvody2_form').hide();
			}
			if( $('#operation').val()=='2' ){
				$('#id_duvody2').show();
				$('#id_duvody').hide();
				nacti_reason2_form();
			}
		});
		";
		endif;
		echo "
		
		
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