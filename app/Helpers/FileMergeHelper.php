<?php
/**
 * Created by PhpStorm.
 * User: joris
 * Date: 16.03.18
 * Time: 18:37
 */

namespace App\Helpers;


class FileMergeHelper
{

    public static function mergeJsonFiles($originalFile, $fileToMerge)
    {
        $newJson = is_file($fileToMerge) ? json_decode(file_get_contents($fileToMerge), true) : [];
        self::mergeJsonIntoFile($originalFile, $newJson);
    }

    public static function mergeJsonIntoFile($originalFile, $jsonToMerge = [])
    {
        $originalJson = is_file($originalFile) ? json_decode(file_get_contents($originalFile), true) : [];
        $mergedJson = self::mergeJsonArrays($originalJson, $jsonToMerge);

        file_put_contents($originalFile, json_encode($mergedJson, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }

    public static function mergeJsonArrays($priority_json, $merge_json)
    {
        foreach ($merge_json as $merge_content_key => $merge_content_value) {
            if (!array_key_exists($merge_content_key, $priority_json)) {
                $priority_json[$merge_content_key] = $merge_content_value;
            } elseif (!is_string($merge_content_value)) {
                $priority_json[$merge_content_key] = self::mergeJsonArrays($priority_json[$merge_content_key], $merge_content_value);
            } else {
                // UPDATE: Dont merge intro "priority" file, when key already exists

                // $value = is_array($merge_content_value) ? $merge_content_value : [$merge_content_value];
                // $priority_json = array_merge($priority_json, $value);
            }
        }
        return $priority_json;
    }

    public static function applyFileMerges($destinationFile, $contentsToMerge)
    {

        if(is_file($destinationFile) && file_exists($destinationFile)) {

            $start = '<<<<<<<' . PHP_EOL;
            $end = '>>>>>>>';
            $pattern = "/$start(.*?)$end/s";

            preg_match_all($pattern, $contentsToMerge, $matches);

            foreach ($matches[1] as $mergePair) {
                $mergePairParts = explode('=======', $mergePair);

                $find = ltrim($mergePairParts[0]);
                $replace = ltrim($mergePairParts[1]);
                FileHelper::findAndReplaceInFile($destinationFile, $find, $replace);
            }
        }

    }


}
