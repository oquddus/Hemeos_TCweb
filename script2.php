<?
//vypis odeslanych mailu
include('opendb.php');

	$vysledek_sub2 = mysql_query("SELECT email, text, datum FROM emaily_log ORDER BY datum DESC");
		if(mysql_num_rows($vysledek_sub2)>0):
			while($zaz_sub2 = mysql_fetch_array($vysledek_sub2)):
				extract($zaz_sub2);
				
				echo "$email - $datum<br>$text<br>-----------------------<br>";
				
			endwhile;
		endif;	
	@mysql_free_result($vysledek_sub2);
	
	

?>