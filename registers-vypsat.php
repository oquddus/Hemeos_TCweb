<?
include('1-head.php');
?>


<?
//admin vidi vse, admin registru jen svuj registr
if($_SESSION['usr_ID_role']!=1):
	$where=" AND registers.RegID='".$_SESSION['usr_RegID']."'";
endif;


if($_SESSION['usr_ID_role']==1):
	if($_GET['action']=="smazat" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")):
		//id,tabulka,oznamit vysledek (0 ne,1 ano)
		smazat($_GET['ID'],"registers",1);
	endif;
endif;


//* =============================================================================
//	Stránkování
//============================================================================= */
strankovani("registers", $_na_stranku);



//* =============================================================================
//	Defaultní øazení
//============================================================================= */
razeni('registers.registr');


$counter=0;


//-----vypis dat
$vysledek = mysql_query("SELECT registers.* FROM registers WHERE registers.ID>0 $where ORDER BY $orderby LIMIT $_od, $_na_stranku");

	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
				<td width=\"30%\">".order('Register', 'registr')."</td>
				<td width=\"20%\">".order('HUB code', 'RegID')."</td>
				<td width=\"20%\">".order('Email', 'email')."</td>
				<td width=\"14%\">".order('Phone', 'telefon')."</td>
				<td width=\"7%\">".order('Acitve', 'aktivni')."</td>
				<td width=\"9%\">Action</td>
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
					echo "<td>$registr</td>";
					echo "<td>$RegID</td>";
					echo "<td>$email</td>";
					echo "<td>$telefon</td>";
					echo "<td>"; if($aktivni): echo "active"; else: echo ""; endif; echo "</td>";
				
					echo "<td>";
					echo "<a href=\"registers-vlozit.php?ID=$ID&amp;h=".$hash."\" title=\"UPRAVIT\">".$ico_upravit."</a> &nbsp;&nbsp;";
					
					if($_SESSION['usr_ID_role']==1):
						echo "<a href=\"$_SERVER[PHP_SELF]?action=smazat&ID=$ID&amp;h=".$hash."\" title=\"SMAZAT\" onclick=\"return potvrd('Opravdu si pøejete smazat tento záznam?')\">".$ico_smazat."</a>";
					endif;
					echo "</td>
				</tr>";
			  
			 } 
			 
			echo "</tbody></table>";
			
			//echo "<p><input type=\"hidden\" name=\"action\" value=\"uprav\">";
			//echo "<input type=\"submit\" class=\"form-send\" value=\"\"></p>";

			
		echo "</form>";
		
		
		
		
		//* =============================================================================
		//	Stránkování
		//============================================================================= */
		strankovani2();
		
		
			
	else:
		message(1, "There are no items.", "", "");
	endif;
	
	
	
//* =============================================================================
//	JS
//============================================================================= */
echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">

	function change_text(ID_span){
		if(document.getElementById(ID_span).innerHTML==\"zobrazit\"){
			document.getElementById(ID_span).innerHTML=\"skrýt\";
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