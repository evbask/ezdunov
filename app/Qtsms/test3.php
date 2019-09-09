<?	header("Content-Type: text/xml; charset=UTF-8");
	
	Include('QTSMS.class.php');
	$sms= new QTSMS('XXXX','***','host');
	
	// !!! Команда на кеширование запросов
	$sms->start_multipost();    
	// Отправка смс
	$sms->post_message('Привет', '+79999999991,+79999999992', 'Vasya');	 
	// Отправка смс по группе
	$sms->post_message_phl('Здраствуйте', 'drugani', 'Petya');
	// статус сообщения SMS_ID=6666
	$sms->status_sms_id(6666);
	// статусы сообщений SMS_GROUP_ID=110
	$sms->status_sms_group_id(110);
	// !!! отправить всё одним запросом и получить результат в XML
	$r_xml=$sms->process();	
	
	
	echo $r_xml; // результат XML