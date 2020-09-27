<?php
declare(strict_types=1);
namespace app\modules\common\services;

use app\modules\common\enums\OssPathEnum;
use app\modules\common\enums\OssTypeEnum;
use app\modules\common\lib\OssUpload;
use app\modules\common\models\common\ImageModel;
use app\modules\common\models\common\ImageTmpModel;
use yii\helpers\ArrayHelper;

class ImageService
{
    const MAX_IMG_SIZE = 2 * 1024 * 1024;
    private $imageModel;
    private $imageTmpModel;
    public function __construct(ImageModel $imageModel, ImageTmpModel $imageTmpModel)
    {
        $this->imageModel = $imageModel;
        $this->imageTmpModel = $imageTmpModel;
    }

    public function createImageTmp(int $userId, int $type, array $fileInfo)
    {
        if (in_array($type, OssPathEnum::IS_NOT_OSS)) {
            list($localPath, $extension) = OssUpload::fielInfo($fileInfo, OssTypeEnum::IMAGE);
            $path = '';
        } else {
            list($path, $localPath, $extension) = OssUpload::upload($fileInfo, $type, OssTypeEnum::IMAGE);
        }
        $params = $this->getParams($userId, $type, $path, 0, $localPath, $extension);
        $imageTmp = $this->imageTmpModel->create($params);
        return $imageTmp->id;
    }

    private function createImage(int $userId, int $type, int $objectId, string $path, string $localPath, string $extension)
    {
        $params = $this->getParams($userId, $type, $path, $objectId, $localPath, $extension);
        $this->imageModel->create($params);
    }

    /**
     * 操作图片，返回本地图片路径列表
     *
     * @param integer $userId
     * @param integer $objectId
     * @param array $imageIds
     * @param boolean $isDeleteOriginal
     * @return void
     */
    public function operationImage(int $userId, int $objectId, array $imageIds, bool $isDeleteOriginal = false)
    {
        $paths = [];
        $imageTmps = $this->findImageTmpByIds($imageIds);
        foreach ($imageTmps as $imageTmp) {
            $type = (int)$imageTmp['type'];
            $path = $imageTmp['path'];
            $paths[] = $imageTmp['local_path'];
            $this->createImage($userId, $type, $objectId, $path, $imageTmp['local_path'], $imageTmp['extension']);
        }
        $this->deleteImageTmpByIds($imageIds);
        if ($isDeleteOriginal) {
            foreach ($paths as $path) {
                @unlink($path);
            }
        }
        return $paths;
    }

    /**
     * 返回阿里远端链接列表
     *
     * @param integer $userId
     * @param integer $objectId
     * @param array $imageIds
     * @return void
     */
    public function getPathFromOperationImage(int $userId, int $objectId, array $imageIds)
    {
        $paths = [];
        $imageTmps = $this->findImageTmpByIds($imageIds);
        foreach ($imageTmps as $imageTmp) {
            $type = (int)$imageTmp['type'];
            $paths[] = $imageTmp['path'];
            $path = $imageTmp['local_path'];
            $this->createImage($userId, $type, $objectId, $path, $imageTmp['local_path'], $imageTmp['extension']);
        }
        $this->deleteImageTmpByIds($imageIds);

        return $paths;
    }

    public function getUlrByImageTmpIds(array $ids)
    {
        $images = $this->findImageTmpByIds($ids);
        $urls = array_map(function($image){
            return \Yii::$app->params['oss']['host'] . '/' . $image['path'];
        }, $images);
        return $urls;
    }

    public function findImageTmpByIds(array $ids)
    {
        return $this->imageTmpModel->findByIds($ids);
    }

    public function deleteImageTmpByIds(array $ids)
    {
        $this->imageTmpModel->deleteByIds($ids);
    }

    public function deleteByObjectIdAndType(int $objectId, int $type)
    {
        $this->imageModel->deleteByObjectIdAndType($objectId, $type);
    }

    public function findByObjectIdAndType(int $objectId, int $type)
    {
        return $this->imageModel->findByObjectIdAndType($objectId, $type);
    }

    public function getUrlByObjectIdAndType(int $objectId, int $type)
    {
        $images = $this->findByObjectIdAndType($objectId, $type);
        $urls = array_map(function($image){
            return \Yii::$app->params['oss']['host'] . '/' . $image['path'];
        }, $images);
        return $urls;
    }

    private function getParams(int $userId, int $type, string $path, $objectId = 0, string $localPath = '', string $extension)
    {
        $currentTime = time();
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'path' => $path,
            'extension' => $extension,
            'created_time' => $currentTime,
            'updated_time' => $currentTime
        ];
        if ($objectId) {
            $data['object_id'] = $objectId;
        }
        if ($localPath){
            $data['local_path'] = $localPath;
        }
        return $data;
    }
}
