<?
require('opendb.php');

$vysledek = mysql_query("SELECT * FROM hlavicky ORDER BY datum DESC");
	if(mysql_num_rows($vysledek)>0):
		while($zaz = mysql_fetch_assoc($vysledek)):
		extract($zaz);
		
		echo $datum.": ".htmlspecialchars($hlavicky)."<br><br><br>";
		endwhile;
	endif;
@mysql_free_result($vysledek);

?>