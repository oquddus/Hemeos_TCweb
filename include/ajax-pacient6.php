<?
header('Content-type: text/html; charset=windows-1250'); 

//--vytvoření session
session_start(); 

if($_SESSION['usr_ID']):

//* =============================================================================
//	Ziskat udaje o darci pacienta atd.
//============================================================================= */
require "../opendb.php";
	
	echo "<select name=\"DonorID\" id=\"DonorID\" class=\"selectbox_donora\" style=\"width:230px;\">";
	echo "<option value=\"\">- Donor ID# -</option>";
	
	if($_GET['id_pacienta']):

		$vysledek_s = mysql_query("SELECT ID2, DonorNumber FROM patient_donor WHERE PatientNum='".mysql_real_escape_string($_GET['id_pacienta'])."' AND RegID='".mysql_real_escape_string($_GET['RegID'])."' ORDER BY ID2");
			if(mysql_num_rows($vysledek_s)>0):
				while($zaz_s = mysql_fetch_array($vysledek_s)):
					extract($zaz_s);
							
						echo "<option value=\"$ID2\">$ID2</option>";

				endwhile;
			endif;	
		@mysql_free_result($vysledek_s);

	endif;
	
	echo "</select>";

endif;
?>

