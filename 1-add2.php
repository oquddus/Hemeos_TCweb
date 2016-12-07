<?
if($_GET['inc']!=1):
header('Content-type: text/html; charset=windows-1250'); 
endif;
?>




<?
require "opendb.php";

unset($seznam_id);
$pole_id=array();

	$pole_id=explode("|", $_GET['idcka']);
	
	foreach($pole_id as $id):
		if($id>0):
			if($seznam_id):
				$seznam_id.=", ".$id;
			else:
				$seznam_id=$id;
			endif;
		endif;
	endforeach;
	
	if($seznam_id):
	
		$sloupce=str_replace(",",",' ',",$_GET['sl']);
	
		//echo "SELECT $_GET[tab].ID AS IDaj, CONCAT($sloupce) AS hodnoty FROM $_GET[tab] WHERE ID IN ($seznam_id)";
	
		$vysledek_ajax = mysql_query("SELECT ".clean_high($_GET['tab']).".ID AS IDaj, CONCAT(".mysql_real_escape_string($sloupce).") AS hodnoty FROM ".clean_high($_GET['tab'])." WHERE ID IN (".clean_high($seznam_id).")");
		
			if (mysql_num_rows($vysledek_ajax)):
				
				$citac=1;
				while($zaz_ajax = mysql_fetch_array($vysledek_ajax)):
					
					extract($zaz_ajax);
					
					
					//--vypisuj do divu
					if($_GET['zpusob']==1):
						
						//--tato podminka je rozsirenim helpdesku - pro filtry se to vypisuje trochu jinak
						if($_GET['cil_id']=="ID_firem_filtr" || $_GET['cil_id']=="ID_resitelu_filtr" || $_GET['cil_id']=="ID_filtr_projektu"):
							if($citac>1): echo " &nbsp;|&nbsp; "; endif;
							echo "<a href=\"javascript:delAjItem".$_GET[ID_stranky]."('$IDaj', '$_GET[cil_form]', '$_GET[cil_id]', '$_GET[cil_text]', '$_GET[tab]', '$_GET[sl]', '$_GET[zpusob]', '$_GET[podrazeny_form]'); document.formular_hledej.submit();\" class=\"odkaz-selectbox8\" title=\"odstranit polo�ku\">".$hodnoty."</a>";
						else:
							echo "<div class=\"selectbox9\">
								<div class=\"selectbox6\">
									<div class=\"selectbox7\">";
									
									if($odkaz_z_hodnoty):
										echo "<a href=\"".$odkaz_z_hodnoty."$IDaj\" class=\"odkaz-selectbox7\">".$hodnoty."</a>";
									else:
										echo $hodnoty;
									endif;
									
									echo "</div>
									<div class=\"selectbox8b\" style=\"$ajax_ikony\"><a href=\"javascript:delAjItem".$_GET[ID_stranky]."('$IDaj', '$_GET[cil_form]', '$_GET[cil_id]', '$_GET[cil_text]', '$_GET[tab]', '$_GET[sl]', '$_GET[zpusob]', '$_GET[podrazeny_form]')\"><img src=\"img/ico/smazat.png\" border=\"0\" width=\"17\" height=\"20\" alt=\"smazat\" style=\"position: relative; top:-2px;\"></a></div>
								</div>
							</div>";
						endif;
						
					endif;
					
					
					
					//--vypisuj jen proste hodnoty, jdou do inputu
					if($_GET['zpusob']==2):
						echo $hodnoty;
					endif;
					
						
				$citac++;
				endwhile;
									
			endif;

		@mysql_free_result($vysledek_ajax);
		
	endif;

	
	unset($odkaz_z_hodnoty);

?>

