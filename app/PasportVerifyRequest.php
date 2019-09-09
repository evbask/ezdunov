<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

use App\Traites\ApiArrays;
use App\Components\Toolkit;
use App\User;
class PasportVerifyRequest extends Model{

    
    use ApiArrays;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'pasport_verify_requests';
    public $primaryKey = 'id';

    const T_REQUEST_SENT            = 1;
    const T_REQUEST_NEED_CORRECTION = 2;
    const T_REQUEST_DENIED          = 3;
    const T_REQUEST_ACCEPTED        = 4;

    const T_STRINGS = [
        self::T_REQUEST_SENT            => 'отправлена на проверку',
        self::T_REQUEST_NEED_CORRECTION => 'нуждается в корректировке',
        self::T_REQUEST_DENIED          => 'отклонена',
        self::T_REQUEST_ACCEPTED        => 'одобрена'
    ];

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'passport_number',
        'user_fio',
        'comment_to_user',
        'comment_to_manager',
        'request_status'
    ];

    /** Какие поля выводить в api json ответах для профиля пользователя */
    protected $forSmallArray = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
        'user_updated_id',
        'user_fio',
        'request_status'
    ];

    /** Какие поля выводить в api json ответах для профиля пользователя */
    protected $forBigArray = [
        'id',
        'date_of_birth',
        'passport_number',
        'user_fio',
        'comment_to_user',
        'comment_to_manager',
        'request_status'
    ];

   
    public function afterGetSmallArray(array $array) : array{
        $user = User::find($array['user_id']);
        $user_updated = User::find($array['user_updated_id']);
        $array['user'] = $array['user_fio'];
        if($user_updated) {
            $array['user_updated'] = $user_updated->name;
        } else {
            $array['user_updated'] = ' ';
        }
        $array['request_status'] = self::T_STRINGS[$array['request_status']];
        $array['created_at'] = Toolkit::GetNormalDate($array['created_at']->timestamp);
        $array['updated_at'] = Toolkit::GetNormalDate($array['updated_at']->timestamp);
        return $array;
    }

    public function afterGetBigArray($array){
        $array['date_of_birth'] = date("Y-m-d", $array['date_of_birth']);
        $passport_number = str_replace(" ","", $array['passport_number']);
        $serial = substr($passport_number, 0,4);
        $number = substr($passport_number, 4);

        $array['serial'] = $serial;
        $array['number'] = $number;

        $passport_photos = PasportVerifyPhoto::where('request_id', $this->id)->get();
        $array['photos'] = [];

        $user_id_hash = Toolkit::createHash($this->user_id);

        $base_url = config('urls.passport_photos').$user_id_hash.DIRECTORY_SEPARATOR;
        
        foreach($passport_photos as $photo) {
            $array['photos'][] = ['id' => $photo->id,'url' => $base_url.$photo->img_url];
        }

        return $array;
    }
}