<?php

namespace app\modules\common\models;

use app\exceptions\ServiceException;
use app\modules\common\events\BatchInsertEvent;
use app\modules\common\lib\ElkLogType;
use Mobvista\MixTools\Src\Elk\Elk;
use Yii;
use yii\base\Event;
use yii\caching\Cache;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Command;

abstract class BaseModel extends ActiveRecord
{
    public $cacheTime = 3600;
    private $cache;
    public $softDelete = true;

    const EVENT_AFTER_BATCH_INSERT = 'afterBatchInsert';

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'addCallback']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'updateCallback']);
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'deleteCallback']);
        $this->on(self::EVENT_AFTER_BATCH_INSERT, [$this, 'BatchInsertCallback']);
    }

    /** @var ActiveQuery $activeQuery */
    public static $activeQuery;

    public static function find()
    {
        $activeQuery =  parent::find();
        self::$activeQuery = $activeQuery;
        return $activeQuery;
    }

    /**
     *  ActiveRecord没有批量入库方法，这里给他封装一个
     * @param $data
     * @return int
     * @throws \yii\db\Exception
     */
    public static function batchInsert($data)
    {
        $limit = Yii::$app->params['sql_batch_size'];

        $fields = array_keys($data[0]);
        $batchData = [];
        foreach ($data as $value) {
            $batchData[] = array_values($value);
        }
        $result = 0;
        //做下入库限制，免得数据库爆了
        $insertData = array_chunk($batchData, $limit);
        foreach ($insertData as $value) {
            $count = Yii::$app->db->createCommand()->batchInsert(static::tableName(), $fields, $value)->execute();
            $result += $count;
        }
        self::batchInsertEvent($data);
        return $result;
    }

     /**
     * 批量修改,根据主键id作为条件，数据格式为
     * $data = [
     *     1 => [
     *        'a' => 1, 'b' => 1
     *     ],
     *     2 => [
     *        'a' => 1, 'b' => 1
     *     ],
     * ]
     */
    public static function batchUpdate(array $params)
    {
        $limit = \Yii::$app->params['sql_batch_size'];
        $currentTime = time();
        $params = array_map(function ($param) use ($currentTime) {
            $param['updated_time'] =  $currentTime;
            return $param;
        }, $params);
        //做下入库限制，免得数据库爆了
        $updateDatas = array_chunk($params, $limit);

        foreach ($updateDatas as $updateData) {
            $sql = self::formatSql($updateData);
            try {
                $result = \Yii::$app->db->createCommand($sql)->execute();
            } catch (\Exception $exception) {
                Elk::log(ElkLogType::BATCH_UPDATE_ERROR, $exception->getMessage(), Elk::LEVEL_ERROR);
            }
        }
    }

    /**
     * 格式化批量操作的修改
     * @param array $params
     * @return string
     */
    private static function formatSql(array $params)
    {
        $sql = 'UPDATE ' . static::tableName() . ' SET ';
        $sqlList = [];
        $ids = [];
        foreach ($params as $data) {
            $id = $data['id'];
            $ids[] = $id;
            foreach ($data as $key => $val) {
                if (is_numeric($val)) {
                    $sqlList[$key][] = ' WHEN '. $id .' THEN ' . $val;
                } else {
                    $sqlList[$key][] = ' WHEN ' . $id.' THEN \'' . addslashes($val) . '\'';
                }
            }
        }

        $count = count($sqlList);
        $i = 0;
        foreach ($sqlList as $key => $wheres) {
            $sql .= $key . '= CASE id ';
            $sqlSplice = implode("\r\n", $wheres);
            $sql .= $sqlSplice;
            if (++$i == $count) {
                $sql .= ' END ';
            } else {
                $sql .= ' END, ';
            }
        }
        $idsStr = implode(',', $ids);
        $sql .= 'WHERE id in (' . $idsStr . ')';
        return $sql;
    }

    private static function batchInsertEvent(array $data)
    {
        $self = new static();
        $batchInsertEvent = new BatchInsertEvent();
        $batchInsertEvent->setParams($data);
        $self->trigger(self::EVENT_AFTER_BATCH_INSERT, $batchInsertEvent);
    }

    /**
     * 新增数据并且返回自增id
     * @param $data
     * @return int|string
     * @throws \yii\db\Exception
     */
    public static function insertData($data)
    {
        if (!empty($data)) {
            Yii::$app->db->createCommand()->insert(static::tableName(), $data)->execute();
            return Yii::$app->db->lastInsertID;
        } else {
            return 0;
        }
    }

    /**
     * 将数组复制到类属性中
     * @param $class
     * @param array $array
     * @return mixed
     */
    public function arrayToProperties($class, array $array)
    {
        foreach ($array as $key => $value)
        {
            $class->$key = $value;
        }
        return $class;
    }

    public function create(array $data)
    {
        $model = new static();
        $model->arrayToProperties($model, $data);
        $model->save();
        return $model;
    }

    public function updateModel(BaseModel $model, array $data)
    {
        $model->arrayToProperties($model, $data);
        $model->save();
        return $model;
    }

    public function findById(int $id) :? BaseModel
    {
        return self::find()
            ->where("id = :id")
            ->params(['id' => $id])
            ->cache($this->cacheTime)
            ->one();
    }

    /**
     * 删除数据，根据softDelete判断是否用软删
     * @param int $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function deleteById(int $id)
    {
        if ($this->softDelete) {
            $this->softDeleteById($id);
        } else {
            $this->physicalDeleteById($id);
        }
    }

    /**
     * 物理删除
     * @param int $id
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function physicalDeleteById(int $id)
    {
        $model = $this->findById($id);
        $model->delete();
    }

    /**
     * 软删除
     * @param int $id
     */
    public function softDeleteById(int $id)
    {
        $model = $this->findById($id);
        $model->status = 0;
        $model->save();
    }

    public function deleteCallback(Event $event)
    {
        $id = (int)$event->sender->id;
        $this->findById($id);
        $this->deleteCache();
    }

    /**
     * 插入后
     * @param Event $event
     */
    public function addCallback(Event $event)
    {
        $id = (int)$event->sender->id;
        $this->findById($id);
        $this->deleteCache();
    }

    /**
     * 更新回调
     * @param Event $event
     */
    public function updateCallback(Event $event)
    {
        $id = (int)$event->sender->id;
        $this->findById($id);
        $this->deleteCache();
    }

    public function batchInsertCallback(BatchInsertEvent $batchInsertEvent)
    {

    }

    /**
     * 删除缓存 清除单个实体传:fetch,清除列表缓存传:fetchAll
     * @param string $type
     */
    public function deleteCache(string $type = 'fetch')
    {
        $command = self::$activeQuery->createCommand();
        $sql = $command->getRawSql();
        $cache = $this->getCacheService();
        $cacheKey = [
            Command::class,
            $type,
            null,
            $command->db->dsn,
            $command->db->username,
            $sql,
        ];
        $cache->delete($cacheKey);
    }

    /**
     * 获取缓存服务
     * @return Cache
     */
    public function getCacheService() :Cache
    {
        if (!$this->cache) {
            $this->cache = \Yii::$app->cache;
        }
        return $this->cache;
    }

}
