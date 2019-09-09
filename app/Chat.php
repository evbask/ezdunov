<?php

namespace App;

use Auth;
use Carbon\Carbon;
use Exception;
use User;
use App\Components\Toolkit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; 

/**
 * @property int $id 
 * @property int $chat_id ид чата
 * @property int $user_id ид пользователя отправившего сообщение
 * @property string $data
 * @property int $type
 * @property boolean $viewed
 * @todo JIorD 2019/01/19 написано лиш бы было, работу с файлами вынести
 * в отдельный общий класс, так же скорее всего нужно вынести логику build в класс
 */
class Chat extends Model
{
    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'chat_id',
        'user_id',
        'data',
        'type',
        'viewed'
    ];

    const T_MESSAGE = 1;
    const T_FILE    = 2;

    protected $user;
    protected $directory;
    protected $request;
    protected $files = [];
    protected $errors = [];
    private $success = false;

    /**
     * удаляем файлы если что то пошло не так
     */
    public function __destruct()
    {
        if (!$this->success && $this->files) {
            foreach ($this->files as $file) {
                File::delete($this->directory . DIRECTORY_SEPARATOR . $file);
            }
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * заполняет необходимые атрибуты
     */
    public function build(Request $request)
    {
        $this->user = $request->user();
        /**
         * @todo условимся на том что юзер не передает chat_id, требуется переделать нормально
         * добавить проверку по типу пользователя
         * или впринципе переделать логику
         */
        $this->chat_id = $request->chat_id ?? $this->user->id;
        $this->user_id = $this->user->id;
        $this->viewed = false;
        $dirHashName = Toolkit::createHash($this->user->id);

        $this->directory = config('folders.chats') . $dirHashName;
        $this->request = $request;

        switch ($this->request->type) {
            case self::T_MESSAGE:
                $this->data = $this->request->data;
                $this->type = self::T_MESSAGE;
                break;
            case self::T_FILE:
                $this->fileSave();
                $this->type = self::T_FILE;
                break;
        }
    }

    /**
     * сохраняет изображения на диск 
     * и заполняет массив photo полученных файлов
     * @return bool
     * @todo JIorD 2018/01/16 вынести работу с файлами в отдельные класс, который будет отвечать
     * за обработку любых файлов пришедших из запросов, необходимо делать валидацию запроса
     * то что попадает в класс должно быть обработано как это возможно (сжать, пересоздать и т.д)
     */
    protected function fileSave()
    {
        if (!File::exists($this->directory)) {
            File::makeDirectory($this->directory, 0755, true);
        }

        if (!$this->request->hasFile('files')) {
            $this->errors[] = 'Файлы не найдены';
            return false;
        }

        try {
            foreach ($this->request->file('files') as $file) {
                $mimeType = $file->getMimeType();
                $ext = $file->extension();
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $ext = 'webp';
                }
                $oldFullPath = $file->getRealPath();
                $newFileName = Toolkit::getUniqName($this->directory, $ext);
                $newFullPath = $this->directory . DIRECTORY_SEPARATOR . $newFileName;
                switch ($mimeType) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($oldFullPath);
                        imagewebp($image, $newFullPath);
                        imagedestroy($image);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($oldFullPath);
                        imagewebp($image, $newFullPath);
                        imagedestroy($image);
                        break;
                    case 'application/msword':
                    case 'application/vnd.ms-excel':
                    case 'application/pdf':
                    case 'text/plain':
                        File::move($oldFullPath, $newFullPath);
                        break;
                    default:
                        throw New Exception('Произошла какая-то ошибка');
                }
                $this->files[] = $newFileName;
            }
        } catch (Exception $ex) {
            $this->errors[] = 'Что-то не так с файлом:' . $file->getClientOriginalName();
            return false;
        }
        return true;
    }

    /**
     * добавить новое сообщение
     */
    public function add()
    {
        if ($this->errors) {
            return false;
        }

        if ($this->type == self::T_MESSAGE) {
            $this->save();
            return true;
        }

        if ($this->type == self::T_FILE) {
            foreach ($this->files as $file) {
                self::create([
                    'chat_id'   => $this->chat_id,
                    'user_id'   => $this->user_id,
                    'data'      => $file,
                    'type'      => $this->type,
                    'viewed'    => $this->viewed
                ]);
            }
            $this->success = true;
            return true;
        }
    }

    /**
     * получить сообщения в виде массива
     */
    public static function getArrayMessage(User $user = null)
    {
        $user_id = Auth::user()->id;
        if($user !== null && Auth::user()->checkGroup('staff')){
            $user_id = $user->id;
        }
        $messages = self::where('chat_id', $user_id)->orderBy('id', 'ASC')->get();

        $arrayMessages = [];

        $user_id_hash = Toolkit::createHash($user_id);
        $base_url = config('urls.chat_files').$user_id_hash.DIRECTORY_SEPARATOR;

        foreach ($messages as $message) {

            if($message->type == 2) {
                $message->data = $base_url . $message->data; //полный путь до файла нужен для фронта
            }
                $arrayMessages[] = [
                    'id'        => $message->id,
                    'user_id'   => $message->user_id,
                    'chat_id'   => $message->chat_id,
                    'data'      => $message->data,
                    'type'      => $message->type,
                    'viewed'    => $message->viewed,
                    'created'   => $message->created_at
                ];
            }

        return $arrayMessages;
    }

    public static function getAdminMessages(){
        return self::where('chat_id', Auth::user()->id)->where('user_id', '!=', Auth::user()->id)->where('viewed', false);
    }

    /**
     * =========          мутаторы           =========
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = trim(htmlspecialchars($value));
    }



    public function getCreatedAtAttribute($date)
    {
        return Toolkit::GetNormalDate($date);
        //return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m H:i');
    }

    /**
     * =========          релейшены          =========
     */
}
