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
	
		$vysledek_s = mysql_query("SELECT CONCAT(last_name,' ',first_name) AS jmeno, PatientNum, date_birth, RegID, ci_fa_a, ci_fa_b, ci_fa_c, ci_sa_a, ci_sa_b, ci_sa_c,
		cii_fa_a, cii_fa_b, cii_fa_c, cii_fa_d, cii_sa_a, cii_sa_b, cii_sa_c, cii_sa_d, gender, drb345_select, search_urgent, 
		nastaveni_diagnoza.diagnoza FROM search_request LEFT JOIN nastaveni_diagnoza ON(search_request.diagnosis=nastaveni_diagnoza.ID) 
		WHERE PatientNum='".mysql_real_escape_string($_GET['id_pacienta'])."' AND RegID='".mysql_real_escape_string($_GET['RegID'])."' LIMIT 1");
			if(mysql_num_rows($vysledek_s)>0):
				while($zaz_s = mysql_fetch_array($vysledek_s)):
					extract($zaz_s);
					
				endwhile;
			endif;	
		@mysql_free_result($vysledek_s);

	endif;
	
	
	echo "<input type=\"hidden\" name=\"aj_jmeno\" id=\"aj_jmeno\" value=\"$jmeno\">";
	echo "<input type=\"hidden\" name=\"aj_PatientNum\" id=\"aj_PatientNum\" value=\"$PatientNum\">";
	echo "<input type=\"hidden\" name=\"aj_date_birth\" id=\"aj_date_birth\" value=\"".date($_SESSION['date_format_php'],$date_birth)."\">";
	echo "<input type=\"hidden\" name=\"aj_RegID\" id=\"aj_RegID\" value=\"$RegID\">";
	echo "<input type=\"hidden\" name=\"aj_diagnoza\" id=\"aj_diagnoza\" value=\"$diagnoza\">";
	echo "<input type=\"hidden\" name=\"aj_gender\" id=\"aj_gender\" value=\"$gender\">";
	
	echo "<input type=\"hidden\" name=\"aj_search_urgent\" id=\"aj_search_urgent\" value=\"$search_urgent\">";
	
	
	
	echo "<input type=\"hidden\" name=\"aj_ci_fa_a\" id=\"aj_ci_fa_a\" value=\"$ci_fa_a\">";
	echo "<input type=\"hidden\" name=\"aj_ci_fa_b\" id=\"aj_ci_fa_b\" value=\"$ci_fa_b\">";
	echo "<input type=\"hidden\" name=\"aj_ci_fa_c\" id=\"aj_ci_fa_c\" value=\"$ci_fa_c\">";
	echo "<input type=\"hidden\" name=\"aj_ci_sa_a\" id=\"aj_ci_sa_a\" value=\"$ci_sa_a\">";
	echo "<input type=\"hidden\" name=\"aj_ci_sa_b\" id=\"aj_ci_sa_b\" value=\"$ci_sa_b\">";
	echo "<input type=\"hidden\" name=\"aj_ci_sa_c\" id=\"aj_ci_sa_c\" value=\"$ci_sa_c\">";
	echo "<input type=\"hidden\" name=\"aj_cii_fa_a\" id=\"aj_cii_fa_a\" value=\"$cii_fa_a\">";
	echo "<input type=\"hidden\" name=\"aj_cii_fa_b\" id=\"aj_cii_fa_b\" value=\"$cii_fa_b\">";
	
	echo "<input type=\"hidden\" name=\"aj_cii_fa_c\" id=\"aj_cii_fa_c\" value=\"$cii_fa_c\">";
	echo "<input type=\"hidden\" name=\"aj_cii_fa_d\" id=\"aj_cii_fa_d\" value=\"$cii_fa_d\">";
	echo "<input type=\"hidden\" name=\"aj_cii_sa_a\" id=\"aj_cii_sa_a\" value=\"$cii_sa_a\">";
	echo "<input type=\"hidden\" name=\"aj_cii_sa_b\" id=\"aj_cii_sa_b\" value=\"$cii_sa_b\">";
	
	echo "<input type=\"hidden\" name=\"aj_cii_sa_c\" id=\"aj_cii_sa_c\" value=\"$cii_sa_c\">";
	echo "<input type=\"hidden\" name=\"aj_cii_sa_d\" id=\"aj_cii_sa_d\" value=\"$cii_sa_d\">";
	
	if($drb345_select=="DRB 3"): $kam=3; endif;
	if($drb345_select=="DRB 4"): $kam=4; endif;
	if($drb345_select=="DRB 5"): $kam=5; endif;
	
	echo "<input type=\"hidden\" name=\"aj_kam\" id=\"aj_kam\" value=\"$kam\">";

endif;
?>

