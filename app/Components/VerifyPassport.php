<?php

namespace App\Components;

use Exception;
use App\PasportVerifyRequest;
use App\PasportVerifyPhoto;
use App\Components\Toolkit;
use App\Components\ImageWebp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class VerifyPassport
{
    protected $user;
    protected $request;
    protected $errors = [];
    protected $path;
    protected $has_files;

    /** объект для работы с картинками @var ImageWebp */
    protected $ImageWebp;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->user = $request->user();
        $this->request = $request;

        if($request->hasFile('passport_photo')){
            $this->has_files = true;

            $path = config('folders.passport_photos') . Toolkit::createHash($this->user->id);
            $this->path = $path;

            $this->ImageWebp = new ImageWebp();
            $this->ImageWebp->build($request->file('passport_photo'), $this->path);

            if (!$this->ImageWebp->convert()) {
                $this->errors += $this->ImageWebp->getErrors();
            }
        }
    }

    /**
     * сохраняет изображения и данные пользователя
     * @return bool
     */
    public function add()
    {
        if ($this->isErrors()) {
            return false;
        }

        try {
            DB::beginTransaction();
            
            $passport = PasportVerifyRequest::updateOrCreate(
                ['user_id'=> $this->user->id],
                [
                    'date_of_birth'     => strtotime($this->request->date_of_birth),
                    'passport_number'   => $this->request->passport_number,
                    'user_fio'          => $this->request->name,
                    'request_status'    => PasportVerifyRequest::T_REQUEST_SENT,
                ]
            );

            if($this->has_files){
                foreach ($this->ImageWebp->getNames() as $photo) {
                    PasportVerifyPhoto::create([
                        'img_url'       =>  $photo,
                        'request_id'    =>  $passport->id,
                    ]);
                }
            }

            DB::commit();
        } catch (Exception $ex) {
            DB::rollback();
            if($this->has_files) {
                $this->ImageWebp->dontSave();
            }
            $this->errors[] = 'Что-то пошло не так';
            return false;
        }
        return true; 
    }

    /**
     * удаляет прикрепленные юзером старые фото
     * @return bool
     */
    public function  deleteOldPhotos($images_id){
        $photos = PasportVerifyPhoto::where('request_id', $this->user->passport->id)->whereIn('id', $images_id);
        array_map('unlink', $photos->get()->pluck('full_name')->toArray());
        $photos->delete();
    }
    /**
     * вернет возникшие ошибки
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function isErrors()
    {
        if (!empty($this->errors)) {
            return true;
        } else {
            return false;
        }
    }
}
