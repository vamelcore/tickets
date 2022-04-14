<?php

namespace App\Helpers;

class StorageHelper
{
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
