<?
include('1-head.php');
?>


<?
if($_GET['PatientNum'] && $_GET['h']==sha1($_GET['Rid'].$_GET['PatientNum']."SaltIsGoodForLife45")) {


//* =============================================================================
//	Pri prvnim vstupu na tuto stranku smazat vsechny filtry na sloupce
//============================================================================= */
if(count($_GET)==2 && !$_POST['action']):	//jen Rid a PatientNum a neni post
	mysql_query("DELETE FROM uzivatel_sr_filtry WHERE ID_uzivatele='".$_SESSION['usr_ID']."'");
endif;


//* =============================================================================
//	Operace s vybranymi
//============================================================================= */
if($_POST['operation']):

	$_donori=array();
	foreach($_POST as $klic=>$hodnota):
		$broken=explode("_",$klic);
		if($broken[0]=="cb" && $hodnota>0):
		
			$data=explode("|",$hodnota);
			$_donori[]=$data[1];
			
		endif;
	endforeach;
	
	if($_POST['operation']==1):	//typing
		echo "<script type=\"text/javascript\">
			window.location.href = 'donor-typing-request.php?Pn=".$data[0]."&RegID=".$data[2]."&donors=".implode(",",$_donori)."';
		</script>";
	endif;
	
	if($_POST['operation']==2):	//sample
		echo "<script type=\"text/javascript\">
			window.location.href = 'donor-sample-request.php?Pn=".$data[0]."&RegID=".$data[2]."&donors=".implode(",",$_donori)."';
		</script>";
	endif;
	
	if($_POST['operation']==3):	//workup
		echo "<script type=\"text/javascript\">
			window.location.href = 'donor-workup-request.php?Pn=".$data[0]."&RegID=".$data[2]."&donors=".$_donori[0]."';
		</script>";
	endif;
	
endif;


//* =============================================================================
//	Vyber sloupcu
//============================================================================= */
$mozne_sloupce['DonorUpdate']="Donor<br>Update";
$mozne_sloupce['RecordUpdate']="Record<br>Update";
$mozne_sloupce['Hub']="Hub";
$mozne_sloupce['ID2']="Donor&nbsp;ID";
$mozne_sloupce['Status']="Status";
$mozne_sloupce['Type']="Type";
$mozne_sloupce['Sex']="Sex";
$mozne_sloupce['BirthDate']="BirthDate";
$mozne_sloupce['MatchGrade']="Match&nbsp;Grade&nbsp;ABDR";
$mozne_sloupce['MatchGradeInternal']="Match&nbsp;Grade";
$mozne_sloupce['A1']="A1";
$mozne_sloupce['A2']="A2";
$mozne_sloupce['B1']="B1";
$mozne_sloupce['B2']="B2";
$mozne_sloupce['C1']="C1";
$mozne_sloupce['C2']="C2";
$mozne_sloupce['DRB11x']="DRB11";
$mozne_sloupce['DRB12x']="DRB12";
$mozne_sloupce['DQB11x']="DQB11";
$mozne_sloupce['DQB12x']="DQB12";


//$mozne_sloupce['MatchGradeInt']="MatchGradeInt";
//$mozne_sloupce['PhenotypeQuality']="PhenotypeQuality";

$mozne_sloupce['Ethnic']="Ethnic";
$mozne_sloupce['ABO']="ABO/Rh";
$mozne_sloupce['CMV']="CMV";
$mozne_sloupce['CMVDate']="CMVDate";

$mozne_sloupce['ProbMM0']="P(10/10)";
$mozne_sloupce['ProbMM1']="P(9/10)";

$mozne_sloupce['PROBA']="P(A)";
$mozne_sloupce['PROBB']="P(B)";
$mozne_sloupce['PROBC']="P(C)";
$mozne_sloupce['PROBDR']="P(DR)";
$mozne_sloupce['PROBDQ']="P(DQ)";

$mozne_sloupce['lasttyping']="Last&nbsp;typing";
$mozne_sloupce['lastsample']="Last&nbsp;sample";
$mozne_sloupce['lastworkup']="Last&nbsp;workup";

//$mozne_sloupce['HLA_A1']="HLA_A1";
//$mozne_sloupce['HLA_A1']="HLA_A1";
//$mozne_sloupce['HLA_A2']="HLA_A2";
//$mozne_sloupce['HLA_B1']="HLA_B1";
//$mozne_sloupce['HLA_B2']="HLA_B2";
//$mozne_sloupce['HLA_C1']="HLA_C1";
//$mozne_sloupce['HLA_C2']="HLA_C2";
//$mozne_sloupce['HLA_DR1']="HLA_DR1";
//$mozne_sloupce['HLA_DR2']="HLA_DR2";
//$mozne_sloupce['HLA_DQ1']="HLA_DQ1";
//$mozne_sloupce['HLA_DQ2']="HLA_DQ2";
//$mozne_sloupce['DRB11']="DRB11";
//$mozne_sloupce['DRB12']="DRB12";
//$mozne_sloupce['DRB31']="DRB31";
//$mozne_sloupce['DRB32']="DRB32";
//$mozne_sloupce['DRB41']="DRB41";
//$mozne_sloupce['DRB42']="DRB42";
//$mozne_sloupce['DRB51']="DRB51";
//$mozne_sloupce['DRB52']="DRB52";
//$mozne_sloupce['DQB11']="DQB11";
//$mozne_sloupce['DQB12']="DQB12";
//$mozne_sloupce['DQA11']="DQA11";
//$mozne_sloupce['DQA12']="DQA12";
//$mozne_sloupce['DPB11']="DPB11";
//$mozne_sloupce['DPB12']="DPB12";
//$mozne_sloupce['DPA11']="DPA11";
//$mozne_sloupce['DPA12']="DPA12";
//$mozne_sloupce['DNA_A2']="DNA_A2";
//$mozne_sloupce['DNA_B1']="DNA_B1";
//$mozne_sloupce['DNA_B2']="DNA_B2";
//$mozne_sloupce['DNA_C1']="DNA_C1";
//$mozne_sloupce['DNA_C2']="DNA_C2";


$sloupce_napoprve_ne=array("Ethnic","ABO","CMV","CMVDate","ProbMM0","ProbMM1");
		
if($_POST['action']=="column_select"):

	mysql_query("DELETE FROM uzivatel_sr_sloupce WHERE ID_uzivatele='".$_SESSION['usr_ID']."'");

	foreach($_POST as $sloupec_chci=>$hodnota):
	
		if($sloupec_chci!="action"):
			mysql_query("INSERT INTO uzivatel_sr_sloupce (ID_uzivatele, sloupec) VALUES
				('".$_SESSION['usr_ID']."',
				'".mysql_real_escape_string($sloupec_chci)."')");
		endif;
		
	endforeach;
endif;

//* =============================================================================
//	Pri prvnim zobrazeni vlozit preddefinovane sloupce
//============================================================================= */
$sloupce_definovane = mysql_result(mysql_query("SELECT COUNT(*) FROM uzivatel_sr_sloupce WHERE ID_uzivatele='".$_SESSION['usr_ID']."'"), 0);
if($sloupce_definovane==0):
	foreach($mozne_sloupce as $sloupec=>$nazev_sloupce):
	
		if(!in_array($sloupec,$sloupce_napoprve_ne)):
			mysql_query("INSERT INTO uzivatel_sr_sloupce (ID_uzivatele, sloupec) VALUES
				('".$_SESSION['usr_ID']."',
				'".mysql_real_escape_string($sloupec)."')");
		endif;
		
	endforeach;
endif;



//* =============================================================================
//	Nacist seznam pozadovanych sloupcu
//============================================================================= */
$pozadovane_sloupce=array();
$vysledek = mysql_query("SELECT sloupec FROM uzivatel_sr_sloupce WHERE ID_uzivatele='".$_SESSION['usr_ID']."'");
	if(mysql_num_rows($vysledek) > 0):
		while($zaz = mysql_fetch_array($vysledek)):
			extract($zaz);
			$pozadovane_sloupce[]=$sloupec;
		endwhile;
	endif;
@mysql_free_result($vysledek);
	


	
	
//* =============================================================================
//	Filtr dle hodnot sloupce
//============================================================================= */
$_sloupce=array();
if($_POST['action']=="column_filter"):

	if($_POST['where_hodnoty_old']):
		foreach(explode("/*/",$_POST['where_hodnoty_old']) as $sada):
			
			if(count($sada)>0):
				$_sada=explode("=",$sada);
				
				foreach(explode("|",$_sada[1]) as $data):
					if($_sada[0] && $data):
						$_hodnoty[$_sada[0]][]=$data;
					endif;
				endforeach;	
			endif;
			
		endforeach;
	endif;

	foreach($_POST as $klic=>$hodnota):
	
		$explode=explode("_",$klic);
		if($explode[0]=="filter"):
			$_hodnoty[$explode[1]][]=$hodnota;
		endif;
	
	endforeach;
	
	
	if(count($_hodnoty)>0):
		
		foreach($_hodnoty as $sloupec_hodnot=>$pole_hodnot):
			if(count($pole_hodnot)>0):
				$where_hodnoty.=" AND $sloupec_hodnot IN('".implode("', '",$pole_hodnot)."')";
				$_sloupce[]=$sloupec_hodnot;
			endif;
		endforeach;

	else:
		$where_hodnoty="AND ID IN(0)";
	endif;
	
	//echo "<br>".$where_hodnoty."<br>";
	//print_r($_hodnoty);
	//print_r($_sloupce);

endif;


//* =============================================================================
//	Filtr dle hodnot sloupce - nove v db
//============================================================================= */
$_sloupce=array();
if($_POST['action']=="column_filter_new"):

	foreach($_POST as $klic=>$hodnota):
		$explode=explode("_",$klic);
		if($explode[0]=="filter"):
			$_hodnoty[$explode[1]][]=$hodnota;
		endif;
	endforeach;
	
	if(count($_hodnoty)>0):
		
		foreach($_hodnoty as $sloupec_hodnot=>$pole_hodnot):
			if(count($pole_hodnot)>0):
				//ulozit do db - vlozit nebo update
				mysql_query("INSERT INTO uzivatel_sr_filtry (ID_uzivatele, sloupec, hodnoty) VALUES
				('".$_SESSION['usr_ID']."',
				'".mysql_real_escape_string($sloupec_hodnot)."',
				'".mysql_real_escape_string(implode("|*|",$pole_hodnot))."')
				ON DUPLICATE KEY UPDATE hodnoty='".mysql_real_escape_string(implode("|*|",$pole_hodnot))."'");
			endif;
		endforeach;

	else:
		//nejsou vybrane zadne hodnoty - nic se tedy nema zobrazit
		//ulozit do db - vlozit nebo update
		mysql_query("INSERT INTO uzivatel_sr_filtry (ID_uzivatele, sloupec, hodnoty) VALUES
		('".$_SESSION['usr_ID']."',
		'".mysql_real_escape_string($_POST['column_name'])."',
		'xoxo*')
		ON DUPLICATE KEY UPDATE hodnoty='xoxo*'");
	endif;

endif;
	

//* =============================================================================
//	Vytvorit filtr sloupcu
//============================================================================= */
$_hodnoty=array();
$where_hodnoty="";
$vysledek = mysql_query("SELECT sloupec, hodnoty FROM uzivatel_sr_filtry WHERE ID_uzivatele='".$_SESSION['usr_ID']."'");
	if(mysql_num_rows($vysledek) > 0):
		while($zaz = mysql_fetch_array($vysledek)):
			extract($zaz);
			$_hodnoty[$sloupec]=explode("|*|",$hodnoty);
			$where_hodnoty.=" AND $sloupec IN('".implode("', '",$_hodnoty[$sloupec])."')";
		endwhile;
	endif;
@mysql_free_result($vysledek);
//echo "<br>".$where_hodnoty."<br>";
//print_r($_hodnoty);



//* =============================================================================
//	Filtrovac� formul��
//============================================================================= */
echo "<form method=\"GET\" action=\"$_SERVER[PHP_SELF]\" name=\"formular_razeni\" style=\"clear:both; margin-bottom:20px;\">";

	$vysledek = mysql_query("SELECT last_name, first_name, RegID, date_birth, ci_fa_a, ci_fa_b, ci_fa_c, ci_sa_a, ci_sa_b, ci_sa_c,
	cii_fa_a, cii_fa_b, cii_fa_c, cii_fa_d, cii_sa_a, cii_sa_b, cii_sa_c, cii_sa_d
	FROM search_request WHERE search_request.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' AND RegID='".mysql_real_escape_string($_GET['Rid'])."' LIMIT 1");
		if(mysql_num_rows($vysledek) > 0):
			$zaz = mysql_fetch_array($vysledek);
				extract($zaz);
		endif;
	@mysql_free_result($vysledek);



	
	echo "<div style=\"float:left; width:100%; margin-bottom:19px; text-align:left; font-size:16px;\">Donors for patient: <b>$last_name $first_name</b>, Patient ID: <b>".$RegID.$_GET['PatientNum']."P</b>, Date of birth: "; if($date_birth<>0): echo date($_SESSION['date_format_php'],$date_birth); endif; echo "</div>";
	echo "<div style=\"float:left; width:100%; margin-bottom:1px; text-align:left;\">
		<table cellspacing=\"0\" width=\"560\" style=\"border:0; border-bottom:1px solid #b8b8b8;\" id=\"tb-form\">
			<tr>
				<td width=\"23%\" style=\"height:5px; padding: 2px 0 2px 0;\"></td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">A</td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">B</td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">C</td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">DRB1</td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">DRB3/4/5</td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">DQB1</td>
				<td width=\"11%\" style=\"height:5px; text-align:center; padding: 2px 0 2px 0;\">DPB1</td>
			</tr>
			<tr>
				<td style=\"height:20px; padding: 2px 0 2px 4px;\"><b>First antigen:</b></td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$ci_fa_a</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$ci_fa_b</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$ci_fa_c</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_fa_a</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_fa_b</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_fa_c</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_fa_d</td>
			</tr>
			<tr>
				<td style=\"height:20px; padding: 2px 0 2px 4px;\"><b>Second antigen:</b></td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$ci_sa_a</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$ci_sa_b</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$ci_sa_c</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_sa_a</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_sa_b</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_sa_c</td>
				<td style=\"height:20px; padding: 2px 0 2px 0; text-align:center;\">$cii_sa_d</td>
			</tr>
		</table>
	</div>";

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
	
	echo "
	<div style=\"float:left; width:60px; text-align:left; margin-top:4px; margin-bottom:15px;\"><b>Sort by:</b></div>
	<div style=\"float:left; text-align:left; margin-top:4px; width:255px;\"><label for=\"razeni1\">Latest First - Sort accorting to date and HUBs</label></div><div style=\"float:left; width:50px; text-align:left;\"><input type=\"radio\" name=\"razeni\" id=\"razeni1\" value=\"1\""; if(!$_GET['razeni'] || $_GET['razeni']==1): echo " checked"; endif; echo " onclick=\"document.formular_razeni.submit();\"></div>
	<div style=\"float:left; text-align:left; margin-top:4px; width:150px;\"><label for=\"razeni2\">Best First by Match Grade</label></div><div style=\"float:left; width:50px; text-align:left;\"><input type=\"radio\" name=\"razeni\" id=\"razeni2\" value=\"2\""; if($_GET['razeni']==2): echo " checked"; endif; echo " onclick=\"document.formular_razeni.submit();\"></div>
	<div style=\"float:left; text-align:left; margin-top:4px; width:265px;\"><label for=\"razeni3\">Best First by Probability - P(10/10), P(9/10), Age</label></div><div style=\"float:left; width:50px; text-align:left;\"><input type=\"radio\" name=\"razeni\" id=\"razeni3\" value=\"3\""; if($_GET['razeni']==3): echo " checked"; endif; echo " onclick=\"document.formular_razeni.submit();\"></div>";

	
	echo "<input type=\"hidden\" name=\"h\" value=\"".$_GET['h']."\">";
	echo "<input type=\"hidden\" name=\"PatientNum\" value=\"$_GET[PatientNum]\">";
	echo "<input type=\"hidden\" name=\"Rid\" value=\"$_GET[Rid]\">";
		
echo "</form>";
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











//* =============================================================================
//	Str�nkov�n�
//============================================================================= */
//strankovani("patient_donor WHERE patient_donor.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' $where_hledej", $_na_stranku);



//* =============================================================================
//	Defaultn� �azen�
//============================================================================= */
razeni('RecordUpdate DESC, Hub ASC, MatchGradeInt ASC, PhenotypeQuality DESC, DonorNumber DESC');	//defaultni razeni

if(!$_GET['col']):	//pokud neni razeni pomoci vybraneho sloupce
	if($_GET['razeni']==1):
		razeni('RecordUpdate DESC, Hub ASC, MatchGradeInt ASC, PhenotypeQuality DESC, DonorNumber DESC');
	endif;
	if($_GET['razeni']==2):
		razeni('MatchGradeInt ASC, PhenotypeQuality DESC, DonorNumber DESC');
	endif;
	if($_GET['razeni']==3):
		razeni('ProbMM0 DESC, ProbMM1 DESC, MMCount ASC, MMAntCount ASC, BirthDate DESC');
	endif;
else:
	razeni('RecordUpdate DESC, Hub ASC, MatchGradeInt ASC, PhenotypeQuality DESC, DonorNumber DESC');
endif;


$counter=0;

	
//-----vypis dat
//echo $where_hodnoty;
$vysledek = mysql_query("SELECT * FROM patient_donor WHERE patient_donor.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' AND patient_donor.RegID='".mysql_real_escape_string($_GET['Rid'])."'
 $where_hodnoty $where_hledej ORDER BY $orderby");
	$num = mysql_num_rows($vysledek);
	

		echo "<table cellspacing=\"0\" style=\"clear:left;\" id=\"tab_vypis\">
			<thead id=\"tb-head\">
			  <tr>
				<td style=\"padding:0; width:12px;\"><div style=\"position:relative; float:left;\"><img src=\"img/ico/column.png\" width=\"10\" height=\"10\" id=\"column_select\" alt=\"Select columns\" style=\"cursor:pointer;\">
					<div id=\"div_column_select\" style=\"display:none;\">";
						echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"form_column_select\">";
						
							echo "<div style=\"float:left; width:160px;\"><div style=\"float:left; width:20px;\"><input type=\"checkbox\" name=\"check_all\" id=\"check_all\" value=\"1\"></div><div style=\"float:left; width:140px; margin-top:5px;\"><label for=\"check_all\"><i>check all</i></label></div></div>";
						
							foreach($mozne_sloupce as $sloupec=>$nazev_sloupce):
								echo "<div style=\"float:left; width:160px;\"><div style=\"float:left; width:20px;\"><input type=\"checkbox\" name=\"$sloupec\" id=\"$sloupec\" class=\"sloupec\" value=\"1\""; if(in_array($sloupec,$pozadovane_sloupce)): echo " checked"; endif; echo "></div><div style=\"float:left; width:140px; margin-top:5px;\"><label for=\"$sloupec\">$nazev_sloupce</label></div></div>";
							endforeach;
							echo "<div style=\"float:left; width:100%; height:30px; margin-top:10px; text-align:center;\"><input type=\"submit\" class=\"form-ok2\" value=\"\"></div>";
							echo "<input type=\"hidden\" name=\"action\" value=\"column_select\">";
						echo "</form>";
					echo "</div>
				</td>";
				
				//vypis pozadovanych sloupcu
				//foreach($pozadovane_sloupce as $sloupec_db):
				
				
				foreach($mozne_sloupce as $sloupec_db=>$nazev_sl):
					if(in_array($sloupec_db,$pozadovane_sloupce)):
						echo "<td>
						<div style=\"position:relative; float:left;\">".order($mozne_sloupce[$sloupec_db], $sloupec_db)."";
						
							echo "&nbsp;<img src=\"img/ico/filter.png\" width=\"10\" height=\"10\" class=\"filter_column\" id=\"col_$sloupec_db\" alt=\"Filter column\" style=\"cursor:pointer;\">";
							
							echo "<div id=\"filter_col_$sloupec_db\" class=\"div_column_filter\" style=\"display:none; padding:8px;\">";
							
								echo "<img src=\"img/ico/close.png\" width=\"14\" height=\"14\" class=\"filter_close\" alt=\"close\">";

								echo "<form style=\"margin:0;padding:0;\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"form_filter\">";
									$poradi=1;
									$vysledek_sub = mysql_query("SELECT DISTINCT(".mysql_real_escape_string($sloupec_db).") AS hodnota FROM patient_donor WHERE patient_donor.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' AND patient_donor.RegID='".mysql_real_escape_string($_GET['Rid'])."' 
									$where_hledej ORDER BY $sloupec_db");
										if(mysql_num_rows($vysledek_sub) > 0):
											while($zaz_sub = mysql_fetch_array($vysledek_sub)):
												extract($zaz_sub);

												$checkbox_name="filter_".$sloupec_db."_".$poradi;
												echo "<div style=\"clear:left; float:left; width:180px; padding:2px;\"><div style=\"float:left; width:20px;\"><input type=\"checkbox\" name=\"$checkbox_name\" id=\"$checkbox_name\" value=\"$hodnota\""; 
													
													if(empty($_hodnoty[$sloupec_db])):	//pokud neni na tento sloupec zadny filtr na hodnoty, vse zaskrtnuto
														echo " checked";
													else:
														if(in_array($hodnota,$_hodnoty[$sloupec_db])):
															echo " checked";
														endif;
													endif;
													
													/*
													if($_POST['action']!="column_filter_new"):
														echo " checked";
													else:
														if(!in_array($sloupec_db,$_sloupce)):
															echo " checked";
														else:
															foreach($_hodnoty as $sloupec_hodnot=>$data_hodnot):
																if(in_array($hodnota,$data_hodnot)):
																	echo " checked";
																endif;
															endforeach;
														endif;
													endif;
													*/
													
													//uprava hodnot pro zobrazeni
													if($sloupec_db=="RecordUpdate"):
														if($hodnota>0 && date("Y",$hodnota)!=1970):
															$hodnota=date($_SESSION['date_format_php'],$hodnota);
														else:
															$hodnota="";
														endif;
													endif;
													
												echo "></div><div style=\"float:left; width:160px; margin-top:5px;\"><label for=\"$checkbox_name\">$hodnota</label></div></div>";
											
											$poradi++;
											endwhile;
										endif;
									@mysql_free_result($vysledek_sub);
								
								echo "<div style=\"float:left; width:100%; height:30px; margin-top:10px; text-align:center;\"><input type=\"submit\" class=\"form-ok2\" value=\"\"></div>";
								echo "<input type=\"hidden\" name=\"action\" value=\"column_filter_new\">";
								echo "<input type=\"hidden\" name=\"column_name\" value=\"".$sloupec_db."\">";
								
								//echo "<textarea style=\"display:none;\" name=\"where_hodnoty_old\">";
								//	if(count($_hodnoty)>0):
								//	foreach($_hodnoty as $sloupec_hodnot=>$pole_hodnot):
								//		if($sloupec_db!=$sloupec_hodnot):
								//			echo $sloupec_hodnot."=".implode("|",$pole_hodnot);
								//			echo "/*/";
								//		endif;
								//	endforeach;
								//	endif;
								//echo "</textarea>";
								
								echo "</form>";
							
							echo "</div>";
						
						echo "</div></td>";
					endif;
				endforeach;
				
				
				
				
				
				echo "<td width=\"8%\">Action</td>
				<td></td>
			  </tr>
			</thead>";
			
			echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">";
		

		if($num > 0):		
			echo "<tbody id=\"tb-body\">";
			
			
			
			$barva['ABDRAlleleMismatch']="146da0";	//modra
			$barva['cxStyleABDRSplitBroadMismatch']="11b699";	//tyrkys
			$barva['ABDRSplitBroadMismatch']="11b699";	//tyrkys
			
			
			
			while($zaz = mysql_fetch_array($vysledek)){

				extract($zaz);
			
				++$counter;
			
				$last_typing="";
				$last_sample="";
				$last_workup="";
				
				//posledni typing request
				$vysledek_sub = mysql_query("SELECT typing_request.date_completing AS last_typing 
				FROM typing_request 
				LEFT JOIN typing_request_donor ON(typing_request.ID=typing_request_donor.ID_typing)
				WHERE typing_request.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' AND typing_request_donor.DonorID='".mysql_real_escape_string($ID2)."' AND typing_request.RegID='".mysql_real_escape_string($RegID)."'
				GROUP BY typing_request.ID ORDER BY typing_request.date_completing DESC LIMIT 1");
					if(mysql_num_rows($vysledek_sub)>0):
						$zaz_sub = mysql_fetch_assoc($vysledek_sub);
							extract($zaz_sub);
							$last_typing=date($_SESSION['date_format_php'],$last_typing);
					endif;
				@mysql_free_result($vysledek_sub);
				
				//posledni sample request
				$vysledek_sub = mysql_query("SELECT sample_request.date_completing AS last_sample 
				FROM sample_request 
				LEFT JOIN sample_request_donor ON(sample_request.ID=sample_request_donor.ID_sample)
				WHERE sample_request.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' AND sample_request_donor.DonorID='".mysql_real_escape_string($ID2)."' AND sample_request.RegID='".mysql_real_escape_string($RegID)."'
				ORDER BY sample_request.date_completing DESC LIMIT 1");
					if(mysql_num_rows($vysledek_sub)>0):
						$zaz_sub = mysql_fetch_assoc($vysledek_sub);
							extract($zaz_sub);
							$last_sample=date($_SESSION['date_format_php'],$last_sample);
					endif;
				@mysql_free_result($vysledek_sub);
				
				//posledni workup request
				$vysledek_sub = mysql_query("SELECT workup_request.date_completing AS last_workup 
				FROM workup_request 
				WHERE workup_request.PatientNum='".mysql_real_escape_string($_GET['PatientNum'])."' AND workup_request.DonorID='".mysql_real_escape_string($ID2)."' AND workup_request.RegID='".mysql_real_escape_string($RegID)."'
				ORDER BY workup_request.date_completing DESC LIMIT 1");
					if(mysql_num_rows($vysledek_sub)>0):
						$zaz_sub = mysql_fetch_assoc($vysledek_sub);
							extract($zaz_sub);
							$last_workup=date($_SESSION['date_format_php'],$last_workup);
					endif;
				@mysql_free_result($vysledek_sub);
				
				
				
			
				if($counter%2==0):
					$styl="class=\"barva1\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva1'\"";
				else:
					$styl="class=\"barva2\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva2'\"";
				endif;
					
			
				echo "<tr ".$styl.">";
					echo "<td style=\"padding:0;\"></td>";
					
					//vypis dat do pozadovanych sloupcu
					foreach($mozne_sloupce as $sloupec_db=>$nazev_sl):
						if(in_array($sloupec_db,$pozadovane_sloupce)):
							
							if($sloupec_db=="RecordUpdate"):
								if($RecordUpdate>0 && date("Y",$RecordUpdate)!=1970):

									${$sloupec_db}=date($_SESSION['date_format_php'],$RecordUpdate);
								else:
									${$sloupec_db}="";
								endif;
							endif;
							
							if($sloupec_db=="lasttyping"):
								${$sloupec_db}=$last_typing;
							endif;
							
							if($sloupec_db=="lastsample"):
								${$sloupec_db}=$last_sample;
							endif;
							
							if($sloupec_db=="lastworkup"):
								${$sloupec_db}=$last_workup;
							endif;
							
							
							
							$bold1="<b>";
							$bold2="</b>";
							
							if($sloupec_db=="A1"):
								if(${$sloupec_db}==$ci_fa_a): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$A1Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="A2"):
								if(${$sloupec_db}==$ci_sa_a): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$A2Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="B1"):
								if(${$sloupec_db}==$ci_fa_b): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$B1Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="B2"):
								if(${$sloupec_db}==$ci_sa_b): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$B2Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="C1"):
								if(${$sloupec_db}==$ci_fa_c): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$C1Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="C2"):
								if(${$sloupec_db}==$ci_sa_c): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$C2Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="DRB11x"):
								if(${$sloupec_db}==$cii_fa_a): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$DRB11Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="DRB12x"):
								if(${$sloupec_db}==$cii_sa_a): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$DRB12Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="DQB11x"):
								if(${$sloupec_db}==$cii_fa_c): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$DQB11Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							if($sloupec_db=="DQB12x"):
								if(${$sloupec_db}==$cii_sa_c): unset($bold1, $bold2); endif;
								${$sloupec_db}=$bold1."<span style=\"color:#".$barva[$DQB12Style]."\">".${$sloupec_db}."</span>".$bold2;
							endif;
							
							/*
							$mozne_sloupce['ProbMM0']="P(10/10)";
$mozne_sloupce['ProbMM1']="P(9/10)";

$mozne_sloupce['PROBA']="P(A)";
$mozne_sloupce['PROBB']="P(B)";
$mozne_sloupce['PROBC']="P(C)";
$mozne_sloupce['PROBDR']="P(DR)";
$mozne_sloupce['PROBDQ']="P(DQ)";
*/
							if($sloupec_db=="ProbMM0"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;
							if($sloupec_db=="ProbMM1"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;
							if($sloupec_db=="PROBA"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;
							if($sloupec_db=="PROBB"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;
							if($sloupec_db=="PROBC"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;
							if($sloupec_db=="PROBDR"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;
							if($sloupec_db=="PROBDQ"):
								${$sloupec_db}=round(${$sloupec_db})."%";
							endif;

						
							echo "<td>".${$sloupec_db}."</td>";
						endif;
					endforeach;
				
					$hash=sha1($ID."SaltIsGoodForLife45");
					
					echo "<td><a href=\"results-donor.php?ID=$ID&amp;h=".$hash."\" title=\"VIEW\">".$ico_upravit."</a>";
					//echo "&nbsp;<a href=\"donor-typing-request.php?Pn=".$PatientNum."&amp;RegID=".$RegID."&amp;donors=$ID2\" title=\"NEW TYPING REQUEST\"><img src=\"img/ico/typing.png\" width=\"20\" height=\"20\" alt=\"NEW TYPING REQUEST\" style=\"margin-top:3px;\"></a>";
					//echo "&nbsp;<a href=\"donor-sample-request.php?Pn=".$PatientNum."&amp;RegID=".$RegID."&amp;donors=$ID2\" title=\"NEW SAMPLE REQUEST\"><img src=\"img/ico/typing.png\" width=\"20\" height=\"20\" alt=\"NEW SAMPLE REQUEST\" style=\"margin-top:3px;\"></a>";
					echo "</td>";
					
					echo "<td><input type=\"checkbox\" name=\"cb_$ID\" value=\"$PatientNum|$ID2|$RegID\"></td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			
			
			echo "<div style=\"float:right; width:56px; margin-left:7px;\"><input type=\"submit\" class=\"form-ok\" value=\"\"></div>";
			
			echo "<div style=\"float:right; width:300px; text-align:right;\">For selected: ";
			echo "<select name=\"operation\" id=\"operation\">
				<option value=\"1\""; if($operation==1): echo " selected"; endif; echo ">send typing request</option>
				<option value=\"2\""; if($operation==2): echo " selected"; endif; echo ">send sample request</option>
				<option value=\"3\""; if($operation==3): echo " selected"; endif; echo ">send workup request</option>
			</select>";
			
			echo "<input type=\"hidden\" name=\"action\" value=\"request\">";
			echo "</div>";
			
			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send\" value=\"\"></p>";

			
		echo "</form>";
		
		
		
		
		//* =============================================================================
		//	Str�nkov�n�
		//============================================================================= */
		//strankovani2();
		
		
			
	else:
	
		echo "</tbody></table>";
		echo "<p>&nbsp;</p>";
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
	
	
	$('input[name=check_all]').live('click', function(){  

		if(this.checked) {
			$('.sloupec').attr('checked',true);
		}else{
			$('.sloupec').attr('checked',false);	
		}
	
	});
	
			
</SCRIPT>";


}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>			

<SCRIPT language="JavaScript" type="text/javascript">

$(document).ready(function(){

	$sirka_tabulky=$("#tab_vypis").width();
	
	
	$width=$sirka_tabulky+300;
	
	if($width<=<? echo $width; ?>){
		$width=<? echo $width; ?>;
		$width_zaklad='100%';
		$("#tab_vypis").css('width','100%');
	}else{
		$width_zaklad=$width+500;
	}
	
	if($width<=$(window).width()){
		$width_zaklad='100%';
	}

	
	$(".zaklad0").css('width',$width_zaklad);
	$(".zaklad").css('width',$width);
	$(".prava").css('width',$width-229);
	$(".zalozky").css('width',$width-235);
	$(".vnitrek-obal").css('width',$width-239);
	$(".stin-dole").css('width',$width-235);
	$(".vnitrek").css('width',$width-279);
	$(".patka").css('width',$width_zaklad);
	
});
			
</SCRIPT>			
			
<?
include('1-end.php');
?>