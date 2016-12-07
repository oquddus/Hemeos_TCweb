<?
header('Content-type: text/html; charset=windows-1250'); 

//--vytvoøení session
session_start(); 

if($_SESSION['usr_ID']):


//* =============================================================================
//	Prirazeni k transplant. centru podle registru
//============================================================================= */
require "../opendb.php";
	
	echo "<select name=\"ID_centra\" id=\"ID_centra\" style=\"width:".$width6."px;\">";

			echo "<OPTION class=\"form\" value=\"\">-choose the Transplant centre-</option>";

			//admin vidi vsechny centra, bezny uzivatel vidi uz jenom svoje centrum, pokud nejake ma
			if($_SESSION['usr_ID_role']==2):
				$where_centrum="AND transplant_centers.ID='".$_SESSION['usr_ID_centra']."'";
			endif;
					
			$vysledek_if = mysql_query("SELECT transplant_centers.ID AS IDcentra, transplant_centers.centrum FROM transplant_centers WHERE aktivni='1' AND ID_registru='$_GET[ID_registru]' $where_centrum ORDER BY centrum");
			if(mysql_num_rows($vysledek_if)):
				while($zaz_if = mysql_fetch_array($vysledek_if)):
					extract($zaz_if);
					
					echo "<OPTION class=\"form\" value=\"$IDcentra\""; if($_GET['ID_centra']==$IDcentra): echo "selected"; endif; echo ">$centrum</option>";
					
				endwhile;
			endif;
			@mysql_free_result($vysledek_if);

	echo "</select>";

endif;
?>

