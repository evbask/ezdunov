<?php

namespace App\Components;

use App\LogsPush;
use \Exception;

/**
 * @todo взято в тупую с пешкарей, переделать
 * взять свой ключ
 * написать обработчик ошибок
 */
class Firebase
{
    public $title = "Ездунов";
    public $text;
    public $gsmArray;
    public $userArray;
    public $data;
    protected $result;

    const API_ACCESS_KEY = "AAAAjn90tpI:APA91bHQJCwXqz8_FD7C59JuvdfPtcH-YAOUU2yl5IBo995biu68LzLOrD4oHSdlxgZSrHiGaJw_HJ5cpWrGMZw0pbQpzQhXBEN9e3jBNV_WW2b8wPeHDjCA6mFJGcjSdj3yYMLJBL_e";
   
    protected function pushGcm()
    {
        $fields = [
            'registration_ids' => $this->gcmArray,
            'data' => $this->data
        ];
        $fields = json_encode($fields);
        
        $headers = [
            'Authorization: key=' . self::API_ACCESS_KEY,
            'Content-Type: application/json'
        ];
        
        $url = 'https://fcm.googleapis.com/fcm/send';
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);

        /**
         * иногда результ возвращает null, при этом
         * curl_error, curl_errno ошибок не выдают
         */
        if ($result ?? false) {
            $this->result = json_decode($result, true);
            return true;
        } else {
            return false;
        }
    }

    /**
     * инициализация класса
     * @param array $users массив пользователей
     * @param array $data набор атрибутов для отправки
     * @return void
     */
    public function __construct(Array $users, Array $data)
    {
        $this->userArray = $users;
        foreach ($users as $user) {
            // $this->gcmArray[] = $user->gcm_token ?? null;
            $this->gcmArray[] = 'dEGFKGiIReE:APA91bEMrDte4jW8F44fwZScoAgjt3fp_XZaplYm3vEcFZx4BcepsATFxB4EN6TQfgewRnYvGrTBVkmKswg-Nj7Bz-CHoRRkC9afWj8bA8SGhU6L8ZwiY1_zLq4a20kiZINKVqCDff4a';
        }

        /**
         * @todo гавно, переделать
         */
        $this->title = $data['title'] ?? 'Ездунов';
        $this->body = $data['body'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->sound = $data['sound'] ?? '';
        $this->type = $data['type'] ?? 0;

        $this->data['title'] = $this->title;
        $this->data['body'] = $this->body;
        $this->data['type'] = $this->type;
        $this->data['sound'] = $this->sound;
        $this->data['type'] = $this->type;
    }

    public function send()
    {
        if ($this->pushGcm()) {
            $user = null;
            $users = $this->userArray;

            /**
             * @todo неизвестно возможно ли это
             * подумать что дороже, отправить пуш два раза одному человеку
             * или кто то его не получит
             */
            if (count($this->userArray) != count($this->result['results'])) {
                throw new Exception('количество ответов меньше количество пользователей рассылки');
            }
            foreach ($this->result['results'] as $to) {
                if (array_key_exists('message_id', $to)) {
                    $status = LogsPush::S_SUCCESS;
                    $response = [
                        'multicast_id' => $this->result['multicast_id'],
                        'canonical_ids' => $this->result['canonical_ids'],
                        'message_id' => $to['message_id'],
                    ];
                }
                if (array_key_exists('error', $to)) {
                    $status = LogsPush::S_ERROR;
                    $response = [
                        'multicast_id' => $this->result['multicast_id'],
                        'canonical_ids' => $this->result['canonical_ids'],
                        'error' => $to['error'],
                    ];
                }
                $user = array_shift($users);
                LogsPush::create([
                    'user_id'   =>  $user->id,
                    'status'    =>  $status,
                    'type'      =>  1,  /** @todo добавить типы пушей */
                    'data'      =>  $this->data,
                    'response'  =>  $response,
                ]);
            }

            return true;
        } else {
            /**
             * @todo нужна ли эта ветка кода????
             */
            foreach ($this->userArray as $user) {
                LogsPush::create([
                    'user_id'   =>  $user->id,
                    'status'    =>  LogsPush::S_ERROR,
                    'type'      =>  1,  /** @todo добавить типы пушей */
                    'data'      =>  $this->data,
                ]);
            }
            return false;
        }
    }
}