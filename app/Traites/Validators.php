<?php

namespace App\Traites;
use App\Rules\RequestExistence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait Validators
{
    public function validatePassport(Request $request)
    {
        return Validator::make(
            array_merge($request->all(), ["user_id" => $request->user()->id]),
            [
            'user_id'       =>      ['unique:pasport_verify_requests'],
            'passport_number' =>    ['required', 'string', 'regex:/^\d{4}\s\d{6}$/','unique:pasport_verify_requests'],
            'name' =>               ['required', 'string'],
            'date_of_birth' =>      ['required', 'date', 'after:1900-01-01', 'before:'.(new \DateTime())->modify('-5 years')->format('Y-m-d')],
            'passport_photo' =>     ['required', 'array','min:3'],
            'passport_photo.*' =>   ['required', 'image','mimes:jpg,jpeg,png'],
        ],
        ['user_id.unique' => 'Вы уже отправили заявку.']);
    }

    public function validateUpdatedPassport(Request $request,$id){
        return Validator::make(
            $request->all(),
            [
                'passport_number' =>    ['required', 'string', 'regex:/^\d{4}\s\d{6}$/','unique:pasport_verify_requests,passport_number,'.$id],
                'date_of_birth' =>  ['required', 'date', 'after:1900-01-01', 'before:'.(new \DateTime())->modify('-5 years')->format('Y-m-d')],
                'passport_photo.*' =>   ['image','mimes:jpg,jpeg,png'],
            ],
            ['passport_photo.*' => 'Принимаются только файлы форматов jpg и png.']
        );
    }

}