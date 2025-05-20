<?php

namespace app\hrose;
/**
 * Simple autoloader, so we don't need Composer just for this.
 */
class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
            if (file_exists(DOC_ROOT . $file)) {
                require DOC_ROOT . $file;
                return true;
            } else {
                error_log("Autoloader: File not found for class $class at " . DOC_ROOT . "$file");
            }
            return false;
        });
    }
}