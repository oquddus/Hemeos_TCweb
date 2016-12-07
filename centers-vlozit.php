<?
include('1-head.php');
?>


<?
if(!$_GET['ID'] || ($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) ){


//--vlozit zaznam
if($_POST['action']=="vlozit"):

	$_POST['datum_vlozeni']=time();
	
	insert_tb('transplant_centers');
	
	
	if($idcko):	//--pokud vse vlozeno, vim id vlozeneho radku
	
		$vysledek_if = mysql_query("SELECT ID AS ID_polozky FROM setting_preliminary");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					mysql_query("INSERT INTO setting_preliminary_custom (ID_item, ID_center, visible, required, label) VALUES
					('".clean_high($ID_polozky)."',
					'".clean_basic($idcko)."',
					'".clean_basic($_POST['visible_'.$ID_polozky])."',
					'".clean_basic($_POST['required_'.$ID_polozky])."',
					'".clean_basic($_POST['label_'.$ID_polozky])."'
					)");

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);
		
		
		$vysledek_if = mysql_query("SELECT ID AS ID_polozky FROM setting_sample");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					mysql_query("INSERT INTO setting_sample_custom (ID_item, ID_center, visible, required, label) VALUES
					('".clean_high($ID_polozky)."',
					'".clean_basic($idcko)."',
					'".clean_basic($_POST['samp_visible_'.$ID_polozky])."',
					'".clean_basic($_POST['samp_required_'.$ID_polozky])."',
					'".clean_basic($_POST['samp_label_'.$ID_polozky])."'
					)");

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);
		
		
		mysql_query("INSERT INTO setting_sample_default (ID_center, sample_to_institution, invoice_to_institution, sample_address, invoice_address, sample_attention, invoice_attention,
		sample_phone, invoice_phone, sample_fax, invoice_fax, sample_email, invoice_email,
		monday, tuesday, wednesday, thursday, friday, saturday, sunday,
		mls_edta, mls_heparin, mls_acd, mls_clotted, mls_dna, mls_cpa, tubes_edta, tubes_heparin, tubes_acd, tubes_clotted, tubes_dna, tubes_cpa) VALUES
			('".clean_high($idcko)."',
			'".clean_basic($_POST['sample_to_institution'])."',
            '".clean_basic($_POST['invoice_to_institution'])."',
			'".clean_basic($_POST['sample_address'])."',
			'".clean_basic($_POST['invoice_address'])."',
			'".clean_basic($_POST['sample_attention'])."',
			'".clean_basic($_POST['invoice_attention'])."',
			'".clean_basic($_POST['sample_phone'])."',
			'".clean_basic($_POST['invoice_phone'])."',
			'".clean_basic($_POST['sample_fax'])."',
			'".clean_basic($_POST['invoice_fax'])."',
			'".clean_basic($_POST['sample_email'])."',
			'".clean_basic($_POST['invoice_email'])."',
			'".clean_basic($_POST['monday'])."',
			'".clean_basic($_POST['tuesday'])."',
			'".clean_basic($_POST['wednesday'])."',
			'".clean_basic($_POST['thursday'])."',
			'".clean_basic($_POST['friday'])."',
			'".clean_basic($_POST['saturday'])."',
			'".clean_basic($_POST['sunday'])."',
			'".clean_basic($_POST['mls_edta'])."',
			'".clean_basic($_POST['mls_heparin'])."',
			'".clean_basic($_POST['mls_acd'])."',
			'".clean_basic($_POST['mls_clotted'])."',
			'".clean_basic($_POST['mls_dna'])."',
			'".clean_basic($_POST['mls_cpa'])."',
			'".clean_basic($_POST['tubes_edta'])."',
			'".clean_basic($_POST['tubes_heparin'])."',
			'".clean_basic($_POST['tubes_acd'])."',
			'".clean_basic($_POST['tubes_clotted'])."',
			'".clean_basic($_POST['tubes_dna'])."',
			'".clean_basic($_POST['tubes_cpa'])."'
			)");
			
		//volitelne labely reason2
		$vysledek_if = mysql_query("SELECT ID AS ID_polozky FROM setting_reasons2");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					$input_name="reas_label_".$ID_polozky;
					
					if($_POST[$input_name]):
						mysql_query("INSERT INTO setting_reasons2_custom (ID_item, ID_center, label) VALUES
						'".clean_high($ID_polozky)."',
						'".clean_basic($idcko)."',
						'".clean_basic($_POST[$input_name])."'
						)");
					endif;

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);
	
		message(3, "Record was inserted.", "Insert next record", "$_SERVER[PHP_SELF]");
	else:
		message(1, "Record was not inserted.", "", "");
		
		call_post();	//--zrusi akci, post promenne da do lokalnich
	endif;
		
endif;




//--upravit zaznam
if($_POST['action']=="upravit"):

	/*
	if(!$_POST['extra_inputs']):
		unset($_POST['def_mm_hla_a'], $_POST['def_mm_hla_b'], $_POST['def_mm_hla_c'], $_POST['def_mm_hla_dr'], $_POST['def_mm_hla_dq']);
	endif;
	*/
	
	$update[0]=array("ID","datum_vlozeni");	//--ktere sloupce vyloucit z updatu
	$update[1]=array("WHERE ID='".clean_high($_GET['ID'])."'");	//--where

	
	update_tb('transplant_centers',$update);	//--tabulka   |   pole vyloucenych sloupcu
	
	if($SQL):	//--pokud update neco zmenil
	
		//volitelne hodnoty preliminary formulare
		mysql_query("DELETE FROM setting_preliminary_custom WHERE ID_center='".clean_high($_GET['ID'])."'");
		
		$vysledek_if = mysql_query("SELECT ID AS ID_polozky FROM setting_preliminary");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					mysql_query("INSERT INTO setting_preliminary_custom (ID_item, ID_center, visible, required, label) VALUES
					('".clean_high($ID_polozky)."',
					'".clean_high($_GET['ID'])."',
					'".clean_basic($_POST['visible_'.$ID_polozky])."',
					'".clean_basic($_POST['required_'.$ID_polozky])."',
					'".clean_basic($_POST['label_'.$ID_polozky])."'
					)");

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);
		
		
		//volitelne hodnoty sample formulare
		mysql_query("DELETE FROM setting_sample_custom WHERE ID_center='".clean_high($_GET['ID'])."'");
		
		$vysledek_if = mysql_query("SELECT ID AS ID_polozky FROM setting_sample");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					mysql_query("INSERT INTO setting_sample_custom (ID_item, ID_center, visible, required, label) VALUES
					('".clean_high($ID_polozky)."',
					'".clean_high($_GET['ID'])."',
					'".clean_basic($_POST['samp_visible_'.$ID_polozky])."',
					'".clean_basic($_POST['samp_required_'.$ID_polozky])."',
					'".clean_basic($_POST['samp_label_'.$ID_polozky])."'
					)");

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);
		
		
		mysql_query("INSERT INTO setting_sample_default (ID_center, sample_to_institution, invoice_to_institution, sample_address, invoice_address, sample_attention, invoice_attention,
		sample_phone, invoice_phone, sample_fax, invoice_fax, sample_email, invoice_email,
		monday, tuesday, wednesday, thursday, friday, saturday, sunday,
		mls_edta, mls_heparin, mls_acd, mls_clotted, mls_dna, mls_cpa, tubes_edta, tubes_heparin, tubes_acd, tubes_clotted, tubes_dna, tubes_cpa) VALUES
			('".clean_high($_GET['ID'])."',
			'".clean_basic($_POST['sample_to_institution'])."',
			'".clean_basic($_POST['invoice_to_institution'])."',
			'".clean_basic($_POST['sample_address'])."',
			'".clean_basic($_POST['invoice_address'])."',
			'".clean_basic($_POST['sample_attention'])."',
			'".clean_basic($_POST['invoice_attention'])."',
			'".clean_basic($_POST['sample_phone'])."',
			'".clean_basic($_POST['invoice_phone'])."',
			'".clean_basic($_POST['sample_fax'])."',
			'".clean_basic($_POST['invoice_fax'])."',
			'".clean_basic($_POST['sample_email'])."',
			'".clean_basic($_POST['invoice_email'])."',
			'".clean_basic($_POST['monday'])."',
			'".clean_basic($_POST['tuesday'])."',
			'".clean_basic($_POST['wednesday'])."',
			'".clean_basic($_POST['thursday'])."',
			'".clean_basic($_POST['friday'])."',
			'".clean_basic($_POST['saturday'])."',
			'".clean_basic($_POST['sunday'])."',
			'".clean_basic($_POST['mls_edta'])."',
			'".clean_basic($_POST['mls_heparin'])."',
			'".clean_basic($_POST['mls_acd'])."',
			'".clean_basic($_POST['mls_clotted'])."',
			'".clean_basic($_POST['mls_dna'])."',
			'".clean_basic($_POST['mls_cpa'])."',
			'".clean_basic($_POST['tubes_edta'])."',
			'".clean_basic($_POST['tubes_heparin'])."',
			'".clean_basic($_POST['tubes_acd'])."',
			'".clean_basic($_POST['tubes_clotted'])."',
			'".clean_basic($_POST['tubes_dna'])."',
			'".clean_basic($_POST['tubes_cpa'])."'
			)
			ON DUPLICATE KEY UPDATE
			sample_to_institution='".clean_basic($_POST['sample_to_institution'])."',
			invoice_to_institution='".clean_basic($_POST['invoice_to_institution'])."',
			sample_address='".clean_basic($_POST['sample_address'])."',
			invoice_address='".clean_basic($_POST['invoice_address'])."',
			sample_attention='".clean_basic($_POST['sample_attention'])."',
			invoice_attention='".clean_basic($_POST['invoice_attention'])."',
			sample_phone='".clean_basic($_POST['sample_phone'])."',
			invoice_phone='".clean_basic($_POST['invoice_phone'])."',
			sample_fax='".clean_basic($_POST['sample_fax'])."',
			invoice_fax='".clean_basic($_POST['invoice_fax'])."',
			sample_email='".clean_basic($_POST['sample_email'])."',
			invoice_email='".clean_basic($_POST['invoice_email'])."',
			monday='".clean_basic($_POST['monday'])."',
			tuesday='".clean_basic($_POST['tuesday'])."',
			wednesday='".clean_basic($_POST['wednesday'])."',
			thursday='".clean_basic($_POST['thursday'])."',
			friday='".clean_basic($_POST['friday'])."',
			saturday='".clean_basic($_POST['saturday'])."',
			sunday='".clean_basic($_POST['sunday'])."',
			mls_edta='".clean_basic($_POST['mls_edta'])."',
			mls_heparin='".clean_basic($_POST['mls_heparin'])."',
			mls_acd='".clean_basic($_POST['mls_acd'])."',
			mls_clotted='".clean_basic($_POST['mls_clotted'])."',
			mls_dna='".clean_basic($_POST['mls_dna'])."',
			mls_cpa='".clean_basic($_POST['mls_cpa'])."',
			tubes_edta='".clean_basic($_POST['tubes_edta'])."',
			tubes_heparin='".clean_basic($_POST['tubes_heparin'])."',
			tubes_acd='".clean_basic($_POST['tubes_acd'])."',
			tubes_clotted='".clean_basic($_POST['tubes_clotted'])."',
			tubes_dna='".clean_basic($_POST['tubes_dna'])."',
			tubes_cpa='".clean_basic($_POST['tubes_cpa'])."'
			");
		
		
		//volitelne labely reason2
		mysql_query("DELETE FROM setting_reasons2_custom WHERE ID_center='".clean_high($_GET['ID'])."'");
		
		$vysledek_if = mysql_query("SELECT ID AS ID_polozky FROM setting_reasons2");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					$input_name="reas_label_".$ID_polozky;
					
					if($_POST[$input_name]):
						mysql_query("INSERT INTO setting_reasons2_custom (ID_item, ID_center, label) VALUES
						('".clean_high($ID_polozky)."',
						'".clean_high($_GET['ID'])."',
						'".clean_basic($_POST[$input_name])."'
						)");
					endif;

				endwhile;
			endif;
		@mysql_free_result($vysledek_if);
		
		
		
		message(1, "Record was edited.", "", "");
	else:
		message(1, "Record was not edited.", "", "");
	endif;
	
	unset($_POST['action']);
endif;








//-----------------------------------------formular
if(!$_POST['action']):


	$vysledek = mysql_query("SELECT * FROM transplant_centers WHERE ID='".clean_high($_GET['ID'])."' LIMIT 1");
	if(mysql_num_rows($vysledek)>0):
		$zaz = mysql_fetch_array($vysledek);
			$zaz=htmlspecialchars_array_encode($zaz);
			extract($zaz);

	endif;
	

	
	$form_name="formular";
	
	echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">

		<div class=\"form-lr\">
			<div class=\"form-left\">Register: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><select name=\"ID_registru\" id=\"ID_registru\" style=\"width:".$width6."px;\">";

					echo "<OPTION class=\"form\" value=\"\">-choose the Register-</option>";

					//admin vidi vsechny registry, ostatni vidi jen svuj registr
					if($_SESSION['usr_ID_role']!=1):
						$where_registry="AND registers.ID='".$_SESSION['usr_ID_registru']."'";
					endif;
					
					$vysledek_if = mysql_query("SELECT ID AS IDregistru, registr FROM registers WHERE aktivni='1' $where_registry ORDER BY registr");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							echo "<OPTION class=\"form\" value=\"$IDregistru\""; if($ID_registru==$IDregistru): echo "selected"; endif; echo ">$registr</option>";
							
						endwhile;
					endif;
					@mysql_free_result($vysledek_if);

			echo "</select></div>
		</div>		
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Transplant centre: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"centrum\" value=\"$centrum\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Transplant centre ID: <span class=\"povinne\">*</span></div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"InstID\" value=\"$InstID\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Email:</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"email\" value=\"$email\"></div>
		</div>
		
		<div class=\"form-lr\">
			<div class=\"form-left\">Phone:</div>
			<div class=\"form-right\"><input type=\"text\" style=\"width:".$width6."px;\" name=\"telefon\" value=\"$telefon\"></div>
		</div>";
		
		/*
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">Special inputs in search request:</div>
			<div class=\"form-right\"><input type=\"checkbox\" name=\"extra_inputs\" id=\"extra_inputs\" value=\"1\""; if($extra_inputs): echo " checked"; endif; echo "></div>
		</div>";
		*/

		
		
		
		echo "<div class=\"form-lr\">
			<div class=\"form-left\">Active:</div>
			<div class=\"form-right\"><input type=\"checkbox\" name=\"aktivni\" value=\"1\""; if($aktivni || !$_GET['ID']): echo " checked"; endif; echo "></div>
		</div>";
		
		
		if($_GET['ID']):
			echo "<div style=\"float:left; width:100%; margin-top:20px;\">
			
			<h2 style=\"border-bottom:1px solid #969696; margin-bottom:10px;\">Preliminary request form setting</h2>";
			
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Label</b></td>
					<td width=\"10%\"><b>Visible</b></td>
					<td width=\"10%\"><b>Required</b></td>
					<td width=\"30%\"><b>New label</b></td>
				</tr>";
				
				//nacteni custom hodnot
				$_hodnoty=array();
				$ma_udaje=0;
				$vysledek_if = mysql_query("SELECT ID_item, visible, required, label FROM setting_preliminary_custom WHERE ID_center='".clean_high($_GET['ID'])."'");
					if(mysql_num_rows($vysledek_if)):
						$ma_udaje=1;
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
						
							$_hodnoty[$ID_item]['v']=$visible;
							$_hodnoty[$ID_item]['r']=$required;
							$_hodnoty[$ID_item]['l']=$label;
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				//vypis tabulky
				$vysledek_if = mysql_query("SELECT ID AS ID_polozky, title, visible, required, change_v, change_r, change_l FROM setting_preliminary ORDER BY sorting");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
								<td style=\"padding-top:7px; padding-bottom:7px;\">$title</td>
								<td>"; if($change_v): echo "<input type=\"checkbox\" name=\"visible_".$ID_polozky."\" value=\"1\""; if($_hodnoty[$ID_polozky]['v']==1 || ($ma_udaje==0 && $visible) ): echo " checked"; endif; echo ">"; endif; echo "</td>
								<td>"; if($change_r): echo "<input type=\"checkbox\" name=\"required_".$ID_polozky."\" value=\"1\""; if($_hodnoty[$ID_polozky]['r']==1 || ($ma_udaje==0 && $required) ): echo " checked"; endif; echo ">"; endif; echo "</td>
								<td>"; if($change_l): echo "<input type=\"text\" name=\"label_".$ID_polozky."\" value=\"".$_hodnoty[$ID_polozky]['l']."\" style=\"width:250px;\">"; endif; echo "</td>
							</tr>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				echo "</table>";
				
				
				echo "<h3 style=\"margin-bottom:10px; text-align:left; font-weight:normal;\">Default values</h3>";
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Value for</b></td>
					<td width=\"50%\"><b>Value</b></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Mismatches acceptable</td>
					<td><input type=\"checkbox\" name=\"def_mmaccept\" value=\"1\""; if($def_mmaccept==1): echo " checked"; endif; echo "></td>
				</tr>";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Mismatches default values</td>
					<td>
					
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"def_mm_hla_a\" id=\"def_mm_hla_a\" value=\"1\""; if($def_mm_hla_a==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"def_mm_hla_a\">HLA-A</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"def_mm_hla_b\" id=\"def_mm_hla_b\" value=\"1\""; if($def_mm_hla_b==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"def_mm_hla_b\">HLA-B</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"def_mm_hla_c\" id=\"def_mm_hla_c\" value=\"1\""; if($def_mm_hla_c==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"def_mm_hla_c\">HLA-C</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"def_mm_hla_dr\" id=\"def_mm_hla_dr\" value=\"1\""; if($def_mm_hla_dr==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"def_mm_hla_dr\">HLA-DR</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"def_mm_hla_dq\" id=\"def_mm_hla_dq\" value=\"1\""; if($def_mm_hla_dq==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"def_mm_hla_dq\">HLA-DQ</label></div>
					
					</td>
				</tr>";
				
				echo "</table>";
			
			
			echo "</div>";
			
			
			echo "<div style=\"float:left; width:100%; margin-top:20px;\">
			
			<h2 style=\"border-bottom:1px solid #969696; margin-bottom:10px;\">Sample request form setting</h2>";
			
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Label</b></td>
					<td width=\"10%\"><b>Visible</b></td>
					<td width=\"10%\"><b>Required</b></td>
					<td width=\"30%\"><b>New label</b></td>
				</tr>";
				
				//nacteni custom hodnot
				$_hodnoty=array();
				$ma_udaje=0;
				$vysledek_if = mysql_query("SELECT ID_item, visible, required, label FROM setting_sample_custom WHERE ID_center='".clean_high($_GET['ID'])."'");
					if(mysql_num_rows($vysledek_if)):
						$ma_udaje=1;
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
						
							$_hodnoty[$ID_item]['v']=$visible;
							$_hodnoty[$ID_item]['r']=$required;
							$_hodnoty[$ID_item]['l']=$label;
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				//vypis tabulky
				$vysledek_if = mysql_query("SELECT ID AS ID_polozky, title, visible, required, change_v, change_r, change_l FROM setting_sample ORDER BY sorting");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
								<td style=\"padding-top:7px; padding-bottom:7px;\">$title</td>
								<td>"; if($change_v): echo "<input type=\"checkbox\" name=\"samp_visible_".$ID_polozky."\" value=\"1\""; if($_hodnoty[$ID_polozky]['v']==1 || ($ma_udaje==0 && $visible) ): echo " checked"; endif; echo ">"; endif; echo "</td>
								<td>"; if($change_r): echo "<input type=\"checkbox\" name=\"samp_required_".$ID_polozky."\" value=\"1\""; if($_hodnoty[$ID_polozky]['r']==1 || ($ma_udaje==0 && $required) ): echo " checked"; endif; echo ">"; endif; echo "</td>
								<td>"; if($change_l): echo "<input type=\"text\" name=\"samp_label_".$ID_polozky."\" value=\"".$_hodnoty[$ID_polozky]['l']."\" style=\"width:250px;\">"; endif; echo "</td>
							</tr>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				echo "</table>";
			
			
				//nacteni custom hodnot
				$vysledek_if = mysql_query("SELECT sample_to_institution, invoice_to_institution, sample_address, invoice_address, sample_attention, invoice_attention,
				sample_phone, invoice_phone, sample_fax, invoice_fax, sample_email, invoice_email,
				monday, tuesday, wednesday, thursday, friday, saturday, sunday, 
				mls_edta, mls_heparin, mls_acd, mls_clotted, mls_dna, mls_cpa, tubes_edta, tubes_heparin, tubes_acd, tubes_clotted, tubes_dna, tubes_cpa
				FROM setting_sample_default WHERE ID_center='".clean_high($_GET['ID'])."' LIMIT 1");
					if(mysql_num_rows($vysledek_if)):
						$zaz_if = mysql_fetch_array($vysledek_if);
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
					endif;
				@mysql_free_result($vysledek_if);
				
				echo "<h3 style=\"margin-bottom:10px; text-align:left; font-weight:normal;\">Default values</h3>";
				
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Value for</b></td>
					<td width=\"50%\"><b>Value</b></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Acceptable days of the week to receive samples</td>
					<td>
					
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"monday\" id=\"monday\" value=\"1\""; if($monday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"monday\">Mon</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"tuesday\" id=\"tuesday\" value=\"1\""; if($tuesday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"tuesday\">Tue</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"wednesday\" id=\"wednesday\" value=\"1\""; if($wednesday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"wednesday\">Wed</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"thursday\" id=\"thursday\" value=\"1\""; if($thursday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"thursday\">Thu</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"friday\" id=\"friday\" value=\"1\""; if($friday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"friday\">Fri</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"saturday\" id=\"saturday\" value=\"1\""; if($saturday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"saturday\">Sat</label></div>
						<div style=\"float:left;\"><input type=\"checkbox\" name=\"sunday\" id=\"sunday\" value=\"1\""; if($sunday==1): echo " checked"; endif; echo "></div><div style=\"float:left; margin:3px 25px 0 5px;\"><label for=\"sunday\">Sun</label></div>
					
					</td>
				</tr>";
				
				echo "</table>";
				
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>BLOOD SAMPLE REQUIREMENTS</b></td>
					<td width=\"50%\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">mls EDTA: <input type=\"text\" style=\"width:50px;\" name=\"mls_edta\" id=\"mls_edta\" value=\"$mls_edta\"></td>
					<td>No. of tubes: <input type=\"text\" style=\"width:50px;\" name=\"tubes_edta\" id=\"tubes_edta\" value=\"$tubes_edta\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">mls Heparin: <input type=\"text\" style=\"width:50px;\" name=\"mls_heparin\" id=\"mls_heparin\" value=\"$mls_heparin\"></td>
					<td>No. of tubes: <input type=\"text\" style=\"width:50px;\" name=\"tubes_heparin\" id=\"tubes_heparin\" value=\"$tubes_heparin\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">mls ACD: <input type=\"text\" style=\"width:50px;\" name=\"mls_acd\" id=\"mls_acd\" value=\"$mls_acd\"></td>
					<td>No. of tubes: <input type=\"text\" style=\"width:50px;\" name=\"tubes_acd\" id=\"tubes_acd\" value=\"$tubes_acd\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">mls Clotted: <input type=\"text\" style=\"width:50px;\" name=\"mls_clotted\" id=\"mls_clotted\" value=\"$mls_clotted\"></td>
					<td>No. of tubes: <input type=\"text\" style=\"width:50px;\" name=\"tubes_clotted\" id=\"tubes_clotted\" value=\"$tubes_clotted\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">mls DNA: <input type=\"text\" style=\"width:50px;\" name=\"mls_dna\" id=\"mls_dna\" value=\"$mls_dna\"></td>
					<td>No. of tubes: <input type=\"text\" style=\"width:50px;\" name=\"tubes_dna\" id=\"tubes_dna\" value=\"$tubes_dna\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">mls CPA: <input type=\"text\" style=\"width:50px;\" name=\"mls_cpa\" id=\"mls_cpa\" value=\"$mls_cpa\"></td>
					<td>No. of tubes: <input type=\"text\" style=\"width:50px;\" name=\"tubes_cpa\" id=\"tubes_cpa\" value=\"$tubes_cpa\"></td>
				</tr>";
				
				echo "</table>";
				
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Samples to be shipped to</b></td>
					<td width=\"50%\"><b>Invoice(s) to be sent to:</b></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Institution: <input type=\"text\" style=\"width:350px;\" name=\"sample_to_institution\" value=\"$sample_to_institution\"></td>
					<td>Institution: <input type=\"text\" style=\"width:350px;\" name=\"invoice_to_institution\" value=\"$invoice_to_institution\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Address:&nbsp;&nbsp; <textarea style=\"width:350px;\" name=\"sample_address\">$sample_address</textarea></td>
					<td>Address:&nbsp;&nbsp; <textarea style=\"width:350px;\" name=\"invoice_address\">$invoice_address</textarea></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Attention:&nbsp;&nbsp; <input type=\"text\" style=\"width:350px;\" name=\"sample_attention\" value=\"$sample_attention\"></td>
					<td>Attention:&nbsp;&nbsp; <input type=\"text\" style=\"width:350px;\" name=\"invoice_attention\" value=\"$invoice_attention\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Phone no: <input type=\"text\" style=\"width:350px;\" name=\"sample_phone\" value=\"$sample_phone\"></td>
					<td>Phone no: <input type=\"text\" style=\"width:350px;\" name=\"invoice_phone\" value=\"$invoice_phone\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Fax no: <input type=\"text\" style=\"width:365px;\" name=\"sample_fax\" value=\"$sample_fax\"></td>
					<td>Fax no: <input type=\"text\" style=\"width:365px;\" name=\"invoice_fax\" value=\"$invoice_fax\"></td>
				</tr>";
				
				echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
					<td style=\"padding-top:7px; padding-bottom:7px;\">Email: <input type=\"text\" style=\"width:370px;\" name=\"sample_email\" value=\"$sample_email\"></td>
					<td>Email: <input type=\"text\" style=\"width:370px;\" name=\"invoice_email\" value=\"$invoice_email\"></td>
				</tr>";
				
				echo "</table>";
			
			echo "</div>";
		
		
		
		
			echo "<div style=\"float:left; width:100%; margin-top:20px;\">
			
			<h2 style=\"border-bottom:1px solid #969696; margin-bottom:10px;\">Search request - Reason2 setting</h2>";
			
				echo "<table width=\"100%\">";

					echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
						<td width=\"20%\" style=\"padding-top:7px; padding-bottom:7px;\">Use Reason 2</td>
						<td width=\"80%\"><input type=\"checkbox\" name=\"reason2\" id=\"reason2\" value=\"1\""; if($reason2==1): echo " checked"; endif; echo "></td>
					</tr>";

				echo "</table>";
				
				
				
				echo "<h3 style=\"margin-bottom:10px; text-align:left; font-weight:normal;\">Labels for reason \"Patient was transplanted\"</h3>";
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Default</b></td>
					<td width=\"50%\"><b>Label</b></td>
				</tr>";

				//nacteni custom hodnot
				$_hodnoty=array();
				$vysledek_if = mysql_query("SELECT ID_item, label FROM setting_reasons2_custom WHERE ID_center='".clean_high($_GET['ID'])."'");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							$_hodnoty[$ID_item]=$label;
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				$vysledek_if = mysql_query("SELECT ID AS ID_reas, def_label FROM setting_reasons2 WHERE ID<=19 ORDER BY ID");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							$input_name="reas_label_".$ID_reas;
							echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
								<td style=\"padding-top:7px; padding-bottom:7px;\">".$def_label."</td>
								<td><input type=\"text\" name=\"".$input_name."\" id=\"".$input_name."\" value=\"".$_hodnoty[$ID_reas]."\" style=\"width:350px;\"></td>
							</tr>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				
				echo "</table>";
				
				
				echo "<h3 style=\"margin-bottom:10px; text-align:left; font-weight:normal;\">Labels for reason \"Patient died before transplantation\"</h3>";
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Default</b></td>
					<td width=\"50%\"><b>Label</b></td>
				</tr>";
				
				$vysledek_if = mysql_query("SELECT ID AS ID_reas, def_label FROM setting_reasons2 WHERE ID>=20 AND ID<=21 ORDER BY ID");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							$input_name="reas_label_".$ID_reas;
							echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
								<td style=\"padding-top:7px; padding-bottom:7px;\">".$def_label."</td>
								<td><input type=\"text\" name=\"".$input_name."\" id=\"".$input_name."\" value=\"".$_hodnoty[$ID_reas]."\" style=\"width:350px;\"></td>
							</tr>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				echo "</table>";
				
				
				echo "<h3 style=\"margin-bottom:10px; text-align:left; font-weight:normal;\">Labels for reason \"No suitable donor found\"</h3>";
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Default</b></td>
					<td width=\"50%\"><b>Label</b></td>
				</tr>";
				
				$vysledek_if = mysql_query("SELECT ID AS ID_reas, def_label FROM setting_reasons2 WHERE ID>=22 AND ID<=24 ORDER BY ID");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							$input_name="reas_label_".$ID_reas;
							echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
								<td style=\"padding-top:7px; padding-bottom:7px;\">".$def_label."</td>
								<td><input type=\"text\" name=\"".$input_name."\" id=\"".$input_name."\" value=\"".$_hodnoty[$ID_reas]."\" style=\"width:350px;\"></td>
							</tr>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				echo "</table>";
				
				
				echo "<h3 style=\"margin-bottom:10px; text-align:left; font-weight:normal;\">Labels for reason \"Other\"</h3>";
				
				echo "<table width=\"100%\">";
				echo "<tr style=\"border-bottom:1px solid #f1f1f2; text-transform:uppercase;\">
					<td width=\"50%\"><b>Default</b></td>
					<td width=\"50%\"><b>Label</b></td>
				</tr>";
				
				$vysledek_if = mysql_query("SELECT ID AS ID_reas, def_label FROM setting_reasons2 WHERE ID=25 ORDER BY ID");
					if(mysql_num_rows($vysledek_if)):
						while($zaz_if = mysql_fetch_array($vysledek_if)):
							$zaz_if=htmlspecialchars_array_encode($zaz_if);
							extract($zaz_if);
							
							$input_name="reas_label_".$ID_reas;
							echo "<tr style=\"border-bottom:1px solid #f1f1f2;\">
								<td style=\"padding-top:7px; padding-bottom:7px;\">".$def_label."</td>
								<td><input type=\"text\" name=\"".$input_name."\" id=\"".$input_name."\" value=\"".$_hodnoty[$ID_reas]."\" style=\"width:350px;\"></td>
							</tr>";
							
						endwhile;
					endif;
				@mysql_free_result($vysledek_if);
				
				echo "</table>";
			
			
			
			echo "</div>";
		endif;
		
		
		

		echo "<div class=\"form-lr\">
			<div class=\"form-left\">&nbsp;</div>
			<div class=\"form-right\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>
		</div>";
		

		if($_GET['ID']):
			echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
		else:
			echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
		endif;
		
	echo "</form>";

	
	
	
	
	
endif;

}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>			
			
<SCRIPT LANGUAGE="JavaScript">

	$(document).ready(function(){
	
		$('#extra_inputs').change(function(){

			if($('#extra_inputs').attr('checked')){
				$("#mm_default").show();
			}else{
				$("#mm_default").hide();
			}
			
		});

	});

  <!--
  function Kontrola(){
  
		if(!document.formular.ID_registru.value){
			document.formular.ID_registru.focus();
			alert ("Please, choose the Register.");
			return false;
		}
		
		if(!document.formular.centrum.value){
			document.formular.centrum.focus();
			alert ("Please, fill the Transplant centre.");
			return false;
		}

  		if(!document.formular.InstID.value){
			document.formular.InstID.focus();
			alert ("Please, fill ID.");
			return false;
		}
		
  }
 
// -->
</SCRIPT>  
<?
include('1-end.php');
?>