<?
//vypis odeslanych mailu
include('opendb.php');

	$vysledek_sub2 = mysql_query("SELECT msg, date FROM log_notification ORDER BY date DESC");
		if(mysql_num_rows($vysledek_sub2)>0):
			while($zaz_sub2 = mysql_fetch_array($vysledek_sub2)):
				extract($zaz_sub2);
				
				echo "$msg - $date<br>-----------------------<br>";
				
			endwhile;
		endif;	
	@mysql_free_result($vysledek_sub2);
	
	

?>