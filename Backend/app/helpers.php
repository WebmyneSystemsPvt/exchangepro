<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

if (!function_exists('getLoggedInUserId')) {
    function getLoggedInUserId()
    {
        return Auth::guard('api')->user()->id ?? null;
    }

    function requestLogStore($data,$flag) {
        if($flag == 'new_item_storage'){
            $directoryPath = storage_path('logs/newFileStorage');
            $filename = Str::uuid() . '.json';
            $filePath = $directoryPath . '/' . $filename;

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }
        }elseif($flag == 'update_item_storage'){
            $directoryPath = storage_path('logs/updateFileStorage');
            $filename = Str::uuid() . '.json';
            $filePath = $directoryPath . '/' . $filename;

            if (!File::exists($directoryPath)) {
                File::makeDirectory($directoryPath, 0755, true);
            }
        }
        $jsonData = json_encode($data, JSON_PRETTY_PRINT);

        File::put($filePath, $jsonData);
    }
}
