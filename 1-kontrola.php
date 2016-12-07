<?php
//--zjisteni stranky
$_stranka_cela=str_replace("/","",strrchr($_SERVER['PHP_SELF'],"/"));
$_stranka_bez=explode(".",$_stranka_cela);
$_stranka=explode("-",$_stranka_bez[0]);


//--kontrola platnych udaju
$dnes_kontrola=time();

$vysledek_check = mysql_query("SELECT ID FROM admin WHERE login='".clean_high($_SESSION['usr_login'])."' AND heslo='".mysql_real_escape_string($_SESSION['usr_heslo'])."' AND (platnost_od<='$dnes_kontrola' AND (platnost_do='0' OR platnost_do+86380>='$dnes_kontrola'))");

	if(mysql_num_rows($vysledek_check)>0):

		//----kontrola prav
		if($_stranka[0]!="uvod"):

			$vysledek_c = mysql_query("SELECT ID_admin FROM admin_prava WHERE ID_admin='".clean_high($_SESSION[usr_ID])."' AND modul='".mysql_real_escape_string($_stranka[0])."'");

				if(mysql_num_rows($vysledek_c)==0):
					header("Location: index.php?st=rights"); 
					exit;
				endif;
		endif;
		//----konec kontroly

	else:
		header("Location: index.php?st=logged"); 
		exit;
	endif;


	@mysql_free_result($vysledek_check);


?>