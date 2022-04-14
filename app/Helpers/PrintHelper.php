<?php

namespace App\Helpers;

class PrintHelper
{
    public static function printMessage($message)
    {
        ob_start();
        if (is_scalar($message))
            echo $message.PHP_EOL;
        else
            print_r($message);
        $string = ob_get_contents();
        ob_end_clean();

        return $string;
    }

    public static function printConsoleMessage($message)
    {
        $string = self::printMessage($message);

        fwrite(\STDOUT, $string);
    }
}
