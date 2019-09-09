<?	header("Content-Type: text/xml; charset=UTF-8");
	
	Include('QTSMS.class.php');
	$sms= new QTSMS('XXXX','***','host');
	
	$sms_text = 'Привет';
	$subject =  'This is topic name';
	$file_data = array( "name" => "test_image.jpg",
						"path" => "test_image.jpg" );
	
	// Отправка СМС сообщения по списку адресатов
	$result=$sms->post_mms_message($subject, $sms_text, $file_data, '89099879813,89099879814');
	
	// Отправка СМС по кодовому имени контакт листа
	// $result=$sms->post_message_phl($sms_text, 'druzya', $sender_name);
	echo $result; // результат XML