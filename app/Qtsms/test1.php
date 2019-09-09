<?	header("Content-Type: text/xml; charset=UTF-8");
	
	Include('QTSMS.class.php');
	$sms= new QTSMS('XXXX','***','host');
	
	$sms_text='Привет';
	$sender_name='Petya';
	$period=600;

	// Отправка СМС сообщения по списку адресатов
	$result=$sms->post_message($sms_text, '+78880001122, 89990001122', $sender_name,'x124127456',$period);
	
	// Отправка СМС по кодовому имени контакт листа
	// $result=$sms->post_message_phl($sms_text, 'druzya', $sender_name,'x124127456',$period);
	
	echo $result; // результат XML