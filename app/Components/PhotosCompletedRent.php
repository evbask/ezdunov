<?php

namespace App\Components;

use \Exception;
use Request;

use App\Rent;
use App\PhotosCompletedRent as Photo;
use App\Components\ImageWebp;

use Illuminate\Support\Facades\File;

class PhotosCompletedRent
{
    protected $errors = [];
    protected $user_id;
    protected $rent_id;
    protected $vehicle_id;

    /** 
     * объект для работы с картинками 
     * @var ImageWebp 
     */
    protected $ImageWebp;

    public function add()
    {
        if ($this->isErrors()) {
            return false;
        }

        try {
            foreach($this->ImageWebp->getNames() as $photo) {
                Photo::create([
                    'user_id'   =>  $this->user_id,
                    'rent_id'   =>  $this->rent_id,
                    'vehicle_id'=>  $this->vehicle_id,
                    'photo'     =>  $photo,
                ]);
            } 
        } catch (Exception $e) {
            $this->errors[] = 'Что то пошло не так';
            $this->ImageWebp->dontSave();
            return false;
        }
        return true;
    }

    /**
     * @param object $rent Rent
     */
    public function build(Rent $rent)
    {
        $path = config('folders.post_rent_photos') . $rent->vehicle_id;

        $this->rent_id = $rent->id;
        $this->user_id = $rent->user_id;
        $this->vehicle_id = $rent->vehicle_id;

        $this->ImageWebp = new ImageWebp();
        $this->ImageWebp->build(Request::file('photo'), $path);

        if (!$this->ImageWebp->convert()) {
            $this->errors += $this->ImageWebp->getErrors();
            return false;
        } else {
            return true;
        }
    }

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
