<?php

namespace app\modules\common\lib;


use app\exceptions\ServiceException;
use app\modules\common\enums\OssPathEnum;
use app\modules\common\enums\OssTypeEnum;
use app\response\ErrorCode;
use BaseComponents\base\AliyunOss;
use Yii;

class OssUpload
{
    const MAX_IMG_SIZE = 2 * 1024 * 1024;
    const MAX_VIDEO_SIZE = 10 * 1024 * 1024;

    public static function fielInfo(array $fileInfo, string $ossType = OssTypeEnum::IMAGE) 
    {
        if (!is_uploaded_file($fileInfo['tmp_name'])) {
            return '';
        }

        if ($ossType == OssTypeEnum::IMAGE && strpos($fileInfo['type'], OssTypeEnum::IMAGE) !== false) {
            if ($fileInfo['size'] > self::MAX_IMG_SIZE) {
                ServiceException::send(ErrorCode::FILE_SIZE_GT_2M);
            }
        }elseif ($ossType == OssTypeEnum::VIDEO && strpos($fileInfo['type'], OssTypeEnum::VIDEO) !== false) {
            if ($fileInfo['size'] > self::MAX_VIDEO_SIZE) {
                ServiceException::send(ErrorCode::VIDEO_SIZE_GT_10M);
            }
        } else {
            ServiceException::send(ErrorCode::FILE_TYPE_ILLEGAL);
        }

        $pathInfo = pathinfo($fileInfo['name']);

        $fileSavePath = Yii::$app->basePath . '/web/storage/';
        $fileName = self::getFileName($pathInfo['extension']);
        $dir = 'upload/oss';
        $fileUrl = $dir  . '/' . $fileName;
        #本地保存文件的路径
        $saveFileName = $fileSavePath . $fileUrl;

        return [$saveFileName, $pathInfo['extension']];
    }
    public static function upload(array $fileInfo, int $ossPath, string $ossType = OssTypeEnum::IMAGE, bool $isDelete = false)
    {
        list($saveFileName, $extension) = self::fielInfo($fileInfo, $ossType);

        if (!move_uploaded_file($fileInfo['tmp_name'], $saveFileName)) {
            ServiceException::send(ErrorCode::FILE_MOVEMENT_FAILED);
        }
        $ossPath = self::getOssPath($ossPath, $ossType);
        $fileObject = (new AliyunOss())->uploadLocalFile($saveFileName, $ossPath);
        if (empty($fileObject)) {
            ServiceException::send(ErrorCode::FILE_UPLOAD_FAILED);
        }
        if ($isDelete) {
            unlink($saveFileName);//删除本地资源
        }

        return [$fileObject, $saveFileName, $extension];
    }

    private static function getFileName(string $extension)
    {
        $mic = microtime();
        $micArr = explode(' ', $mic);
        $fileName = $micArr[1] . intval($micArr[0] * 1000) . rand(0, 1000) . '.' . $extension;
        return $fileName;
    }

    private static function getOssPath(int $ossPath, string $ossType = OssTypeEnum::IMAGE)
    {
        $path = '';
        if (!in_array(ENVIRONMENT,['pre_release', 'production'])) {
            $path .= ENVIRONMENT . '/';
        }
        $path .= OssPathEnum::getMessage($ossPath) . '/' . $ossType;
        $date = date('Y_m_d', time());
        list($year, $month, $day) = explode('_', $date);
        $path .= '/' . $year . '/' . $month . '/' . $day . '/';
        return $path;
    }

}
