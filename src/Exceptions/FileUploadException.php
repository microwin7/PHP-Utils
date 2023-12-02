<?php

namespace Microwin7\PHPUtils\Exceptions;

class FileUploadException extends \Exception
{
    public function __construct(int $code)
    {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }
    private function codeToMessage(int $code)
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'Размер принятого файла превысил максимально допустимый размер для загрузки',
            UPLOAD_ERR_FORM_SIZE => 'Превышен размер загружаемого файла',
            UPLOAD_ERR_PARTIAL => 'Файл был получен только частично',
            UPLOAD_ERR_NO_FILE => 'Файл не был загружен',
            UPLOAD_ERR_NO_TMP_DIR => 'Файл не загружен - отсутствует временная директория',
            UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск',
            UPLOAD_ERR_EXTENSION => 'PHP-расширение остановило загрузку файла',
            9 => 'Не удалось загрузить файл',
            default => 'Неизвестная ошибка загрузки'
        };
    }
}
