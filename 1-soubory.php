<?
//* =============================================================================
//	Pokud pristupuji k souborum v adresari /soubory, mod_rewrite me smeruje na tuto stranku pro overeni prav pristupu
//============================================================================= */

//--vytvoøení session
session_start(); 

require "opendb.php";

$dnes_kontrola=time();

	//kontrola, zda existuje uzivatel se zadanym jmenem a heslem//
	$vysledek_check = mysql_query("SELECT ID FROM admin WHERE login='$_SESSION[usr_login]' AND heslo='$_SESSION[usr_heslo]' AND (platnost_od<='$dnes_kontrola' AND (platnost_do='0' OR platnost_do+86380>='$dnes_kontrola'))");
	
	if(mysql_num_rows($vysledek_check)>0):
	
		$cesta="soubory/".$_GET['cesta']."/".$_GET['soubor'];
		
		
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($_GET['soubor']));
		header('Content-Transfer-Encoding: binary');
		ob_clean();
		flush();
		readfile($cesta); //nacte data ze souboru.bez toho by se poslal obsah tohoto okna
		exit;

	endif;


	@mysql_free_result($vysledek_check);
?>