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

		$vysledek_s = mysql_query("SELECT * FROM patient_donor 
		WHERE PatientNum='".mysql_real_escape_string($_GET['id_pacienta'])."' AND ID2='".mysql_real_escape_string($_GET['id_darce'])."' AND RegID='".mysql_real_escape_string($_GET['RegID'])."' LIMIT 1");
			if(mysql_num_rows($vysledek_s)>0):
				$zaz_s = mysql_fetch_array($vysledek_s);
					extract($zaz_s);
				
			endif;	
		@mysql_free_result($vysledek_s);

	endif;
	
	
	echo "<input type=\"hidden\" name=\"aj_donor_reg\" id=\"aj_donor_reg\" value=\"$Hub\">";
	echo "<input type=\"hidden\" name=\"aj_donor_date_birth\" id=\"aj_donor_date_birth\" value=\"$BirthDate\">";	//zde jako varchar
	echo "<input type=\"hidden\" name=\"aj_donor_cmv\" id=\"aj_donor_cmv\" value=\"$CMV\">";
	echo "<input type=\"hidden\" name=\"aj_donor_blood\" id=\"aj_donor_blood\" value=\"$ABO\">";
	echo "<input type=\"hidden\" name=\"aj_donor_gender\" id=\"aj_donor_gender\" value=\"$Sex\">";

endif;
?>

