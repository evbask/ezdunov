<?php

namespace App\Components;

use Auth;
use Illuminate\Support\Facades\Hash;
use App\User;

use Illuminate\Support\Facades\File;

/**
 * @todo класс необходимо почистить под текущий проект!!!!!!!!!1
 */
Class Toolkit
{
    /**
     * ezdunov begin
     */




    /**
     * создает хэш юзера
     * @param integer $user_id
     * @return string
     */
    public static function createHash($user_id)
    {
        return md5($user_id . 'DRwwrSA3ZlILbTmVOWlR0V9fS2hyJFN9fWAObK6u');
    }

    /**
     * создает проверочный код
     * @param integer $length
     * @return string
     */
    public static function createCode(int $length = 10)
    {
        return str_pad(rand(1, 99999), $length, 0, STR_PAD_LEFT);
    }

    /**
     * Сранивает введеное старое значение пароля с тем, что есть в базе
     * @param $entered_password
     * @param $user_password
     *
     * @return bool
     */
    public static function comparePasswords($entered_password, $user_password){
        return Hash::check($entered_password, $user_password);
    }

    /**
     * Очищает строку от ненужных символов
     * @param $str
     *
     * @return string
     */
    public static function clearString($str){
        return htmlspecialchars(trim($str));
    }

    /**
     * приводит телефон к нормализованому формату
     * @param string $phone
     * @return string
     */
    public static function sanitizePhone($phone)
    {
        return preg_replace('~\D+~','', $phone);
    }

    /**
     * нормализует емаил
     * @param string $phone
     * @return string
     */
    public static function sanitizeEmail($email)
    {
        return strtolower(filter_var($email, FILTER_SANITIZE_EMAIL));
    }

    /**
     * создает токен для верификации пользователя
     * @param object \User $user
     * @return
     */
    public static function getVerifyEmailToken(User $user)
    {
        return md5($user->id . '_' . $user->email 
        . '_' . $user->created_at . '_' . 'DZt0Cfgdp!bl}F>4MBZ_');
    }

    /**
     * пытается получить уникальное имя
     * для указанного пути
     * @param string $path путь
     * @param string $extension необходимое расширение файла
     */
    public static function getUniqName($path, $extension = null)
    {
        $extension = $extension ? '.' . $extension : '';
        return uniqid(self::createHash(Auth::user()->id ?? rand(0, 99999))) . $extension;
    }

    /**
     * создает путь для текущей системы из его элементов
     * @param array $path элементы пути
     */
    public static function generatePath(array $path)
    {
        return implode(DIRECTORY_SEPARATOR, $path);
    }


    /**
     * ezdunov end
     */

    private $aliasRes = null;
    private static $inst = null;
    private static $mimes = null;

    public static function app()
    {
        if (self::$inst===null) {
            self::$inst = new self;
        }
        return self::$inst;
    }
    public function init()
    {

    }
	
	public static function errorSummaryString($model)
	{
	    $content='';
	    if(!is_array($model))
	        $model=array($model);
		
	    foreach($model as $m)
	    {
	        foreach($m->getErrors() as $errors)
	        {
	            foreach($errors as $error)
	            {
	                if($error!='')
	                    $content.="$error\n";
	            }
	        }
	    }
	    if($content!=='')
	    {
	        if($header===null)
	            $header=Yii::t('yii','Были найдены ошибки:');
			$preReturnString = $header."\n$content";
			$returnString = strip_tags(str_replace('"', '', $preReturnString));
	        return $returnString;
	    }
	    else
	        return ''; 
	}
	
    public static function getRealIp()
    {
		$ip = '';
	 	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	 		$ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			if ($ip && is_array($ip)) {
                $ip = trim($ip[0]);
            }
	 	} else {
	 		$ip = $_SERVER['REMOTE_ADDR'];
	 	}
		return $ip;
    }
    
    /**
     * Узнаем город пользователя по его ip
     */
    public static function getCityByIp()
    {
        $sypex = new SypexGeo();
        $ipGeoInfo = $sypex->run(self::getRealIp());
        if ($ipGeoInfo) {
            $availCities = [
                'Москва' => 1,
                'Санкт-Петербург' => 2,
                'Екатеринбург' => 3,
                'Краснодар' => 4,
                'Челябинск' => 5,
                'Нижний Новгород' => 6,
                'Владивосток' => 7
            ];
            if (isset($ipGeoInfo['country']) && $ipGeoInfo['country']['iso'] == 'RU') {
                if (isset($ipGeoInfo['city']) && in_array($ipGeoInfo['city']['name_ru'], array_keys($availCities))) {
                    $city = $availCities[$ipGeoInfo['city']['name_ru']];
                    return $city;
                }
            }
        }
        
        return false;
    }
	
    public function aliaser($s)
    {
        if (!$this->aliasRes) {
            $this->aliasRes = Yii::app()->db->createCommand()->
            select('INP input, OTP output')->
            from('SEARCH_ALIAS')->
            order('SORT')->
            queryAll();
        }
        foreach ($this->aliasRes as $k => $v) {
            $s = preg_replace('#'.addcslashes($v['input'], '#').'#ui', $v['output'], $s);
        }
        return $s;
    }
	
	public static function create_join_array($rows, $joins){
    /* build associative multidimensional array with joined tables from query rows */

	    foreach((array)$rows as $row){
	        if (!isset($out[$row['id']])) {
	            $out[$row['id']] = $row;
	        }
	
	        foreach($joins as $name => $item){
	            unset($newitem);
	            foreach($item as $field => $newfield){
	                unset($out[$row['id']][$field]);
	                if (!empty($row[$field]))
	                    $newitem[$newfield] = $row[$field];
	            }
	            if (!empty($newitem))
	                $out[$row['id']][$name][$newitem[key($newitem)]] = $newitem;
	        }
	    }
	
	    return $out;
	}
	
    public static function upword($s)
    {
        $s = ucwords(mb_strtolower($s, 'UTF-8'));
        $lower_exceptions = array("to","a","the","of");
        $higher_exceptions = array("I","II","III","IV","V","VI","VII","VIII","XI","X");
        $s = explode(' ', $s);
        foreach ($s as &$w) {
            if (in_array(mb_strtolower($w, 'UTF-8'), $lower_exceptions))
                $w = str_ireplace($lower_exceptions, $lower_exceptions, $w);
            if (in_array(mb_strtoupper($w, 'UTF-8'), $higher_exceptions))
                $w = str_ireplace($higher_exceptions, $higher_exceptions, $w);
        }
        $s = implode(' ', $s);
        return $s; 
    }
    
	static public function clean_arr_print($arr){
		$str = '';
		if(empty($arr)||!is_array($arr)){
			return $arr;
		}
		foreach ($arr as $key => $value) {
			$str .= $key.' -> '.$value.'<br>';
		}
		return $str;
	}
	
    static public function normalizePath($s)
    {
        return Yii::getPathOfAlias('webroot').'/'.preg_replace('/^\//ui', '', str_replace(Yii::getPathOfAlias('webroot'), '', $s));
    }
    
    public static function image($src,$alt,$htmlOptions=array())
    {
        if (!$src||!file_exists(self::normalizePath($src))) {
            $src = '/css/no_image.png';
        }
        if (!empty($htmlOptions['width']) || !empty($htmlOptions['height'])) {
            $params = array();
            if (!empty($htmlOptions['width'])) {
                $params['width'] = $htmlOptions['width'];
            }
            if (!empty($htmlOptions['height'])) {
                $params['height'] = $htmlOptions['height'];
            }
            if (!empty($htmlOptions['trimIn'])) {
                $params['trimIn'] = $htmlOptions['trimIn'];
            }
            $hash = substr(md5(serialize($params)), 0, 8);
            $newPath = CFileUploader::$uploadCacheDir.$hash.'/'.basename($src);
            if (file_exists(self::normalizePath($newPath))) {
                $src = $newPath;
            } else {
                if ($resizePath = self::app()->iResize($params, $src, $newPath)) {
                    $src = $resizePath;
                }
            }
        }
        return CHtml::image($src, $alt, $htmlOptions);
    }
    /**
     * self::app()->iResize();
     * params["width"]
     * params["height"]
     * params["mode"] = grey
     */
    public function iResize($params=array(),$path1='',$path2='',$del=false)
    {

        if (!$path1) return false;
        $path1 = (substr_count($path1, '/var')?$path1:(Yii::getPathOfAlias('webroot').'/'.$path1));
        $path2 = ($path2?(substr_count($path2, '/var')?$path2:(Yii::getPathOfAlias('webroot').'/'.$path2)):$path1);
        if (!file_exists($path1)) return false;
        extract($params);
        if (empty($width)&&empty($height)) return false;
        if (!$orig = getimagesize($path1)) return true;
        $type = exif_imagetype($path1);
        switch ($type) {
            case 1:
                $handler = imagecreatefromgif($path1);
                $background = imagecolorallocate($handler, 0, 0, 0);
                imagecolortransparent($handler, $background);
                break;
            case 2:
                $handler = imagecreatefromjpeg($path1);
                break;
            case 3:
                $handler = imagecreatefrompng($path1);
                $background = imagecolorallocate($handler, 0, 0, 0);
                imagecolortransparent($handler, $background);
                imagealphablending($handler, false);
                imagesavealpha($handler, true);
                break;

            default:
            return false;
                break;
        }
        self::imagetrim($handler, 0xffffff);
        $original = new wh(imagesx($handler), imagesy($handler));
        $newWrapper = new wh(empty($width)?null:$width, empty($height)?null:$height);
        if (!$newWrapper->x) {
            $k = $newWrapper->y/$original->y;
            $newWrapper->x = $original->x*$k;
        } elseif (!$newWrapper->y) {
            $k = $newWrapper->x/$original->x;
            $newWrapper->y = $original->y*$k;
        } else {
            $k = new wh($newWrapper->x/$original->x, $newWrapper->y/$original->y);
            if (empty($trimIn)) {
                $k = ($k->x>$k->y)?$k->y:$k->x;
            } else {
                $k = ($k->x>$k->y)?$k->x:$k->y;
            }
        }
        $newImage = new wh(ceil($original->x*$k), ceil($original->y*$k));

        $dest = imagecreatetruecolor($newWrapper->x, $newWrapper->y);

        imagefill($dest, 0, 0, imagecolorallocate($dest, 255,255,255));
        imagecopyresampled($dest, $handler, ($newWrapper->x-$newImage->x)*0.5, ($newWrapper->y-$newImage->y)*0.5, 0, 0, $newImage->x, $newImage->y, $original->x, $original->y);

        if (isset($mode) && $mode == 'grey') {
            imagefilter($dest, IMG_FILTER_GRAYSCALE);
        }

        $dirP2 = dirname($path2);
        if (!file_exists($dirP2)) {
            mkdir($dirP2, 0777, true);
        }
        if (isset($ext)&&$ext) {
            switch ($ext) {
                case 'gif':
                    $type=1;
                    break;
                case 'jpeg':
                case 'jpg':
                    $type=2;
                    break;
                case 'png':
                    $type=3;
                    break;
                default:
                    $type=1;
                    break;
            }
        }
        if ($del&&$path2) {
            unlink($path1);
        }
        switch ($type) {
            case 1:
                $path2 = preg_replace('/\.\w+$/ui', '.gif', $path2);
                imagegif($dest, $path2);
                break;
            case 2:
                $path2 = preg_replace('/\.\w+$/ui', '.jpg', $path2);
                imagejpeg($dest, $path2, 100);
                break;
            case 3:
                $path2 = preg_replace('/\.\w+$/ui', '.png', $path2);
                imagepng($dest, $path2);
                break;

            default:
            return false;
                break;
        }
        $path2 = str_replace(Yii::getPathOfAlias('webroot'), '', $path2);
        $path2 = '/'.preg_replace('/^\/+/ui', '', $path2);
        return $path2;
    }

    public static function imagetrim(&$im, $bg, $pad=null)
    {
        // Calculate padding for each side.
        if (isset($pad)){
            $pp = explode(' ', $pad);
            if (isset($pp[3])){
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[2], (int) $pp[3]);
            }else if (isset($pp[2])){
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[2], (int) $pp[1]);
            }else if (isset($pp[1])){
                $p = array((int) $pp[0], (int) $pp[1], (int) $pp[0], (int) $pp[1]);
            }else{
                $p = array_fill(0, 4, (int) $pp[0]);
            }
        }else{
            $p = array_fill(0, 4, 0);
        }
    
        // Get the image width and height.
        $imw = imagesx($im);
        $imh = imagesy($im);
    
        // Set the X variables.
        $xmin = $imw;
        $xmax = 0;
    
        // Start scanning for the edges.
        for ($iy=0; $iy<$imh; $iy++){
            $first = true;
            for ($ix=0; $ix<$imw; $ix++){
                $ndx = imagecolorat($im, $ix, $iy);
                if ($ndx != $bg){
                    if ($xmin > $ix){ $xmin = $ix; }
                    if ($xmax < $ix){ $xmax = $ix; }
                    if (!isset($ymin)){ $ymin = $iy; }
                    $ymax = $iy;
                    if ($first){ $ix = $xmax; $first = false; }
                }
            }
        }
    
        // The new width and height of the image. (not including padding)
        $imw = 1+$xmax-$xmin; // Image width in pixels
        $imh = 1+$ymax-$ymin; // Image height in pixels
    
        // Make another image to place the trimmed version in.
        $im2 = imagecreatetruecolor($imw+$p[1]+$p[3], $imh+$p[0]+$p[2]);
    
        // Make the background of the new image the same as the background of the old one.
        $bg2 = imagecolorallocate($im2, ($bg >> 16) & 0xFF, ($bg >> 8) & 0xFF, $bg & 0xFF);
        imagefill($im2, 0, 0, $bg2);
    
        // Copy it over to the new image.
        imagecopy($im2, $im, $p[3], $p[0], $xmin, $ymin, $imw, $imh);
    
        // To finish up, we replace the old image which is referenced.
        $im = $im2;
    }

    ## Получаем красивую русскую дату
    ## use ex. self::GetNormalDate(now(), array('time' => 'G:i:s', 'after'=> '<br/>'));
    public static function GetNormalDate($date_input, $params=array()) {
        if ($date_input===NULL) return NULL;
        extract($params, EXTR_OVERWRITE);
        if (!isset($time)) $time = 'G:i';
        if (!isset($year)) $year = 'Y';
        if (!isset($after)) $after = $time?' в ':'';

        $monthes = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        if (!substr_count($date_input, ' ')) {
            $date = $date_input;
        } else {
            $date = strtotime($date_input);
        }
		$month = $monthes[date('n',$date)];
        //Сегодня, вчера, завтра
        if(date('Y') == date('Y',$date)) {
            $year = '';
            if(date('z') == date('z', $date)) {
                $result_date = date('Сегодня '.$after.$time, $date);
            } elseif(date('z') == date('z',mktime(0,0,0,date('n',$date),date('j',$date)+1,date('Y',$date)))) {
                $result_date = date('Вчера '.$after.$time, $date);
            } elseif(date('z') == date('z',mktime(0,0,0,date('n',$date),date('j',$date)-1,date('Y',$date)))) {
                $result_date = date('Завтра ('.trim(preg_replace('/\s+/ui', ' ', date('j '.$month.' '.$year, $date))).')'.$after.$time, $date);
            }
            if(isset($result_date)) return $result_date;
        }


        //Месяца
        
        $result_date = preg_replace('/\s+/ui', ' ', date('j '.$month.' '.$year.' '.$after.' '.$time, $date));
        return $result_date;
    }

    ## Склонение слово в зависимости от количества
    ## ex. self::declination(2, array('фотография','фотографии','фотографий'));        | return "2 фотографии"
    public static function declination($n, $s, $showNum=true) {
        return ($showNum?($n.' '):'').$s[(!($d=($h=$n%100)%10)||($h>4&&($h<21||$d>4)))?2:$d!=1];
    }

    /**
     * Возвращаем русское название месяца
     * @param $monthNum
     * @return string
     */
    public static function numMonthToRus($monthNum)
    {
        $monthArr = [
            '01' => 'Январь',
            '02' => 'Февраль',
            '03' => 'Март',
            '04' => 'Апрель',
            '05' => 'Май',
            '06' => 'Июнь',
            '07' => 'Июль',
            '08' => 'Август',
            '09' => 'Сентябрь',
            '10' => 'Октябрь',
            '11' => 'Ноябрь',
            '12' => 'Декабрь'
        ];

        return $monthArr[$monthNum];
    }

    /**
     * Возвращатить короткое русское название месяца
     * @param $monthNum
     * @return string
     */
    public static function numMonthToSmallRus($monthNum)
    {
        $monthArr = [
            '01' => 'Янв',
            '02' => 'Фев',
            '03' => 'Мрт',
            '04' => 'Апр',
            '05' => 'Май',
            '06' => 'Июн',
            '07' => 'Июл',
            '08' => 'Авг',
            '09' => 'Сен',
            '10' => 'Окт',
            '11' => 'Нбр',
            '12' => 'Дек'
        ];

        return $monthArr[$monthNum];
    }

    ## Поиск по многомерному массиву
    ## @param $arr - исходный массив
    ## @param $key_search - ключ вложенного массива, по значению которого будем искать
    ## @param $key_find - ключ вложенного массива, значение которого нужно вернуть
    ## @param $find_text - искомый текст
    ## ex. self::find2array(array('0'=>array('id'=>1,'name'=>'user')), 'name', 'id', 'user' );        | return "1"
    public static function find2array($arr,$key_search,$key_find,$find_text) {
        $cou=count($arr);
        $found = 0;
        for ($i=0; $i<$cou; $i++) {
            if($arr[$i][$key_search] == $find_text) {
                $found = $arr[$i][$key_find];
                break;
            }
        }
        return $found;
    }


    ## Обрезаем часть текста
    ## @param $string - исходный текст
    ## @param $length - необходимая длина
    ## @param $etc - как будем завершать обрезанный текст
    ## @param $break_words - обрезать слова
    ## ex. self::truncate('строка строка строка',10);                    | return "строка..."
    ## ex. self::truncate('строка строка строка',10,'...', true);        | return "строка с..."
    public static function truncate($string, $length = 100, $etc = '...', $break_words = false) {
        if ($length == 0) return '';
        $chars = 'UTF-8';
        $string = trim($string);
        if (mb_strlen($string, $chars) > $length) {
            $length -= min($length, mb_strlen($etc, $chars));

            if (!$break_words) {
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, $chars));
            }

            if (substr_count($string,' ') < 2 and $length>20) {
                $string = mb_substr_replace($string, ' ', $length/2, 0, $chars);
            }

            $string = trim(mb_substr($string, 0, $length, $chars)) . $etc;
        }
        return $string;
    }

    ## Простая защита пост запросов
    ## ex. self::clearPOST();
    public static function clearPOST() {
        foreach($_POST as $key => $val){
            if (!is_array($val)){
                $_POST[$key]=trim(strip_tags($val));
            } else
                foreach($val as $key1 => $val1)    {
                    if (!is_array($val1)){
                        $_POST[$key][$key1]=trim(strip_tags($val1));
                    } else {
                        foreach($val1 as $key2 => $val2)
                            $_POST[$key][$key1][$key2]=trim(strip_tags($val2));
                    }
                }
        }
    }

    ## Проверка файла по mimetype
    ## @param $file - путь до файла
    ## @param $mType - нужный mimetype. Можно указать массив с несколькими значениями
    ## ex. self::checkMimeType('img.jpg','jpg');                         | return 1
    ## ex. self::checkMimeType('img.jpg','png');                         | return 0
    ## ex. self::checkMimeType('img.jpg',array('jpg','png','gif'));        | return 1
    public static function checkMimeType($file,$mType) {
        if (!self::$mimes) {
            self::$mimes = require_once(YII_PATH.'/utils/mimeTypes.php');
        }
        $cur_mime = mime_content_type($file);
        if (is_array($mType)) {
            $t = array_flip(self::$mimes);
            return (isset($t[$cur_mime]) && in_array($t[$cur_mime], $mType)) ;
        } else {
            return (isset(self::$mimes[$mType]) && self::$mimes[$mType]==$cur_mime);
        }
    }

    public function insertToTable($a,$t,$d='')
    {
        if (!$a||!$t) return 'SELECT NULL;';
        $a=$this->parseSQLVals($a);

        if (is_array($a[key($a)])) {
            $keys = array_keys($a[key($a)]);
            $values = array();
            foreach ($a as $k => $v) {
                $a[$k] = "(".implode(",", array_values($v)).")";
            }
            $values = implode(',', array_values($a));
        } else {
            $keys = array_keys($a);
            $values = "(".implode(",", array_values($a)).")";
        }
        if ($d) {
            if ($d===true) {
                $d = array();
                foreach ($keys as $k => $v) {
                    $d[] = $v."=VALUES(".$v.")";
                }
                $d = implode(',', $d);
            }
            $d = ' ON DUPLICATE KEY UPDATE '.$d;
        }
        $q = "INSERT INTO `".$t."` (".implode(",", $keys).") VALUES ".$values." ".$d.";";
        return $q;
    }

    public function updateTable($a,$t,$w=array())
    {
        $a=$this->parseSQLVals($a);
        $sets = array();
        foreach ($a as $k => $v) {
            $sets[] = $k."=".$v;
        }
        if ($w) {
            $w = $this->parseSQLVals($w);
            $a=array();
            foreach ($w as $k => $v) {
                $a[] = $k."=".$v;
            }
            $w = ' WHERE ('.implode(') AND (', $a).')';
        }
        $q = "UPDATE `".$t."` SET ".implode(",", $sets).($w?$w:'').";";
        return $q;
    }

    private function parseSQLVals($a) {
        $a2 = array();
        foreach ($a as $k0 => $v0) {
            if (!is_array($v0)) {
                $v0 = array($k0=>$v0);
            }
            foreach ($v0 as $k => $v) {
                if ($v === '') {
                    $a2[$k0][$k] = "''";//////Доделать!!!!
                    continue;
                }
                if (preg_match('/SELECT|UPDATE|INSERT|DELETE/', $v)) {
                    $a2[$k0][$k] = $v;
                    continue;
                }
                if (substr_count($v, '[var]@')) {
                    $a2[$k0][$k] = str_replace('[var]@', '@', $v);
                    continue;
                }
                $k = '`'.$k.'`';
                if ($v === 'NULL'||$v === NULL) {
                    $a2[$k0][$k] = 'NULL';
                    continue;
                }
                if (preg_match('/^\d*[\.\,]+\d*$/', $v)) {
                    $a2[$k0][$k] = sprintf('%.2f', str_replace(',', '.', $v));
                    continue;
                }
                $a2[$k0][$k] = "'".strip_tags(addslashes($v))."'";
            }
        }

        if (($kt=key($a))&&(!is_array($a[$kt]))) {
            $a = array();
            foreach ($a2 as $k => $v) {
                $a[key($v)] = $v[key($v)];
            }
            $a2 =& $a;
        }
        return $a2;
    }
    public static function parseMoney($v, $cents=false)
    {
        return number_format((float)str_replace(',', '.', preg_replace('/[^\d\.,]/', '', $v)),$cents?2:0,'.','&nbsp;');
    }
    public static function dt($days)
    {
        return $days===NULL?NULL:time()+$days*3600*24;
    }
    public static function draw($msg=null,$size=null)
    {
        $path = YII_PATH.'/../images/text/'.md5($msg.$size);
        if (file_exists($path)) {
            return '/images/text/'.md5($msg.$size);
        }
        $font = 'tahoma.ttf'; //default font. directory relative to script directory.
        $msg = $msg?$msg:""; // default text to display.
        $size = $size?$size:24; // default font size.
        $rot = 0; // rotation in degrees.
        $pad = 0; // padding.
        $transparent = 1; // transparency set to on.
        $red = 0; // black text...
        $grn = 0;
        $blu = 0;
        $bg_red = 255; // on white background.
        $bg_grn = 255;
        $bg_blu = 255;
        $font = YII_PATH.'/../fonts/'.$font;
        $width = 0;
        $height = 0;
        $offset_x = 0;
        $offset_y = 0;
        $bounds = array();
        $image = "";

        // get the font height.
        $bounds = ImageTTFBBox($size, $rot, $font, "W");
        if ($rot < 0)
        {
            $font_height = abs($bounds[7]-$bounds[1]);
        }
        else if ($rot > 0)
        {
        $font_height = abs($bounds[1]-$bounds[7]);
        }
        else
        {
            $font_height = abs($bounds[7]-$bounds[1]);
        }
        // determine bounding box.
        $bounds = ImageTTFBBox($size, $rot, $font, $msg);
        if ($rot < 0)
        {
            $width = abs($bounds[4]-$bounds[0]);
            $height = abs($bounds[3]-$bounds[7]);
            $offset_y = $font_height;
            $offset_x = 0;
        }
        else if ($rot > 0)
        {
            $width = abs($bounds[2]-$bounds[6]);
            $height = abs($bounds[1]-$bounds[5]);
            $offset_y = abs($bounds[7]-$bounds[5])+$font_height;
            $offset_x = abs($bounds[0]-$bounds[6]);
        }
        else
        {
            $width = abs($bounds[4]-$bounds[6]);
            $height = abs($bounds[7]-$bounds[1]);
            $offset_y = $font_height;;
            $offset_x = 0;
        }

        $image = imagecreatetruecolor($width+($pad*2)+1,$height+($pad*2)+1);
        imagesavealpha($image, true);
        imagealphablending($image, true);
        $background = imagecolorallocatealpha($image, $bg_red, $bg_grn, $bg_blu, 127);
        $foreground = imagecolorallocate($image, $red, $grn, $blu);        
        imagefill($image, 0, 0, $background);

        imageinterlace($image, false);

        // render the image
        ImageTTFText($image, $size, $rot, $offset_x+$pad, $offset_y+$pad, $foreground, $font, $msg);
        //imageantialias($image,0);
        // output PNG object.
        imagePNG($image, $path);
        return '/images/text/'.md5($msg.$size);
    }
    public static function textImg($text, $size=10, $attributes=array())
    {
        if (!isset($attributes['style'])) $attributes['style'] = 'vertical-align: middle;margin: 0 1em;';
        return CHtml::image(self::draw($text, $size), 'text', $attributes);
    }
	public static function phoneImg($text, $size=10, $attributes=array())
    {
        if (!isset($attributes['style'])) $attributes['style'] = 'vertical-align: middle;margin: 0 1em;';
        return CHtml::image(self::draw(self::formatPhone($text), $size), 'text', $attributes);
    }
    function translit($str)
    {
        $tr = array(
            "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
            "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
            "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya"
        );
        return strtr($str,$tr);
    }

    public static function urlAlias($s)
    {
        $alias = "";
        if (preg_match('/[^a-zA-Z0-9\-_]/ui', $s)) {
            $alias = self::app()->translit($s);
            $alias = preg_replace("/ /ui", "_", $alias);
            $alias = preg_replace('/[^a-zA-Z0-9\-_]+/ui', '', $alias);
            $alias = preg_replace('/__/ui', '_', $alias);
            $alias = preg_replace('/__/ui', '_', $alias).'_';
            $alias = preg_replace('/[_\-]+$/ui', '', $alias);
            if($alias[strlen($alias)-1]=="_"){
                $alias = substr($alias, 0, strlen($alias)-1);
            }
            return strtolower($alias);
        }
        return strtolower($s);
    }

    public static function groupArray($a,$fields,$clearField=false)
    {
        if (is_array($fields)) {
            foreach ($fields as $v) {
                $a = self::groupArray($a, $v);
            }
            return $a;
        }
        $a2 = array();
        foreach ($a as $v) {
            if (isset($v[$fields])&&!isset($a2[$v[$fields]])) {
                $a2[$v[$fields]] = $v;
                if ($clearField) {
                    unset($a2[$v[$fields]][$fields]);
                }
            }
        }
        return array_values($a2);
    }

    public static function sortArray($data, $field, $direct='ASC', $saveKeys=false)
    {
        if(!is_array($field)) $field = array($field);
        $func = $saveKeys?'uasort':'usort';
        $func($data, function($a, $b) use($field, $direct) {
            $retval = 0;
            foreach($field as $fieldname) {
                if($retval == 0) $retval = (is_numeric($a[$fieldname])&&is_numeric($b[$fieldname]))?(($a[$fieldname]>$b[$fieldname])?1:(($a[$fieldname]==$b[$fieldname])?0:-1)):strnatcmp($a[$fieldname],$b[$fieldname]);
            }
            return ($direct=='ASC')?($retval):(-$retval);
        });
        return $data;
    }
	
	public static function numberLen($num=0, $len=4)
	{
		$result ="";
		if(strlen($num)<$len){
			for($i=0; $i<($len-strlen($num)); $i++){
				$result.=0;
			}
		}
		$result.=$num;
		return $result;
	}

    public static function sortModel($data, $field, $direct='ASC', $saveKeys=false)
    {
        if(!is_array($field)) $field = array($field);
        $func = $saveKeys?'uasort':'usort';
        $func($data, function($a, $b) use($field, $direct) {
            $retval = 0;
            foreach($field as $fieldname) {
                if($retval == 0) $retval = (is_numeric($a->$fieldname)&&is_numeric($b->$fieldname))?(($a->$fieldname>$b->$fieldname)?1:(($a->$fieldname==$b->$fieldname)?0:-1)):strnatcmp($a->$fieldname,$b->$fieldname);
            }
            return ($direct=='ASC')?($retval):(-$retval);
        });
        return $data;
    }

    public static function encrypt($a)
    {
        return base64_encode(mcrypt_encrypt(MCRYPT_BLOWFISH, Yii::app()->params['key'], $a, MCRYPT_MODE_ECB));
    }
    public static function encryptObject($a)
    {
        return self::encrypt(serialize($a));
    }

    public static function decrypt($a)
    {
        return trim(mcrypt_decrypt(MCRYPT_BLOWFISH, Yii::app()->params['key'], base64_decode($a), MCRYPT_MODE_ECB));
    }
    public static function decryptObject($a)
    {
        return unserialize(self::decrypt($a));
    }

    public static function gridColumn($model, $field, $relation=null)
    {
        if (substr_count($field, '.')) {
            $m = explode('.', $field);
            $field = array_pop($m);
            $modelName = $m[sizeof($m)-1];
            $model = new $modelName;
            $relation = implode('.', $m);
            array_unshift($m, '$data');
            $srelation = implode('->', $m);
        } else {
            $modelName = get_class($model);
            $m = array();
            if ($relation) {
                $m = explode('.', $relation);
            }
            array_unshift($m, '$data');
            $srelation = implode('->', $m);
        }
        $a = array('name'=>($relation?($relation.'.'):'').$field);
        $a['sortable'] = true;
        if (isset($model->getMetadata()->columns[$field])&&$model->getMetadata()->columns[$field]) {
            $type = $model->getMetadata()->columns[$field]->dbType;
            if (preg_match('/^varchar\(\d+\)$/', $type)||$type=='mediumtext'||$type=='text') {
                $a['filter'] = CHtml::activeTextField($model, $field);
            } elseif(preg_match_all('/^(tiny)?int\((\d+)\)$/', $type, $m)&&isset($m[2][0])&&is_numeric($m[2][0])&&$m[2][0]<=3) {
                if ($model instanceof CMActiveRecord) {
                    $a['filter'] = CHtml::activeDropDownList($model, $field, $model->getEnums($field), array('prompt'=>'-----'));
                    $a['value'] = '(isset('.$srelation.'->'.$field.')&& '.$srelation.' instanceof CMActiveRecord)?('.$srelation.'->getEnumData("'.$field.'", '.$srelation.'->'.$field.')):null';
                } else {
                    $a['filter'] = CHtml::activeDropDownList($model, $field, array(1=>'да', 0=>'нет'), array('prompt'=>'-----'));
                    $a['value'] = '(isset('.$srelation.'->'.$field.')?'.$srelation.'->'.$field.':null)?"да":"нет"';
                }
            } elseif(isset($m[2][0])&&is_numeric($m[2][0])&&$m[2][0]>3) {
                $a['filter'] = CHtml::activeTextField($model, $field);
            } elseif(preg_match_all('/^enum\((.+)\)$/', $type, $m)) {
                try {
                    $enums = eval('return array('.$m[1][0].');');
                    $enumVals = array();
                    foreach ($enums as $ke => $ve) {
                        if (!is_numeric($ve)) {
                            $enumVals[$ve] = $model->getAttributeLabel($ve);
                        } else {
                            $enumVals[$ve] = $ve;
                        }
                    }
                    $a['filter'] = CHtml::activeDropDownList($model, $field, $enumVals, array('prompt'=>'-----'));
                    $a['value'] = 'isset('.$srelation.'->'.$field.')?'.$modelName.'::model()->getAttributeLabel('.$srelation.'->'.$field.'):null';
                } catch (Exception $e) {}
            } elseif(preg_match('/^bit\(\d+\)$/', $type)) {

            }
        }
        return $a;
    }

    public static function compare($a, $b)
    {
        $a = preg_replace("/[^\w]/i", "", strtoupper($a));
        $b = preg_replace("/[^\w]/i", "", strtoupper($b));
        similar_text($a, $b, $p);
        return $p;
    }

    public static function push($cids, $text) {
        /*
         * $cids - ID канала, либо массив, у которого каждый элемент - ID канала
         * $text - сообщение, которое необходимо отправить
         */
        $text = preg_replace('/^"(.*)"$/ui', '$1', json_encode(json_encode($text)));
        $c = curl_init();
        $url = 'http://voloton.ru/pub?id=';

        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_POST, true);

        if (is_array($cids)) {
            foreach ($cids as $v) {
                curl_setopt($c, CURLOPT_URL, $url.$v);
                curl_setopt($c, CURLOPT_POSTFIELDS, $text);
                $r = curl_exec($c);
            }
        } else {
            curl_setopt($c, CURLOPT_URL, $url.$cids);
            curl_setopt($c, CURLOPT_POSTFIELDS, $text);
            $r = curl_exec($c);
        }

        curl_close($c);

    }
	
	public static function pushGcm($gcmArray, $type, $oid, $text, $earnings = null) {
		// prep the bundle
		$msg = array
		(
		    'message' => $text,
			'oid' => $oid,
		    'type' => $type,
    		'earnings' => $earnings
		);
		
		$fields = array
		(
		    'registration_ids'  => $gcmArray,
		    'data'              => $msg
		);
		
		$headers = array
		(
		    'Authorization: key=' . Yii::app()->gcm->apiKey,
		    'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		curl_close( $ch );
		
		//echo $result;
    }

    public static function array_unique($a1)
    {
        $a2 = array();
        foreach ($a1 as $v) {
            $key = md5(serialize($v));
            $a2[$key] = $v;
        }
        return array_values($a2);
    }
    public static function generatePass()
    {
        return substr(md5(microtime().rand(1,1000)), 0, 6);
    }
	public static function generatePhoneCode($phone=FALSE)
	{
		if($phone){
			$phoneCode = substr(preg_replace("/[^0-9]/i","", md5($phone)), 0, 5);
			return $phoneCode;
		}
	}
	public static function formatPhone($phone = false)
	{
		if (strlen($phone) == 11) {
			return $phone[0].' ('.substr($phone, 1, 3).') '.substr($phone, 4, 3).'-'.substr($phone, 7, 2).'-'.substr($phone, 9, 2);
		} else {
			return $phone;
		}
    }
    public static function formatPhone2($phone)
	{
		return preg_replace("/^\+7(\d{3})(\d{3})(\d{2})(\d{2})$/", "+7($1)$2-$3-$4", $phone);
    }

    /**
    * Вернет для телефонов вида 89998889988 +7 000 111 22 33 +7(000)111-22-22 и т.п следующий вид +7(000)-111-22-33
    * Телефон должен иметь 11 значный формат
    * Вернет телефон в изначальном формате если преобразование не возможно
    */
    public static function formatPhone3($phone)
	{
        $phone = preg_replace('/[^0-9,]/', '', $phone);
        return strlen($phone) == 11 ? preg_replace("/^\+?(\d{1})(\d{3})(\d{3})(\d{2})(\d{2})/", "+7($2)-$3-$4-$5", $phone) : $phone;
    }
	
	/**
	 * Делает телефон кликабельным (возможным для обзвона по клику)
	 * @param string $formattedPhone
	 * Принимает телефон в формате 79998887766
	 * @param string $label
	 * Принимает надпись, по клике на которую должен происходить вызов
	 * @param boolean $inRef
	 * Определяет, вставляется ли ссылка в ссылку
	 * @return string $res
	 * Конструкция с ссылкой на телефон
	 */
	public static function sipPhoneWrapper ($formattedPhone, $label = false, $inRef = false)
	{
		if ($inRef) {
		    if ($label) {
		        return '<object><a href="sip:' . $formattedPhone . '">' . $label . '</a></object>';
		    } else {
		        return '<object><a href="sip:' . $formattedPhone . '">' . $formattedPhone . '</a></object>';
		    }
		} else {
			if ($label) {
		        return '<a href="sip:' . $formattedPhone . '">' . $label . '</a>';
		    } else {
		        return '<a href="sip:' . $formattedPhone . '">' . $formattedPhone . '</a>';
		    }
		}		
	}
	
    public static function mergeJsonArray()
    {
        $a = array();
        if (func_num_args()>1) {
            foreach (func_get_args() as $k => $v) {
                if ($v)
                    $a = array_merge((array)$a, (array)json_decode($v));
            }
            return json_encode($a);
        } else {
            return func_get_arg(0);
        }
    }
    public static function mask2array($m)
    {
        $a = array();
        $m = (int)$m;
        $hb = pow(2, floor(log($m, 2)));
        while ($hb) {
            if ($m & $hb) array_push($a, $hb);
            $hb = $hb >> 1;
        }
        $a = array_reverse($a);
        return $a;
    }
    public static function array2mask($a)
    {
        if (!is_array($a)) return 0;
        return array_sum($a);
    }
    
    public static function furl($path, $name=null)
    {
        $a = array('path'=>$path);
        if ($name) {
            $a['name'] = $name;
        }
        return Yii::app()->createUrl('/site/download', array('file'=>self::encryptObject($a)));
    }
    
    public static function decodePhone($s)
    {
        $s = self::encodePhone($s);
        $s = preg_replace('/^\+?[7]?(?:8(?=812|800))?/ui', '', $s);
        $s = '+7 '.preg_replace('/^(\d{3})/ui', '($1) ', $s);
        return self::formatPhone($s);
    }
    public static function encodePhone($s)
    {
        return preg_replace('/\D/ui', '', $s);
    }
    
    // Нахождение расстояния между точками
    public static function distance($longitude1, $latitude1, $longitude2, $latitude2)
	{
        // Cредний радиус Земли в метрах
        $earth_radius = 6372797;
        
        $dLat = deg2rad($latitude2 - $latitude1);
        $dLon = deg2rad($longitude2 - $longitude1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * asin(sqrt($a));
        $d = $earth_radius * $c;
        
        return $d;
	}
	
	public static function distanceMtoKm($distance) //перевод из метро в километры
	{
		return number_format($distance/1000, 1);
	}
	
	/**
	 * Возвращает сумму прописью
	 * @author runcore
	 * @uses morph(...)
	 */
	public static function num2str($num){
		$nul='ноль';
		$ten=array(
			array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
			array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
		);
		$a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
		$tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
		$hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
		$unit=array( // Units
			array('копейка' ,'копейки' ,'копеек',	 1),
			array('рубль'   ,'рубля'   ,'рублей'    ,0),
			array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
			array('миллион' ,'миллиона','миллионов' ,0),
			array('миллиард','милиарда','миллиардов',0),
		);
		//
		list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
		$out = array();
		if (intval($rub)>0) {
			foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
				if (!intval($v)) continue;
				$uk = sizeof($unit)-$uk-1; // unit key
				$gender = $unit[$uk][3];
				list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
				// mega-logic
				$out[] = $hundred[$i1]; # 1xx-9xx
				if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
				else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
				// units without rub & kop
				if ($uk>1) $out[]= self::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
			} //foreach
		}
		else $out[] = $nul;
		$out[] = self::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
		$out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
		return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
	}

	/**
	 * Склоняем словоформу
	 * @ author runcore
	 */
	public static function morph($n, $f1, $f2, $f5){
		$n = abs(intval($n)) % 100;
		if ($n>10 && $n<20) return $f5;
		$n = $n % 10;
		if ($n>1 && $n<5) return $f2;
		if ($n==1) return $f1;
		return $f5;
	}

    public static function morphAr($n, $arrayNames){
        return self::morph(
            $n,
            $arrayNames[0],
            $arrayNames[1],
            $arrayNames[2]
        );
    }

    public static function checkPhoneInRossvyaz($regionCode, $number)
    {			
		$url = 'https://rossvyaz.ru/activity/num_resurs/registerNum/';
		$paramsString = 'act=search' . '&abcdef=' . $regionCode . '&number=' . $number;
			
		if ($curl = curl_init()) {
    		curl_setopt($curl, CURLOPT_URL, $url);
    		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    		curl_setopt($curl, CURLOPT_POST, true);
    		curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsString);
    		
    		$result = curl_exec($curl);
			$result = iconv('windows-1251', 'utf-8', $result);
    		
            curl_close($curl);
            
            return $result;
        }
        return false; 
    }
	
    public static function addBeanstalkPushJob($payload)
    {
        $client = Yii::app()->beanstalk->getClient();
		$client->connect();
        $client->useTube('default');
	
		//echo "Tube used: {$client->listTubeUsed()}.\n";
        //print_r($client->stats());
        
        if (isset($payload['favoriteList'])) {
            $favList = json_decode($payload['favoriteList']);
            if ($favList && count($favList)) {
                $gcmArray = [];
                foreach ($favList as $k => $v) {
                    if (in_array($v, User::appTestUsers())) {
                        (new Log($v, false, true))->info('push', $payload);
                        $user = User::model()->findByPk($v);
                        if ($user->gcm_token) {
                            $gcmArray[] = $user->gcm_token;
                            unset($favList[$k]);
                            $flag = true;
                        }
                    }
                }

                if ($gcmArray && count($gcmArray)) {
                    LibUser::pushGcm(
                        $gcmArray,
                        $payload['type'],
                        $payload['message'],
                        $payload['oid'],
                        $payload['earnings'],
                        $payload['distance'],
                        $payload['timer']
                    );
                }
            }
        }

		foreach(['ios', 'android'] as $platform) {
            if ($platform == 'android' && $favList && count($favList)) {
                $payload['favoriteList'] = json_encode(array_values($favList));
            } elseif ($platform == 'android' && !$favList && $flag) {
                continue;
            }

			$payload['platform'] = $platform;
			$jobDetails = [
			    'application' => 'beanstalk push',
			    'payload' => $payload
            ];
			$jobDetailString = json_encode($jobDetails);
			$ret = $client->put(
			    0, // priority
			    0, // do not wait, put in immediately
			    90, // will run within n seconds
			    $jobDetailString // job body
            );
		}
		//echo "Added $jobDetailString to queue.\n";
		//echo "Return was: $ret.\n";
        $client->disconnect();
	}
	
    public static function fetchAddressCoordinatesGoogle($address)
    {
        $address = preg_replace("/ /i", "%20", $address);
        $url = "https://maps.google.com/maps/api/geocode/json?address=" . $address . "&sensor=false";

        $data = self::file_get_contents_curl($url);
        
        // Считаем общее количество запросов за день
        VariousCounters::incrementCounterValue('google_geocode_requests');
        
        $json = json_decode($data);
        $locationType = $json->results[0]->geometry->location->location_type;
		
		$lat = $json->results[0]->geometry->location->lat;
		$lng = $json->results[0]->geometry->location->lng;
		
		if ($lat && $lng) {
            $result = [];
            
            if (!$locationType || ($locationType != 'ROOFTOP' || $locationType != 'RANGE_INTERPOLATED')) {
                $result['notFound'] = 1;
            } else {
                $result['notFound'] = 0;
            }
            
			$result['lat'] = $lat;
			$result['lng'] = $lng;
			return $result;
        }
        
		return false;
	}
    
    /**
     * @see https://tech.yandex.ru/maps/doc/jsapi/2.1/ref/reference/geocode-docpage/
     */
    public static function fetchAddressCoordinatesYandex($address)
    {
        $address = preg_replace("/ /i", "%20", $address);
        $url = "https://geocode-maps.yandex.ru/1.x/?format=json&geocode=" . $address;

        $data = self::file_get_contents_curl($url);

        // Считаем общее количество запросов за день
		VariousCounters::incrementCounterValue('yandex_geocode_requests');

        $json = json_decode($data);
        $presicion = $json->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->precision;
        $kind = $json->response->GeoObjectCollection->featureMember[0]->GeoObject->metaDataProperty->GeocoderMetaData->kind;
		$pos = explode(" ", $json->response->GeoObjectCollection->featureMember[0]->GeoObject->Point->pos);
		
		$lat = $pos[1];
		$lng = $pos[0];
		
		if ($lat && $lng) {
            $result = [];

            if ($presicion == 'other' && $kind != 'metro') {
                $result['notFound'] = 1;
            } else {
                $result['notFound'] = 0;
            }

			$result['lat'] = $lat;
			$result['lng'] = $lng;
			return $result;
        }

		return false;
	}

    /**
     * Находим все номера банковских карт и добавляем пробелы
     * @param string $str <p>
     * Строка, в которой осуществляется поиск номеров карт
     * </p>
     * @param int $minLength <p>
     * Минимальная длина номера карты
     * </p>
     * @param int $maxLength <p>
     * Максимальная длина номера карты
     * </p>
     * @param int $step <p>
     * Шаг, через которые вставляется пробел
     * </p>
     * @return string <p>
     * Строка с разделенными номерами карт
     * </p>
     */
    public static function splitCardNumber($str, $minLength = 15, $maxLength = 19, $step = 4){
        $patt = '/[0-9]{'.$minLength.','.$maxLength.'}/';
        $newStr = $str;
        preg_match_all($patt, $str, $matches);

        foreach ($matches[0] as $match) {
            $newStr = str_replace($match, chunk_split($match, $step, ' '), $newStr);
        }

        return $newStr;
    }

    /**
     * Функция возвращает различные названия курьеров в нужных падежах
     * Например: курьером, магазином, колл-центром
     * @param int $userId <p>
     * id юзера
     * </p>
     * @param int $case <p>
     * Номер падежа
     * </p>
     * @param boolean $includeName <p>
     * Добавить имя пользователя
     * </p>
     *
     * @return string <p>
     * Текст для вывода
     * </p>
     */
    public static function getUserCaseName($userId, $case = 1, $includeName = false){
        $user   = User::model()->findByPk($userId);
        $result = '';

        if ($user){
            $names = [
                1 => [
                    1 => 'курьер',
                    2 => 'магазин',
                    3 => 'сортировочный центр',
                    96 => 'робот',
                    97 => 'колл-центр',
                    98 => 'менеджер',
                    99 => 'администратор',
                ],
                2 => [
                    1 => 'курьера',
                    2 => 'магазина',
                    3 => 'сортировочного центра',
                    96 => 'робота',
                    97 => 'колл-центра',
                    98 => 'менеджера',
                    99 => 'администратора',
                ],
                3 => [
                    1 => 'курьеру',
                    2 => 'магазину',
                    3 => 'сортировочному центру',
                    96 => 'роботу',
                    97 => 'колл-центру',
                    98 => 'менеджеру',
                    99 => 'администратору',
                ],
                4 => [
                    1 => 'курьера',
                    2 => 'магазин',
                    3 => 'сортировочный центр',
                    96 => 'робота',
                    97 => 'колл-центр',
                    98 => 'менеджера',
                    99 => 'администратора',
                ],
                5 => [
                    1 => 'курьером',
                    2 => 'магазином',
                    3 => 'сортировочным центром',
                    96 => 'роботом',
                    97 => 'колл-центром',
                    98 => 'менеджером',
                    99 => 'администратором',
                ],
                6 => [
                    1 => 'о курьере',
                    2 => 'о магазине',
                    3 => 'о сортировочном центре',
                    96 => 'р роботе',
                    97 => 'о колл-центре',
                    98 => 'о менеджере',
                    99 => 'об администраторе',
                ]
            ];

            $result = $names[$case][$user->type];

            $result = $includeName ? $result.' ('.$user->fio.')' : $result;
        }

        return $result;
    }
    
   /**
     * Проверка на наличие в строке чисел в определенном диапозоне.
     * @param $checkString строка для проверки
     * @param $minValue нижнее граничное значение диапозона
     * @param $maxValue верхнее граничное значение диапозона.
     * @return первое найденное число или false если число не найдено
     */
    public static function checkStringForNumbersBetweenValues($checkString, $minValue, $maxValue) {
        $noLetterString = preg_replace('/[^0-9]+/', '_', $checkString);
        $numbersInTarget = explode('_', $noLetterString);
        foreach ($numbersInTarget as $number) {
            if ($number && (int)$number >= $minValue && (int)$number <= $maxValue) {
                return (int)$number; //возвращаем первое вхождение
            }
        }
        return false;
    }
    
    /**
     * Проверка на наличие в строке отдельно стоящих чисел (отделенных определенным разделителем) в определенном диапозоне,
     * @param $checkString строка для проверки
     * @param $minValue нижнее граничное значение диапозона.
     * @param $maxValue верхнее граничное значение диапозона.
     * @param $delimiter разделитель.
     * @return первое найденное число или false если число не найдено
     */
    public static function checkSymbolDelimetedStringForNumbersBetweenValues($checkString, $minValue, $maxValue, $delimiter = " ")
    {
        // Пока решили отключить (18.06.2018)
        return false;
        
        // Разделить по пустому символу это как делить на ноль - не даем
        if ($delimiter == "") { 
            return false;
        }    
        $explodedString = explode($delimiter, $checkString);
        foreach ($explodedString as $subString) {
            if ($subString && $subString == (int)$subString && $subString >= $minValue && $subString <= $maxValue) {
                return ['badWord' => $subString, 'title' => 'Числа в заказе', 'forWithoutBuyout' => true];
            }
        }
        return false;
    }

    public static function checkStringBuyout($checkString)
    {
        $row = Stop_words::model()->findByAttributes(['name' => 'order_without_buyout']);
        $wordsArr = explode(', ', $row->value);
        foreach ($wordsArr as $badWord) {
            if ($badWord) {
                if (strpos($checkString, $badWord) !== false) {
                    return ['badWord' => $badWord, 'title' => $row->title, 'forWithoutBuyout' => true];
                }
            }
        }
        return false;
    }
    
    /**
     * Функция проверяет строку на наличие стоп слов, слова обозначенные символом '!' обозначают полное соответствие
     * Так например '!соль' не будет срабатывать на слово 'Посольство', в отличие от стоп слова 'соль'
     * @param string $checkString строка которую необходимо проверить
     * @return mixed Возвращает массив который содержит найденное слово в строке 'badWord' и категорию стоп слов 'title'
     * false если стоп слова не входят в строку
     */
    public static function checkStringStopWords($checkString)
    {
        $arrStopWords = Stop_words::model()->findAll(['condition' => 'name != "order_without_buyout"']);
        $checkString = mb_strtolower($checkString, 'utf-8');
        $checkString = preg_replace("/[^,\p{Cyrillic}\s]/ui", '', $checkString);
        $checkString = preg_replace("/[\s]{2,}/ui", ' ', $checkString);
        $checkString = trim($checkString);
        $checkStringArr = explode(' ', $checkString);
        foreach ($arrStopWords as $row) {
            $wordsArr = explode(', ', $row->value);
            foreach ($wordsArr as $badWord) {
                if($badWord[0] == "!")
                {
                    if(in_array(substr($badWord, 1), $checkStringArr))
                        return ['badWord' => $badWord, 'title' => $row->title];
                }
                else
                {
                    if (mb_stripos($checkString, $badWord) !== false)
                        return ['badWord' => $badWord, 'title' => $row->title];
                }
            }
        }
        return false;
        // изменено 2018/08
        /*$arrStopWords = Stop_words::model()->findAll(['condition' => 'name != "order_without_buyout"']);
        foreach ($arrStopWords as $row) {
            $wordsArr = explode(', ', $row->value);
            foreach ($wordsArr as $badWord) {
                if (strpos($checkString, $badWord) !== false) {
                    return ['badWord' => $badWord, 'title' => $row->title];
                }
            }
        }
        return false;*/
	}
		
	public static function generateStopWordRegExp($words){
		$res = array();
		foreach ($words as $word) {
			$word = trim($word);
			$word = str_replace("а", '[а|a]{1,}', $word);
			$word = str_replace('к', '[к|k]{1,}', $word);
			$word = str_replace("р", '[р|p]{1,}', $word);
			$word = str_replace("с", '[с|c]{1,}', $word);
			$word = str_replace("у", '[у|y]{1,}', $word);
			$word = str_replace("х", '[х|x]{1,}', $word);
			$word = str_replace("т", '[т|t]{1,}', $word);
			$word = str_replace("м", '[м|m]{1,}', $word);
			$word = str_replace("н", '[н|h]{1,}', $word);
			$word = str_replace("в", '[в|b]{1,}', $word);
			$word = str_replace("е", '[е|e]{1,}', $word);
			$word = str_replace("о", '[о|o]{1,}', $word);
			$word = str_replace("г", '[г|r]{1,}', $word);
			$res[] = '('.$word.')';
		}
		return implode('|', $res);
	}


    /**
     * Разбитие ФИО на массив отдельных строк
     * @param string $fio
     * @return array
     */
    public static function getNameArray($fio = '')
    {
        $result = [];
        $fio = preg_replace('| +|', ' ', $fio);
        $fio = trim($fio);

        $fioAr = explode(' ', $fio);
        if (!$fioAr)
            return $result;

        $result['lastName'] = $fioAr[0];

        $result['firstName'] = isset($fioAr[1]) ? $fioAr[1] : '';

        $result['patronymic'] = count($fioAr) > 2 ? implode(' ', array_slice($fioAr, 2)) : '';


        return $result;
    }

    /**
     * Разбитие строки на массив фамилия, имя, отчество + приставочные формы ("оглы", "оглу", "улы", "кызы", "гызы"))
     * строка может состоять лишь из символов кириллицы, пробелов и разделителя '-' в фамилии имени и отчестве
     * @param string $fio
     * @return array Возвращает массив НАЙДЕННЫХ свойств фамилия->surname, name->имя, отчество->patronymic, всякие (оглы углы и пр.)->old
     * false если строка пуста или не соответствует требованиям
     */
    public static function fullNameArray($string = '')
    {
        // убираем лишние пробелы
        $string = preg_replace("/[\s]{2,}/", ' ', $string);
        $string = trim($string);

        if(empty($string))
            return false;

        $temp = explode(' ', $string, 4);

        $result = [];
        $key = ['surname','name','patronymic','old'];
        foreach($temp as $row)
        {
            if(preg_match("/^([А-ЯЁа-яё]{1,100}(-[А-ЯЁа-яё]{1,100})*)$/ui", $row))
                $result[array_shift($key)] = mb_convert_case($row, MB_CASE_TITLE, "UTF-8");
            else
                return false;
        }
        return $result;
    }

    /**
     * Проводит валидацию ФИО, на данный момент проверяет количество слов >=2
     * На вход можно подавать результат массив из 4 слов (например результат self::fullNameArray())
     * Или строку
     * @return bool Возвращает результат проверки true/false
     */
    public static function validationFullName($var = null)
    {
        switch (gettype($var)) {
            case "array":
                if(count($var) < 2)
                    return false;
                else
                    return true;
                break;
            case "string":
                return self::validationFullName(self::fullNameArray($var));
                break;
            default:
               return false;
        }
    }

    /**
     * Валидация email введенного пользователем
     * @param string $string строка которую необходимо валидировать
     * @return array Возвращает массив email в случае успеха, и false в случае ошибки
     */
    public static function validateEmail($string)
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL) ? $string : false;
    }

    /**
     * Нормализация email введенного пользователем
     * @param string $string строка которую необходимо валидировать
     * @return array Возвращает массив email в случае успеха, и false в случае ошибки (если после нормализации email стал не валидным)
     */
    public static function normalizationEmail($string)
    {
        $string = filter_var($string, FILTER_SANITIZE_EMAIL);
        return self::validateEmail($string);
    }

    /**
     * Выводит в красивом виде номер карты 4300 **** **** 0777
     * @param string $string строка, номер карты который необходимо отобразить
     * @return string возвращает форматированный номер карты
     */
    public static function cardDisplayedMask($string)
    {
        return substr($string, 0, 4) . " **** **** " . substr($string, -4);
    }

    /**
     * Удаляет из email лишние сиволы
     * @param $email
     * @return mixed
     */
    public static function clearEmail($email)
    {
        $exceptions = ['!', '#', '$', '%', '^', '&', '*', '(', ')', '[', ']', ' '];
        return str_replace($exceptions, '', $email);
    }

    /**
     * Удаление определенного параметра из ссылки
     * @param $url
     * @param $name
     * @return string
     */
    public static function deleteGetParamFromUrl($url, $name)
    {
        list($url_part, $qs_part) = array_pad(explode("?", $url), 2, ""); // Разбиваем URL на 2 части: до знака ? и после
        parse_str($qs_part, $qs_vars); // Разбиваем строку с запросом на массив с параметрами и их значениями
        unset($qs_vars[$name]); // Удаляем необходимый параметр
        if (count($qs_vars) > 0) { // Если есть параметры
            $url = $url_part . "?" . http_build_query($qs_vars); // Собираем URL обратно
        } else
            $url = $url_part; // Если параметров не осталось, то просто берём всё, что идёт до знака ?

        return $url;
    }

    /**
     * Установка "канонического" тэга линк
     * для удаления дубликатов страниц
     */
    public static function getCanonicalLink()
    {
        $request = Yii::app()->request;
        $url = $request->hostInfo . $request->requestUri;

        $url = self::deleteGetParamFromUrl($url, 'token');
        $url = self::deleteGetParamFromUrl($url, 'rf');

        return $url;
    }

    /**
     * Нужен ли канонический адрес на этой странице
     * @param string $actionId
     * @return bool
     */
    public static function needCanonical($actionId = '')
    {
        $array = [
            'deliveryItem',
            'deliveryItemList',
            'page',
            'addPublicDelivery'
        ];

        return Yii::app()->request->getRequestType() == 'GET'
            && in_array($actionId, $array);
    }
	
    function file_get_contents_curl($url)
    {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        if ($info['http_code'] != 200) {
            (new Log("geocode_curl_errors"))->info('Err', $info);
        }
		curl_close($ch);  

        return $data;
	}

    /**
     * Создание записи в таблице
     * @param $className
     * @param $fields
     * ['date' => '2016-02-21 21:21:22']
     * @param bool $runValidation
     * @return mixed|bool
     * Вернёт объект, если он успешно создан, в противном случае false
     */
    public static function add($className, $fields, $runValidation = true)
    {
        try {
            $object = new $className;

            foreach ($fields as $key => $value)
                $object->$key = $value;

            $saved = $object->save($runValidation);
            return $saved ? $object : false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Возвращает строковое отображение знака числа
     * @param int $number
     * @return string
     */
    public static function numberSign($number = 0)
    {
        if ($number > 0)
            $ret = '+';
        elseif ($number < 0)
            $ret = '-';
        else $ret = '';

        return $ret;
    }

    /**
     * Возвращает массив. Делит на части (по дням, месяцам и т.д.)
     * @param $dateFrom
     * @param $dateTo
     * @param string $intervalSpec
     * @param string $dateFormat
     * @return array
     */
    public static function dividePeriod($dateFrom, $dateTo, $intervalSpec = 'P1D', $dateFormat = 'd.m.Y')
    {
        $period = new DatePeriod(
            new DateTime($dateFrom),
            new DateInterval($intervalSpec),
            new DateTime($dateTo)
        );

        return array_map(function($item) use ($dateFormat){
            return $item->format($dateFormat);
        }, iterator_to_array($period));
    }


    /**
     * Получение строки в формате для отчётов
     * Пример: 1 000,00
     * @param $value
     * @return string
     */
    public static function numFormat($value)
    {
        return $value ? number_format((float)$value, 2, ',', ' ') : '';
    }

    /**
     * Получение строки в формате для отчётов
     * Пример: 1000,00
     * @param $value
     * @return string
     * @todo JIorD это ужасно, как быть с кучей одного и того же????
     */
    public static function numFormatForExcel($value)
    {
        return $value ? number_format((float)$value, 2, ',', '') : '';
    }

    /**
     * Форматирует число в удобо читаемое,
     * используется для логов баланса
     * @param integer $value число для преобразования
     * @return string
     */
    public static function numFormatLogs($value)
    {
        return number_format($value, 0, ',', ' ');
    }

    /**
     * Проверка, является текущий
     * экземпляр приложения консольным
     * @return bool
     */
    public static function isConsoleApp()
    {
        return Yii::app() instanceof CConsoleApplication;
    }

    public static function secondsToHoursString($secondsCount)
    {
        if ($secondsCount == 86399) { // все часы ровные, кроме 23:59
            $hoursString = '23:59';
        } else {
            $hoursCount = $secondsCount / 3600;
            if (is_int($hoursCount) && $hoursCount >= 0 && $hoursCount <= 9) {
                $hoursString = '0' . $hoursCount . ':00';
            } elseif (is_int($hoursCount) && $hoursCount >= 10 && $hoursCount <= 23) {
                $hoursString = $hoursCount . ':00';
            } else {
                $hoursString = '00:00';
            }
        }
        
        return $hoursString;
    }

    /**
     * типы заказов, описания, картинки и ссылки для окна выбора типа заказа
     * @return array
     */
    public static function getOrderTypeForCreate()
    {
        $orderTypeForCreate = [
            [
                'title' => 'Доставка',
                'subTitle' => 'товаров и документов',
                'imgUrl' => 'routeIconWhite.png',
                'href' => 'add'
            ],
            [
                'title' => 'Аренда',
                'subTitle' => 'курьера Пешкариков',
                'imgUrl' => 'rentIconWhite.png',
                'href' => 'addByHour'
            ],
            [
                'title' => 'Фото',
                'subTitle' => 'задание для курьера',
                'imgUrl' => 'photoIconWhite.png',
                'href' => 'addPhotoAudit'
            ],
            [
                'title' => 'Выдача',
                'subTitle' => 'товара на пункте',
                'imgUrl' => 'pickupIconWhite.png',
                'href' => 'addPickup'
            ],
        ];

        return $orderTypeForCreate;
    }
}


/* да просят меня боги ООП за это */
/* пока не будет сущестовать эта функция, будем юзать этот костыль */
if (!function_exists("mb_substr_replace")){
    function mb_substr_replace($string,$replacement,$start,$length=null,$encoding = null){
        if ($encoding == null){
            if ($length == null){
                return mb_substr($string,0,$start).$replacement;
            } else {
                return mb_substr($string,0,$start).$replacement.mb_substr($string,$start + $length);
            }
        } else {
            if ($length == null){
                return mb_substr($string,0,$start,$encoding).$replacement;
            } else {
                return mb_substr($string,0,$start,$encoding). $replacement. mb_substr($string,$start + $length,mb_strlen($string,$encoding),$encoding);
            }
        }
    }

}

class wh {
    public $x;
    public $y;
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        return $this;
    }
}