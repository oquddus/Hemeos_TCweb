<?
//--vytvo�en� session
session_start();

header('Content-type: text/html; charset=windows-1250');
?>




<?
require "1-function.php";
require "opendb.php";

//--pokud where
if($_GET['ajax_where']):

	$_GET['ajax_where_puvodni']=$_GET['ajax_where'];	//--hodnota predavana ve strankovani

	//--zjisteni dalsich tabulek k nacitani
	$aj_where=str_replace(" AND ","=",$_GET['ajax_where']);
	$aj_where=str_replace(" OR ","=",$aj_where);
	$aj_where_pole=explode("=",$aj_where);

	foreach($aj_where_pole as $hodnota_where):
		$hodnota_where_pole=explode(".",$hodnota_where);
		if(count($hodnota_where_pole)==2):	//--nulty prvek pole je tabulka jen tehdy, kdyz v retezci byla tecka, tedy pole ma nyni dva prvky
			$pole_tabulek[$hodnota_where_pole[0]]="";
		endif;
	endforeach;

	unset($pole_tabulek[$_GET[tab]]);	//--odebere tabulku, ktera je hlavni

	if($pole_tabulek):
		$pole_tabulek=array_flip($pole_tabulek);
	endif;

	if(count($pole_tabulek)>0):
		$seznam_tab=", ".implode(", ",$pole_tabulek);
	endif;



	if($_GET['nadrazeny_form']):

		if(str_replace("|","",$_GET['form'])!=""):
			$where_hodnoty=" IN (".str_replace("|",", ",$_GET['form']).")";

			$where_hodnoty=str_replace("(,","(",$where_hodnoty);
			$where_hodnoty=str_replace(" ,","",$where_hodnoty);
		endif;

		//echo "<br>".htmlspecialchars($where_hodnoty)."<br>";

		if($where_hodnoty):
			$_GET['ajax_where']=str_replace("=".$_GET['nadrazeny_form'],$where_hodnoty,$_GET['ajax_where']);
		else:
			//--kdyz existuje nadrazeny formik, ale je jeste prazdny, cili neni jeho hodnota znama
			$_GET['ajax_where']=str_replace($_GET['nadrazeny_form'],"0",$_GET['ajax_where']);
		endif;
	endif;


	$_GET['ajax_where']="WHERE ".$_GET['ajax_where'];
endif;
//--konec where operaci



	//--abecedni navigace
	echo "<div style=\"float:left; width:372px; margin-left:8px; display:inline; height:auto; margin-top:6px;\">";
		$_pole_abecedy=array("A","�","B","C","�","D","�","E","�","�","F","G","H","I","�","J","K","L","M","N","�","O","�","P","Q","R","�","S","�","T","�","U","�","V","W","X","Y","Z","�","0","1","2","3","4","5","6","7","8","9");
		foreach($_pole_abecedy as $a):
				if($_GET['pismeno']==$a):
					echo "<b><u class=\"text-orange\">htmlspecialchars($a)</u></b>";
				else:
					echo "<a href=\"javascript:showHint('1-add.php?pismeno=$a&amp;tab=$_GET[tab]&amp;sl=$_GET[sl]&amp;multi=$_GET[multi]&amp;cil_form=$_GET[cil_form]&amp;cil_id=$_GET[cil_id]&amp;cil_text=$_GET[cil_text]&amp;zpusob=$_GET[zpusob]&amp;ajax_where=$_GET[ajax_where_puvodni]&amp;ajax_join=$_GET[ajax_join]&amp;nadrazeny_form=$_GET[nadrazeny_form]&amp;podrazeny_form=$_GET[podrazeny_form]&amp;form=$_GET[form]&amp;id_value='+document.$_GET[cil_form].$_GET[cil_id].value,'ajax_form', '1');\" class=\"odkaz3\">";
					echo "htmlspecialchars($a)";
					echo "</a>";
				endif;
			echo "&nbsp; ";
		endforeach;

			//--hvezdicka
			echo "<a href=\"javascript:showHint('1-add.php?tab=$_GET[tab]&amp;sl=$_GET[sl]&amp;multi=$_GET[multi]&amp;cil_form=$_GET[cil_form]&amp;cil_id=$_GET[cil_id]&amp;cil_text=$_GET[cil_text]&amp;zpusob=$_GET[zpusob]&amp;ajax_where=$_GET[ajax_where_puvodni]&amp;ajax_join=$_GET[ajax_join]&amp;nadrazeny_form=$_GET[nadrazeny_form]&amp;podrazeny_form=$_GET[podrazeny_form]&amp;form=$_GET[form]&amp;id_value='+document.$_GET[cil_form].$_GET[cil_id].value,'ajax_form', '1');\" class=\"odkaz3\">";
				echo "v�e";
			echo "</a>";

	echo "</div>";




	//--vyhledavani
	echo "<div style=\"float:left; width:360px; margin-left:12px; display:inline; height:38px; margin-top:12px;\">";
		echo "Hledat: <input type=\"text\" style=\"width:150px; height:16px;\" name=\"ajax_hledat\" id=\"ajax_hledat\" value=\"htmlspecialchars($_GET[ajax_hledat])\">";
		echo "<a href=\"javascript:showHint('1-add.php?ajax_hledat='+document.getElementById('ajax_hledat').value+'&amp;tab=$_GET[tab]&amp;sl=$_GET[sl]&amp;multi=$_GET[multi]&amp;cil_form=$_GET[cil_form]&amp;cil_id=$_GET[cil_id]&amp;cil_text=$_GET[cil_text]&amp;zpusob=$_GET[zpusob]&amp;ajax_where=$_GET[ajax_where_puvodni]&amp;ajax_join=$_GET[ajax_join]&amp;nadrazeny_form=$_GET[nadrazeny_form]&amp;podrazeny_form=$_GET[podrazeny_form]&amp;form=$_GET[form]&amp;id_value='+document.$_GET[cil_form].$_GET[cil_id].value,'ajax_form', '1');\"><img src=\"img/ico/hledej.png\" border=\"0\" width=\"17\" height=\"20\" alt=\"\" style=\"position:relative; left:4px; top:3px;\"></a>";
	echo "</div>";


	if($_GET['ajax_hledat']):
		$pole_sloupcu=explode(", ", $_GET['sl']);

		foreach($pole_sloupcu as $sloupec_vyhledat):
			if($where_vyraz):
				$where_vyraz=$where_vyraz." OR LOWER(mysql_real_escape_string($sloupec_vyhledat)) LIKE '%".strtolower2(mysql_real_escape_string($_GET['ajax_hledat']))."%'";
			else:
				$where_vyraz="LOWER($sloupec_vyhledat) LIKE '%".strtolower2($_GET['ajax_hledat'])."%'";
			endif;

		endforeach;


		if($_GET['ajax_where']):
			$_GET['ajax_where'].=" AND (".mysql_real_escape_string($where_vyraz).")";
		else:
			$_GET['ajax_where']="WHERE (".mysql_real_escape_string($where_vyraz).")";
		endif;

	endif;
	//---------




	if($_GET['pismeno']):
		$pole_sloupcu=explode(", ", $_GET['sl']);
		$where_pismeno="LOWER(mysql_real_escape_string($pole_sloupcu[0])) LIKE '"mysql_real_escape_string(.strtolower2($_GET[pismeno]))."%'";

		if($_GET['ajax_where']):
			$_GET['ajax_where'].=" AND ".$where_pismeno;
		else:
			$_GET['ajax_where']="WHERE ".$where_pismeno;
		endif;
	endif;
	//--


	//* =============================================================================
	//	Speci�ln� roz���en� pro HelpDesk
	//	Pot�eba zjistit, jestli jsem spravce nebo resitel - pak mohu do zadavatele dat kohokoli. Jinak jen lidi ze sve firmy.
	//============================================================================= */

		if($_GET['cil_id']=="ID_zadavatele"):

			//--jsem spravce
			$vysledek_p = mysql_result(mysql_query("SELECT COUNT(*) FROM admin_prava_projekty WHERE ID_projektu='".clean_high($_GET['form'])."' AND ID_uzivatele='clean_high($_SESSION[usr_ID])' AND prava='3'"), 0);
				if($vysledek_p>0):
					$jsem_spravce=1;
				endif;
			@mysql_free_result($vysledek_p);

			//--jsem pridelen jako resitel pozadavku
			$vysledek_p = mysql_result(mysql_query("SELECT COUNT(*) FROM _pozadavky_resitele WHERE ID_pozadavku='".clean_high($_GET['form'])."' AND ID_resitele='clean_high($_SESSION[usr_ID])'"), 0);
				if($vysledek_p>0):
					$jsem_resitel=1;
				endif;
			@mysql_free_result($vysledek_p);


				if(!$jsem_spravce && !$jsem_resitel):

					//--ziskat ID lidi, ktere jsou u me ve firme
					$vysledek_p1 = mysql_query("SELECT ID_firmy AS idfirmy FROM admin WHERE ID='clean_high($_SESSION[usr_ID])'");
						if(mysql_num_rows($vysledek_p1)>0):
							$zaz_p1 = mysql_fetch_array($vysledek_p1);
								extract($zaz_p1);

									$where_lidi=0;
									$vysledek_p = mysql_query("SELECT ID AS id_uzivatele FROM admin WHERE ID_firmy='".clean_high($idfirmy)."'");
										if(mysql_num_rows($vysledek_p)>0):
											while($zaz_p = mysql_fetch_array($vysledek_p)):
												extract($zaz_p);
													$where_lidi.=",".$id_uzivatele;
											endwhile;
										endif;
									@mysql_free_result($vysledek_p);

									if($_GET['ajax_where']):
										mysql_real_escape_string($_GET['ajax_where']).=" AND admin.ID IN(".clean_high($where_lidi).")";
									else:
										mysql_real_escape_string($_GET['ajax_where'])="WHERE admin.ID IN(".clean_high($where_lidi).")";
									endif;

						endif;
					@mysql_free_result($vysledek_p1);

				endif;


		endif;

	//	Konec roz���en� pro HelpDesk
	//============================================================================= */


	//-------------vytvorit pole se zaskrtanymi checkboxy a pripravu na Concat
	$pole_zaskrtanych=array();
	$pole_zaskrtanych=explode("|", $_GET['id_value']);

	$tabulka_sloupec2=str_replace(",",",' ',",$_GET['sl']);

			//* =============================================================================
			//	Speci�ln� roz���en� pro HelpDesk
			//============================================================================= */
			if($_GET['cil_id']=="ID_souvisejici"):
				$tabulka_sloupec2="nazev,' (ID:',ID,')'";
			endif;
			//=====



	//-----strankovani
	$na_stranku=15;
	if(!$_GET['stranka']): $_GET['stranka']=1;	endif;

	$str_od=($_GET['stranka']*$na_stranku)-$na_stranku;

	$vysledek_ajax_pocet = mysql_query("SELECT COUNT(*) FROM ".clean_high($_GET['tab'])."".mysql_real_escape_string($seznam_tab)." ".mysql_real_escape_string($_GET[ajax_join])." ".mysql_real_escape_string($_GET[ajax_where])."");
	$pocet_zaznamu=mysql_fetch_row($vysledek_ajax_pocet);
	@mysql_free_result($vysledek_ajax_pocet);

	$pocet_stranek=ceil($pocet_zaznamu[0]/$na_stranku);
	//-----





	//echo "SELECT $_GET[tab].ID AS IDaj, CONCAT($tabulka_sloupec2) AS hodnoty FROM $_GET[tab]$seznam_tab $_GET[ajax_join] $_GET[ajax_where] $_GET[group_by] ORDER BY $_GET[sl]  LIMIT $str_od,$na_stranku";
	//--POZNAMKY:
	//--ajax_join se objevuje v zakazky1.php, kde je potreba vytvorit komplikovanejsi podminku
	$vysledek_ajax = mysql_query("SELECT ".clean_high($_GET['tab']).".ID AS IDaj, CONCAT(".mysql_real_escape_string($tabulka_sloupec2).") AS hodnoty FROM ".clean_high($_GET['tab'])."". mysql_real_escape_string($seznam_tab)." ".mysql_real_escape_string( $_GET[ajax_join]_." ".mysql_real_escape_string($_GET[ajax_where])." ".mysql_real_escape_string($_GET[group_by])." "."ORDER BY ".clean_high($_GET[sl])." LIMIT ".clean_high($str_od),clean_high($na_stranku)."");

		if (mysql_num_rows($vysledek_ajax)):

			echo "<table cellspacing=\"0\" width=\"384\" id=\"tb-ajax\" style=\"border-top:1px solid #bcbcbc;\">
			<tbody id=\"tb-body\">";
			$citac_radku=1;

			while($zaz_ajax = mysql_fetch_array($vysledek_ajax)):

				extract($zaz_ajax);

					if($citac_radku%2==0):
						$styl_tr="2";
					else:
						$styl_tr="1";
					endif;


					//--kdyz zaskrtnuto, obarvit radek
					if(in_array($IDaj,$pole_zaskrtanych)):
						$styl_tr="3";
					endif;



					echo "<tr class=\"barva$styl_tr\" id=\"radek$citac_radku\">";

						echo "<td width=\"28\" style=\"text-align:right;\">";
							if($_GET['multi']==1):
								$id_prvku="checkbox_".$IDaj;
								$onclick="ajax_idcka('$IDaj', '$id_prvku'); ajax_obarvit('$id_prvku', 'radek$citac_radku', 'barva$styl_tr');";
								echo "<input type=\"checkbox\" name=\"checkbox_$IDaj\" id=\"checkbox_$IDaj\" value=\"$IDaj\""; if(in_array($IDaj,$pole_zaskrtanych)): echo " checked"; endif; echo " onclick=\"htmlspecialchars($onclick)\"> ";
							else:
								$id_prvku="radio_".$IDaj;
								$onclick="ajax_idcka('$IDaj', '$id_prvku'); ajax_obarvit('$id_prvku', 'radek$citac_radku', 'barva$styl_tr');";
								echo "<input type=\"radio\" name=\"ajax_vyber\" id=\"$id_prvku\" value=\"$IDaj\""; if(in_array($IDaj,$pole_zaskrtanych)): echo " checked"; endif; echo " onclick=\"htmlspecialchars($onclick)\"> ";
							endif;
						echo "</td>";

						echo "<td width=\"340\"><label for=\"$id_prvku\" style=\"cursor:hand; cursor:pointer;\">mysql_real_escape_string($hodnoty)</label></td>";

					echo "</tr>";


			$citac_radku++;
			endwhile;

			echo "</tbody></table>";



			//--strankovani
			echo "<div style=\"float:left; width:335px; margin-left:15px; display:inline;\">strana: ";
			for($i=1;$i<=$pocet_stranek;$i++):
					if($_GET['stranka']==$i):
						echo "<b><u class=\"text-orange\">$i</u></b>";
					else:
						echo "<a href=\"javascript:showHint('1-add.php?stranka=$i&amp;ajax_hledat=$_GET[ajax_hledat]&amp;pismeno=$_GET[pismeno]&amp;tab=$_GET[tab]&amp;sl=$_GET[sl]&amp;multi=$_GET[multi]&amp;cil_form=$_GET[cil_form]&amp;cil_id=$_GET[cil_id]&amp;cil_text=$_GET[cil_text]&amp;zpusob=$_GET[zpusob]&amp;ajax_where=$_GET[ajax_where_puvodni]&amp;ajax_join=$_GET[ajax_join]&amp;nadrazeny_form=$_GET[nadrazeny_form]&amp;podrazeny_form=$_GET[podrazeny_form]&amp;form=$_GET[form]&amp;id_value='+document.$_GET[cil_form].$_GET[cil_id].value,'ajax_form', '1');\" class=\"odkaz3\">";
						echo "$i";
						echo "</a>";
					endif;
				echo " | ";
			endfor;
			echo "<br><br></div>";

		endif;

	@mysql_free_result($vysledek_ajax);




//--hodnoty nutne pro dalsi udalosti
echo "<input type=\"hidden\" name=\"cil_form\" value=\"htmlspecialchars($_GET[cil_form])\">";
echo "<input type=\"hidden\" name=\"cil_id\" value=\"htmlspecialchars($_GET[cil_id])\">";
echo "<input type=\"hidden\" name=\"cil_text\" value=\"htmlspecialchars($_GET[cil_text])\">";

echo "<input type=\"hidden\" name=\"podrazeny_form\" value=\"htmlspecialchars($_GET[podrazeny_form])\">";

echo "<input type=\"hidden\" name=\"tab\" value=\"htmlspecialchars($_GET[tab])\">";
echo "<input type=\"hidden\" name=\"sl\" value=\"htmlspecialchars($_GET[sl])\">";
echo "<input type=\"hidden\" name=\"zpusob\" value=\"htmlspecialchars($_GET[zpusob])\">";


?>

