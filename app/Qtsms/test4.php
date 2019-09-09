<?	header("Content-Type: text/xml; charset=UTF-8");
	
	Include('QTSMS.class.php');
	$sms= new QTSMS('XXXX','***','host');
	
	// получение баланса
	$r_xml=$sms->get_balance();
	
	echo $r_xml; // результат XML