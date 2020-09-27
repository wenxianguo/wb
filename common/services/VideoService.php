<?php
declare(strict_types=1);
namespace app\modules\common\services;

use app\modules\common\enums\OssTypeEnum;
use app\modules\common\lib\OssUpload;
use app\modules\common\models\common\VideoModel;
use app\modules\common\models\common\VideoTmpModel;
use \Yii;

class VideoService
{
    private $videoModel;
    private $videoTmpModel;
    public function __construct(VideoModel $videoModel, VideoTmpModel $videoTmpModel)
    {
        $this->videoModel = $videoModel;
        $this->videoTmpModel = $videoTmpModel;
    }

    public function createVideoTmp(int $userId, int $type, array $fileInfo)
    {
        list($path, $localPath) = OssUpload::upload($fileInfo, $type, OssTypeEnum::VIDEO, true);
        $params = $this->getParams($userId, $type, $path, 0, $localPath);
        $videoTmp = $this->videoTmpModel->create($params);
        return $videoTmp->id;
    }

    private function createVideo(int $userId, int $type, int $objectId, string $path)
    {
        $params = $this->getParams($userId, $type, $path, $objectId);
        $this->videoModel->create($params);
    }

    public function operationVideo(int $userId, int $objectId, array $imageIds, bool $isDeleteOriginal = false)
    {
        $paths = [];
        $videoTmps = $this->findVideoTmpByIds($imageIds);
        foreach ($videoTmps as $videoTmp) {
            $type = (int)$videoTmp['type'];
            $path = $videoTmp['path'];
            $paths[] = $videoTmp['local_path'];
            $this->createVideo($userId, $type, $objectId, $path);
        }
        $this->deleteVideoTmpByIds($imageIds);
        if ($isDeleteOriginal) {
            foreach ($paths as $path) {
                @unlink($path);
            }
        }
        return $paths;
    }

    public function getUlrByVideoTmpIds(array $ids)
    {
        $videos = $this->findVideoTmpByIds($ids);
        $urls = array_map(function($video){
            return Yii::$app->params['oss']['host'] . '/' . $video['path'];
        }, $videos);
        return $urls;
    }

    public function findVideoTmpByIds(array $ids)
    {
        return $this->videoTmpModel->findByIds($ids);
    }

    public function deleteVideoTmpByIds(array $ids)
    {
        $this->videoTmpModel->deleteByIds($ids);
    }

    public function findByObjectIdAndType(int $objectId, int $type)
    {
        return $this->videoModel->findByObjectIdAndType($objectId, $type);
    }

    private function getParams(int $userId, int $type, string $path, $objectId = 0, string $localPath = '')
    {
        $currentTime = time();
        $data = [
            'user_id' => $userId,
            'type' => $type,
            'path' => $path,
            'created_time' => $currentTime,
            'updated_time' => $currentTime
        ];
        if ($objectId) {
            $data['object_id'] = $objectId;
        } else {
            $data['local_path'] = $localPath;
        }
        return $data;
    }
}
