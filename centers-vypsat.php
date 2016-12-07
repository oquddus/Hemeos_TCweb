<?
include('1-head.php');
?>


<?
if($_GET['action']=="smazat" && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")):
	//id,tabulka,oznamit vysledek (0 ne,1 ano)
	smazat($_GET['ID'],"transplant_centers",1);
endif;


//* =============================================================================
//	Str�nkov�n�
//============================================================================= */
strankovani("transplant_centers", $_na_stranku);



//* =============================================================================
//	Defaultn� �azen�
//============================================================================= */
razeni('transplant_centers.centrum');


//Pokud se diva bezny uzivatel nebo spravce registru, vidi jen ty ze sveho centra, pripadne registru pokud ma
if($_SESSION['usr_ID_role']==2 || $_SESSION['usr_ID_role']==3):
	if($_SESSION['usr_ID_registru']):
		$where.=" AND ID_registru='".$_SESSION['usr_ID_registru']."'";
	endif;
endif;

$counter=0;

//-----vypis dat
$vysledek = mysql_query("SELECT transplant_centers.* FROM transplant_centers WHERE aktivni=1 ".mysql_real_escape_string($where)." ORDER BY ".clean_high($orderby)." LIMIT ".clean_high($_od).", ".clean_high($_na_stranku)."");

	$num = mysql_num_rows($vysledek);
	if($num > 0):

		echo "<form style=\"margin:0;padding:0\" enctype=\"multipart/form-data\" method=\"POST\" action=\"$_SERVER[PHP_SELF]\" name=\"formular\">";
		
		echo "<table cellspacing=\"0\" width=\"100%\">
			<thead id=\"tb-head\">
			  <tr>
				<td width=\"5%\">".order('ID', 'transplant_centers.ID')."</td>
				<td width=\"27%\">".order('Transplant centre', 'centrum')."</td>
				<td width=\"12%\">".order('Centre ID', 'InstID')."</td>
				<td width=\"25%\">".order('Email', 'email')."</td>
				<td width=\"14%\">".order('Phone', 'telefon')."</td>
				<td width=\"8%\">".order('Acitve', 'aktivni')."</td>
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
					echo "<td>$ID</td>";
					echo "<td>$centrum</td>";
					echo "<td>$InstID</td>";
					echo "<td>$email</td>";
					echo "<td>$telefon</td>";
					echo "<td>"; if($aktivni): echo "active"; else: echo ""; endif; echo "</td>";
				
					echo "<td>";
					echo "<a href=\"centers-vlozit.php?ID=$ID&amp;h=".$hash."\" title=\"UPRAVIT\">".$ico_upravit."</a> &nbsp;&nbsp;";
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