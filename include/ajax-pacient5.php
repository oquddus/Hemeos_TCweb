<?
header('Content-type: text/html; charset=windows-1250'); 

//--vytvoøení session
session_start(); 

if($_SESSION['usr_ID']):

//* =============================================================================
//	Ziskat udaje o pacientovi atd.
//============================================================================= */
require "../opendb.php";
	
	if($_GET['id_pacienta']):
	
		$vysledek_s = mysql_query("SELECT * FROM search_request WHERE PatientNum='".mysql_real_escape_string($_GET['id_pacienta'])."' AND RegID='".mysql_real_escape_string($_GET['RegID'])."' LIMIT 1");
			if(mysql_num_rows($vysledek_s)>0):
				while($zaz_s = mysql_fetch_array($vysledek_s)):
					extract($zaz_s);
					
					unset($gender_text);
					if($gender==1):
						$gender_text="Female";
					endif;
					if($gender==2):
						$gender_text="Male";
					endif;
					
					unset($cvm_text);
					if($cvm_status==1):
						$cvm_text="Positive";
					endif;
					if($cvm_status==2):
						$cvm_text="Negative";
					endif;
					
				endwhile;
			endif;	
		@mysql_free_result($vysledek_s);

	endif;
	
	
	echo "<input type=\"hidden\" name=\"aj_jmeno\" id=\"aj_jmeno\" value=\"$last_name $first_name\">";
	echo "<input type=\"hidden\" name=\"aj_PatientNum\" id=\"aj_PatientNum\" value=\"$PatientNum\">";
	echo "<input type=\"hidden\" name=\"aj_date_birth\" id=\"aj_date_birth\" value=\"".$date_birth."\">";
	echo "<input type=\"hidden\" name=\"aj_date_birth_text\" id=\"aj_date_birth_text\" value=\"".date($_SESSION['date_format_php'],$date_birth)."\">";
	echo "<input type=\"hidden\" name=\"aj_RegID\" id=\"aj_RegID\" value=\"$RegID\">";
	
	echo "<input type=\"hidden\" name=\"aj_gender\" id=\"aj_gender\" value=\"$gender\">";
	echo "<input type=\"hidden\" name=\"aj_gender_text\" id=\"aj_gender_text\" value=\"$gender_text\">";
	
	echo "<input type=\"hidden\" name=\"aj_weight\" id=\"aj_weight\" value=\"$weight\">";
	
	echo "<input type=\"hidden\" name=\"aj_cmv\" id=\"aj_cmv\" value=\"$cvm_status\">";
	echo "<input type=\"hidden\" name=\"aj_cmv_text\" id=\"aj_cmv_text\" value=\"$cvm_text\">";
	echo "<input type=\"hidden\" name=\"aj_rhesus_1\" id=\"aj_rhesus_1\" value=\"$rhesus_1\">";
	echo "<input type=\"hidden\" name=\"aj_rhesus_2\" id=\"aj_rhesus_2\" value=\"$rhesus_2\">";
	echo "<input type=\"hidden\" name=\"aj_rhesus_text\" id=\"aj_rhesus_text\" value=\"$rhesus_1 $rhesus_2\">";

endif;
?>

