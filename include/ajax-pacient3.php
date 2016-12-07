<?
header('Content-type: text/html; charset=windows-1250'); 

//--vytvoření session
session_start(); 

if($_SESSION['usr_ID']):

//* =============================================================================
//	Ziskat udaje o darci pacienta atd.
//============================================================================= */
require "../opendb.php";
	
	if($_GET['id_darce']):

		$_sloupce=array("A1","A2","B1","B2","C1","C2","DRB11x","DRB12x","DQB11x","DQB12x","DRB11","DRB12","DRB31","DRB41","DRB42","DRB51","DRB52","DQB11","DQB12","DQA11","DQA12","DPB11","DPB12","DPA11","DPA12","DNA_A1","DNA_A2","DNA_B1","DNA_B2","DNA_C1","DNA_C2");
		
		$vysledek_s = mysql_query("SELECT ".implode(", ",$_sloupce)." FROM patient_donor WHERE PatientNum='".mysql_real_escape_string($_GET['id_pacienta'])."' AND ID2='".mysql_real_escape_string($_GET['id_darce'])."' AND RegID='".mysql_real_escape_string($_GET['RegID'])."' LIMIT 1");
			if(mysql_num_rows($vysledek_s)>0):
				$zaz_s = mysql_fetch_array($vysledek_s);
					extract($zaz_s);
					
					foreach($_sloupce as $hodnota):
					
						echo "<div style=\"float:left; width:130px; margin:0 5px 0 0;\"><b>$hodnota:</b> ".${$hodnota}."</div>";
					
					endforeach;
					
			endif;	
		@mysql_free_result($vysledek_s);

	endif;

endif;
?>

