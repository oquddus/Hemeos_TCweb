<?php
require('opendb.php');

class WebApp{

	/**
	 * @return boolean
	 */

	//private function autentizace(){
	function autentizace(){
	
		//return true;
	
		if($_SERVER['HTTP_HOST']=="127.0.0.1"):
			return true;
		endif;
	
		$hlavicky = file_get_contents('php://input'); 
	
		mysql_query("INSERT INTO hlavicky (ID, hlavicky, IP, datum) VALUES ('NULL', '".mysql_real_escape_string($hlavicky)."', '".mysql_real_escape_string($_SERVER['REMOTE_ADDR'])."', CURRENT_TIMESTAMP)");
	
		preg_match('/<user xsi:type="xsd:string">.*<\/user>/', $hlavicky, $matches);
		$login=trim($matches[0]);
		$login=str_replace("<user xsi:type=\"xsd:string\">","",$login);
		$login=str_replace("</user>","",$login);

		preg_match('/<password xsi:type="xsd:string">.*<\/password>/', $hlavicky, $matches);
		$pass=trim($matches[0]);
		$pass=str_replace("<password xsi:type=\"xsd:string\">","",$pass);
		$pass=str_replace("</password>","",$pass);

		if($login=="aaa" && $pass=="bbb"):
			return true;
		else:
			return false;
		endif;

	}

	
	/**
	 * @return string
	 */

	//fce pro test
	function GetTest(){
	
		$hlavicky = file_get_contents('php://input'); 
		/*$hlavicky='<?xml version="1.0"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"><SOAP-ENV:Header SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" xmlns:NS1="urn:wsUserHeader"><NS1:TwsSecureSoapHeader xsi:type="NS1:TwsSecureSoapHeader"><user xsi:type="xsd:string">aaa</user><password xsi:type="xsd:string">bbb</password></NS1:TwsSecureSoapHeader></SOAP-ENV:Header><SOAP-ENV:Body SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"><NS2:GetPreliminaryRequests xmlns:NS2="tvorbawww.cz"><RegID xsi:type="xsd:string">BE</RegID></NS2:GetPreliminaryRequests></SOAP-ENV:Body></SOAP-ENV:Envelope>';*/
		
		preg_match('/<user xsi:type="xsd:string">.*<\/user>/', $hlavicky, $matches);
		$login=trim($matches[0]);
		$login=str_replace("<user xsi:type=\"xsd:string\">","",$login);
		$login=str_replace("</user>","",$login);

		preg_match('/<password xsi:type="xsd:string">.*<\/password>/', $hlavicky, $matches);
		$pass=trim($matches[0]);
		$pass=str_replace("<password xsi:type=\"xsd:string\">","",$pass);
		$pass=str_replace("</password>","",$pass);
	
		if($login=="aaa" && $pass=="bbb"):
			$vysledek="OK";
		else:
			$vysledek="chyba";
		endif;
		
		return $vysledek;
	
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce vraci preliminary requesty cekajici na odeslani
	function GetPreliminaryRequests($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
			
			$_pole_nahrad=array("hap"=>"hap_identified");
			$_pole_nevypsat=array("datum_vlozeni","ci_search_prognosis","cii_search_prognosis","ID_stavu","PatientNum","duvod_zamitnuti","requesting_registry","coordinator","telephone","fax","email","hap","datum_zpracovani","datum_odeslani","duvod_zmeny_stavu");
				
				$vysledek = mysql_query("SELECT * FROM search_request WHERE ID_stavu='1' AND RegID='".mysql_real_escape_string($RegID)."' ORDER BY ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_assoc($vysledek)):

							$xml.="<form>";
							
							foreach($zaz as $klic=>$hodnota):
								if(!in_array($klic,$_pole_nevypsat)):
								
									if($klic=="ID"):
										$ID=$hodnota;
									endif;
								
									if($klic=="date_request"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); else: $hodnota=""; endif;
									endif;
									if($klic=="date_birth"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); else: $hodnota=""; endif;
									endif;
									if($klic=="date_diagnosis"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); else: $hodnota=""; endif;
									endif;
									if($klic=="temp_transpl_date"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); else: $hodnota=""; endif;
									endif;
									if($klic=="cmv_date"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); else: $hodnota=""; endif;
									endif;
									
									
									
									if(array_key_exists($klic,$_pole_nahrad)):
										$klic=$_pole_nahrad[$klic];
									endif;

									$xml.="<$klic>".$hodnota."</$klic>";

								endif;
							endforeach;

							mysql_query("UPDATE search_request SET datum_odeslani='".$dnes."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
							if(mysql_affected_rows()>0):
								$return_code=1;
							else:
								$return_code=5003;
							endif;
							
							$xml.="<return_code>$return_code</return_code>";
							$xml.="</form>";
							
						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";

			//$xml=$_SERVER;
			//$xml = file_get_contents('php://input'); 
			

			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	
	/**
	 * @param  integer
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro potvrzeni preliminary request
	function ConfirmPreliminaryReq($ID,$PatientNum,$StatusXml=""){
		if(autentizace()){

			$sql_edit="";
			if($StatusXml):
				$xml=simplexml_load_string($StatusXml);
				if($xml):
					$status=$xml->form->status;
					if($status):
						$sql_edit="status='".mysql_real_escape_string($status)."',";
					endif;
				endif;
			endif;
			
			
			//test za uz neni PatientNum v Registru pouzito
			$vysledek = mysql_query("SELECT RegID, InstID FROM search_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			$pnum_je = mysql_result(mysql_query("SELECT COUNT(*) FROM search_request WHERE ID!='".mysql_real_escape_string($ID)."' AND RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."'"), 0);
			if($pnum_je):
				$return_code=5006;
			else:
				$vysledek = mysql_query("UPDATE search_request SET ID_stavu='2',".$sql_edit." datum_zpracovani='".time()."', duvod_zamitnuti='', PatientNum='".mysql_real_escape_string($PatientNum)."', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_affected_rows()>0):
					$return_code=1;
					send_email($InstID,1,$RegID.$PatientNum."P");
				else:
					$return_code=5004;
				endif;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;		
		}
	}
	
	
	/**
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro zamitnuti preliminary request
	function DeniedPreliminaryRequestBy($ID,$duvod){
		if(autentizace()){
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT RegID, InstID FROM search_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			$vysledek = mysql_query("UPDATE search_request SET ID_stavu='3', datum_zpracovani='".time()."', duvod_zamitnuti='".mysql_real_escape_string($duvod)."', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,2,$ID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
			
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce vraci denied requesty
	function GetDeniedRequests($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
			
			$_pole_nahrad=array("hap"=>"hap_identified");
			$_pole_nevypsat=array("datum_vlozeni","ci_search_prognosis","cii_search_prognosis","ID_stavu","PatientNum","duvod_zamitnuti","requesting_registry","coordinator","telephone","fax","email","hap","datum_zpracovani","datum_odeslani","duvod_zmeny_stavu");
				
				$vysledek = mysql_query("SELECT * FROM search_request WHERE ID_stavu='3' AND RegID='".mysql_real_escape_string($RegID)."' ORDER BY ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_assoc($vysledek)):

							$xml.="<form>";
							
							foreach($zaz as $klic=>$hodnota):
								if(!in_array($klic,$_pole_nevypsat)):
								
									if($klic=="ID"):
										$ID=$hodnota;
									endif;
								
									if($klic=="date_request"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_birth"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_diagnosis"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="temp_transpl_date"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="cmv_date"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									
									
									
									if(array_key_exists($klic,$_pole_nahrad)):
										$klic=$_pole_nahrad[$klic];
									endif;

									$xml.="<$klic>".$hodnota."</$klic>";

								endif;
							endforeach;

							//mysql_query("UPDATE search_request SET datum_odeslani='".$dnes."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
							//if(mysql_affected_rows()>0):
								$return_code=1;
							//else:
							//	$return_code=5003;
							//endif;
							
							$xml.="<return_code>$return_code</return_code>";
							$xml.="</form>";
							
						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";

			//$xml=$_SERVER;
			//$xml = file_get_contents('php://input'); 
			

			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  integer
	 * @return string
	 */

	//fce pro navrat zpet do preliminary
	function SetPreliminaryReq($ID){
		if(autentizace()){
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT InstID FROM search_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			$vysledek = mysql_query("UPDATE search_request SET ID_stavu='1', datum_zpracovani='".time()."', duvod_zamitnuti='', PatientNum='', datum_zmeny_stavu=NOW() WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,3,$ID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;		
		}
	}
	
	
	/**
	 * @param  string
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro prijem donoru pro pacienta
	function SendDonorForPatient($RegID, $PatientNum, $aDonorData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($aDonorData);
			
			if($xml):
				foreach ($xml->Donor as $Donor):

					$pole_sloupcu=array();
					$pole_hodnot=array();
					
					unset($DonorNumber);
				
					foreach ($Donor as $atribut=>$data):
					
						
						if($atribut=="DRB1.1"): $atribut=str_replace("DRB1.1","DRB11x",$atribut); endif;
						if($atribut=="DRB1.2"): $atribut=str_replace("DRB1.2","DRB12x",$atribut); endif;
						if($atribut=="DQB1.1"): $atribut=str_replace("DQB1.1","DQB11x",$atribut); endif;
						if($atribut=="DQB1.2"): $atribut=str_replace("DQB1.2","DQB12x",$atribut); endif;
						
						
						$atribut=str_replace(".","",$atribut);
						$atribut=str_replace("-","_",$atribut);
					
						
						if($atribut=="RecordUpdate"):
							if($data!=""):
								$explode=explode(".",$data);
								$data=MkTime(0,0,0,$explode[1],$explode[0],$explode[2]);
							endif;
						endif;

						//do databaze zapisovat jako string
						/*
						if($atribut=="CMVDate"):
							if($data):
								$explode=explode(".",$data);
								$data=MkTime(0,0,0,$explode[1],$explode[0],$explode[2]);
							endif;
						endif;
						*/
						
						if($atribut=="ID"):
							$atribut="ID2";
						endif;
						
						if($atribut=="DonorNumber"):
							$DonorNumber=$data;
						endif;
						
						
						if($atribut=="PROBA" || $atribut=="PROBB" || $atribut=="PROBC" || $atribut=="PROBDR" || $atribut=="PROBDQ"):
							$data=str_replace(" ","",$data);
							$data=str_replace(",",".",$data);
						endif;
						
					
						$pole_sloupcu[]=$atribut;
						$pole_hodnot[]=mysql_real_escape_string($data);
					endforeach;
					
					unset($zaznam);
					$zaznam="<PatientNum>$PatientNum</PatientNum><DonorNumber>$DonorNumber</DonorNumber>";
					
					
					//dohledani udaju
					$vysledek = mysql_query("SELECT InstID FROM search_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");
						if(mysql_num_rows($vysledek)>0):
							$zaz = mysql_fetch_assoc($vysledek);
								extract($zaz);
						endif;
					@mysql_free_result($vysledek);
			
					
					$vysledek_check = mysql_result(mysql_query("SELECT COUNT(ID) FROM patient_donor WHERE DonorNumber='".mysql_real_escape_string($DonorNumber)."' AND RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."'"), 0);
					if($vysledek_check):
						$pole_update=array();
						$citac=0;
						foreach($pole_sloupcu as $sloupec):
						
							$pole_update[]=$sloupec."='".$pole_hodnot[$citac]."'";
						
						$citac++;
						endforeach;
					
						$dotaz="UPDATE patient_donor SET ".implode(", ",$pole_update).", datum_vlozeni='$dnes' WHERE DonorNumber='".mysql_real_escape_string($DonorNumber)."' AND RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1";
						//echo $dotaz."<br><br>";
						$vysledek = mysql_query($dotaz);
						if(mysql_affected_rows()>0):
							$zaznam.="<return_code>1</return_code>";
							send_email($InstID,4,$RegID.$PatientNum."P");
						else:
							$zaznam.="<return_code>5004</return_code>";
						endif;
					else:
						$dotaz="INSERT INTO patient_donor (ID, RegID, PatientNum, ".implode(", ",$pole_sloupcu).", datum_vlozeni) VALUES ('NULL', '".mysql_real_escape_string($RegID)."', '".mysql_real_escape_string($PatientNum)."', '".implode("', '",$pole_hodnot)."', '$dnes')";
						$vysledek = mysql_query($dotaz);
						if($vysledek):
							$zaznam.="<return_code>1</return_code>";
							send_email($InstID,5,$RegID.$PatientNum."P");
						else:
							$zaznam.="<return_code>5004</return_code>";
						endif;
					endif;
					
					$xml_data.="<Record>".$zaznam."</Record>";

				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro zaslani stopped pacientu
	function GetStoppedPatients($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
				
				$vysledek = mysql_query("SELECT ID, PatientNum, RegID, duvod_zmeny_stavu, DATE_FORMAT(datum_zmeny_stavu,'%d.%m.%Y %H:%i') AS datum_zmeny_stavu2 FROM search_request WHERE ID_stavu='6' AND PatientNum>0 AND RegID='".mysql_real_escape_string($RegID)."' ORDER BY ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_assoc($vysledek)):
							extract($zaz);
							
							$xml.="<form>";

							$xml.="<RegID>".$RegID."</RegID>";
							$xml.="<PatientNum>".$PatientNum."</PatientNum>";
							$xml.="<Reason>".$duvod_zmeny_stavu."</Reason>";
							$xml.="<ChangeDate>".$datum_zmeny_stavu2."</ChangeDate>";
							
							//podrobnosti k duvodu
							$xml.="<ReasonData>";
								
								unset($tb);
								switch($duvod_zmeny_stavu):
									case 20: $tb="reasons_transplanted"; break;
									case 21: $tb="reasons_died"; break;
									case 23: $tb="reasons_nodonor"; break;
									case 24: $tb="reasons_other"; break;
								endswitch;
								
								if($tb):
									$vysledek_sub = mysql_query("SELECT * FROM ".$tb." WHERE ID_request='".$ID."' LIMIT 1");
										if(mysql_num_rows($vysledek_sub)>0):
											$zaz_sub = mysql_fetch_assoc($vysledek_sub);
							
											foreach($zaz_sub as $klic=>$hodnota):
												if(!in_array($klic,$_pole_nevypsat)):
												
													if($klic=="trans_date0" || $klic=="trans_date1" || $klic=="trans_date2" || $klic=="die_date"):
														if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); else: $hodnota=""; endif;
													endif;

													$xml.="<$klic>".$hodnota."</$klic>";

												endif;
											endforeach;
											
										endif;
									@mysql_free_result($vysledek_sub);
								endif;
							$xml.="</ReasonData>";
							
							mysql_query("UPDATE search_request SET datum_odeslani='$dnes' WHERE ID='$ID' LIMIT 1");
							if(mysql_affected_rows()>0):
								$return_code=1;
							else:
								$return_code=5003;
							endif;
							
							$xml.="<return_code>$return_code</return_code>";
							$xml.="</form>";
							
						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";
			
			return $xml;			
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro zaslani suspended pacientu
	function GetSuspendedPatients($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
						
				$vysledek = mysql_query("SELECT ID, PatientNum, RegID, duvod_zmeny_stavu, DATE_FORMAT(datum_zmeny_stavu,'%d.%m.%Y %H:%i') AS datum_zmeny_stavu2 FROM search_request WHERE ID_stavu='7' AND PatientNum>0 AND RegID='".mysql_real_escape_string($RegID)."' ORDER BY ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_array($vysledek)):
							extract($zaz);
							
							$xml.="<form>";

							$xml.="<RegID>".$RegID."</RegID>";
							$xml.="<PatientNum>".$PatientNum."</PatientNum>";
							$xml.="<Reason>".$duvod_zmeny_stavu."</Reason>";
							$xml.="<ChangeDate>".$datum_zmeny_stavu2."</ChangeDate>";

							mysql_query("UPDATE search_request SET datum_odeslani='$dnes' WHERE ID='$ID' LIMIT 1");
							
							if(mysql_affected_rows()>0):
								$return_code=1;
							else:
								$return_code=5003;
							endif;
							
							$xml.="<return_code>$return_code</return_code>";
							$xml.="</form>";
							
						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";
			
			return $xml;			
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro zaslani active pacientu 
	function GetActivePatients($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
						
				$vysledek = mysql_query("SELECT ID, PatientNum, RegID, DATE_FORMAT(datum_zmeny_stavu,'%d.%m.%Y %H:%i') AS datum_zmeny_stavu2 FROM search_request WHERE ID_stavu='8' AND PatientNum>0 AND RegID='".mysql_real_escape_string($RegID)."' ORDER BY ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_array($vysledek)):
							extract($zaz);
							
							$xml.="<form>";

							$xml.="<RegID>".$RegID."</RegID>";
							$xml.="<PatientNum>".$PatientNum."</PatientNum>";
							$xml.="<ChangeDate>".$datum_zmeny_stavu2."</ChangeDate>";
							
							mysql_query("UPDATE search_request SET datum_odeslani='$dnes' WHERE ID='$ID' LIMIT 1");
							
							if(mysql_affected_rows()>0):
								$return_code=1;
							else:
								$return_code=5003;
							endif;
							
							$xml.="<return_code>$return_code</return_code>";
							$xml.="</form>";
							
						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";
			
			return $xml;			
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	
	/**
	 * @param  string
	 * @param  integer
	 * @return string
	 */

	//fce pro nastaveni stopped pacientu
	function SetStoppedPatients($RegID,$PatientNum){
		if(autentizace()){
		
			//dohledani udaju
			$vysledek = mysql_query("SELECT InstID FROM search_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE search_request SET ID_stavu='5', datum_zpracovani='".time()."', datum_zmeny_stavu=NOW() WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");
		
			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,6,$RegID.$PatientNum."P");
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
			
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	 * @param  string
	 * @param  integer
	 * @return string
	 */

	//fce pro nastaveni suspended pacientu
	function SetSuspendedPatients($RegID,$PatientNum){
		if(autentizace()){
		
			//dohledani udaju
			$vysledek = mysql_query("SELECT InstID FROM search_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE search_request SET ID_stavu='4', datum_zpracovani='".time()."', datum_zmeny_stavu=NOW() WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,7,$RegID.$PatientNum."P");
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	 * @param  string
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro nastaveni active pacientu
	function SetActivePatients($RegID,$PatientNum,$StatusXml=""){
		if(autentizace()){
			
			$sql_edit="";
			if($StatusXml):
				$xml=simplexml_load_string($StatusXml);
				if($xml):
					$status=$xml->form->status;
					if($status):
						$sql_edit="status='".mysql_real_escape_string($status)."',";
					endif;
				endif;
			endif;
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT InstID FROM search_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE search_request SET ID_stavu='2',".$sql_edit." datum_zpracovani='".time()."', datum_zmeny_stavu=NOW() WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."' LIMIT 1");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,8,$RegID.$PatientNum."P");
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce vraci typing requesty cekajici na odeslani
	function GetTypingRequests($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
			
			$_pole_nahrad=array();
			$_pole_nevypsat=array("datum_vlozeni","aktivni","ID_uzivatele");
				
				$vysledek = mysql_query("SELECT typing_request.*, typing_request_donor.DonorNumber 
				FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) 
				WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.ID_stavu=0 GROUP BY ID_typing, DonorNumber ORDER BY typing_request.ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_assoc($vysledek)):
							extract($zaz);
							
							//dohledat temp_transpl_date
							$temp_transpl_date="";
							$vysledek8 = mysql_query("SELECT temp_transpl_date FROM search_request WHERE RegID='".$RegID."' AND InstID='".$InstID."' AND PatientNum='".$PatientNum."' ORDER BY ID DESC LIMIT 1");
								if(mysql_num_rows($vysledek8)>0):
									$zaz8 = mysql_fetch_array($vysledek8);
										extract($zaz8);
										if($temp_transpl_date<>0):
											$temp_transpl_date=date("Y-m-d",$temp_transpl_date); else: $temp_transpl_date="";
										endif;
								endif;
							@mysql_free_result($vysledek8);
							
							$xml.="<form>
							
							<ID>$ID</ID> 
							<ia_hospital>$ia_hospital</ia_hospital>
							<ia_contact_name>$ia_contact_name</ia_contact_name>
							<ia_address>$ia_address</ia_address>
							<ia_phone>$ia_phone</ia_phone>
							<ia_fax>$ia_fax</ia_fax>
							<ia_email>$ia_email</ia_email>
							<person_completing>$person_completing</person_completing>
							<signature>$signature</signature>
							<date_completing>".date("Y-m-d",$date_completing)."</date_completing>
							<InstID>$InstID</InstID>
							<RegID>$RegID</RegID>
							<patient>
								<PatientNum>$PatientNum</PatientNum>                     
								<PatientID>$PatientID</PatientID>                    
								<patient_name>$patient_name</patient_name>    
								<patient_registry>$patient_registry</patient_registry>           
								<patient_id_dn>$patient_id_dn</patient_id_dn>
								<diagnosis>$diagnosis</diagnosis>
								<date_birth>".date("Y-m-d",$date_birth)."</date_birth>
								<search_urgent>".$search_urgent."</search_urgent>
								<temp_transpl_date>".$temp_transpl_date."</temp_transpl_date>
								<patient_hla>
									<method_used>$patient_hla</method_used>
									<a_1>$a_1</a_1>
									<b_1>$b_1</b_1>
									<c_1>$c_1</c_1>
									<drb1_1>$drb1_1</drb1_1>
									<drb3_1>$drb3_1</drb3_1>
									<drb4_1>$drb4_1</drb4_1>
									<drb5_1>$drb5_1</drb5_1>
									<dqb1_1>$dqb1_1</dqb1_1>
									<dpb1_1>$dpb1_1</dpb1_1>
									<dqa1_1>$dqa1_1</dqa1_1>
									<dpa1_1>$dpa1_1</dpa1_1>
									<a_2>$a_2</a_2>
									<b_2>$b_2</b_2>
									<c_2>$c_2</c_2>
									<drb1_2>$drb1_2</drb1_2>
									<drb3_2>$drb3_2</drb3_2>
									<drb4_2>$drb4_2</drb4_2>
									<drb5_2>$drb5_2</drb5_2>
									<dqb1_2>$dqb1_2</dqb1_2>
									<dpb1_2>$dpb1_2</dpb1_2>
									<dqa1_2>$dqa1_2</dqa1_2>
									<dpa1_2>$dpa1_2</dpa1_2>      
								</patient_hla>
							</patient>
							";

							
							$donori=array();
							//pripojit informace o donorech
							$sloupce=array("a","b","c","drb1","drb3","drb4","drb5","dqb1","dpb1","dqa1","dpa1");
							if($DonorNumber):
								$vysledek_sub = mysql_query("SELECT typing_request_donor.* FROM typing_request_donor WHERE ID_typing='$ID' AND DonorNumber='$DonorNumber' ORDER BY DonorNumber, resolution");
									if(mysql_num_rows($vysledek_sub)>0):
										while($zaz_sub = mysql_fetch_array($vysledek_sub)):
											extract($zaz_sub);
											
											switch($resolution):
												case 1: $resolution_text="L"; break;
												case 2: $resolution_text="M"; break;
												case 3: $resolution_text="H"; break;
											endswitch;
											
											$donors_number[$DonorID]=$DonorNumber;
											
											foreach($sloupce as $klic=>$sloupec):
												if(${$sloupec}==1):
													$donori[$DonorID][$klic]=$resolution_text;
												else:
													if($donori[$DonorID][$klic]==""):
														$donori[$DonorID][$klic]="-";
													endif;
												endif;
											endforeach;
											reset($sloupce);
											
										endwhile;
										
										foreach($donori as $Donor_id=>$pole_hodnot):
										
											$xml.="<donor>
											<DonorNum>".$DonorNumber."</DonorNum>
											<DonorID>".$Donor_id."</DonorID>
											<Resolution>".implode("",$pole_hodnot)."</Resolution>
											</donor>";
										
										endforeach;
										
										mysql_query("UPDATE typing_request_donor SET datum_odeslani='".$dnes."' WHERE ID_typing='$ID' AND DonorNumber='$DonorNumber'");
										
										if(mysql_affected_rows()>0):
											$return_code=1;
										else:
											$return_code=5003;
										endif;
										
										$xml.="<return_code>$return_code</return_code>";
										
										
									endif;
								@mysql_free_result($vysledek_sub);
							endif;
							

							$xml.="</form>";

						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";

			return $xml;
			
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	* @param  string
	 * @param  integer
	 * @param  integer
	 * @param  integer
	 * @return string
	 */

	//fce pro schvaleni typing requestu konkretniho darce
	function ConfirmTypingReq($RegID, $ID, $DonorNum, $aLogMsgNum){
		if(autentizace()){
		
			$dnes=time();
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT InstID, PatientNum FROM typing_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) 
			SET typing_request_donor.aLogMsgNum='$aLogMsgNum', typing_request_donor.datum_zpracovani='".$dnes."', typing_request_donor.ID_stavu='1' 
			WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.ID_typing='".mysql_real_escape_string($ID)."' AND typing_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."'");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,9,$RegID.$PatientNum."P");
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
	
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	* @param  string
	 * @param  integer
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro zamitnuti typing requestu konkretniho darce
	function DenyTypingReq($RegID, $ID, $DonorNum, $duvod){
		if(autentizace()){
		
			$dnes=time();
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT InstID, PatientNum FROM typing_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) SET typing_request_donor.datum_zpracovani='".$dnes."', typing_request_donor.ID_stavu='2', typing_request_donor.duvod='".mysql_real_escape_string($duvod)."' WHERE typing_request.RegID='$RegID' AND typing_request_donor.ID_typing='".mysql_real_escape_string($ID)."' AND typing_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."'");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,10,$RegID.$PatientNum."P");
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
	
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	* @param  string
	* @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro zamitnuti typing requestu konkretniho darce
	function DenySendedTypingReq($RegID, $aLogMsgNum, $duvod){
		if(autentizace()){
		
			$dnes=time();
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT typing_request.InstID, typing_request.PatientNum FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) 
			WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) SET typing_request_donor.ID_stavu='3', typing_request_donor.datum_zpracovani='".$dnes."', typing_request_donor.duvod='".mysql_real_escape_string($duvod)."' WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."'");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,11,$RegID.$PatientNum."P");
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
	
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro prijem vysledku TypingRequestu
	function SendResultOfTyping($RegID, $aLogMsgNum, $aResultData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml_data=simplexml_load_string($aResultData);
			
			if($xml_data):
			
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
						<request>";
			
				//foreach asi zbytecny, form se zde vyskytne jen 1x..?
				foreach ($xml_data->form as $Result):
				
					$xml.="<form>";

					$pole_sloupcu=array();
					$pole_hodnot=array();
					
					unset($PatientID, $DonorID);
				
					foreach ($Result as $atribut=>$data):
						
						if($atribut=="REQ_DATE"):
							if($data!=""):
								if($data=="30.12.1899"):	
									$data="0";
								else:
									$explode=explode(".",$data);
									$data=MkTime(0,0,0,$explode[1],$explode[0],$explode[2]);
								endif;
							endif;
						endif;
						
						if($atribut=="D_BIRTH_DATE"):
							if($data!=""):
								if($data=="30.12.1899"):	
									$data="0";
								else:
									$explode=explode(".",$data);
									$data=MkTime(0,0,0,$explode[1],$explode[0],$explode[2]);
								endif;
							endif;
						endif;
						
						if($atribut=="D_CMV_DATE"):
							if($data!=""):
								if($data=="30.12.1899"):	
									$data="0";
								else:
									$explode=explode(".",$data);
									$data=MkTime(0,0,0,$explode[1],$explode[0],$explode[2]);
								endif;
							endif;
						endif;
						
						if($atribut=="TX_DATE"):
							if($data!=""):
								if($data=="30.12.1899"):	
									$data="0";
								else:
									$explode=explode(".",$data);
									$data=MkTime(0,0,0,$explode[1],$explode[0],$explode[2]);
								endif;
							endif;
						endif;
						
						if($atribut=="P_ID"):
							$atribut="PatientID";
							$PatientID=$data;
						endif;
						
						if($atribut=="D_ID"):
							$atribut="DonorID";
							$DonorID=$data;
						endif;

						
						$pole_sloupcu[]=$atribut;
						$pole_hodnot[]=mysql_real_escape_string($data);
					endforeach;
					
					//kontrola, jestli je aLogMsgNum v donorech
					$cislo_existuje = mysql_result(mysql_query("SELECT COUNT(*) FROM typing_request_donor WHERE aLogMsgNum='$aLogMsgNum'"), 0);
					
					if($cislo_existuje):
					
						unset($probehlo);
						
						//dohledani udaju
						$vysledek = mysql_query("SELECT typing_request.InstID, typing_request.PatientNum FROM typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) 
						WHERE typing_request.RegID='".mysql_real_escape_string($RegID)."' AND typing_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' LIMIT 1");
							if(mysql_num_rows($vysledek)>0):
								$zaz = mysql_fetch_assoc($vysledek);
									extract($zaz);
							endif;
						@mysql_free_result($vysledek);
						
					
					
						$vysledek_check = mysql_result(mysql_query("SELECT COUNT(*) FROM typing_result WHERE RegID='$RegID' AND aLogMsgNum='$aLogMsgNum'"), 0);
						if($vysledek_check):
							$pole_update=array();
							$citac=0;
							foreach($pole_sloupcu as $sloupec):
							
								$pole_update[]=$sloupec."='".$pole_hodnot[$citac]."'";
							
							$citac++;
							endforeach;
						
							$dotaz="UPDATE typing_result SET ".implode(", ",$pole_update).", datum_vlozeni='$dnes' WHERE RegID='$RegID' AND aLogMsgNum='$aLogMsgNum' LIMIT 1";
							//echo $dotaz."<br><br>";
							$vysledek = mysql_query($dotaz);
							if($vysledek):
								if(mysql_affected_rows()>0):
									$dotaz=mysql_affected_rows();
									$updatovano++;
									$probehlo=1;
									
									send_email($InstID,12,$RegID.$PatientNum."P and Donor ID ".$DonorID);
								else:
									unset($dotaz);
								endif;
							endif;
						else:
							$dotaz="INSERT INTO typing_result (ID, RegID, aLogMsgNum, ".implode(", ",$pole_sloupcu).", datum_vlozeni) VALUES ('NULL', '$RegID', '$aLogMsgNum', '".implode("', '",$pole_hodnot)."', '$dnes')";
							$vysledek = mysql_query($dotaz);
							if($vysledek):
								$probehlo=1;
								$importovano++;
								
								send_email($InstID,13,$RegID.$PatientNum."P and Donor ID ".$DonorID);
							endif;
						endif;
						
						if($probehlo):
							$xml.="<return_code>1</return_code>";
						else:
							$xml.="<return_code>5004</return_code>";
						endif;
						
					else:
						$xml.="<return_code>5005</return_code>";
					endif;
					
					$xml.="</form>";

				endforeach;

			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;

			//vratit xml, pokud update nebo import probehl
			$xml.="</request>";
			return $xml;
			
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce vraci sample requesty cekajici na odeslani
	function GetSampleRequests($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
			
				$vysledek = mysql_query("SELECT sample_request.*, sample_request_donor.DonorNumber 
				FROM sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
				WHERE sample_request.RegID='".mysql_real_escape_string($RegID)."' AND sample_request_donor.ID_stavu=0 ORDER BY sample_request.ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_assoc($vysledek)):
							extract($zaz);
							
							//dohledat temp_transpl_date
							$temp_transpl_date="";
							$vysledek8 = mysql_query("SELECT temp_transpl_date FROM search_request WHERE RegID='".$RegID."' AND InstID='".$InstID."' AND PatientNum='".$PatientNum."' ORDER BY ID DESC LIMIT 1");
								if(mysql_num_rows($vysledek8)>0):
									$zaz8 = mysql_fetch_array($vysledek8);
										extract($zaz8);
										if($temp_transpl_date<>0):
											$temp_transpl_date=date("Y-m-d",$temp_transpl_date); else: $temp_transpl_date="";
										endif;
								endif;
							@mysql_free_result($vysledek8);
							
							
							$xml.="<form>
							<ID>$ID</ID> 
							<transplant_center>$transplant_center</transplant_center>
							<preferred_courier>$preferred_courier</preferred_courier>
							<sample_to_institution>$sample_to_institution</sample_to_institution>
							<invoice_to_institution>$invoice_to_institution</invoice_to_institution>
							<sample_address>$sample_address</sample_address>
							<invoice_address>$invoice_address</invoice_address>
							<sample_attention>$sample_attention</sample_attention>
							<invoice_attention>$invoice_attention</invoice_attention>
							<sample_phone>$sample_phone</sample_phone>
							<invoice_phone>$invoice_phone</invoice_phone>
							<sample_fax>$sample_fax</sample_fax>
							<invoice_fax>$invoice_fax</invoice_fax>
							<sample_email>$sample_email</sample_email>
							<invoice_email>$invoice_email</invoice_email>
							<date_completing>".date("Y-m-d",$date_completing)."</date_completing>
							<mls_edta>$mls_edta</mls_edta>
							<mls_heparin>$mls_heparin</mls_heparin>
							<mls_acd>$mls_acd</mls_acd>
							<mls_clotted>$mls_clotted</mls_clotted>
							<mls_dna>$mls_dna</mls_dna>
							<mls_cpa>$mls_cpa</mls_cpa>
							<tubes_edta>$tubes_edta</tubes_edta>
							<tubes_heparin>$tubes_heparin</tubes_heparin>
							<tubes_acd>$tubes_acd</tubes_acd>
							<tubes_clotted>$tubes_clotted</tubes_clotted>
							<tubes_dna>$tubes_dna</tubes_dna>
							<tubes_cpa>$tubes_cpa</tubes_cpa>
							<monday>$monday</monday>
							<tuesday>$tuesday</tuesday>
							<wednesday>$wednesday</wednesday>
							<thursday>$thursday</thursday>
							<friday>$friday</friday>
							<saturday>$saturday</saturday>
							<sunday>$sunday</sunday>
							<InstID>$InstID</InstID>
							<RegID>$RegID</RegID>
							<patient>
								<PatientNum>$PatientNum</PatientNum>                     
								<PatientID>$PatientID</PatientID>                    
								<patient_name>$patient_name</patient_name>    
								<patient_id_dn>$patient_id_dn</patient_id_dn>
								<date_birth>".date("Y-m-d",$date_birth)."</date_birth>
								<gender>$gender</gender>
								<temp_transpl_date>$temp_transpl_date</temp_transpl_date>
								<patient_hla>
									<a_1>$a_1</a_1>
									<b_1>$b_1</b_1>
									<c_1>$c_1</c_1>
									<drb1_1>$drb1_1</drb1_1>
									<dqb1_1>$dqb1_1</dqb1_1>
									<a_2>$a_2</a_2>
									<b_2>$b_2</b_2>
									<c_2>$c_2</c_2>
									<drb1_2>$drb1_2</drb1_2>
									<dqb1_2>$dqb1_2</dqb1_2>
									<drb3_1>$drb3_1</drb3_1>
									<drb4_1>$drb4_1</drb4_1>
									<drb5_1>$drb5_1</drb5_1>
									<dpb1_1>$dpb1_1</dpb1_1>
									<dqa1_1>$dqa1_1</dqa1_1>
									<dpa1_1>$dpa1_1</dpa1_1>
									<drb3_2>$drb3_2</drb3_2>
									<drb4_2>$drb4_2</drb4_2>
									<drb5_2>$drb5_2</drb5_2>
									<dpb1_2>$dpb1_2</dpb1_2>
									<dqa1_2>$dqa1_2</dqa1_2>
									<dpa1_2>$dpa1_2</dpa1_2>
								</patient_hla>
							</patient>
							";

							
							//pripojit informace o donorech
							if($DonorNumber):
								$vysledek_sub = mysql_query("SELECT sample_request_donor.* FROM sample_request_donor WHERE ID_sample='$ID' AND DonorNumber='$DonorNumber' LIMIT 1");
									if(mysql_num_rows($vysledek_sub)>0):
										$zaz_sub = mysql_fetch_array($vysledek_sub);
											extract($zaz_sub);
										
											$xml.="<donor>
											<DonorNum>".$DonorNumber."</DonorNum>
											<DonorID>".$DonorID."</DonorID>
											</donor>";
											
											mysql_query("UPDATE sample_request_donor SET datum_odeslani='".$dnes."' WHERE ID_sample='$ID' AND DonorNumber='$DonorNumber'");
										
											if(mysql_affected_rows()>0):
												$return_code=1;
											else:
												$return_code=5003;
											endif;
											
											$xml.="<return_code>$return_code</return_code>";
										
									endif;
								@mysql_free_result($vysledek_sub);
							endif;
							

							$xml.="</form>";

						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";

			return $xml;
			
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	* @param  string
	 * @param  integer
	 * @param  integer
	 * @param  integer
	 * @return string
	 */

	//fce pro schvaleni sample requestu konkretniho darce
	function ConfirmSampleReq($RegID, $ID, $DonorNum, $aLogMsgNum){
		if(autentizace()){
		
			$dnes=time();
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT sample_request.InstID, sample_request.PatientNum, sample_request_donor.DonorID FROM sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
			WHERE sample_request.RegID='$RegID' AND sample_request_donor.ID_sample='".mysql_real_escape_string($ID)."' AND sample_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);

			
			$vysledek = mysql_query("UPDATE sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
			SET sample_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."', sample_request_donor.datum_zpracovani='".$dnes."', sample_request_donor.ID_stavu='1' 
			WHERE sample_request.RegID='$RegID' AND sample_request_donor.ID_sample='".mysql_real_escape_string($ID)."' AND sample_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."'");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,14,$RegID.$PatientNum."P and Donor ID ".$DonorID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
	
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	* @param  string
	 * @param  integer
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro zamitnuti sample requestu konkretniho darce
	function DenySampleReq($RegID, $ID, $DonorNum, $duvod){
		if(autentizace()){
		
			$dnes=time();
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT sample_request.InstID, sample_request.PatientNum, sample_request_donor.DonorID FROM sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
			WHERE sample_request.RegID='$RegID' AND sample_request_donor.ID_sample='".mysql_real_escape_string($ID)."' AND sample_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
			SET sample_request_donor.datum_zpracovani='".$dnes."', sample_request_donor.ID_stavu='2', sample_request_donor.duvod='".mysql_real_escape_string($duvod)."' 
			WHERE sample_request.RegID='$RegID' AND sample_request_donor.ID_sample='".mysql_real_escape_string($ID)."' AND sample_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."'");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,15,$RegID.$PatientNum."P and Donor ID ".$DonorID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
	
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	* @param  string
	* @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro zamitnuti sample requestu konkretniho darce
	function DenySendedSampleReq($RegID, $aLogMsgNum, $duvod){
		if(autentizace()){
		
			$dnes=time();
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT sample_request.InstID, sample_request.PatientNum, sample_request_donor.DonorID FROM sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
			WHERE sample_request.RegID='".mysql_real_escape_string($RegID)."' AND sample_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
			SET sample_request_donor.ID_stavu='3', sample_request_donor.datum_zpracovani='".$dnes."', sample_request_donor.duvod='".mysql_real_escape_string($duvod)."' 
			WHERE sample_request.RegID='".mysql_real_escape_string($RegID)."' AND sample_request_donor.aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."'");

			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,16,$RegID.$PatientNum."P and Donor ID ".$DonorID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
	
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce vraci workup requesty cekajici na odeslani
	function GetWorkupRequests($RegID){
		if(autentizace()){
		
			$dnes=time();
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>";
			
			$_pole_nevypsat=array("datum_vlozeni","ID_stavu","ID_uzivatele","datum_odeslani","datum_zpracovani","duvod","aLogMsgNum");
				
				$vysledek = mysql_query("SELECT * FROM workup_request WHERE ID_stavu='0' AND RegID='".mysql_real_escape_string($RegID)."' ORDER BY ID");
					if(mysql_num_rows($vysledek)>0):
						while($zaz = mysql_fetch_assoc($vysledek)):

							$xml.="<form>";
							
							//dohledat temp_transpl_date
							$temp_transpl_date="";
							$vysledek8 = mysql_query("SELECT temp_transpl_date FROM search_request WHERE RegID='".$zaz['RegID']."' AND InstID='".$zaz['InstID']."' AND PatientNum='".$zaz['PatientNum']."' ORDER BY ID DESC LIMIT 1");
								if(mysql_num_rows($vysledek8)>0):
									$zaz8 = mysql_fetch_array($vysledek8);
										extract($zaz8);
										if($temp_transpl_date<>0):
											$temp_transpl_date=date("Y-m-d",$temp_transpl_date); else: $temp_transpl_date="";
										endif;
								endif;
							@mysql_free_result($vysledek8);
							
							foreach($zaz as $klic=>$hodnota):
								if(!in_array($klic,$_pole_nevypsat)):
								
									if($klic=="ID"):
										$ID=$hodnota;
									endif;
								
									if($klic=="date_birth"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_completing"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_1"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_2"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_3"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_4"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_5"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									if($klic=="date_6"):
										if($hodnota<>0): $hodnota=date("Y-m-d",$hodnota); endif;
									endif;
									
									if(array_key_exists($klic,$_pole_nahrad)):
										$klic=$_pole_nahrad[$klic];
									endif;

									$xml.="<$klic>".$hodnota."</$klic>";

								endif;
							endforeach;
							
							$xml.="<temp_transpl_date>".$temp_transpl_date."</temp_transpl_date>";
							
							

							mysql_query("UPDATE workup_request SET datum_odeslani='".$dnes."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
							if(mysql_affected_rows()>0):
								$return_code=1;
							else:
								$return_code=5003;
							endif;
							
							$xml.="<return_code>$return_code</return_code>";
							$xml.="</form>";
							
						endwhile;
					else:
						$xml.="<form>";
						$xml.="<return_code>5001</return_code>";
						$xml.="</form>";
					endif;
				@mysql_free_result($vysledek);
			
			$xml.="</request>";

			//$xml=$_SERVER;
			//$xml = file_get_contents('php://input'); 
			

			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	/**
	 * @param  integer
	 * @param  integer
	 * @return string
	 */

	//fce pro potvrzeni workup requestu
	function ConfirmWorkupReq($ID,$aLogMsgNum){
		if(autentizace()){
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT workup_request.RegID, workup_request.InstID, workup_request.PatientNum, workup_request.DonorID FROM workup_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE workup_request SET ID_stavu='1', datum_zpracovani='".time()."', duvod='', aLogMsgNum='".mysql_real_escape_string($aLogMsgNum)."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,17,$RegID.$PatientNum."P and Donor ID ".$DonorID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;		
		}
	}
	
	
	/**
	 * @param  integer
	 * @param  string
	 * @return string
	 */

	//fce pro zamitnuti workup requestu
	function DenyWorkupRequest($ID,$duvod){
		if(autentizace()){
			
			//dohledani udaju
			$vysledek = mysql_query("SELECT workup_request.RegID, workup_request.InstID, workup_request.PatientNum, workup_request.DonorID FROM workup_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
				if(mysql_num_rows($vysledek)>0):
					$zaz = mysql_fetch_assoc($vysledek);
						extract($zaz);
				endif;
			@mysql_free_result($vysledek);
			
			
			$vysledek = mysql_query("UPDATE workup_request SET ID_stavu='2', datum_zpracovani='".time()."', duvod='".mysql_real_escape_string($duvod)."' WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
			if(mysql_affected_rows()>0):
				$return_code=1;
				send_email($InstID,18,$RegID.$PatientNum."P and Donor ID ".$DonorID);
			else:
				$return_code=5004;
			endif;
			
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>$return_code</return_code></form>
			</request>";
			return $xml;
			
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro prijem pacienta z Promethea
	function SendNewPatient($PatientData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($PatientData);
			
			if($xml):
				foreach ($xml->form as $Patient):

					$pole_sloupcu=array();
					$pole_hodnot=array();
					unset($idcko, $xml_data, $RegID, $PatientNum, $InstID);
					
					foreach ($Patient as $atribut=>$data):

						if($atribut=="date_request" || $atribut=="date_birth" || $atribut=="date_diagnosis" || $atribut=="cmv_date" || $atribut=="temp_transpl_date"):
							$data=get_timestamp2($data);
						endif;
						
						//$xml_data.="<$atribut>".$data."</$atribut>";
						
						$pole_sloupcu[]=$atribut;
						$pole_hodnot[]=mysql_real_escape_string($data);
						
						if($atribut=="RegID"): $RegID=$data; endif;
						if($atribut=="PatientNum"): $PatientNum=$data; endif;
						if($atribut=="InstID"): $InstID=$data; endif;
					
					endforeach;
					
					$pnum_je = mysql_result(mysql_query("SELECT COUNT(*) FROM search_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."'"), 0);
					if($pnum_je):
						$xml_data.="<return_code>5006</return_code>";
					else:
						$dotaz="INSERT INTO search_request (ID, ID_stavu, datum_zpracovani, datum_odeslani, ".implode(", ",$pole_sloupcu).", datum_vlozeni) VALUES ('NULL', '2', '$dnes', '$dnes', '".implode("', '",$pole_hodnot)."', '$dnes')";
						$vysledek = mysql_query($dotaz);
						if($vysledek):
							$idcko=MySQL_Insert_Id();
							$xml_data.="<return_code>1</return_code>
							<ID>".$idcko."</ID>";
							
							send_email($InstID,19,$RegID.$PatientNum."P");
						else:
							$xml_data.="<return_code>5004</return_code>";
						endif;
					endif;

				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro prijem typing requestu z Promethea
	function SendNewTypingRequest($TypingData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($TypingData);
			
			if($xml):
				
				$pole_sloupcu_pacient=array();
				$pole_hodnot_pacient=array();
				unset($xml_data, $idcko);
				
				$citac_form=1;
				foreach ($xml->form as $Typing):
				
					unset($RegID,$PatientNum,$DonorNumber,$DonorID,$InstID,$resolution,$aLogMsgNum);
	
					foreach ($Typing as $atribut=>$data):

						if($atribut=="date_completing"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="InstID"):
							$InstID=$data;
						endif;
						
						if($atribut=="RegID"):
							$RegID=$data;
						endif;
						
						if($atribut=="patient"):
							foreach ($data as $atribut2=>$data2):
							
								if($atribut2=="date_birth"):
									$data2=get_timestamp2($data2);
								endif;
								
								if($atribut2=="PatientNum"):
									$PatientNum=$data2;
								endif;
								
								if($atribut2=="patient_hla"):
									foreach ($data2 as $atribut3=>$data3):
										//$xml_data.="<$atribut3>".$data3."</$atribut3>";
										
										if($atribut3=="method_used"): $atribut3="patient_hla"; endif;
										
										$pole_sloupcu_pacient[]=$atribut3;
										$pole_hodnot_pacient[]=mysql_real_escape_string($data3);
									endforeach;
								else:
									//$xml_data.="<$atribut2>".$data2."</$atribut2>";
									
									$pole_sloupcu_pacient[]=$atribut2;
									$pole_hodnot_pacient[]=mysql_real_escape_string($data2);
									
								endif;
							endforeach;
						
						elseif($atribut=="donor"):
							foreach ($data as $atribut2=>$data2):
								//$xml_data.="<$atribut2>".$data2."</$atribut2>";
								
								if($atribut2=="DonorNum"): $DonorNumber=mysql_real_escape_string($data2); endif;
								if($atribut2=="DonorID"): $DonorID=mysql_real_escape_string($data2); endif;
								if($atribut2=="Resolution"): $resolution=$data2; endif;
								if($atribut2=="aLogMsgNum"): $aLogMsgNum=mysql_real_escape_string($data2); endif;
								
							endforeach;
						else:
							//$xml_data.="<$atribut>".$data."</$atribut>";
							
							$pole_sloupcu_pacient[]=$atribut;
							$pole_hodnot_pacient[]=mysql_real_escape_string($data);
						endif;
						
						//$pole_sloupcu[]=$atribut;
						//$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
					if($citac_form==1):

						//vlozit pacienta jednou
						$dotaz="INSERT INTO typing_request (ID, aktivni, ".implode(", ",$pole_sloupcu_pacient).", datum_vlozeni) VALUES ('NULL', '1', '".implode("', '",$pole_hodnot_pacient)."', '$dnes')";
						$vysledek = mysql_query($dotaz);
						if($vysledek):
							$idcko=MySQL_Insert_Id();
							$xml_data.="<return_code>1</return_code>
							<ID>".$idcko."</ID>";
						else:
							$xml_data.="<return_code>5004</return_code>";
						endif;

					endif;
					
					//vlozit donora
					if($idcko>0 && $DonorNumber):
					
						$sloupce=array("a","b","c","drb1","drb3","drb4","drb5","dqb1","dpb1","dqa1","dpa1");
						
						for($i=1;$i<=3;$i++):
							
							$hodnoty=array();
							for($z=0; $z<=10; $z++):
								$znak=mb_substr($resolution, $z, 1, 'UTF-8');
								
								if($i==1):
									if($znak=="L"):	$hodnoty[]=1; else: $hodnoty[]=0; endif;
								endif;
								if($i==2):
									if($znak=="M"):	$hodnoty[]=1; else: $hodnoty[]=0; endif;
								endif;
								if($i==3):
									if($znak=="H"):	$hodnoty[]=1; else: $hodnoty[]=0; endif;
								endif;
							endfor;
							
							//$xml_data.="*".implode("|",$hodnoty);
						
							$dotaz="INSERT INTO typing_request_donor (ID_typing, aLogMsgNum, DonorID, DonorNumber, resolution, ".implode(", ",$sloupce).", ID_stavu, datum_odeslani, datum_zpracovani) 
							VALUES ('$idcko', '".$aLogMsgNum."', '".$DonorID."', '".$DonorNumber."', '".$i."', ".implode(", ",$hodnoty).", '1', $dnes, '$dnes')";
							$vysledek = mysql_query($dotaz);
							if($vysledek):
								$xml_data="<return_code>1</return_code>";
								send_email($InstID,20,$RegID.$PatientNum."P and Donor ID ".$DonorID);
							else:
								$xml_data="<return_code>5004</return_code>";
							endif;
						endfor;
					endif;

				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro prijem sample requestu z Promethea
	function SendNewSampleRequest($SampleData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($SampleData);
			
			if($xml):
				
				$pole_sloupcu_pacient=array();
				$pole_hodnot_pacient=array();
				unset($xml_data, $idcko);
				
				$citac_form=1;
				foreach ($xml->form as $Sample):
				
					unset($RegID, $InstID, $PatientNum, $DonorNumber,$DonorID,$aLogMsgNum);
	
					foreach ($Sample as $atribut=>$data):

						if($atribut=="date_completing"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="InstID"):
							$InstID=$data;
						endif;
						
						if($atribut=="RegID"):
							$RegID=$data;
						endif;
						
						if($atribut=="patient"):
							foreach ($data as $atribut2=>$data2):
							
								if($atribut2=="date_birth"):
									$data2=get_timestamp2($data2);
								endif;
								
								if($atribut2=="PatientNum"):
									$PatientNum=$data2;
								endif;
								
								if($atribut2=="patient_hla"):
									foreach ($data2 as $atribut3=>$data3):
										//$xml_data.="<$atribut3>".$data3."</$atribut3>";
										
										$pole_sloupcu_pacient[]=$atribut3;
										$pole_hodnot_pacient[]=mysql_real_escape_string($data3);
									endforeach;
								else:
									//$xml_data.="<$atribut2>".$data2."</$atribut2>";
									
									$pole_sloupcu_pacient[]=$atribut2;
									$pole_hodnot_pacient[]=mysql_real_escape_string($data2);
									
								endif;
							endforeach;
						
						elseif($atribut=="donor"):
							foreach ($data as $atribut2=>$data2):
								//$xml_data.="<$atribut2>".$data2."</$atribut2>";
								
								if($atribut2=="DonorNum"): $DonorNumber=mysql_real_escape_string($data2); endif;
								if($atribut2=="DonorID"): $DonorID=mysql_real_escape_string($data2); endif;
								if($atribut2=="aLogMsgNum"): $aLogMsgNum=mysql_real_escape_string($data2); endif;
								
							endforeach;
						else:
							//$xml_data.="<$atribut>".$data."</$atribut>";
							
							$pole_sloupcu_pacient[]=$atribut;
							$pole_hodnot_pacient[]=mysql_real_escape_string($data);
						endif;
						
						//$pole_sloupcu[]=$atribut;
						//$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
					if($citac_form==1):
						
						
						//vlozit sample jednou
						$dotaz="INSERT INTO sample_request (ID, aktivni, ".implode(", ",$pole_sloupcu_pacient).", datum_vlozeni) VALUES ('NULL', '1', '".implode("', '",$pole_hodnot_pacient)."', '$dnes')";
						$vysledek = mysql_query($dotaz);
						if($vysledek):
							$idcko=MySQL_Insert_Id();
							$xml_data.="<return_code>1</return_code>
							<ID>".$idcko."</ID>";
						else:
							$xml_data.="<return_code>5004</return_code>";
						endif;

					endif;
					
					//vlozit donora
					if($idcko>0 && $DonorNumber):

						//$xml_data.="*".implode("|",$hodnoty);
					
						$dotaz="INSERT INTO sample_request_donor (ID_sample, aLogMsgNum, DonorID, DonorNumber, ID_stavu, datum_odeslani, datum_zpracovani) 
						VALUES ('$idcko', '".$aLogMsgNum."', '".$DonorID."', '".$DonorNumber."', '1', $dnes, '$dnes')";
						$vysledek = mysql_query($dotaz);
						if($vysledek):
							$xml_data="<return_code>1</return_code>";
							send_email($InstID,21,$RegID.$PatientNum."P and Donor ID ".$DonorID);
						else:
							$xml_data="<return_code>5004</return_code>";
						endif;
						
					endif;

				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro prijem workup requestu z Promethea
	function SendNewWorkupRequest($WorkupData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($WorkupData);
			
			if($xml):
				
				$pole_sloupcu=array();
				$pole_hodnot=array();
				unset($xml_data, $idcko);
				
				$citac_form=1;
				foreach ($xml->form as $Workup):
				
					unset($InstID, $RegID, $PatientNum, $DonorID);
	
					foreach ($Workup as $atribut=>$data):

						if($atribut=="date_birth"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="date_completing"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="date_1"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_2"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_3"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_4"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_5"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_6"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="InstID"):
							$InstID=$data;
						endif;
						
						if($atribut=="RegID"):
							$RegID=$data;
						endif;
						
						if($atribut=="PatientNum"):
							$PatientNum=$data;
						endif;
						
						if($atribut=="DonorID"):
							$DonorID=$data;
						endif;
						
						$pole_sloupcu[]=$atribut;
						$pole_hodnot[]=mysql_real_escape_string($data);
						
						
						//$pole_sloupcu[]=$atribut;
						//$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
					//vlozit workup jednou
					$dotaz="INSERT INTO workup_request (ID, ".implode(", ",$pole_sloupcu).", datum_vlozeni, datum_odeslani, datum_zpracovani, ID_stavu) VALUES ('NULL', '".implode("', '",$pole_hodnot)."', '$dnes', '$dnes', '$dnes', '1')";
					$vysledek = mysql_query($dotaz);
					if($vysledek):
						$idcko=MySQL_Insert_Id();
						$xml_data.="<return_code>1</return_code>
						<ID>".$idcko."</ID>";
						send_email($InstID,22,$RegID.$PatientNum."P and Donor ID ".$DonorID);
					else:
						$xml_data.="<return_code>5004</return_code>";
					endif;
			
				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro update pacienta z Promethea
	function UpdatePatient($PatientData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($PatientData);
			
			if($xml):
				$citac_foreach=0;
				foreach ($xml->form as $Patient):
					$citac_foreach++;

					$pole_sloupcu=array();
					$pole_hodnot=array();
					unset($xml_data, $RegID, $ID, $PatientNum);
					
					foreach ($Patient as $atribut=>$data):
					
						if($atribut=="RegID"): $RegID=$data; endif;
						if($atribut=="PatientNum"): $PatientNum=$data; endif;
						if($atribut=="ID"): $ID=$data; endif;
						
						if($atribut=="RegID" || $atribut=="ID" || $atribut=="InstID"):
							unset($atribut);
						endif;

						if($atribut=="date_request" || $atribut=="date_birth" || $atribut=="date_diagnosis" || $atribut=="cmv_date" || $atribut=="temp_transpl_date"):
							$data=get_timestamp2($data);
							$data_txt.=$data." / ";
						endif;
						
						//$xml_data.="<$atribut>".$data."</$atribut>";
						
						if($atribut):
							$pole_sloupcu[]=$atribut;
							$pole_hodnot[]=mysql_real_escape_string($data);
						endif;
						
					
					endforeach;
					
					$pacient_je = mysql_result(mysql_query("SELECT COUNT(*) FROM search_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."'"), 0);
					if(!$pacient_je):
						$xml_data.="<return_code>5001</return_code>";
					else:
						$pole_update=array();
						$citac=0;
						foreach($pole_sloupcu as $sloupec):
						
							$pole_update[]=$sloupec."='".$pole_hodnot[$citac]."'";
						
						$citac++;
						endforeach;
						
						$pnum_je=0;
						
						if($PatientNum!=""):
							$pnum_je = mysql_result(mysql_query("SELECT COUNT(*) FROM search_request WHERE ID!='".mysql_real_escape_string($ID)."' AND RegID='".mysql_real_escape_string($RegID)."' AND PatientNum='".mysql_real_escape_string($PatientNum)."'"), 0);
						endif;
						
						if($pnum_je):
							$xml_data.="<return_code>5006</return_code>";
						else:
							$dotaz="UPDATE search_request SET ".implode(", ",$pole_update)." WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1";
							$vysledek = mysql_query($dotaz);
							if($vysledek):
								$xml_data.="<return_code>1</return_code>";
								
								//dohledani udaju
								$vysledek = mysql_query("SELECT InstID, PatientNum FROM search_request WHERE ID='".mysql_real_escape_string($ID)."' LIMIT 1");
									if(mysql_num_rows($vysledek)>0):
										$zaz = mysql_fetch_assoc($vysledek);
											extract($zaz);
									endif;
								@mysql_free_result($vysledek);
								
								if($PatientNum):
									send_email($InstID,23," Patient ID ".$RegID.$PatientNum."P");
								else:
									send_email($InstID,23," IntID ".$ID);
								endif;
							else:
								$xml_data.="<return_code>5004</return_code>";
							endif;
						endif;
					endif;

				endforeach;
				
				
				if($citac_foreach==0):
					$xml_data.="<return_code>5002</return_code>";
				endif;
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */
	function UpdateTypingRequest($TypingData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($TypingData);
			
			if($xml):
				
				$pole_sloupcu_pacient=array();
				$pole_hodnot_pacient=array();
				unset($xml_data, $idcko);
				
				$citac_form=1;
				foreach ($xml->form as $Typing):
				
					unset($DonorNumber,$DonorID,$resolution,$aLogMsgNum, $RegID, $InstID, $ID, $PatientNum);
	
					foreach ($Typing as $atribut=>$data):

						if($atribut=="RegID"): $RegID=$data; endif;
						if($atribut=="ID"): $ID=$data; endif;
						
						if($atribut=="InstID" || $atribut=="RegID" || $atribut=="ID"):	//neupdatovat
							unset($atribut);
						endif;
						
						if($atribut=="date_completing"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="patient"):
							foreach ($data as $atribut2=>$data2):
								
								if($atribut2=="PatientNum" || $atribut2=="PatientID" || $atribut2=="patient_name" || $atribut2=="patient_registry" || $atribut2=="diagnosis" || $atribut2=="date_birth"):	//neupdatovat
									unset($atribut2);
								endif;
								
								
								if($atribut2=="date_birth"):
									$data2=get_timestamp2($data2);
								endif;
								
								
								
								if($atribut2=="patient_hla"):
									foreach ($data2 as $atribut3=>$data3):
										//$xml_data.="<$atribut3>".$data3."</$atribut3>";
										
										if($atribut3=="method_used"): $atribut3="patient_hla"; endif;
										
										$pole_sloupcu_pacient[]=$atribut3;
										$pole_hodnot_pacient[]=mysql_real_escape_string($data3);
									endforeach;
								else:
									//$xml_data.="<$atribut2>".$data2."</$atribut2>";
									
									if($atribut2):
										$pole_sloupcu_pacient[]=$atribut2;
										$pole_hodnot_pacient[]=mysql_real_escape_string($data2);
									endif;
									
								endif;
							endforeach;
						
						elseif($atribut=="donor"):
							foreach ($data as $atribut2=>$data2):
								//$xml_data.="<$atribut2>".$data2."</$atribut2>";
								
								if($atribut2=="DonorNum"): $DonorNumber=mysql_real_escape_string($data2); endif;
								if($atribut2=="DonorID"): $DonorID=mysql_real_escape_string($data2); endif;
								if($atribut2=="Resolution"): $resolution=$data2; endif;
								if($atribut2=="aLogMsgNum"): $aLogMsgNum=mysql_real_escape_string($data2); endif;
								
							endforeach;
						else:
							//$xml_data.="<$atribut>".$data."</$atribut>";
							
							if($atribut):
								$pole_sloupcu_pacient[]=$atribut;
								$pole_hodnot_pacient[]=mysql_real_escape_string($data);
							endif;
						endif;
						
						//$pole_sloupcu[]=$atribut;
						//$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
					if($citac_form==1):
						
						//dohledani udaju
						$vysledek = mysql_query("SELECT typing_request.InstID, typing_request.PatientNum FROM typing_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1");
							if(mysql_num_rows($vysledek)>0):
								$zaz = mysql_fetch_assoc($vysledek);
									extract($zaz);
							endif;
						@mysql_free_result($vysledek);
						
						
						$pole_update=array();
						$citac=0;
						foreach($pole_sloupcu_pacient as $sloupec):
						
							$pole_update[]=$sloupec."='".$pole_hodnot_pacient[$citac]."'";
						
						$citac++;
						endforeach;
						
						$zaznam_je = mysql_result(mysql_query("SELECT COUNT(*) FROM typing_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."'"), 0);
						if(!$zaznam_je):
							$xml_data.="<return_code>5006</return_code>";
						else:
							//vlozit pacienta jednou
							$dotaz="UPDATE typing_request SET ".implode(", ",$pole_update)." WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1";
							$vysledek = mysql_query($dotaz);
							if($vysledek):
							
								//upravit donora
								if($DonorID):
								
									$sloupce=array("a","b","c","drb1","drb3","drb4","drb5","dqb1","dpb1","dqa1","dpa1");
									
									for($i=1;$i<=3;$i++):
										
										$hodnoty=array();
										for($z=0; $z<=10; $z++):
											$znak=mb_substr($resolution, $z, 1, 'UTF-8');
											
											if($i==1):
												if($znak=="L"):	$hodnoty[]=1; else: $hodnoty[]=0; endif;
											endif;
											if($i==2):
												if($znak=="M"):	$hodnoty[]=1; else: $hodnoty[]=0; endif;
											endif;
											if($i==3):
												if($znak=="H"):	$hodnoty[]=1; else: $hodnoty[]=0; endif;
											endif;
										endfor;
										
										//$xml_data.="*".implode("|",$hodnoty);
									
										$pole_update_donor=array();
										$citac_sl=0;
										foreach($sloupce as $sloupec):
										
											$pole_update_donor[]=$sloupec."='".$hodnoty[$citac_sl]."'";
										
										$citac_sl++;
										endforeach;
						
										$dotaz="UPDATE typing_request_donor SET ".implode(", ", $pole_update_donor)." 
										WHERE ID_typing='".mysql_real_escape_string($ID)."' AND DonorID='".mysql_real_escape_string($DonorID)."' AND resolution='".$i."'";
										$vysledek = mysql_query($dotaz);
										if($vysledek):
											if($i==1):
												$xml_data.="<return_code>1</return_code>";
												send_email($InstID,24,$RegID.$PatientNum."P and Donor ID ".$DonorID);
											endif;
										else:
											if($i==1):
												$xml_data="<return_code>5004</return_code>";
											endif;
										endif;
									endfor;
									
								else:
									$xml_data.="<return_code>1</return_code>";
									send_email($InstID,24,$RegID.$PatientNum."P");
								endif;

							else:
								$xml_data.="<return_code>5004</return_code>";
							endif;
						endif;

					endif;
					
					

				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	/**
	 * @param  string
	 * @return string
	 */
	function UpdateSampleRequest($SampleData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($SampleData);
			
			if($xml):
				
				$pole_sloupcu_pacient=array();
				$pole_hodnot_pacient=array();
				unset($xml_data, $idcko);
				
				$citac_form=1;
				foreach ($xml->form as $Sample):
				
					unset($DonorNumber,$DonorID,$aLogMsgNum,$ID,$RegID);
	
					foreach ($Sample as $atribut=>$data):

						if($atribut=="ID"):
							$ID=$data;
						endif;
						
						if($atribut=="RegID"): $RegID=$data; endif;
						
						if($atribut=="date_completing"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="smp_arr_date"):
							$data=get_timestamp2($data);
						endif;
					
						
						if($atribut=="transplant_center" || $atribut=="ID" || $atribut=="RegID" || $atribut=="InstID"):	//neupdatovat
							unset($atribut);
						endif;
						
						if($atribut=="patient"):
							foreach ($data as $atribut2=>$data2):
							
								if($atribut2=="PatientNum" || $atribut2=="PatientID" || $atribut2=="patient_name" || $atribut2=="date_birth" || $atribut2=="gender"):	//neupdatovat
									unset($atribut2);
								endif;
						
								if($atribut2=="date_birth"):
									$data2=get_timestamp2($data2);
								endif;
								
								
								if($atribut2=="patient_hla"):
									foreach ($data2 as $atribut3=>$data3):
										//$xml_data.="<$atribut3>".$data3."</$atribut3>";
										
										$pole_sloupcu_pacient[]=$atribut3;
										$pole_hodnot_pacient[]=mysql_real_escape_string($data3);
									endforeach;
								else:
									//$xml_data.="<$atribut2>".$data2."</$atribut2>";
									
									if($atribut2):
										$pole_sloupcu_pacient[]=$atribut2;
										$pole_hodnot_pacient[]=mysql_real_escape_string($data2);
									endif;
									
								endif;
							endforeach;
						
						elseif($atribut=="donor"):
							foreach ($data as $atribut2=>$data2):
								//$xml_data.="<$atribut2>".$data2."</$atribut2>";
								
								if($atribut2=="DonorNum"): $DonorNumber=mysql_real_escape_string($data2); endif;
								if($atribut2=="DonorID"): $DonorID=mysql_real_escape_string($data2); endif;
								if($atribut2=="aLogMsgNum"): $aLogMsgNum=mysql_real_escape_string($data2); endif;
								
							endforeach;
						else:
							//$xml_data.="<$atribut>".$data."</$atribut>";
							
							if($atribut):
								$pole_sloupcu_pacient[]=$atribut;
								$pole_hodnot_pacient[]=mysql_real_escape_string($data);
							endif;
						endif;
						
						//$pole_sloupcu[]=$atribut;
						//$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
					if($citac_form==1):
						
						$pole_update_pacient=array();
						$citac=0;
						foreach($pole_sloupcu_pacient as $sloupec):
						
							$pole_update_pacient[]=$sloupec."='".$pole_hodnot_pacient[$citac]."'";
						
						$citac++;
						endforeach;
						
						$zaznam_je = mysql_result(mysql_query("SELECT COUNT(*) FROM sample_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."'"), 0);
						if(!$zaznam_je):
							$xml_data.="<return_code>5006</return_code>";
						else:
							//upravit sample jednou
							$dotaz="UPDATE sample_request SET ".implode(", ",$pole_update_pacient)." WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1";
							$vysledek = mysql_query($dotaz);
							if($vysledek):
								$xml_data.="<return_code>1</return_code>";
								
								//dohledani udaju
								$vysledek = mysql_query("SELECT sample_request.InstID, sample_request.PatientNum FROM sample_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1");
									if(mysql_num_rows($vysledek)>0):
										$zaz = mysql_fetch_assoc($vysledek);
											extract($zaz);
									endif;
								@mysql_free_result($vysledek);
						
								send_email($InstID,25,$RegID.$PatientNum."P");
							else:
								$xml_data.="<return_code>5004</return_code>";
							endif;
						endif;

					endif;
					
					//upravit donora - NEupravovat
					/*
					if($idcko>0 && $DonorNumber):

						//$xml_data.="*".implode("|",$hodnoty);
					
						$dotaz="UPDATE sample_request_donor SET DonorID='".$DonorID."', DonorNumber='".$DonorNumber."' WHERE ID_sample='".mysql_real_escape_string($ID)."' AND aLogMsgNum='".$aLogMsgNum."' LIMIT 1";
						$vysledek = mysql_query($dotaz);
						if($vysledek):
							$xml_data="<return_code>1</return_code>";
						else:
							$xml_data="<return_code>5004</return_code>";
						endif;
						
					endif;
					*/

				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	/**
	 * @param  string
	 * @return string
	 */
	function UpdateWorkupRequest($WorkupData){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			$updatovano=0;
			
			$xml=simplexml_load_string($WorkupData);
			
			if($xml):
				
				$pole_sloupcu=array();
				$pole_hodnot=array();
				unset($xml_data, $idcko);
				
				$citac_form=1;
				foreach ($xml->form as $Workup):
	
					$pole_sloupcu=array();
					$pole_hodnot=array();
					unset($ID, $RegID);
					
					foreach ($Workup as $atribut=>$data):

						if($atribut=="ID"):
							$ID=$data;
						endif;
						
						
						if($atribut=="patient_name" || $atribut=="PatientNum" || $atribut=="PatientID" || $atribut=="patient_registry" || $atribut=="transplant_center" || $atribut=="gender" || $atribut=="weight" || $atribut=="date_birth" || $atribut=="cmv" || $atribut=="rhesus_1" || $atribut=="rhesus_2" || $atribut=="DonorID" || $atribut=="donor_registry" || $atribut=="donor_date_birth" || $atribut=="donor_cmv" || $atribut=="donor_rhesus" || $atribut=="donor_weight" || $atribut=="donor_gender" || $atribut=="InstID"):	//neupdatovat
							unset($atribut);
						endif;
						
						
						if($atribut=="RegID"): $RegID=$data; endif;
						
						if($atribut=="date_birth"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="date_completing"):
							$data=get_timestamp2($data);
						endif;
						
						if($atribut=="date_1"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_2"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_3"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_4"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_5"):
							$data=get_timestamp2($data);
						endif;
						if($atribut=="date_6"):
							$data=get_timestamp2($data);
						endif;
						
						
						$pole_sloupcu[]=$atribut;
						$pole_hodnot[]=mysql_real_escape_string($data);
						
						
						//$pole_sloupcu[]=$atribut;
						//$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
						$pole_update=array();
						$citac=0;
						foreach($pole_sloupcu as $sloupec):
						
							$pole_update[]=$sloupec."='".$pole_hodnot[$citac]."'";
						
						$citac++;
						endforeach;
					
						$zaznam_je = mysql_result(mysql_query("SELECT COUNT(*) FROM workup_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."'"), 0);
						if(!$zaznam_je):
							$xml_data.="<return_code>5006</return_code>";
						else:
							//upravit workup jednou
							$dotaz="UPDATE workup_request SET ".implode(", ",$pole_update)." WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1";

							$vysledek = mysql_query($dotaz);
							if($vysledek):
								$xml_data.="<return_code>1</return_code>";
								
								//dohledani udaju
								$vysledek = mysql_query("SELECT workup_request.InstID, workup_request.PatientNum FROM workup_request WHERE RegID='".mysql_real_escape_string($RegID)."' AND ID='".mysql_real_escape_string($ID)."' LIMIT 1");
									if(mysql_num_rows($vysledek)>0):
										$zaz = mysql_fetch_assoc($vysledek);
											extract($zaz);
									endif;
								@mysql_free_result($vysledek);
								
								send_email($InstID,26,$RegID.$PatientNum."P");
							else:
								$xml_data.="<return_code>5004</return_code>";
							endif;
						endif;
			
				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}
	
	
	
	/**
	 * @param  string
	 * @return string
	 */

	//fce pro odpoved k requestu
	function SendReqResponse($XMLdata){
		if(autentizace()){
			
			$dnes=time();
			$importovano=0;
			
			$xml=simplexml_load_string($XMLdata);
			
			if($xml):
				
				$pole_sloupcu=array();
				$pole_hodnot=array();
				unset($xml_data);
				
				$citac_form=1;
				foreach ($xml->form as $ReqResponse):
	
					$pole_sloupcu=array();
					$pole_hodnot=array();
					unset($RequestID, $RegID, $RequestType, $PatientNum, $DonorNum, $ResponseID, $ReasonID, $Msg);
					
					foreach ($ReqResponse as $atribut=>$data):
					
						if($atribut=="RequestID"): $RequestID=$data; endif;
						if($atribut=="RequestType"): $RequestType=$data; endif;
						if($atribut=="DonorNum"): $DonorNum=$data; endif;
						if($atribut=="PatientNum"): $PatientNum=$data; endif;

						$pole_sloupcu[]=$atribut;
						$pole_hodnot[]=mysql_real_escape_string($data);
					
					endforeach;
					
		
					
					//vlozit odpoved
					$dotaz="INSERT INTO request_response (ID, ".implode(", ",$pole_sloupcu).", datum_vlozeni) VALUES ('NULL', '".implode("', '",$pole_hodnot)."', '$dnes')";
					$vysledek = mysql_query($dotaz);
					if($vysledek):
					
						$idcko=MySQL_Insert_Id();
					
						//presunout request do stavu 1
						if($RequestType==1):	//typing
							$sql = mysql_query("UPDATE typing_request_donor LEFT JOIN typing_request ON(typing_request_donor.ID_typing=typing_request.ID) 
							SET typing_request_donor.datum_zpracovani='".$dnes."', typing_request_donor.ID_stavu='1' 
							WHERE typing_request.PatientNum='".mysql_real_escape_string($PatientNum)."' 
							AND typing_request_donor.ID_typing='".mysql_real_escape_string($RequestID)."' AND typing_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."'");
						endif;
						
						if($RequestType==2):	//sample
							$sql = mysql_query("UPDATE sample_request_donor LEFT JOIN sample_request ON(sample_request_donor.ID_sample=sample_request.ID) 
							SET sample_request_donor.datum_zpracovani='".$dnes."', sample_request_donor.ID_stavu='1' 
							WHERE sample_request.PatientNum='".mysql_real_escape_string($PatientNum)."' 
							AND sample_request_donor.ID_sample='".mysql_real_escape_string($RequestID)."' AND sample_request_donor.DonorNumber='".mysql_real_escape_string($DonorNum)."'");
						endif;
						
						if($RequestType==3):	//workup
							$sql = mysql_query("UPDATE workup_request 
							SET workup_request.datum_zpracovani='".$dnes."', workup_request.ID_stavu='1' 
							WHERE workup_request.PatientNum='".mysql_real_escape_string($PatientNum)."' 
							AND workup_request.ID='".mysql_real_escape_string($RequestID)."'");
						endif;
						
						$ovlivneno=mysql_affected_rows();
						
						if($idcko && $ovlivneno>0):
							$xml_data.="<return_code>1</return_code>";
						else:
							$xml_data.="<return_code>5004</return_code>";
						endif;
					else:
						$xml_data.="<return_code>5004</return_code>";
					endif;
					
			
				$citac_form++;
				endforeach;
				
				
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form>".$xml_data."</form>
				</request>";
				return $xml;
				
			else:
				$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
				<request>
					<form><return_code>5002</return_code></form>
				</request>";
				return $xml;
			endif;
		
		}else{
			$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<request>
				<form><return_code>5000</return_code></form>
			</request>";
			return $xml;
		}
	}

}


require 'WSDLDocument.php';
$wsdl = new WSDLDocument('WebApp');
header('Content-Type: text/xml');
echo $wsdl->saveXML();

?>