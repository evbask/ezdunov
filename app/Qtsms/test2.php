<?	header("Content-Type: text/xml; charset=UTF-8");
	
	Include('QTSMS.class.php');
	$sms= new QTSMS('XXXX','***','host');
	
	// данные о сообщении SMS_ID=6666
	//$r_xml=$sms->status_sms_id(10001000000000281);
	
	// данные о сообщениях отправки SMS_GROUP_ID=110
	//$r_xml=$sms->status_sms_group_id(25);
	
	// Получить данные сообщений отправленных с 18.12.2007 00:00:00 по 23.12.2007      23:00:00
	$r_xml=$sms->status_sms_date('31.05.2010 00:00:00','01.06.2010 23:00:00');
	
	echo $r_xml; // результат XML