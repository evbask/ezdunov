<?php

namespace App\Http\Controllers\Main;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Chat;

class ChatController extends ResponseController
{
    public function addMessage(Request $request){

        $chat = new Chat;

        $chat->build($request);

        if($chat->add()){
            return $this->buildResponse('success', 'Сообщение успешно отправлено.');
        }else{
            return $this->buildResponse('fail', $chat->getErrors());
        }

    }

    public function getMessages(){
        return Chat::getArrayMessage();
    }

    public function checkNew(){
        $admin_messages = Chat::getAdminMessages();
        $is_new = $admin_messages->count() > 0;
        $admin_messages->update(['viewed' => 1]);
        return $this->buildResponse('new', $is_new);
    }
}
