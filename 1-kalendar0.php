<?
include('1-head-ajax.php');
?>

<?
//---------------------------kalendar
	$mesic=(int)$_GET['mesic'];
	$rok=(int)$_GET['rok'];
	$nazev_inputu=htmlspecialchars($_GET['nazev_inputu'],ENT_QUOTES,'ISO-8859-1');
	$form_name=htmlspecialchars($_GET['form_name'],ENT_QUOTES,'ISO-8859-1');
	
	
//function kalendar($mesic, $rok, $nazev_inputu){




		//--nastaveni pohybu vzad a vpred
		$razitko_prev=mktime(0,0,0,$mesic-1,1,$rok);
		$razitko_next=mktime(0,0,0,$mesic+1,1,$rok);
		
		$mesic_prev=date(n,$razitko_prev);
		$rok_prev=date(Y,$razitko_prev);
		
		$mesic_next=date(n,$razitko_next);
		$rok_next=date(Y,$razitko_next);
		
		
		
		switch($mesic):
			case 1: $mesic_text="January"; break;
			case 2: $mesic_text="February"; break;
			case 3: $mesic_text="March"; break;
			case 4: $mesic_text="April"; break;
			case 5: $mesic_text="May"; break;
			case 6: $mesic_text="June"; break;
			case 7: $mesic_text="July"; break;
			case 8: $mesic_text="August"; break;
			case 9: $mesic_text="September"; break;
			case 10: $mesic_text="October"; break;
			case 11: $mesic_text="November"; break;
			case 12: $mesic_text="December"; break;
		endswitch;
		
	


			echo "<div class=\"kalendar-prev\"><a href=\"javascript:showHint('1-kalendar0.php?mesic=$mesic_prev&amp;rok=$rok_prev&amp;nazev_inputu=$nazev_inputu&amp;form_name=$form_name','crm_kalendar', '1');\" class=\"kal-next\">< previous</a></div>";
			echo "<div class=\"kalendar-date bold text-large2\">";
			
			

			
			echo "&nbsp; <select class=\"cal-form\" size=\"1\" name=\"mesic\" onchange=\"javascript:showHint('1-kalendar0.php?mesic='+document.ajax_cal_formular.mesic.value+'&amp;rok=$rok&amp;nazev_inputu=$nazev_inputu&amp;form_name=$form_name','crm_kalendar', '1');\">
			<option value=\"1\""; if($mesic==1): echo " selected"; endif; echo ">January</option>
			<option value=\"2\""; if($mesic==2): echo " selected"; endif; echo ">February</option>
			<option value=\"3\""; if($mesic==3): echo " selected"; endif; echo ">March</option>
			<option value=\"4\""; if($mesic==4): echo " selected"; endif; echo ">April</option>
			<option value=\"5\""; if($mesic==5): echo " selected"; endif; echo ">May</option>
			<option value=\"6\""; if($mesic==6): echo " selected"; endif; echo ">June</option>
			<option value=\"7\""; if($mesic==7): echo " selected"; endif; echo ">July</option>
			<option value=\"8\""; if($mesic==8): echo " selected"; endif; echo ">August</option>
			<option value=\"9\""; if($mesic==9): echo " selected"; endif; echo ">September</option>
			<option value=\"10\""; if($mesic==10): echo " selected"; endif; echo ">October</option>
			<option value=\"11\""; if($mesic==11): echo " selected"; endif; echo ">November</option>
			<option value=\"12\""; if($mesic==12): echo " selected"; endif; echo ">December</option>
			</select>";

		
			echo "&nbsp; <select class=\"cal-form\" size=\"1\" name=\"rok\" onchange=\"javascript:showHint('1-kalendar0.php?mesic=$mesic&amp;rok='+document.ajax_cal_formular.rok.value+'&amp;nazev_inputu=$nazev_inputu&amp;form_name=$form_name','crm_kalendar', '1');\">";
			
				for($r=1940;$r<=date("Y")+2;$r++):
					echo "<option value=\"$r\""; if($r==$rok): echo " selected"; endif; echo ">$r</option>";
				endfor;

			echo "</select>";

			
			echo "</div>";
			echo "<div class=\"kalendar-next\"><a href=\"javascript:showHint('1-kalendar0.php?mesic=$mesic_next&amp;rok=$rok_next&amp;nazev_inputu=$nazev_inputu&amp;form_name=$form_name','crm_kalendar', '1');\" class=\"kal-next\">next ></a></div>";

	
	
	echo "<div class=\"kalendar0\"><div class=\"kalendar cal_text-nadpis\">";
	
		echo "<div class=\"kalendar-head0 kalendar-no-b\"><div class=\"kalendar-den\">Mo</div></div>";
		echo "<div class=\"kalendar-head\"><div class=\"kalendar-den\">Tue</div></div>";
		echo "<div class=\"kalendar-head\"><div class=\"kalendar-den\">We</div></div>";
		echo "<div class=\"kalendar-head\"><div class=\"kalendar-den\">Thu</div></div>";
		echo "<div class=\"kalendar-head\"><div class=\"kalendar-den\">Fri</div></div>";
		echo "<div class=\"kalendar-head\"><div class=\"kalendar-den\">Sa</div></div>";
		echo "<div class=\"kalendar-head2\"><div class=\"kalendar-den\">Su</div></div>";
	
	
	
	
	
	
		//--pocet dnu ve zvolenem mesici
		$dni_v_mesici = Date("t", MkTime(0,0,0, $mesic, 1, $rok));

		
		// k prvnímu dni v mìsíci pøiøadit jeho èíslo v týdnu (1 = pondìlí, ...) 
			$date = Date("w", MkTime(0,0,1,$mesic,1,$rok)); 
			if($date==0): $date=7; endif;	//--nedele je nula, zmen na 7

			$first = $date;  // èíslo prvního dne v mìsíci (1 = pondìlí, ...) 
			
			
			
			
			
		//--kdyz napr. prvni den v mesici je nedele (7. den), vlozime 6 prazdnych poli na uvod
		$vypsan_prvni=0;
		for($i=1;$i<=$first-1;$i++):
			echo "<div class=\"kalendar-bunka"; if($vypsan_prvni!=1): echo "2"; endif; echo "\"><div class=\"kalendar-den\">&nbsp;</div></div>";
			$vypsan_prvni=1;
		endfor;
			
			
			
			
		//-------------------------------------------------------------------------------------------------------------------vypis vsechny dny v mesici
		$den_v_radku=$first;
		
		for($i=1;$i<=$dni_v_mesici;$i++):
		
			//--implicitni nastaveni bunky
			$styl_bunky="kal-den";
			$link=1;
			$spec=0;
			$notice="";
			$poznamka="";
			
			
			
			//-dnesni den
			if(date("n")==$mesic&&date("Y")==$rok&&date("d")==$i):
				$styl_bunky="kal-den";
				$link=1;
				$spec=1;
				$notice="";
				$poznamka="";
			endif;
			
		
			echo "<div class=\"kalendar-bunka"; if($den_v_radku==1): echo "2"; endif; echo "\">";
			
				//--podbarveni dne
				if($spec): echo "<div class=\"kalendar-full".$spec."\">"; endif;
				
				
				
				echo "<div class=\"kalendar-den\">";
					
					if($link==1):
					
						if($mesic<10): $mesic_ukaz="0".$mesic; else: $mesic_ukaz=$mesic; endif;
						if($i<10): $i_ukaz="0".$i; else: $i_ukaz=$i; endif;
						
						$razitko_dne=MkTime(0,0,0,$mesic_ukaz,$i_ukaz,$rok);
					
						echo "<a href=\"javascript:crm_kalendar('".date($_SESSION['date_format_php'],$razitko_dne)."','$nazev_inputu','$form_name')\" class=\"$styl_bunky\" $notice>";
					else:
						echo "<span class=\"$styl_bunky\" $notice>";
					endif;
					
					
					echo $i;
					
					
					if($link==1):
						echo "</a>";
					else:
						echo "</span>";
					endif;
					
					
				//--uzavreni podbarveni dne	
				if($spec): echo "</div>"; endif;
					
				echo "</div></div>";
				
				
				
				if($den_v_radku==7):
					$den_v_radku=1;
				else:
					$den_v_radku++;
				endif;
					
		endfor;
		
		
		
		//--dopln prazdne bunky
		$pocet_radku=ceil((($first-1)+$dni_v_mesici)/7);
		$pocet_bunek=7*$pocet_radku;
		$zbyva_vypsat=$pocet_bunek-($dni_v_mesici+$first-1);
		
		for($i=1;$i<=$zbyva_vypsat;$i++):
			echo "<div class=\"kalendar-bunka\"><div class=\"kalendar-den\">&nbsp;</div></div>";	
		endfor;
						
						
						
	echo "</div></div>";
	//------------konec kalendare	
	
//}
	
?>
