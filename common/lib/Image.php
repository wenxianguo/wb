<?php

namespace app\modules\common\lib;

use app\exceptions\ServiceException;
use app\response\ErrorCode;
use Yii;

class Image
{
    const DEFAULT_DIR = '/storage/upload/';
   
    /**
     * 上传图片
     *
     * @param array $fileInfo
     * @param string $fileName
     * @return string
     */
    public static function uploadImage(array $fileInfo, string $fileUrl = '')
    {
        $name = $fileInfo['name'] ?? [];
        $tmpName = $fileInfo['tmp_name'] ?? [];
        $pathInfo = pathinfo($name);
        $fileUrl = $fileUrl ?: self::getFileUrl($pathInfo['extension']);
        self::upload( $tmpName, $fileUrl);
        return $fileUrl;
    }

    public static function updateImageBase64(array $image, string $fileUrl = '')
    {
        $fileUrl = $fileUrl ?: self::getFileUrl($image['type']);
        file_put_contents($fileUrl, base64_decode($image['src']));
    }

    /**
     * 批量上传图片
     *
     * @param array $fileInfos
     * @param array $fileNames
     * @return array
     */
    public static function updateImages(array $fileInfos, array $fileUrls = [])
    {
        $count = count($fileInfos['name']);
        $filePaths = [];
        for($i = 0; $i < $count; $i++) {
            $name = $fileInfos['name'][$i] ?? [];
            $tmpName = $fileInfos['tmp_name'][$i] ?? [];
            $pathInfo = pathinfo($name);
            $fileUrl = $fileUrls[$i] ?? self::getFileUrl($pathInfo['extension']);
            self::upload($tmpName, $fileUrl);
            $filePaths[] = $fileUrl;
        }
        return $filePaths;
    }

    public static function updateImagesBase64(array $images, array $fileUrls = [])
    {
        foreach($images as $i => $image) {
            $fileUrl = $fileUrls[$i] ?? '';
            self::updateImageBase64($image, $fileUrl);
        }
    }

    public static function upload(string $tmpName, string $filePath)
    {
        if (!is_uploaded_file($tmpName)) {
            return '';
        }

        $filePath = Yii::$app->basePath . '/web' . $filePath;
        if (!move_uploaded_file($tmpName, $filePath)) {
            ServiceException::send(ErrorCode::FILE_MOVEMENT_FAILED);
        }
    }

    private static function getFileUrl(string $extension)
    {
        $name = self::getName($extension);
        $fileUrl = self::DEFAULT_DIR . $name;
        return $fileUrl;
    }

    private static function getName(string $extension)
    {
        $mic = microtime();
        $micArr = explode(' ', $mic);
        $fileName = $micArr[1] . intval($micArr[0] * 1000) . rand(0, 1000) . '.' . $extension;
        return $fileName;
    }
}
