<?php

namespace App\Http\Controllers\ApiApp;

use Auth;
use Request;
use Validator;

use App\Chat;
use App\PreRentPhoto;
use App\PasportVerifyRequest;
use App\PasportVerifyPhoto;
use App\Components\Toolkit;
use App\Http\Controllers\ApiAppController;

use Illuminate\Support\Facades\File;

/**
 * Контроллер предназначен отдает запрашиваемые файлы
 * 
 * @todo отвратительный контроллер
 */
class FileController extends ApiAppController
{
    /**
     * получить аватар пользователя
     */
    public function getAvatar()
    {
        $filePath = config('folders.avatars') . Auth::user()->avatar;
        if (file_exists($filePath)) {
            $type = mime_content_type($filePath);
            return response(file_get_contents($filePath))->header('Content-Type', $type);
        } else {
            $filePath = config('folders.avatars') . 'profile.jpg';
            return $this->checkFile($filePath);
        }
    }

    /**
     * отдает фото текущих проблем по вехиклу
     */
    public function getPhotoDeviceProblem($id)
    {
        $photo = PreRentPhoto::where('id', $id)->
                            where('active', true)->first();

        if (empty($photo)) {
            return $this->sendAnswerError(['message' => 'Файл не существует', 'code' => 0], 404);
        }

        $filePath = config('folders.pre_rent_photos') . $photo->vehicle_id . DIRECTORY_SEPARATOR . $photo->photo_name;
        return $this->checkFile($filePath);
    }

    /**
     * отдает фото паспорта
     * @param integer $id ид фото
     */
    public function getPassportVerifyPhoto($id)
    {
        $request = PasportVerifyRequest::where('user_id', Auth::user()->id)->first();
        if (empty($request)) {
            return $this->sendAnswerError(['message' => 'Вы не подавали заявку', 'code' => 0], 404);
        }

        $photo = PasportVerifyPhoto::where('id', $id)->
                                    where('request_id', $request->id)->first();
        if (empty($photo)) {
            return $this->sendAnswerError(['message' => 'Файл не существует', 'code' => 0], 404);
        }
        
        if ($photo->request_id != $request->id) {
            return $this->sendAnswerError(['message' => 'Доступ запрещен', 'code' => 0], 403);
        }

        $dirHashName = Toolkit::createHash(Auth::user()->id);
        $sep = DIRECTORY_SEPARATOR;
        $filePath = config('folders.passport_photos') . $sep . $dirHashName . $sep . $photo->img_url;
        return $this->checkFile($filePath);
    }

    /**
     * отдает файла чата пользователя
     * @param integer $id ид файла
     */
    public function getChatFile($id)
    {
        $user = Auth::user();
        $file = Chat::find($id);

        if (empty($file)) {// || $file->type != Chat::T_FILE) {
            echo "Что то идет не так";
            exit;
            return $this->sendAnswerError(['message' => 'Файл не существует', 'code' => 0], 404);
        }

        if ($file->chat_id != $user->id) {
            return $this->sendAnswerError(['message' => 'Доступ запрещен', 'code' => 0], 403);
        }

        $dirHashName = Toolkit::createHash($user->id);

        $filePath = config('folders.chats') . $dirHashName . DIRECTORY_SEPARATOR . $file->data;
        return $this->checkFile($filePath);
    }

    /**
     * проверка существования файла
     */
    protected function checkFile(string $filePath)
    {
        if (file_exists($filePath)) {
            $type = mime_content_type($filePath);
            return response(file_get_contents($filePath))->header('Content-Type', $type);
        } else {
            return $this->sendAnswerError(['message' => 'Файл не существует', 'code' => 0], 404);
        }
    }
}
