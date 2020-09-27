<?php

namespace app\modules\common\excel;

use slr\graphql\src\Assemble;


class BaseExcel
{
    public $fields = [];
    public $headers = [];

    public function conversionField()
    {
        $oldField = \Yii::$app->request->get('field', '');
        $oldFields = Assemble::parseQuery($oldField);
        $newFields = [];
        foreach ($oldFields as $name => $val) {
            if (in_array($name, array_keys($this->fields))) {
                $excelField = $this->fields[$name];
                $newFields[] = $excelField['name'];
                $this->headers[$excelField['header']] = $excelField['type'];
            }
        }
        $field = '{' . implode(',', $newFields) . '}';
        return $field;
    }
}
