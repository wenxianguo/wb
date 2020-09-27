<?php

namespace app\modules\common\events;

class BatchInsertEvent extends BaseEvent
{
    private $params = [];

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }
}
