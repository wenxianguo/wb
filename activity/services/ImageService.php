<?php
namespace app\modules\activity\services;

use app\modules\common\lib\Image;
use app\modules\common\models\activity\ImageModel;

class ImageService
{
    private $imageModel;
    public function __construct(ImageModel $imageModel, ActivityService $activityService)
    {
        $this->imageModel = $imageModel;
        $this->activityService = $activityService;
    }

    /**
     * 创建
     *
     * @param integer $userId
     * @param integer $type
     * @param integer $objectId
     * @param string $url
     * @param array $image
     * @return id
     */
    public function create(int $userId, int $type, int $objectId, string $url, array $image)
    {
        if($url) {
            $paths = explode(WB_API, $url);
            $path = $paths[1];
        } else {
            $path = Image::uploadImage($image);
        }
        $id = $this->storage($userId, $type, $objectId, $path);
        return $id;
    }

    public function storage(int $userId, int $type, int $objectId, string $path)
    {
        $params = $this->getParams($userId, $type, $objectId, $path);
        $params['created_time'] = time();
        $imageModel = $this->imageModel->create($params);
        return $imageModel->id;
    }
    
    /**
     * 更新
     *
     * @param integer $id
     * @param string $url
     * @param array $image
     * @return id
     */
    public function update(int $id, string $url, array $image)
    {
        $imageModel = $this->imageModel->findById($id);
        if($url) {
            $paths = explode(WB_API, $url);
            $path = $paths[1];
        } else {
            $path = Image::uploadImage($image);
        }
        $params = [
            'path' => $path,
            'updated_time' => time()
        ];
        $imageModel = $this->imageModel->updateModel($imageModel, $params);
    }

    public function list(int $objectId, int $type)
    {
        return $this->imageModel->findByObjectIdAndType($objectId, $type);
    }

    public function deleteById(int $id)
    {
        return $this->imageModel->deleteById($id);
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @param integer $type
     * @param integer $objectId
     * @param string $path
     * @return array
     */
    private function getParams(int $userId, int $type, int $objectId, string $path)
    {
        $params = [
            'user_id' => $userId,
            'object_id' => $objectId,
            'type' => $type,
            'path' => $path,
        ];
        return $params;
    }
}
