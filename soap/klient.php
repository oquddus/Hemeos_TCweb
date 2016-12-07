<?php

// turn off the WSDL cache
ini_set("soap.wsdl_cache_enabled", "0");

if($_SERVER['HTTP_HOST']=="127.0.0.1"):
	$client = new SoapClient("http://127.0.0.1/swe/soap/server/server.php?wsdl", array('login'=>"aaa", 'password'=>"bbb"));
else:
	//$client = new SoapClient("http://tr.dpa.nu/soap/server/server.php?wsdl");
	$client = new SoapClient("http://tc.hemeos.com/soap/server/server.php?wsdl");
endif;
	



//print_r($client->__getFunctions());



$headers[] = new SoapHeader('http://127.0.0.1', 'user', 'aaa');
$headers[] = new SoapHeader('http://127.0.0.1', 'password', 'bbb');
$client->__setSoapHeaders($headers);



//* =============================================================================
//	Test
//============================================================================= */
/*
$vysledek = $client->GetTest();
echo $vysledek;
*/

//* =============================================================================
//	Nacte preliminary requesty
//============================================================================= */
//$vysledek = $client->GetPreliminaryRequests('CZ');
//echo $vysledek;

//* =============================================================================
//	Potvrzeni requestu
//============================================================================= */
/*
$StatusXml="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<Patient>
<form>
  <status>2</status> 
  </form>
  </Patient>"; 
*/
/*
$vysledek = $client->ConfirmPreliminaryReq(17,171717,2);
echo $vysledek;
*/

//* =============================================================================
//	Zamitnuti requestu
//============================================================================= */
/*
$vysledek = $client->DeniedPreliminaryRequestBy(8,'Å¡patnÄ› vyplnÄ›nÃ© Ãºdaje');
echo $vysledek;
*/

//* =============================================================================
//	Vypis zamitnutych requestu
//============================================================================= */
//$vysledek=$client->GetDeniedRequests('CZ1');
//echo $vysledek;

//* =============================================================================
//	Zamitnute requesty do preliminary
//============================================================================= */
/*
$vysledek=$client->SetPreliminaryReq('8');
echo $vysledek;
*/

//* =============================================================================
//	Seznam stopped patient
//============================================================================= */
//$vysledek=$client->GetStoppedPatients('CZ1');
//echo $vysledek;

//* =============================================================================
//	Seznam suspended patient
//============================================================================= */
//$vysledek=$client->GetSuspendedPatients('CZ');
//echo $vysledek;

//* =============================================================================
//	Potvrdit stopped
//============================================================================= */
/*
$vysledek=$client->SetStoppedPatients('CZ1',15935488);
echo $vysledek;
*/

//* =============================================================================
//	Potvrdit active
//============================================================================= */
/*
$StatusXml="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<Patient>
<form>
  <status>2</status> 
  </form>
  </Patient>"; 
  
$StatusXml="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<patient>
	<form>
		<status>2</status>
	</form>
</patient>";
*/
//$vysledek=$client->SetActivePatients('CZ',1000078,$StatusXml);
//$vysledek=$client->SetActivePatients('CZ1',171717,1);
//$vysledek=$client->SetActivePatients('CZ',1000078,2);
  
//echo $vysledek;


//* =============================================================================
//	Potvrdit suspended
//============================================================================= */
//$vysledek=$client->SetSuspendedPatients('CZ1',88888);
//echo $vysledek;


//* =============================================================================
//	Poslat darce pro pacienta
//============================================================================= */
/*
$aDonorData="<?xml version=\"1.0\" encoding=\"UTF-8\"?>
			<Donors_Data>
				<Donor>
					<DonorUpdate>New</DonorUpdate>
					<RecordUpdate>07.8.2010</RecordUpdate>
					<Hub>TCCZ1</Hub>
					<ID>KKOOE2-111111</ID>
					<Status>AV99</Status>
					<Type>MD</Type>
					<Sex>MF</Sex>
					<BirthDate>1974</BirthDate>
					<MatchGrade>5/6 Antigen Match</MatchGrade>
					<MatchGradeInt>9346</MatchGradeInt>
					<PhenotypeQuality>13</PhenotypeQuality>
					<DonorNumber>766655</DonorNumber>
					<MatchGradeInternal>3/4</MatchGradeInternal>
					<A.1>01:KTBN</A.1>
				<A.1Style>ABDRAlleleMismatch</A.1Style>
					<A.2>03:KAKX</A.2>
				<A.2Style>ABDRAlleleMismatch</A.2Style>
					<B.1>44:MDBH</B.1>
				<B.1Style>ABDRSplitBroadMismatch</B.1Style>
					<B.2>35:MFCB</B.2>
				<B.2Style>ABDRAlleleMismatch</B.2Style>
					<C.1>05:AFW</C.1>
				<C.1Style>ABDRSplitBroadMismatch</C.1Style>
					<C.2>07:KRN</C.2>
				<C.2Style>ABDRSplitBroadMismatch</C.2Style>
					<DRB1.1>01:01</DRB1.1>
				<DRB1.1Style>ABDRSplitBroadMismatch</DRB1.1Style>
					<DRB1.2>15:01</DRB1.2>
				<DRB1.2Style>ABDRAlleleMismatch</DRB1.2Style>
					<DQB1.1>555</DQB1.1>
				<DQB1.1Style>ABDRSplitBroadMismatch</DQB1.1Style>
					<DQB1.2>666</DQB1.2>
				<DQB1.2Style>ABDRAlleleMismatch</DQB1.2Style>
					<Ethnic>UK</Ethnic>
					<ABO>O+</ABO>
					<CMV>Both IgG and IgM negative</CMV>
					<CMVDate>07.09.2011</CMVDate>
				<ProbMM0>30.12.1899</ProbMM0>
				<ProbMM1>30.12.1899</ProbMM1>
				<PROBA>100</PROBA>
				<PROBB>50.5</PROBB>
				<PROBC>67.78</PROBC>
				<PROBDR>50,14</PROBDR>
				<PROBDQ>22</PROBDQ>
				<MMCount>2.1.1900</MMCount>
				<MMAntCount>1.1.1900</MMAntCount>
				</Donor>
			</Donors_Data>";
			
	$RegID="CZ1";
	$PatientNum=88888;
$vysledek=$client->SendDonorForPatient($RegID, $PatientNum, $aDonorData);
echo $vysledek;
*/

//* =============================================================================
//	Prijmout typing requesty
//============================================================================= */
/*
$vysledek=$client->GetTypingRequests('CZ1');
echo $vysledek;
*/

//* =============================================================================
//	Potvrdit typing request
//============================================================================= */
//$vysledek=$client->ConfirmTypingReq('CZ1', 6, 766655, '00006');
//echo $vysledek;

//* =============================================================================
//	Zamitnout typing request
//============================================================================= */
//$vysledek=$client->DenyTypingReq('CZ1', 6, 766655, 'nebereme');
//echo $vysledek;

//* =============================================================================
//	Poslat vysledek typingu
//============================================================================= */
/*
$aResultData="<?xml version=\"1.0\" encoding=\"utf-8\"?>
<result>
  <form>
    <P_ID>CZ188888P</P_ID>
    <D_ID>KKOOE2-111111</D_ID>
    <REQ_DATE>5.6.2015</REQ_DATE>
    <REF_CODE>181940CZ1</REF_CODE>
    <RESOLUT>L----------</RESOLUT>
    <D_BIRTH_DATE>2006-05-06</D_BIRTH_DATE>
    <D_SEX>M</D_SEX>
    <D_ABO>A-</D_ABO>
    <D_CMV>?</D_CMV>
    <D_CMV_DATE>2011-05-10</D_CMV_DATE>
    <D_A1>?</D_A1>
    <D_A2>?</D_A2>
    <D_B1>?</D_B1>
    <D_B2>?</D_B2>
    <D_C1>?</D_C1>
    <D_C2>?</D_C2>
    <D_DNA_A1>?</D_DNA_A1>
    <D_DNA_A2>?</D_DNA_A2>
    <D_DNA_B1>?</D_DNA_B1>
    <D_DNA_B2>?</D_DNA_B2>
    <D_DNA_C1>?</D_DNA_C1>
    <D_DNA_C2>?</D_DNA_C2>
    <D_DR1>?</D_DR1>
    <D_DR2>?</D_DR2>
    <D_DQ1>?</D_DQ1>
    <D_DQ2>?</D_DQ2>
    <D_DRB11>?</D_DRB11>
    <D_DRB12>?</D_DRB12>
    <D_DRB31>?</D_DRB31>
    <D_DRB32>?</D_DRB32>
    <D_DRB41>?</D_DRB41>
    <D_DRB42>?</D_DRB42>
    <D_DRB51>?</D_DRB51>
    <D_DRB52>?</D_DRB52>
    <D_DQA11>?</D_DQA11>
    <D_DQA12>?</D_DQA12>
    <D_DQB11>?</D_DQB11>
    <D_DQB12>?</D_DQB12>
    <D_DPA11>?</D_DPA11>
    <D_DPA12>?</D_DPA12>
    <D_DPB11>?</D_DPB11>
    <D_DPB12>?</D_DPB12>
    <REMARK>?</REMARK>
    <HLA_NOM_VER>3</HLA_NOM_VER>
    <DISCREP_ORIG>?</DISCREP_ORIG>
    <CONCLUSION>?</CONCLUSION>
    <REL_REASON>?</REL_REASON>
    <TX_DATE>12.1.2014</TX_DATE>
    <CB_SAMPLE_TYPE>?</CB_SAMPLE_TYPE>
    <HUB_SND>CZ</HUB_SND>
    <HUB_RCV>CZ</HUB_RCV>
  </form>
</result>";
			
$vysledek=$client->SendResultOfTyping('CZ1', 6, $aResultData);
echo $vysledek;
*/




//* =============================================================================
//	Prijmout sample requesty
//============================================================================= */
//$vysledek=$client->GetSampleRequests('CZ1');
//echo $vysledek;


//* =============================================================================
//	Potvrdit sample request
//============================================================================= */
//$vysledek=$client->ConfirmSampleReq('CZ1', 8, '766655', 8);
//echo $vysledek;

//* =============================================================================
//	Zamitnout sample request
//============================================================================= */
//$vysledek=$client->DenySampleReq('CZ1', 8, 766655, 'nepÅ™ijato');
//echo $vysledek;


//* =============================================================================
//	Zamitnout odeslany sample request
//============================================================================= */
//$vysledek=$client->DenySendedSampleReq('CZ1', 8, 'oprava, zamÃ­tnuto');
//echo $vysledek;


/*
$vysledek=$client->GetSuspendedPatients('CZ1');
echo $vysledek."<br>";
*/
/*
$vysledek=$client->GetStoppedPatients('CZ1');
echo $vysledek."<br>";
*/
/*
$vysledek=$client->GetActivePatients('CZ1');
echo $vysledek."<br>";
*/

/*
$vysledek = $client->SetStoppedPatients('CZ1','999987478');
if($vysledek): echo "ok"; else: echo "ne"; endif;
$vysledek = $client->SetSuspendedPatients('CZ1','578744787');
if($vysledek): echo "ok"; else: echo "ne"; endif;
$vysledek = $client->SetActivePatients('CZ1','123456');
if($vysledek): echo "ok"; else: echo "ne"; endif;
*/



//$vysledek=$client->GetTypingRequests('CZ1');
//echo $vysledek;


//* =============================================================================
//	Zamitnuti jiz poslaneho typing req.
//============================================================================= */
//$vysledek=$client->DenySendedTypingReq('CZ1', 6, 'NehodÃ­ se');
//echo $vysledek;



//* =============================================================================
//	Nacte workup requesty
//============================================================================= */
//$vysledek = $client->GetWorkupRequests('CZ1');
//echo $vysledek;

//* =============================================================================
//	Potvrdi workup request
//============================================================================= */
//$vysledek = $client->ConfirmWorkupReq(4,44);
//echo $vysledek;

//* =============================================================================
//	Zamitne workup request
//============================================================================= */
//$vysledek = $client->DenyWorkupRequest(4,'nepÅ™Ã­sluÅ¡Ã­ sem');
//echo $vysledek;

//* =============================================================================
//	Poslat noveho pacienta
//============================================================================= */
/*
$pacient="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<Patient>
<form>
  <date_request>2014-08-21</date_request> 
  <search_type>1</search_type> 
  <search_urgent>1</search_urgent> 
  <mismatches>1</mismatches> 
  <last_name>Fafejta</last_name> 
  <first_name>Marek</first_name> 
  <date_birth>1981-02-04</date_birth> 
  <gender>2</gender> 
  <weight>95</weight> 
  <cvm_status>2</cvm_status> 
  <diagnosis>1</diagnosis> 
  <date_diagnosis>2014-08-01</date_diagnosis> 
  <diagnosis_text>kdovÃ­ jak to bude</diagnosis_text> 
  <race>rasa</race> 
  <ethnic>2</ethnic> 
  <ci_fa_a>a1</ci_fa_a> 
  <ci_fa_b>b1</ci_fa_b> 
  <ci_fa_c>c1</ci_fa_c> 
  <ci_sa_a>a2</ci_sa_a> 
  <ci_sa_b>b2</ci_sa_b> 
  <ci_sa_c>c2</ci_sa_c> 
  <ci_test_a>2</ci_test_a> 
  <ci_test_b>2</ci_test_b> 
  <ci_test_c>2</ci_test_c> 
  <cii_fa_a>drb1_1</cii_fa_a> 
  <cii_fa_b>drb4_1</cii_fa_b> 
  <cii_fa_c>dqb1_1</cii_fa_c> 
  <cii_fa_d>dpb1_1</cii_fa_d> 
  <cii_sa_a>drb1_2</cii_sa_a> 
  <cii_sa_b>drb4_2</cii_sa_b> 
  <cii_sa_c>dqb1_2</cii_sa_c> 
  <cii_sa_d>dpb1_2</cii_sa_d> 
  <ci_search_prognosis>1</ci_search_prognosis> 
  <cii_search_prognosis>1</cii_search_prognosis> 
  <physician>good</physician> 
  <temp_transpl_date>2014-08-06</temp_transpl_date> 
  <bsn_number>123</bsn_number> 
  <disease_phase>C1 = 1st Complete Remission</disease_phase> 
  <cmv_date>2014-08-04</cmv_date> 
  <rhesus_1>B</rhesus_1> 
  <rhesus_2>+</rhesus_2> 
  <cond_scheme>condition</cond_scheme> 
  <preferences>PBSC</preferences> 
  <mm_hla_a>1</mm_hla_a> 
  <mm_hla_b>1</mm_hla_b> 
  <mm_hla_c>1</mm_hla_c> 
  <mm_hla_dr>1</mm_hla_dr> 
  <mm_hla_dq>1</mm_hla_dq> 
  <inform_collection>1</inform_collection> 
  <inform_storage>1</inform_storage> 
  <selection_criteria_1>Gender</selection_criteria_1> 
  <selection_criteria_2>Abo</selection_criteria_2> 
  <selection_criteria_3>Age</selection_criteria_3> 
  <selection_criteria_4>CMV</selection_criteria_4> 
  <InstID>TCCZ1</InstID> 
  <RegID>CZ1</RegID> 
  <PatientNum>112233</PatientNum> 
  </form>
  </Patient>";
  */
  
/*
$pacient="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<Patient>
<form>
  <date_request>2014-09-07</date_request> 
  <patient_id>moje IDÄ�ko 4443</patient_id>
  <search_type>1</search_type> 
  <search_urgent>1</search_urgent> 
  <mismatches>1</mismatches> 
  <last_name>Test</last_name> 
  <first_name>VloÅ¾enÃ­</first_name> 
  <date_birth>2014-08-04</date_birth> 
  <gender>1</gender> 
  <weight>50</weight> 
  <cvm_status>2</cvm_status> 
  <diagnosis>1</diagnosis> 
  <date_diagnosis>2014-08-05</date_diagnosis> 
  <diagnosis_text>aaaaaa</diagnosis_text> 
  <study_protocol>SP00077</study_protocol> 
  <race>bbbbbb</race> 
  <ethnic>1</ethnic> 
  <ci_fa_a>a1</ci_fa_a> 
  <ci_fa_b /> 
  <ci_fa_c /> 
  <ci_sa_a /> 
  <ci_sa_b>b2</ci_sa_b> 
  <ci_sa_c /> 
  <ci_test_a>2</ci_test_a> 
  <ci_test_b>2</ci_test_b> 
  <ci_test_c>2</ci_test_c> 
  <cii_fa_a /> 
  <cii_fa_b>b1</cii_fa_b>
  <cii_fa_c /> 
  <cii_fa_d /> 
  <cii_sa_a>drb1_2</cii_sa_a> 
  <cii_sa_b>f1</cii_sa_b>
  <cii_sa_c /> 
  <cii_sa_d /> 
  <search_prognosis>1</search_prognosis> 
  <physician>cccccccc</physician> 
  <temp_transpl_date>2014-08-06</temp_transpl_date> 
  <bsn_number>23122</bsn_number> 
  <disease_phase>PI = Primary Induction Therapy</disease_phase> 
  <cmv_date>2014-08-05</cmv_date> 
  <rhesus_1>B</rhesus_1> 
  <rhesus_2>+</rhesus_2> 
  <cond_scheme>ddd</cond_scheme> 
  <preferences>PBSC</preferences> 
  <drb345_select>DRB 4</drb345_select>
  <drb345_select2>DRB 5</drb345_select2>
  <mm_hla_a>1</mm_hla_a> 
  <mm_hla_b>1</mm_hla_b> 
  <mm_hla_c>1</mm_hla_c> 
  <mm_hla_dr>0</mm_hla_dr> 
  <mm_hla_dq>0</mm_hla_dq> 
  <inform_collection>1</inform_collection> 
  <inform_storage>1</inform_storage> 
  <selection_criteria_1>Gender</selection_criteria_1> 
  <selection_criteria_2>CMV</selection_criteria_2> 
  <selection_criteria_3>Abo</selection_criteria_3> 
  <selection_criteria_4>Age</selection_criteria_4> 
  <InstID>TCCZ1</InstID> 
  <RegID>CZ</RegID> 
  <PatientNum>54747</PatientNum>
  </form>
  </Patient>";  

$vysledek=$client->SendNewPatient($pacient);
echo $vysledek;
*/


//* =============================================================================
//	Poslat typing request
//============================================================================= */
/*
$typing="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<TypingRequest>
 <form>
  <ia_hospital /> 
  <ia_contact_name /> 
  <ia_address /> 
  <ia_phone /> 
  <ia_fax /> 
  <ia_email /> 
  <person_completing /> 
  <signature /> 
  <date_completing>2014-08-21</date_completing> 
  <InstID>TCCZ1</InstID> 
  <RegID>CZ1</RegID> 
 <patient>
  <PatientNum>112233</PatientNum> 
  <PatientID>CZ1112233P</PatientID> 
  <patient_name>Fafejta Marek</patient_name> 
  <patient_registry>CZ1</patient_registry> 
  <patient_id_dn /> 
  <diagnosis>Acute Myelogenous Leukaemia</diagnosis> 
  <date_birth>1981-02-04</date_birth> 
 <patient_hla>
  <method_used>aaaaa</method_used>
  <a_1>a1</a_1> 
  <b_1>b1</b_1> 
  <c_1>c1</c_1> 
  <drb1_1>drb1_1</drb1_1> 
  <drb3_1>drb4_1</drb3_1> 
  <drb4_1 /> 
  <drb5_1 /> 
  <dqb1_1>dqb1_1</dqb1_1> 
  <dpb1_1>dpb1_1</dpb1_1> 
  <dqa1_1 /> 
  <dpa1_1 /> 
  <a_2>a2</a_2> 
  <b_2>b2</b_2> 
  <c_2>c2</c_2> 
  <drb1_2>drb1_2</drb1_2> 
  <drb3_2>drb4_2</drb3_2> 
  <drb4_2 /> 
  <drb5_2 /> 
  <dqb1_2>dqb1_2</dqb1_2> 
  <dpb1_2>dpb1_2</dpb1_2> 
  <dqa1_2 /> 
  <dpa1_2 /> 
  </patient_hla>
  </patient>
 <donor>
  <DonorNum>766654</DonorNum> 
  <DonorID>KKOOE3-60114048</DonorID> 
  <Resolution>LLLMMMHHHML</Resolution>
  <aLogMsgNum>111</aLogMsgNum>
  </donor>
  </form>
  <form>
  <ia_hospital /> 
  <ia_contact_name /> 
  <ia_address /> 
  <ia_phone /> 
  <ia_fax /> 
  <ia_email /> 
  <person_completing /> 
  <signature /> 
  <date_completing>2014-08-21</date_completing> 
  <InstID>TCCZ1</InstID> 
  <RegID>CZ1</RegID> 
 <patient>
  <PatientNum>112233</PatientNum> 
  <PatientID>CZ1112233P</PatientID> 
  <patient_name>Fafejta Marek</patient_name> 
  <patient_registry>CZ1</patient_registry> 
  <patient_id_dn /> 
  <diagnosis>Acute Myelogenous Leukaemia</diagnosis> 
  <date_birth>1981-02-04</date_birth> 
 <patient_hla>
  <method_used>aaaaa</method_used>
  <a_1>a1</a_1> 
  <b_1>b1</b_1> 
  <c_1>c1</c_1> 
  <drb1_1>drb1_1</drb1_1> 
  <drb3_1>drb4_1</drb3_1> 
  <drb4_1 /> 
  <drb5_1 /> 
  <dqb1_1>dqb1_1</dqb1_1> 
  <dpb1_1>dpb1_1</dpb1_1> 
  <dqa1_1 /> 
  <dpa1_1 /> 
  <a_2>a2</a_2> 
  <b_2>b2</b_2> 
  <c_2>c2</c_2> 
  <drb1_2>drb1_2</drb1_2> 
  <drb3_2>drb4_2</drb3_2> 
  <drb4_2 /> 
  <drb5_2 /> 
  <dqb1_2>dqb1_2</dqb1_2> 
  <dpb1_2>dpb1_2</dpb1_2> 
  <dqa1_2 /> 
  <dpa1_2 /> 
  </patient_hla>
  </patient>
 <donor>
  <DonorNum>666654</DonorNum> 
  <DonorID>KKOOE2-60114048</DonorID> 
  <Resolution>LMHLMHLMHLM</Resolution> 
  <aLogMsgNum>222</aLogMsgNum>
  </donor>
  </form>
</TypingRequest>";

$vysledek=$client->SendNewTypingRequest($typing);
echo $vysledek;
*/





//* =============================================================================
//	Poslat sample request
//============================================================================= */
/*
$sample="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<SampleRequest>
<form>
<transplant_center>TCCZ1</transplant_center>
<preferred_courier>service</preferred_courier>
<sample_to_institution>a</sample_to_institution>
<invoice_to_institution>b</invoice_to_institution>
<sample_address>c</sample_address>
<invoice_address>d</invoice_address>
<sample_attention>e</sample_attention>
<invoice_attention>f</invoice_attention>
<sample_phone>g</sample_phone>
<invoice_phone>h</invoice_phone>
<sample_fax>i</sample_fax>
<invoice_fax>j</invoice_fax>
<sample_email>k</sample_email>
<invoice_email>l</invoice_email>
<date_completing>2014-08-23</date_completing>
<mls_edta>1</mls_edta>
<mls_heparin>3</mls_heparin>
<mls_acd>5</mls_acd>
<mls_clotted>7</mls_clotted>
<mls_dna>9</mls_dna>
<mls_cpa>11</mls_cpa>
<tubes_edta>2</tubes_edta>
<tubes_heparin>4</tubes_heparin>
<tubes_acd>6</tubes_acd>
<tubes_clotted>8</tubes_clotted>
<tubes_dna>10</tubes_dna>
<tubes_cpa>12</tubes_cpa>
<monday>1</monday>
<tuesday>1</tuesday>
<wednesday>1</wednesday>
<thursday>1</thursday>
<friday>1</friday>
<saturday>1</saturday>
<sunday>1</sunday>
<InstID>TCCZ1</InstID>
<RegID>CZ1</RegID>
<patient>
	<PatientNum>112233</PatientNum>                     
	<PatientID>CZ1112233P</PatientID>                    
	<patient_name>Fafejta Marek</patient_name>    
	<patient_id_dn>159159</patient_id_dn>
	<date_birth>1981-02-04</date_birth>
	<gender>2</gender>
	<patient_hla>
		<a_1>a1</a_1>
		<b_1>b1</b_1>
		<c_1>c1</c_1>
		<drb1_1>drb1_1</drb1_1>
		<dqb1_1>dqb1_1</dqb1_1>
		<a_2>a2</a_2>
		<b_2>b2</b_2>
		<c_2>c2</c_2>
		<drb1_2>drb1_2</drb1_2>
		<dqb1_2>dqb1_2</dqb1_2>
		<drb3_1>1</drb3_1>
		<drb4_1>h</drb4_1>
		<drb5_1>3</drb5_1>
		<dpb1_1>j</dpb1_1>
		<dqa1_1>5</dqa1_1>
		<dpa1_1>7</dpa1_1>
		<drb3_2>2</drb3_2>
		<drb4_2>l06</drb4_2>
		<drb5_2>4</drb5_2>
		<dpb1_2>n</dpb1_2>
		<dqa1_2>606</dqa1_2>
		<dpa1_2>8</dpa1_2>
	</patient_hla>
</patient>
<donor>
	<DonorNum>766654</DonorNum>
	<DonorID>KKOOE3-60114048</DonorID>
	<aLogMsgNum>742</aLogMsgNum>
</donor>
</form>
</SampleRequest>";

$vysledek=$client->SendNewSampleRequest($sample);
echo $vysledek;
*/


//* =============================================================================
//	Poslat workup request
//============================================================================= */
/*
$workup="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<WorkupRequest>
<form>
  <aLogMsgNum>1155</aLogMsgNum>
  <patient_name>Fafejta Marek</patient_name>
  <PatientNum>112233</PatientNum> 
  <PatientID>CZ1112233P</PatientID> 
  <patient_registry>CZ1</patient_registry> 
  <patient_id_dn>159159</patient_id_dn> 
  <transplant_center>TCCZ1</transplant_center> 
  <gender>2</gender> 
  <weight>95</weight> 
  <date_birth>1981-02-04</date_birth> 
  <cmv>2</cmv> 
  <rhesus_1>B</rhesus_1> 
  <rhesus_2>+</rhesus_2> 
  <DonorID>KKOOE2-60114048</DonorID> 
  <donor_registry>DE</donor_registry> 
  <donor_date_birth>1970</donor_date_birth> 
  <donor_cmv>Both IgG and IgM negative</donor_cmv> 
  <donor_rhesus>O+</donor_rhesus> 
  <donor_weight /> 
  <donor_gender>MF</donor_gender> 
  <product_request>5</product_request> 
  <product_reason>mÅ¯j reason4</product_reason> 
  <donor_pref_1>2</donor_pref_1> 
  <donor_pref_2>1</donor_pref_2> 
  <donor_pref_3>2</donor_pref_3> 
  <explanation>moje explain2</explanation> 
  <min_days>62</min_days>
  <add_hpc_marrow>1</add_hpc_marrow> 
  <add_hpc_apheresis>1</add_hpc_apheresis> 
  <tcell_apheresis>1</tcell_apheresis> 
  <tcell_apheresis_no>101</tcell_apheresis_no> 
  <other>1</other> 
  <other_text>201</other_text> 
  <days_conditioning>301</days_conditioning> 
  <days_chemo>401</days_chemo> 
  <days_radiation>501</days_radiation> 
  <previous_transplant>601</previous_transplant> 
  <donor_requested>1</donor_requested> 
  <cryo>1</cryo> 
  <product_infused>1</product_infused> 
  <received_transplans>1</received_transplans> 
  <date_1>2014-08-04</date_1> 
  <date_2>2014-08-07</date_2> 
  <date_3>2014-08-05</date_3> 
  <date_4>2014-08-08</date_4> 
  <date_5>2014-08-06</date_5> 
  <date_6>2014-08-09</date_6> 
  <comment>mÅ¯j koment2</comment> 
  <InstID>TCCZ1</InstID> 
  <RegID>CZ1</RegID> 
  <date_completing>2015-05-23</date_completing> 
</form>
</WorkupRequest>";
$vysledek=$client->SendNewWorkupRequest($workup);
echo $vysledek;
*/



//* =============================================================================
//	Upravit pacienta
//============================================================================= */
/*
$pacient="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<Patient>
<form>
  <ID>4</ID>
  <date_request>2014-12-03</date_request>
  <patient_id>moje ID2</patient_id>
  <search_type>2</search_type>
  <status>4</status>
  <search_urgent>2</search_urgent>
  <mismatches>1</mismatches>
  <last_name>TestoviÄ�x</last_name>
  <first_name>Mirko</first_name>
  <date_birth>2014-09-11</date_birth>
  <gender>1</gender>
  <weight>80</weight>
  <cvm_status>2</cvm_status>
  <diagnosis>2</diagnosis>
  <date_diagnosis>2014-12-11</date_diagnosis>
  <diagnosis_text/><race/>
  <ethnic>0</ethnic>
  <ci_fa_a>ax</ci_fa_a><ci_fa_b>b</ci_fa_b><ci_fa_c>c</ci_fa_c><ci_sa_a>aa</ci_sa_a><ci_sa_b>bb</ci_sa_b><ci_sa_c>cc</ci_sa_c><ci_test_a>2</ci_test_a><ci_test_b>2</ci_test_b><ci_test_c>2</ci_test_c>
  <cii_fa_a>dr</cii_fa_a><cii_fa_b>dr</cii_fa_b><cii_fa_c>adf</cii_fa_c><cii_fa_d>fe</cii_fa_d><cii_sa_a/><cii_sa_b/><cii_sa_c/><cii_sa_d/>
  <search_prognosis>1</search_prognosis>
  <physician/><temp_transpl_date/><bsn_number/><disease_phase>8</disease_phase>
  <cmv_date>2014-12-09</cmv_date><rhesus_1/><rhesus_2/>
  <cond_scheme/><preferences/><mm_hla_a>1</mm_hla_a><mm_hla_b>1</mm_hla_b><mm_hla_c>1</mm_hla_c><mm_hla_dr>0</mm_hla_dr><mm_hla_dq>1</mm_hla_dq><inform_collection>0</inform_collection>
  <inform_storage>0</inform_storage><selection_criteria_1/><selection_criteria_2/><selection_criteria_3/><selection_criteria_4/>
  <RegID>CZ1</RegID>
  </form>
  </Patient>";  
*/
/*
$pacient="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<Patient>
<form>
  <ID>24</ID><date_request>2015-10-29</date_request><patient_id>646546</patient_id><search_type>3</search_type><search_urgent>1</search_urgent><mismatches>1</mismatches><last_name>Test</last_name><first_name>Commentu</first_name><date_birth>2015-10-01</date_birth><gender>1</gender><weight>55</weight><cvm_status>1</cvm_status><diagnosis>1</diagnosis><date_diagnosis>2015-10-08</date_diagnosis><diagnosis_text></diagnosis_text><study_protocol></study_protocol><race></race><ethnic>0</ethnic><ci_fa_a>aasdfasdf</ci_fa_a><ci_fa_b>basdfasdf</ci_fa_b><ci_fa_c>cadfasd</ci_fa_c><ci_sa_a>aasdfasdf</ci_sa_a><ci_sa_b>badsfasdf</ci_sa_b><ci_sa_c>casdfasdfa</ci_sa_c><ci_test_a>2</ci_test_a><ci_test_b>2</ci_test_b><ci_test_c>2</ci_test_c><drb345_select>DRB 3</drb345_select><drb345_select2>DRB 3</drb345_select2><cii_fa_a>aasdfasdf</cii_fa_a><cii_fa_b>dasdfasdf</cii_fa_b><cii_fa_c>dasdfasdf</cii_fa_c><cii_fa_d>feasdfasdfd</cii_fa_d><cii_sa_a></cii_sa_a><cii_sa_b></cii_sa_b><cii_sa_c>eadsfadf</cii_sa_c><cii_sa_d></cii_sa_d><search_prognosis>1</search_prognosis><physician></physician><temp_transpl_date></temp_transpl_date><bsn_number></bsn_number><disease_phase>1</disease_phase><cmv_date>2015-10-08</cmv_date><rhesus_1></rhesus_1><rhesus_2></rhesus_2><cond_scheme></cond_scheme><preferences></preferences><mm_hla_a>1</mm_hla_a><mm_hla_b>0</mm_hla_b><mm_hla_c>1</mm_hla_c><mm_hla_dr>1</mm_hla_dr><mm_hla_dq>1</mm_hla_dq><inform_collection>1</inform_collection><inform_storage>1</inform_storage><selection_criteria_1></selection_criteria_1><selection_criteria_2></selection_criteria_2><selection_criteria_3></selection_criteria_3><selection_criteria_4></selection_criteria_4><InstID>TCCZ1</InstID><RegID>CZ1</RegID><status>0</status><comment></comment>
  <RegID>CZ1</RegID>
   <PatientNum></PatientNum>
  </form>
  </Patient>"; 
*/  

/*
$pacient="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<request>
<form><ID>412</ID><date_request>2015-11-11</date_request><patient_id>666</patient_id><search_type>2</search_type><search_urgent>1</search_urgent><mismatches>1</mismatches><last_name>Test</last_name><first_name>NULL3</first_name><date_birth>2015-11-02</date_birth><gender>1</gender><weight>0</weight><cvm_status>0</cvm_status><diagnosis>1</diagnosis><date_diagnosis>2015-11-12</date_diagnosis><diagnosis_text></diagnosis_text><study_protocol></study_protocol><race></race><ethnic>0</ethnic><ci_fa_a></ci_fa_a><ci_fa_b></ci_fa_b><ci_fa_c></ci_fa_c><ci_sa_a></ci_sa_a><ci_sa_b></ci_sa_b><ci_sa_c></ci_sa_c><ci_test_a>2</ci_test_a><ci_test_b>2</ci_test_b><ci_test_c>2</ci_test_c><drb345_select>DRB 3</drb345_select><drb345_select2>DRB 3</drb345_select2><cii_fa_a></cii_fa_a><cii_fa_b></cii_fa_b><cii_fa_c></cii_fa_c><cii_fa_d></cii_fa_d><cii_sa_a></cii_sa_a><cii_sa_b></cii_sa_b><cii_sa_c></cii_sa_c><cii_sa_d></cii_sa_d><search_prognosis>0</search_prognosis><physician></physician><temp_transpl_date></temp_transpl_date><bsn_number></bsn_number><disease_phase>0</disease_phase><cmv_date></cmv_date><rhesus_1></rhesus_1><rhesus_2></rhesus_2><cond_scheme></cond_scheme><preferences></preferences><mm_hla_a>1</mm_hla_a><mm_hla_b>1</mm_hla_b><mm_hla_c>1</mm_hla_c><mm_hla_dr>1</mm_hla_dr><mm_hla_dq>1</mm_hla_dq><inform_collection>0</inform_collection><inform_storage>0</inform_storage><selection_criteria_1></selection_criteria_1><selection_criteria_2></selection_criteria_2><selection_criteria_3></selection_criteria_3><selection_criteria_4></selection_criteria_4>
<InstID>CZMOT</InstID><RegID>CZ</RegID>
<PatientNum>88888888</PatientNum>
<status>0</status>
<comment>bbb</comment><datum_zmeny_stavu>0000-00-00 00:00:00</datum_zmeny_stavu>
</form>
</request>";

$vysledek=$client->UpdatePatient($pacient);
echo $vysledek;
*/


//* =============================================================================
//	Upravit sample request
//============================================================================= */
/*
$sample="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<SampleRequest><form>
<ID>10</ID>
<preferred_courier>servicex</preferred_courier>
<sample_to_institution>ax</sample_to_institution>
<invoice_to_institution>bx</invoice_to_institution>
<sample_address>cx</sample_address>
<invoice_address>dx</invoice_address>
<sample_attention>e</sample_attention>
<invoice_attention>f</invoice_attention>
<sample_phone>g</sample_phone>
<invoice_phone>h</invoice_phone>
<sample_fax>i</sample_fax>
<invoice_fax>j</invoice_fax>
<sample_email>k</sample_email>
<invoice_email>l</invoice_email>
<date_completing>2014-08-23</date_completing>
<mls_edta>1</mls_edta>
<mls_heparin>3</mls_heparin>
<mls_acd>5</mls_acd>
<mls_clotted>7</mls_clotted>
<mls_dna>9</mls_dna>
<mls_cpa>11</mls_cpa>
<tubes_edta>2</tubes_edta>
<tubes_heparin>4</tubes_heparin>
<tubes_acd>6</tubes_acd>
<tubes_clotted>8</tubes_clotted>
<tubes_dna>10</tubes_dna>
<tubes_cpa>12</tubes_cpa>
<monday>1</monday>
<tuesday>1</tuesday>
<wednesday>1</wednesday>
<thursday>0</thursday>
<friday>1</friday>
<saturday>1</saturday>
<sunday>1</sunday>
<RegID>CZ1</RegID>
<patient>
	<patient_id_dn>159159</patient_id_dn>
	<patient_hla>
		<a_1>a1v</a_1>
		<b_1>b1</b_1>
		<c_1>c1v</c_1>
		<drb1_1>drb1_1</drb1_1>
		<dqb1_1>dqb1_1</dqb1_1>
		<a_2>a2</a_2>
		<b_2>b2</b_2>
		<c_2>c2</c_2>
		<drb1_2>drb1_2</drb1_2>
		<dqb1_2>dqb1_2</dqb1_2>
	</patient_hla>
</patient>
</form>
</SampleRequest>";
*/
/*
$sample="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<SampleRequest><form>
<ID>2</ID>
<preferred_courier>servicex</preferred_courier>
<sample_to_institution>ax</sample_to_institution>
<invoice_to_institution>bx</invoice_to_institution>
<sample_address>cx</sample_address>
<invoice_address>dx</invoice_address>
<sample_attention>e</sample_attention>
<invoice_attention>f</invoice_attention>
<sample_phone>g</sample_phone>
<invoice_phone>h</invoice_phone>
<sample_fax>i</sample_fax>
<invoice_fax>j</invoice_fax>
<sample_email>k</sample_email>
<invoice_email>l</invoice_email>
<date_completing>2014-08-23</date_completing>
<mls_edta>1</mls_edta>
<mls_heparin>3</mls_heparin>
<mls_acd>5</mls_acd>
<mls_clotted>7</mls_clotted>
<mls_dna>9</mls_dna>
<mls_cpa>11</mls_cpa>
<tubes_edta>2</tubes_edta>
<tubes_heparin>4</tubes_heparin>
<tubes_acd>6</tubes_acd>
<tubes_clotted>8</tubes_clotted>
<tubes_dna>10</tubes_dna>
<tubes_cpa>12</tubes_cpa>
<monday>1</monday>
<tuesday>1</tuesday>
<wednesday>1</wednesday>
<thursday>0</thursday>
<friday>1</friday>
<saturday>1</saturday>
<sunday>1</sunday>
<RegID>CZ1</RegID>
<patient>
	<patient_id_dn>159159</patient_id_dn>
	<patient_hla>
		<a_1>a1</a_1>
		<b_1>b1</b_1>
		<c_1>c1</c_1>
		<drb1_1>g</drb1_1>
		<dqb1_1>i</dqb1_1>
		<a_2>d</a_2>
		<b_2>e</b_2>
		<c_2>f</c_2>
		<drb1_2>k</drb1_2>
		<dqb1_2>m</dqb1_2>
		<drb3_1>1</drb3_1>
		<drb4_1>h</drb4_1>
		<drb5_1>3</drb5_1>
		<dpb1_1>j</dpb1_1>
		<dqa1_1>5</dqa1_1>
		<dpa1_1>7</dpa1_1>
		<drb3_2>2</drb3_2>
		<drb4_2>l06</drb4_2>
		<drb5_2>4</drb5_2>
		<dpb1_2>n</dpb1_2>
		<dqa1_2>606</dqa1_2>
		<dpa1_2>8</dpa1_2>
	</patient_hla>
</patient>
</form>
</SampleRequest>";
$vysledek=$client->UpdateSampleRequest($sample);
echo $vysledek;
*/


//* =============================================================================
//	Upravit typing request
//============================================================================= */
/*
$typing="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<TypingRequest>
 <form>
  <ID>3</ID> 
	<ia_hospital></ia_hospital>
	<ia_contact_name></ia_contact_name>
	<ia_address></ia_address>
	<ia_phone></ia_phone>
	<ia_fax></ia_fax>
	<ia_email></ia_email>
	<person_completing></person_completing>
	<signature></signature>
	<date_completing>2015-04-02</date_completing>
	<InstID>TCCZ1</InstID>
	<RegID>CZ1</RegID>
 <patient>
  <patient_id_dn>89798</patient_id_dn>
  <search_urgent>2</search_urgent>
 <patient_hla>
  <method_used></method_used>
	<a_1>a</a_1>
	<b_1>b</b_1>
	<c_1>c</c_1>
	<drb1_1>g</drb1_1>
	<drb3_1>h</drb3_1>
	<drb4_1></drb4_1>
	<drb5_1></drb5_1>
	<dqb1_1>i</dqb1_1>
	<dpb1_1>j</dpb1_1>
	<dqa1_1></dqa1_1>
	<dpa1_1></dpa1_1>
	<a_2>d</a_2>
	<b_2>e</b_2>
	<c_2>f</c_2>
	<drb1_2>k</drb1_2>
	<drb3_2>l</drb3_2>
	<drb4_2></drb4_2>
	<drb5_2></drb5_2>
	<dqb1_2>m</dqb1_2>
	<dpb1_2>n</dpb1_2>
	<dqa1_2></dqa1_2>
	<dpa1_2></dpa1_2> 
  </patient_hla>
  </patient>
  <donor>
	<DonorNum>766655</DonorNum>
	<DonorID>KKOOE2-111111</DonorID>
	<aLogMsgNum>742</aLogMsgNum>
	<Resolution>LMHLMHLMHLM</Resolution>
</donor>
  </form>
</TypingRequest>";

$vysledek=$client->UpdateTypingRequest($typing);
echo $vysledek;
*/


//* =============================================================================
//	Upravit workup request
//============================================================================= */
/*
$workup="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<WorkupRequest>
<form>
  <ID>3</ID>
  <aLogMsgNum>1155</aLogMsgNum>
  <patient_id_dn>159159</patient_id_dn> 
  <product_request>5</product_request> 
  <product_reason>mÅ¯j reason4</product_reason> 
  <donor_pref_1>2</donor_pref_1> 
  <donor_pref_2>1</donor_pref_2> 
  <donor_pref_3>2</donor_pref_3> 
  <explanation>moje explain2</explanation> 
  <min_days>28</min_days> 
  <add_hpc_marrow>1</add_hpc_marrow> 
  <add_hpc_apheresis>1</add_hpc_apheresis> 
  <tcell_apheresis>1</tcell_apheresis> 
  <tcell_apheresis_no>101</tcell_apheresis_no> 
  <other>1</other> 
  <other_text>201</other_text> 
  <days_conditioning>301</days_conditioning> 
  <days_chemo>401</days_chemo> 
  <days_radiation>501</days_radiation> 
  <previous_transplant>601</previous_transplant> 
  <donor_requested>1</donor_requested> 
  <cryo>1</cryo> 
  <product_infused>1</product_infused> 
  <received_transplans>1</received_transplans> 
  <date_1>2014-08-04</date_1> 
  <date_2>2014-08-07</date_2> 
  <date_3>2014-08-05</date_3> 
  <date_4>2014-08-08</date_4> 
  <date_5>2014-08-06</date_5> 
  <date_6>2014-08-09</date_6> 
  <comment>mÅ¯j koment2</comment> 
  <RegID>CZ1</RegID> 
  <date_completing>2014-08-23</date_completing> 
</form>
</WorkupRequest>";
$vysledek=$client->UpdateWorkupRequest($workup);
echo $vysledek;
*/


//* =============================================================================
//	Poslat response na request
//============================================================================= */
/*
$response="<?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
<ReqResponse>
 <form>
  <RegID>CZ1</RegID>
  <RequestID>19</RequestID>
  <RequestType>2</RequestType>
  <PatientNum>88888</PatientNum>
  <DonorNum>766655</DonorNum>
  <ResponseID>2</ResponseID>
  <ReasonID>15</ReasonID>
  <Msg>xxxxxxxxxx</Msg>
</form>
 <form>
  <RegID>CZ1</RegID>
  <RequestID>5</RequestID>
  <RequestType>3</RequestType>
  <PatientNum>112233</PatientNum>
  <DonorNum>0000</DonorNum>
  <ResponseID>3</ResponseID>
  <ReasonID>18</ReasonID>
  <Msg>yyy</Msg>
</form>
 <form>
  <RegID>CZ1</RegID>
  <RequestID>20</RequestID>
  <RequestType>1</RequestType>
  <PatientNum>88888</PatientNum>
  <DonorNum>766655</DonorNum>
  <ResponseID>1</ResponseID>
  <ReasonID>2</ReasonID>
  <Msg>asdlkfasjldfkasdlfk lkÄ�Å™Ä�Å™df dfdf</Msg>
</form>
</ReqResponse>";

$vysledek=$client->SendReqResponse($response);
echo $vysledek;
*/


?>
