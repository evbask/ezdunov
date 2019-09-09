<?php

namespace App\Http\Controllers\ApiApp;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\ApiAppController;
use Auth;
use App\User; 
use App\Components\VerifyPassport;
use Illuminate\Support\Facades\DB;

use App\Chat;

/**
 * верификация пользователей
 */
class SupportController extends ApiAppController
{
    /**
     * запрос смс на верификацию телефона
     */
    public function getMessages()
    {
        $this->answer['messages'] = Chat::getArrayMessage();
        return response()->json($this->answer);
    }

    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => ['required', 'integer', 'min:' . Chat::T_MESSAGE, 'max:' . Chat::T_FILE],
        ]);
        if ($validator->fails()) {
            return $this->sendValidateError($validator);
        }

        if ($request->type == Chat::T_MESSAGE) {
            $validator = Validator::make($request->all(), [
                'data' => ['required', 'string', 'max:255']
            ]);
            if ($validator->fails()) {
                return $this->sendValidateError($validator);
            }
            
        }
        if ($request->type == Chat::T_FILE) {
            $mimeList = [
                'jpg',
                'jpeg',
                'png', 
                'doc',
                'docx',
                'xls',
                'pdf',
                'txt'
            ];
            $validator = Validator::make($request->all(), [
                'data'      => ['array', 'min:1', 'max:5'],
                'data.*'    => ['mimes:' . implode(',', $mimeList)],
            ]);
            if ($validator->fails()) {
                return $this->sendValidateError($validator);
            }
        }
        $chat = new Chat();
        $chat->build($request);
        if ($chat->add()) {
            return response()->json($this->answer);
        } else {
            return $this->sendAnswerError(['message' => $chat->getErrors()[0], 'code' => 0]);
        }
    }
}