<?php

//* =============================================================================
//	Klic je nazev modulu, ve vnorenem poli jsou odkazy framovych zalozek
//	menu se radi automaticky na zaklade zde serazenych poli
//============================================================================= */

	//$_pole_menu['requests']=array("requests-vlozit", "requests-sent", "requests-active", "requests-suspended", "requests-stopped");
	$_pole_menu['requests']=array("requests-vlozit", "requests-sent", "requests-active", "requests-stopped", "requests-working");
	
	$_pole_menu['results']=array("results-view");
	
	$_pole_menu['donor']=array("donor-sample-request","donor-workup-request","donor-request-active");
	
	$_pole_menu['donresults']=array("donresults-results");
	
	//$_pole_menu['workup']=array("workup-request","workup-sent");
	//$_pole_menu['records']=array("results");
	
	//$_pole_menu['tracking']=array("results");
	
	$_pole_menu['osobni']=array("osobni-nastaveni");

	$_pole_menu['registers']=array("registers-vypsat","registers-vlozit");	
	
	$_pole_menu['centers']=array("centers-vypsat","centers-vlozit");
	
	$_pole_menu['user']=array("user-vypsat","user-vlozit");
	
	$_pole_menu['admin']=array("admin-vypsat","admin-vlozit");
	

?>