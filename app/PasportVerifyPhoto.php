<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use App\Components\Toolkit;
use Illuminate\Support\Facades\Auth;
class PasportVerifyPhoto extends Model{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'pasport_verify_photos';

    protected $fillable = [
        'request_id',
        'img_url'
    ];

    public function getFullNameAttribute(){
        return config('folders.passport_photos').Toolkit::createHash(Auth::user()->id) . DIRECTORY_SEPARATOR . $this->img_url;
    }

}