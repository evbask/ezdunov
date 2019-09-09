<?php

namespace App\Components;

use App\LogsPush;
use \Exception;

use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

/**
 * конвертирует изображение в Webp
 * @throws Exception
 */
class ImageWebp
{
    protected $files = [];
    protected $images = [];
    private $success = false;

    /**
     * путь куда сохранять файлы
     * @var 
     */
    protected $directory;

    public function __destruct()
    {
        if (!$this->success) {
            foreach ($this->images as $photo) {
                File::delete($this->directory . DIRECTORY_SEPARATOR . $photo);
            }
        }
    }

    /**
     * Конвертировать изображения
     * @throws Exception
     * @return boolean
     */
    public function convert()
    {
        if (!File::exists($this->directory)) {
            File::makeDirectory($this->directory, 0755, true);
        }

        try {
            foreach ($this->files as $photo) {
                $mimeType = $photo->getMimeType();
                $oldFullPath = $photo->getRealPath();
                $newFileName = Toolkit::getUniqName($this->directory, 'webp');
                $newFullPath = $this->directory . DIRECTORY_SEPARATOR . $newFileName;
                switch ($mimeType) {
                    case 'image/jpeg':
                        $image = imagecreatefromjpeg($oldFullPath);
                        break;
                    case 'image/png':
                        $image = imagecreatefrompng($oldFullPath);
                        break;
                    default:
                        throw New Exception('Произошла какая-то ошибка');
                }
                imagewebp($image, $newFullPath);
                imagedestroy($image);
                $this->images[] = $newFileName;
            }
        } catch (Exception $ex) {
            $this->errors[] = 'Что-то не так с одним из изображений: ' . $photo->getClientOriginalName() ?? 'Undefined';
            return $this->success = false;
        }
        return $this->success = true;
    }

    /**
     * передать массив
     * @param array|UploadedFile $files массив фото / фото
     * @param array|string $path путь
     * @throws Exception
     * @return void
     */
    public function build($files, $path)
    {
        switch (getType($files)) {
            case 'array':
                foreach ($files as $file) {
                    $this->files[] = $this->check($file);
                }
                break;
            case 'object':
                $this->files[] = $this->check($files);
                break;
            default:
                $this->errors[] = 'Неверный тип аргумента file';
                // throw new Exception('Не верный тип аргумента1');
        }
        $this->setPathSave($path);
    }

    /**
     * установить путь для сейва
     * @param array $path элементы пути
     * @throws Exception
     * @return void
     */
    public function setPathSave($path)
    {
        switch (getType($path)) {
            case 'array':
                $this->directory = implode(DIRECTORY_SEPARATOR, $path);
                break;
            case 'string':
                $this->directory = $path;
                break;
            default:
                $this->errors[] = 'Неверный тип аргумента path';
                // throw new Exception('Не верный тип аргумента1');
        }
    }

    /**
     * проверка класса
     * @throws Exception
     * @return UploadedFile
     */
    public function check($file)
    {
        if ($file instanceof UploadedFile) {
            return $file;
        } else {
            $this->errors[] = 'Неверный тип аргумента';
            // throw new Exception('Не верный тип аргумента2');
        }
    }

    /**
     * Вернет имена новых файлов
     * @return array
     */
    public function getNames()
    {
        return $this->images;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isErrors()
    {
        if (!empty($this->errors)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * удалить автоматически изображения, после работы
     */
    public function dontSave()
    {
        $this->success = false;
    }
}
