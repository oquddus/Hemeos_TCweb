<?
include('1-head.php');
?>


<?
if(!$_GET['ID'] || ($_GET['ID'] && $_GET['h']==sha1($_GET['ID']."SaltIsGoodForLife45")) ){
	
//objekt pro praci s tokeny
$Token = new Token();

$pocet_donoru = 20;


function uprav_data()
{
    //get_timestamp('date_birth');	//neprevadet, v inputu je uz integer
    get_timestamp('date_completing');

    get_timestamp('date_1');
    get_timestamp('date_2');
    get_timestamp('date_3');
    get_timestamp('date_4');
    get_timestamp('date_5');
    get_timestamp('date_6');


    $_POST['ID_uzivatele'] = $_SESSION['usr_ID'];
}


//--vlozit zaznam
if ($_POST['action'] == "vlozit"):
	if($Token->useToken($_POST['token'])):

		$_POST['datum_vlozeni'] = time();

		uprav_data();

		
		insert_tb('workup_request');
		if ($idcko):    //--pokud vse vlozeno, vim id vlozeneho radku
			message(3, "Item was successfuly inserted.", "Insert next one", "$_SERVER[PHP_SELF]");
		else:
			message(1, "Item was not inserted.", "", "");

			call_post();    //--zrusi akci, post promenne da do lokalnich
		endif;
		
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
	
	
	
	
	

endif;


//--upravit zaznam
if ($_POST['action'] == "upravit"):
	if($Token->useToken($_POST['token'])):

		uprav_data();

		$_POST['datum_vlozeni'] = time();

		$update[0] = array("ID", "ID_stavu", "datum_odeslani", "datum_zpracovani", "duvod");    //--ktere sloupce vyloucit z updatu
		$update[1] = array("WHERE ID='" . mysql_real_escape_string($_GET['ID']) . "'");    //--where

		mysql_query("DELETE FROM workup_request WHERE ID='".clean_high($_GET['ID'])."'");
		
		insert_tb('workup_request');
		//update_tb('workup_request', $update);    //--tabulka   |   pole vyloucenych sloupcu

		
		
				if ($SQL):    //--pokud update neco zmenil
			message(1, "Item was successfuly edited.", "", "");
		else:
			message(1, "Item was not edited.", "", "");
		endif;

		unset($_POST['action']);
	else:
		message(1, "Form can not be sent, token not found.", "", "");
	endif;
endif;


//-----------------------------------------formular
if (!$_POST['action']):

	//Generuj token
	$token=$Token->getToken();

    //--kontrola povinnyh poli v  JS
    $povinne[0] = array("formular");    //--nazev formulare
    $povinne[1] = array("patient_select", "DonorID");    //--nazev povinnych inputu
    $povinne[2] = array("Patient", "Donor");    //--textove nazvy kontrolovanych poli
    $povinne[3] = array(3, 3);    //--typ (textbox=1, checkbox=2, selectbox=3)

    get_povinne_js($povinne);


    //--nacteni udadu pro editaci
    if ($_GET['ID']):
        $vysledek = mysql_query("SELECT * FROM workup_request WHERE ID='" . mysql_real_escape_string($_GET['ID']) . "' LIMIT 1");
        if (mysql_num_rows($vysledek) > 0):
            $zaz = mysql_fetch_array($vysledek);
            $zaz = htmlspecialchars_array_encode($zaz);
            extract($zaz);

        endif;
        @mysql_free_result($vysledek);
    endif;


//admin vidi vse, bezny uzivatel jen za svoje TC pripadne registr
    if ($_SESSION['usr_ID_role'] != 1):
        $where_pomocny = " AND search_request.RegID='" . $_SESSION['usr_RegID'] . "'";

        if ($_SESSION['usr_InstID']):
            $where_pomocny .= " AND search_request.InstID='" . $_SESSION['usr_InstID'] . "'";
        endif;
    endif;


    $form_name = "formular";

    if ($_SESSION['usr_InstID']):

        if ($ID_stavu == 0 || $ID_stavu == 2):
            echo "<form onsubmit=\"return Kontrola();\" enctype=\"multipart/form-data\" method=\"POST\" action=\"\" name=\"formular\">";
        else:
            message(1, "Record was already sent, now you can only read.", "", "");
        endif;

    else:
        message(1, "You are not a TC member, you cant fill the form.", "", "");
    endif;

    echo "<h1>WORKUP REQUEST</h1>

		<div style=\"float:left;\"><h2>PATIENT DATA:</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient name:</b></div>
				<div style=\"float:left; margin-left:5px;\"><input type=\"hidden\" style=\"width:260px;\" name=\"patient_name\" id=\"patient_name\" value=\"$patient_name\" readonly>
				<select name=\"patient_select\" id=\"patient_select\" style=\"width:250px;\" onchange=\"zmena_pacienta();\" tabindex=\"1\">";

    echo "<option value=\"\">- choose patient -</option>";

    $vysledek_sub = mysql_query("SELECT last_name, first_name, PatientNum AS PatientN, RegID AS rid FROM search_request WHERE (search_request.ID_stavu='2' OR PatientNum='" . mysql_real_escape_string($PatientNum) . "') AND PatientNum!='' $where_pomocny ORDER BY PatientNum");
    if (mysql_num_rows($vysledek_sub) > 0):
        while ($zaz_sub = mysql_fetch_assoc($vysledek_sub)):
			$zaz_sub = htmlspecialchars_array_encode($zaz_sub);
            extract($zaz_sub);

            echo "<option value=\"$PatientN\"";
            if ($_GET['Pn'] == $PatientN || $PatientNum == $PatientN): echo " selected"; endif;
            echo " data-reg=\"" . $rid . "\">$last_name $first_name, ($PatientN)</option>";

        endwhile;
    endif;
    @mysql_free_result($vysledek_sub);

    echo "</select>
				</div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Patient ID number:</b><br>(assigned by Hemeos)</div>
				<div style=\"float:left; margin-left:5px;\">
				<input type=\"text\" style=\"width:260px;\" name=\"PatientID\" id=\"PatientID\" value=\"";
    if ($PatientID): echo $PatientID; endif;
    if ($_GET['Pn']): echo $_GET['RegID'] . $_GET['Pn'] . "P"; endif;
    echo "\" class=\"bg1\" readonly>
				<input type=\"hidden\" style=\"width:260px;\" name=\"PatientNum\" id=\"PatientNum\" value=\"";
    if ($PatientNum): echo $PatientNum; endif;
    if ($_GET['Pn']): echo $_GET['Pn']; endif;
    echo "\" readonly></div>
			</td>
		</tr>
		
		<tr>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Transplant center:</b></div>
				<div style=\"float:left; margin:4px 0 0 5px;\"><span id=\"id_transplant_center\">";
    if ($InstID): echo "$InstID";
    else: echo $_SESSION['usr_InstID']; endif;
    echo "</span><input type=\"hidden\" style=\"width:255px;\" name=\"transplant_center\" value=\"";
    if ($InstID): echo "$InstID";
    else: echo $_SESSION['usr_InstID']; endif;
    echo "\" tabindex=\"2\" class=\"bg1\" readonly></div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Gender:</b></div>
				<div style=\"float:left; width:84px; margin:4px 0 0 5px;\"><span id=\"id_gender\">";
    if ($gender == 1): echo "Female"; endif;
    if ($gender == 2): echo "Male"; endif;
    echo "</span> <input type=\"hidden\" value=\"$gender\" name=\"gender\" id=\"gender\"></div>
				
				<div style=\"float:left; margin:4px 0 0 20px;\"><b>Weight:</b></div>
				<div style=\"float:left; margin:4px 0 0 5px;\"><span id=\"id_weight\">$weight</span><input type=\"hidden\" value=\"$weight\" style=\"width:25px;\" name=\"weight\" id=\"weight\"></div>
				<div style=\"float:left; margin:4px 0 0 5px;\">kg</div>
			</td>
		</tr>
		
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of birth:</b><br>(" . $_SESSION['date_format'] . ")</div>
				<div style=\"float:left; margin:4px 0 0 5px;\"><div style=\"float:left;\"><span id=\"id_date_birth\">";
    if ($date_birth): echo date($_SESSION['date_format_php'], $date_birth); endif;
    echo "</span><input type=\"hidden\" style=\"width:250px;\" name=\"date_birth\" id=\"date_birth\" value=\"";
    if ($date_birth): echo date($_SESSION['date_format_php'], $date_birth); endif;
    echo "\" class=\"bg1\" readonly></div>";
    echo "<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    //get_calendar('date_birth',$form_name);
    echo "</div>";
    echo "</div>
			</td>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>CMV:</b></div>
				<div style=\"float:left; width:100px; margin:4px 0 0 5px;\"><span id=\"id_cmv\">";
    if ($cmv == 1):
        echo "Positive";
    endif;
    if ($cmv == 2):
        echo "Negative";
    endif;
    echo "</span><input type=\"hidden\" value=\"$cmv\" name=\"cmv\" id=\"cmv\"></div>
				
				<div style=\"float:left; margin:4px 0 0 20px;\"><b>Blood group / rhesus:</b></div>
				<div style=\"float:left; margin:4px 0 0 5px;\"><span id=\"id_rhesus\">$rhesus_1 $rhesus_2</span><input type=\"hidden\" value=\"$rhesus_1\" name=\"rhesus_1\" id=\"rhesus_1\"><input type=\"hidden\" value=\"$rhesus_2\" name=\"rhesus_2\" id=\"rhesus_2\"></div>
			</td>
		</tr>
		</table>
		
		
		<div style=\"float:left;\"><h2>DONOR DATA:</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Donor ID:</b></div>
				<div style=\"float:left; margin:0 0 0 5px;\" id=\"id_donor\">";

    if ($_GET['ID'] && $PatientNum):
        echo "<select name=\"DonorID\" id=\"DonorID\" class=\"selectbox_donora\" style=\"width:230px;\">";
        echo "<option value=\"\">- Donor ID# -</option>";

        $vysledek_s = mysql_query("SELECT ID2, DonorNumber FROM patient_donor WHERE PatientNum='" . mysql_real_escape_string($PatientNum) . "' ORDER BY ID2");
        if (mysql_num_rows($vysledek_s) > 0):
            while ($zaz_s = mysql_fetch_array($vysledek_s)):
				$zaz_s = htmlspecialchars_array_encode($zaz_s);
                extract($zaz_s);

                echo "<option value=\"$ID2\"";
                if ($DonorID == $ID2): echo " selected"; endif;
                echo ">$ID2</option>";

            endwhile;
        endif;
        @mysql_free_result($vysledek_s);

        echo "</select>";
    endif;
    echo "</div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Date of birth:</b></div>
				<div style=\"float:left; margin:4px 0 0 5px;\"><div style=\"float:left;\"><span id=\"id_donor_date_birth\">";
    if ($donor_date_birth): echo $donor_date_birth; endif;
    echo "</span><input type=\"hidden\" style=\"width:250px;\" name=\"donor_date_birth\" id=\"donor_date_birth\" value=\"";
    if ($donor_date_birth): echo $donor_date_birth; endif;
    echo "\" class=\"bg1\" readonly></div>";
    echo "<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    //get_calendar('date_birth',$form_name);
    echo "</div>";
    echo "</div>
			</td>
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>CMV:</b></div>
				<div style=\"float:left; width:100px; margin:4px 0 0 5px;\"><span id=\"id_donor_cmv\">$donor_cmv</span><input type=\"hidden\" value=\"$donor_cmv\" name=\"donor_cmv\" id=\"donor_cmv\"></div>
				
				<div style=\"float:left; margin:4px 0 0 20px;\"><b>Blood group / rhesus:</b></div>
				<div style=\"float:left; margin:4px 0 0 5px;\"><span id=\"id_donor_rhesus\">$donor_rhesus</span><input type=\"hidden\" value=\"$donor_rhesus\" name=\"donor_rhesus\" id=\"donor_rhesus\"></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Weight:</b></div>
				<div style=\"float:left; margin:0 0 0 5px;\"><span id=\"id_donor_weight\">$donor_weight</span><input type=\"hidden\" value=\"$donor_weight\" style=\"width:25px;\" name=\"donor_weight\" id=\"donor_weight\"></div>
				<div style=\"float:left; margin:4px 0 0 5px;\">kg</div>
			</td>
			
			<td width=\"50%\" valign=\"top\">
				<div style=\"float:left; margin-top:4px;\"><b>Gender:</b></div>
				<div style=\"float:left; width:84px; margin:4px 0 0 5px;\"><span id=\"id_donor_gender\">$donor_gender</span> <input type=\"hidden\" value=\"$donor_gender\" name=\"donor_gender\" id=\"donor_gender\"></div>
			</td>
		</tr>
		
		
		</table>
		
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>Product request</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"product_request\" id=\"product_request1\" value=\"1\"";
    if ($product_request == 1): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 10px;\"><label for=\"product_request1\">HPC, marrow ONLY</label></div>
			</td>
		</tr>
		<tr>
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"product_request\" id=\"product_request2\" value=\"2\"";
    if ($product_request == 2): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 10px;\"><label for=\"product_request2\">HPC, Apheresis ONLY</label></div>
			</td>
		</tr>
		<tr>
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"product_request\" id=\"product_request3\" value=\"3\"";
    if ($product_request == 3): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 10px;\"><label for=\"product_request3\">T-cells, Apheresis, please specify number of DLI</label></div>
			</td>
		</tr>
		<tr>
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"product_request\" id=\"product_request4\" value=\"4\"";
    if ($product_request == 4): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 10px;\"><label for=\"product_request4\">HPC, Marrow, second option: HPC Apheresis</label></div>
			</td>
		</tr>
		<tr>
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"product_request\" id=\"product_request5\" value=\"5\"";
    if ($product_request == 5): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 10px;\"><label for=\"product_request5\">HPC, Apheresis , second option HPC marrow</label></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left; margin:4px 0 0 0;\">Reason for prod. Preference:</div>
				<div style=\"float:left; margin:0 0 0 10px;\"><input type=\"text\" value=\"$product_reason\" name=\"product_reason\" id=\"product_reason\" style=\"width:700px;\"></div>
			</td>
		</tr>
		</table>
		
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>Donor preference (in case of HPC, Marrow and/or HPC, Apheresis)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"70%\">Are any other donors under consideration for donation of behalf of this patient?</td>
			<td width=\"30%\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"donor_pref_1\" id=\"donor_pref_1_1\" value=\"1\"";
    if ($donor_pref_1 == 1): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"donor_pref_1_1\">Yes</label></div>
				<div style=\"float:left; margin:0 0 0 30px;\"><input type=\"radio\" name=\"donor_pref_1\" id=\"donor_pref_1_2\" value=\"2\"";
    if ($donor_pref_1 == 2): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"donor_pref_1_2\">No</label></div>
			</td>
		</tr>
		<tr>
			<td width=\"70%\">Are any other donors in process of physical examination on behalf of this patient?</td>
			<td width=\"30%\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"donor_pref_2\" id=\"donor_pref_2_1\" value=\"1\"";
    if ($donor_pref_2 == 1): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"donor_pref_2_1\">Yes</label></div>
				<div style=\"float:left; margin:0 0 0 30px;\"><input type=\"radio\" name=\"donor_pref_2\" id=\"donor_pref_2_2\" value=\"2\"";
    if ($donor_pref_2 == 2): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"donor_pref_2_2\">No</label></div>
			</td>
		</tr>
		<tr>
			<td width=\"70%\">If you have answered yes to either of these questions above, is this donor requested for stem cell collection on this form the preferred donor?</td>
			<td width=\"30%\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"donor_pref_3\" id=\"donor_pref_3_1\" value=\"1\"";
    if ($donor_pref_3 == 1): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"donor_pref_3_1\">Yes</label></div>
				<div style=\"float:left; margin:0 0 0 30px;\"><input type=\"radio\" name=\"donor_pref_3\" id=\"donor_pref_3_2\" value=\"2\"";
    if ($donor_pref_3 == 2): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"donor_pref_3_2\">No</label></div>
			</td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0; margin-bottom:0;\" id=\"tb-form\">
		<tr>
			<td width=\"100%\" align=\"center\">
				<div style=\"float:left; margin:4px 0 0 0;\">If no, please explain:</div>
				<div style=\"float:left; margin:0 0 0 10px;\"><input type=\"text\" value=\"$explanation\" name=\"explanation\" id=\"explanation\" style=\"width:750px;\"></div>
			</td>
		</tr>
		</table>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"70%\">Minimum number of days prior to collection that donor clearance must be received:</td>
			<td width=\"30%\">
				<input type=\"text\" value=\"$min_days\" name=\"min_days\" id=\"min_days\" style=\"width:228px;\">
			</td>
		</tr>
		</table>
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>Preparative Regimen</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">

		<tr>
			<td width=\"70%\">The number of days of conditioning regimen:</td>
			<td width=\"30%\"><input type=\"text\" name=\"days_conditioning\" id=\"days_conditioning\" value=\"$days_conditioning\" style=\"width:230px;\"></td>
		</tr>
		<tr>
			<td width=\"70%\">Number of days of chemotherapy the patient will receive prior to infusion:</td>
			<td width=\"30%\"><input type=\"text\" name=\"days_chemo\" id=\"days_chemo\" value=\"$days_chemo\" style=\"width:230px;\"></td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"70%\">Number of days of radiation the patient will receive prior to infusion:</td>
			<td width=\"30%\"><input type=\"text\" name=\"days_radiation\" id=\"days_radiation\" value=\"$days_radiation\" style=\"width:230px;\"></td>
		</tr>
		</table>
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>Transplant history (in case of HPC, Marrow and/or HPC, Apheresis)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"70%\">Had this patient received any previous stem cell transplants?</td>
			<td width=\"30%\">
				<div style=\"float:left;\"><input type=\"radio\" name=\"received_transplans\" id=\"received_transplans1\" value=\"1\"";
    if ($received_transplans == 1): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"received_transplans1\">Yes</label></div>
				<div style=\"float:left; margin:0 0 0 30px;\"><input type=\"radio\" name=\"received_transplans\" id=\"received_transplans2\" value=\"2\"";
    if ($received_transplans == 2): echo " checked"; endif;
    echo "></div><div style=\"float:left; margin:2px 0 0 6px;\"><label for=\"received_transplans2\">No</label></div>
			</td>
		</tr>
		</table>
		
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>Product delivery date (in order of preference)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr>
			<td width=\"50%\">
				<div style=\"float:left; margin:4px 0 0 20px;\">Preferred transplant date 1:</div>
				<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:180px;\" name=\"date_1\" id=\"date_1\" value=\"";
    if (!$date_1): echo $_SESSION['date_format'];
    else: echo date($_SESSION['date_format_php'], $date_1); endif;
    echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    get_calendar('date_1', $form_name);
    echo "</div></div>
			</td>
			<td width=\"50%\">
				<div style=\"float:left; margin:4px 0 0 20px;\">Coresponding collection date 1:</div>
				<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:180px;\"
				name=\"date_4\" id=\"date_4\" value=\"";
    if (!$date_4): echo $_SESSION['date_format'];
    else: echo date
    ($_SESSION['date_format_php'], $date_4); endif;
    echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    get_calendar('date_4', $form_name);
    echo "</div></div>
			</td>
		</tr>
		<tr>
			<td width=\"50%\">
				<div style=\"float:left; margin:4px 0 0 20px;\">Preferred transplant date 2:</div>
				<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:180px;\"
				name=\"date_2\" id=\"date_2\" value=\"";
    if (!$date_2): echo $_SESSION['date_format'];
    else: echo date
    ($_SESSION['date_format_php'], $date_2); endif;
    echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    get_calendar('date_2', $form_name);
    echo "</div></div>
			</td>
			<td width=\"50%\">
				<div style=\"float:left; margin:4px 0 0 20px;\">Coresponding collection date 2:</div>
				<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:180px;\"
				name=\"date_5\" id=\"date_5\"
				value=\"";
    if (!$date_5): echo $_SESSION['date_format'];
    else: echo date
    ($_SESSION['date_format_php'], $date_5); endif;
    echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    get_calendar('date_5', $form_name);
    echo "</div></div>
			</td>
		</tr>
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"50%\">
				<div style=\"float:left; margin:4px 0 0 20px;\">Preferred transplant date 3:</div>
				<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:180px;\"
				name=\"date_3\" id=\"date_3\" value=\"";
    if (!$date_3): echo $_SESSION['date_format'];
    else: echo date
    ($_SESSION['date_format_php'], $date_3); endif;
    echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    get_calendar('date_3', $form_name);
    echo "</div></div>
			</td>
			<td width=\"50%\">
				<div style=\"float:left; margin:4px 0 0 20px;\">Coresponding collection date 3:</div>
				<div style=\"float:left; margin-left:28px;\"><input type=\"text\" style=\"width:180px;\" name=\"date_6\" id=\"date_6\" value=\"";
    if (!$date_6): echo $_SESSION['date_format'];
    else: echo date($_SESSION['date_format_php'], $date_6); endif;
    echo "\"></div>
				<div style=\"float:left; margin-top:2px; margin-left:5px; display:inline;\">";
    get_calendar('date_6', $form_name);
    echo "</div></div>
			</td>
		</tr>
		</table>
		
		<div style=\"float:left; margin-bottom:5px;\"><h2>Additional comments (please include description of patient prep, cell dose for patient, total cell dose, and pre-collection blood sample requests and shipping address, as well as any notes)</h2></div>
		
		<table cellspacing=\"0\" width=\"100%\" style=\"border:0;\" id=\"tb-form\">
		<tr style=\"border-bottom:1px solid #b8b8b8;\">
			<td width=\"100%\"><textarea name=\"comment\" id=\"comment\" style=\"width:890px; height:80px;\">$comment</textarea></td>
		</tr>
		</table>";
   
		if (defined('TEST')) {
  echo  "<div style=\"float:left; margin-bottom:5px;\">
         	<h2>
         	<label for='userPdfFile1'>Select file1 to send via email:</label></h2>
         	<input type=\"file\" name=\"f1\" id=\"userPdfFile1\" title=''/>
         	</div>
         		<div style=\"float:left; margin-bottom:5px; margin-left:10px\">
         	<h2>
         	<label for='userPdfFile2'>Select file2 to send via email:</label></h2>
         	<input type=\"file\" name=\"f2\" id=\"userPdfFile2\" title=''/>
         	</div>";
}




              			echo	"<input type=\"hidden\" style=\"width:380px;\" name=\"InstID\" value=\"";
    if ($InstID): echo "$InstID";
    else: echo $_SESSION['usr_InstID']; endif;
    echo "\">
		<input type=\"hidden\" name=\"RegID\" value=\"";
    if ($RegID): echo "$RegID";
    else: echo $_SESSION['usr_RegID']; endif;
    echo "\" readonly>";


    if ($_SESSION['usr_InstID']):
        if ($ID_stavu == 0 || $ID_stavu == 2):
            echo "<div style=\"float:left; width:95%; margin-top:10px; text-align:center;\"><input type=\"submit\" class=\"form-send_en\" name=\"B1\" value=\"\"></div>";
        endif;


        echo "<input type=\"hidden\" style=\"width:180px;\" name=\"date_completing\" value=\"";
        if ($date_completing):
            //echo date($_SESSION['date_format_php'],$date_completing);
            echo date($_SESSION['date_format_php'], time());    //i pri uprave dosadit datum posledniho odeslani
        else:
            echo date($_SESSION['date_format_php'], time());
        endif;
        echo "\">";

        echo "<div style=\"float:left; width:95%; display:none;\" id=\"ajax_pomocny\"></div>";
        echo "<div style=\"float:left; width:95%; display:none;\" id=\"ajax_pomocny2\"></div>";


		echo "<input type=\"hidden\" name=\"token\" id=\"token\" value=\"".$token."\">";
		
        if ($_GET['ID']):
            echo "<input type=\"hidden\" name=\"action\" value=\"upravit\">";
        else:
            echo "<input type=\"hidden\" name=\"action\" value=\"vlozit\">";
        endif;

        if ($ID_stavu == 0 || $ID_stavu == 2):
            echo "</form>";
        endif;
    endif;


//* =============================================================================
//	JS
//============================================================================= */
     echo "<SCRIPT language=\"JavaScript\" type=\"text/javascript\">
	
	function zmena_pacienta(neprepisovat){
		
		regID=$('#patient_select option:selected').attr('data-reg');
		
		$.ajax({
			url: 'include/ajax-pacient5.php?id_pacienta='+document.getElementById('patient_select').value+'&RegID='+regID,
			async: true,
			complete: function(XMLHTTPRequest, textStatus){
				
				document.getElementById('ajax_pomocny').innerHTML=XMLHTTPRequest.responseText;
				
				if(neprepisovat==1){
				}else{
					document.getElementById('patient_name').value=document.getElementById('aj_jmeno').value;
					document.getElementById('PatientNum').value=document.getElementById('aj_PatientNum').value;
					document.getElementById('PatientID').value=document.getElementById('aj_RegID').value+document.getElementById('aj_PatientNum').value+\"P\";
					document.getElementById('date_birth').value=document.getElementById('aj_date_birth').value;
					$('#id_date_birth').html(document.getElementById('aj_date_birth_text').value);
					document.getElementById('patient_registry').value=document.getElementById('aj_RegID').value;
					document.getElementById('gender').value=document.getElementById('aj_gender').value;
					$('#id_gender').html(document.getElementById('aj_gender_text').value);

					document.getElementById('weight').value=document.getElementById('aj_weight').value;
					$('#id_weight').html(document.getElementById('aj_weight').value);
					document.getElementById('cmv').value=document.getElementById('aj_cmv').value;
					$('#id_cmv').html(document.getElementById('aj_cmv_text').value);
					document.getElementById('rhesus_1').value=document.getElementById('aj_rhesus_1').value;
					document.getElementById('rhesus_2').value=document.getElementById('aj_rhesus_2').value;
					$('#id_rhesus').html(document.getElementById('aj_rhesus_text').value);
					
					
					$.ajax({
						url: 'include/ajax-pacient6.php?id_pacienta='+document.getElementById('PatientNum').value+'&RegID='+regID,
						async: true,
						complete: function(XMLHTTPRequest, textStatus){
						
						document.getElementById('id_donor').innerHTML=XMLHTTPRequest.responseText;
						
						
							";
    if ($_GET['Pn']):
        echo "
								$('#DonorID').val('" . $_GET['donors'] . "');
								zmena_donora();
								";
    endif;
    echo "
						

						}
					});

				}
				
				
				
				
			}
		});
		
	}
	
	
	";
    if ($_GET['Pn']):
        echo "
		$(document).ready(function(){
			zmena_pacienta();
		});
		";
    endif;
    echo "
	
	
</SCRIPT>";

endif;

}else{
	message(1, "You can not access this page.", "", "", 2);
}
?>


    <SCRIPT language="JavaScript" type="text/javascript">

        $(document).ready(function () {

            //po vyberu donora
            $('.selectbox_donora').live('change', function () {

                zmena_donora();

            });

        });


        function zmena_donora() {

            ID_donora = $('#DonorID').val();
            regID = $('#patient_select option:selected').attr('data-reg');

            $.ajax({
                url: 'include/ajax-pacient7.php?id_pacienta=' + document.getElementById('PatientNum').value + '&RegID=' + regID + '&id_darce=' + ID_donora,
                async: true,
                complete: function (XMLHTTPRequest, textStatus) {

                    document.getElementById('ajax_pomocny2').innerHTML = XMLHTTPRequest.responseText;

                    document.getElementById('donor_registry').value = document.getElementById('aj_donor_reg').value;
                    $('#id_donor_registry').html(document.getElementById('aj_donor_reg').value);

                    document.getElementById('donor_date_birth').value = document.getElementById('aj_donor_date_birth').value;
                    $('#id_donor_date_birth').html(document.getElementById('aj_donor_date_birth').value);

                    document.getElementById('donor_cmv').value = document.getElementById('aj_donor_cmv').value;
                    $('#id_donor_cmv').html(document.getElementById('aj_donor_cmv').value);

                    document.getElementById('donor_rhesus').value = document.getElementById('aj_donor_blood').value;
                    $('#id_donor_rhesus').html(document.getElementById('aj_donor_blood').value);

                    document.getElementById('donor_gender').value = document.getElementById('aj_donor_gender').value;
                    $('#id_donor_gender').html(document.getElementById('aj_donor_gender').value);


                }
            });

        }


    </SCRIPT>


<?
include('1-end.php');
?>