<?
$ID_stranky=333;
include('1-head.php');
?>


<?
if($_GET['action']=="smazat" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")):
	//id,tabulka,oznamit vysledek (0 ne,1 ano)
	smazat($_GET['ID'],"admin",1);
endif;



//* =============================================================================
//	Str�nkov�n�
//============================================================================= */
strankovani("admin", $_na_stranku);



//* =============================================================================
//	Defaultn� �azen�
//============================================================================= */
razeni('admin.prijmeni');


$counter=0;

//-----vypis dat
$vysledek = mysql_query("SELECT admin.ID, prijmeni, jmeno, login, admin.email FROM admin WHERE ID_role_admin='1' ORDER BY ".clean_high($orderby)." LIMIT ".clean_high($_od).", ".clean_high($_na_stranku)."");

	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
				<td width=\"5%\">".order('ID', 'admin.ID')."</td>
				<td width=\"15%\">".order('Surname', 'prijmeni')."</td>
				<td width=\"15%\">".order('Name', 'jmeno')."</td>
				<td width=\"15%\">".order('Login', 'login')."</td>
				<td width=\"40%\">".order('Email', 'email')."</td>
				<td width=\"10%\">Akce</td>
			  </tr>
			</thead>
			<tbody id=\"tb-body\">";
			
			
			
			while($zaz = mysql_fetch_array($vysledek)){

				extract($zaz);
			
				++$counter;
			
			
				if($counter%2==0):
					$styl="class=\"barva1\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva1'\"";
				else:
					$styl="class=\"barva2\" onmouseover=\"this.className='barva3'\" onmouseout=\"this.className='barva2'\"";
				endif;
			
				$hash=sha1($ID."SaltIsGoodForLife45");
				
				echo "<tr ".$styl.">";
					echo "<td>$ID</td>";
					echo "<td>$prijmeni</td>";
					echo "<td>$jmeno</td>";
					echo "<td>$login</td>";
					echo "<td>$email</td>";
				
					echo "<td>";
					echo "<a href=\"admin-vlozit.php?ID=$ID&amp;h=".$hash."\" title=\"UPRAVIT\">".$ico_upravit."</a> &nbsp;&nbsp;";
					echo "<a href=\"$_SERVER[PHP_SELF]?action=smazat&ID=$ID&amp;h=".$hash."\" title=\"SMAZAT\" onclick=\"return potvrd('Opravdu si p�ejete smazat tento z�znam?')\">".$ico_smazat."</a>
					</td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send\" value=\"\"></p>";

			
		echo "</form>";
		
		
		
		
		//* =============================================================================
		//	Str�nkov�n�
		//============================================================================= */
		if($num>$_na_stranku):
			strankovani2();
		endif;
		
		
			
	else:
		message(1, "There are no items.", "", "");
	endif;
	
	
	
//* =============================================================================
//	JS
//============================================================================= */
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

	function change_text(ID_span){
		if(document.getElementById(ID_span).innerHTML==\"zobrazit\"){
			document.getElementById(ID_span).innerHTML=\"skr�t\";
			var operace=\"zobrazit\";
		}else{
			document.getElementById(ID_span).innerHTML=\"zobrazit\";
			var operace=\"skryt\";
		}
		
		
		//pomoci ajaxu do sessions zapsat zobrazeni filtru
		showHint('ajax-filtry.php?druh_filtru='+ID_span+'&operace='+operace,'ajax_pomocny','1');
	}
	
	
	
	
			
</SCRIPT>";
?>			
			
			
			
<?
include('1-end.php');
?>