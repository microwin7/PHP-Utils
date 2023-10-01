<?php

namespace Microwin7\PHPUtils\Exceptions;

class FileUploadException extends \Exception
{
    public function __construct($code)
    {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }
    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = 'Размер принятого файла превысил максимально допустимый размер для загрузки';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = 'Превышен размер загружаемого файла';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = 'Файл был получен только частично';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = 'Файл не был загружен';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = 'Файл не загружен - отсутствует временная директория';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = 'Не удалось записать файл на диск';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = 'PHP-расширение остановило загрузку файла';
                break;
            case 9:
                $message = 'Не удалось загрузить файл';
            default:
                $message = 'Неизвестная ошибка загрузки';
                break;
        }
        return $message;
    }
}
