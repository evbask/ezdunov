<?php

namespace App\Components;

use Exception;
use Request;
use Validator;

use App\Rent;
use App\PreRentPhoto as Photo;
use App\Components\ImageWebp;

use Illuminate\Support\Facades\File;

class PreRentProblem
{
    protected $errors = [];

    /**
     * объект аренды
     * @var Rent
     */
    protected $Rent;

    /** 
     * объект для работы с картинками 
     * @var ImageWebp 
     */
    protected $ImageWebp;

    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Rent $rent
     */
    public function build(Rent $rent)
    {
        $validator = Validator::make(Request::all(), [
            'problem.rent_photo'    => ['array', 'max:10'],
            'problem.rent_photo.*'  => ['image','mimes:jpg,jpeg,png'],
        ]);
        if ($validator->fails()) {
            $this->errors[] = $validator->errors()->all()[0];
        }

        $this->Rent = $rent;

        $path = config('folders.pre_rent_photos') . $this->Rent->vehicle_id;

        $this->ImageWebp = new ImageWebp();
        $this->ImageWebp->build(Request::file('problem.rent_photo'), $path);

        if (!$this->ImageWebp->convert()) {
            $this->errors += $this->ImageWebp->getErrors();
        }
    }

    public function add()
    {
        if ($this->isErrors()) {
            return false;
        }

        try {
            foreach ($this->ImageWebp->getNames() as $photo) {
                Photo::create([
                    'photo_name'            =>  $photo,
                    'active'                =>  true,
                    'rent_id'               =>  $this->Rent->id,
                    'vehicle_id'            =>  $this->Rent->vehicle_id,  
                ]);
            }
        } catch (Exception $e) {
            $this->errors[] = 'Что то пошло не так';
            $this->ImageWebp->dontSave();
            return false;
        }
        return true;
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
