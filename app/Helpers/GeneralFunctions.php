<?php

namespace App\Helpers;

class GeneralFunctions
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

    public static function getLogPath(string $type, string $storageDir = 'logs')
    {
        $pathArray = [
            storage_path($storageDir),
            $type,
            date("Y"),
            date("m")
        ];

        $dirPath = implode("/", $pathArray);

        if (!is_dir($dirPath)) {
            $newDir = $pathArray[0];
            foreach (array_slice($pathArray, 1) as $onePart) {
                $newDir .= "/".$onePart;
                if (!is_dir($newDir))
                    mkdir($newDir, 0755);
            }
        }

        return $dirPath . "/" . date('d').".log";
    }
}
