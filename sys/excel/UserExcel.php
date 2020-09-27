<?php

namespace app\modules\sys\excel;

use app\modules\common\excel\BaseExcel;
use BaseComponents\base\ExcelHelper;

class UserExcel extends BaseExcel
{
    public $fields = [
        'id' => [
            'header' => '丝路 ID',
            'name' => 'id',
            'type' => 'integer',
        ],
        'created_time' => [
            'header' => '注册时间',
            'name' => 'created_time_excel',
            'type' => 'datetime',
        ],
        'last_login_time' => [
            'header' => '最后登录时间',
            'name' => 'last_login_time_excel',
            'type' => 'datetime',
        ],
        'user_name' => [
            'header' => '用户名称',
            'name' => 'user_name',
            'type' => 'string',
        ],
        'email' => [
            'header' => '邮箱',
            'name' => 'email',
            'type' => 'string',
        ],
        'phone' => [
            'header' => '手机号',
            'name' => 'phone_excel',
            'type' => 'integer',
        ],
        'company' => [
            'header' => '公司',
            'name' => 'company',
            'type' => 'string',
        ],
        'plan' => [
            'header' => '活动名称',
            'name' => 'plan',
            'type' => 'string',
        ],
        'channel' => [
            'header' => '活动来源',
            'name' => 'channel',
            'type' => 'string',
        ],
        'promoter' => [
            'header' => '推广者',
            'name' => 'promoter',
            'type' => 'string',
        ],
        'sss_user_name' => [
            'header' => '3s username',
            'name' => 'sss_user_name',
            'type' => 'string',
        ]
    ];

    public function export(array $list)
    {
        $file = '用户列表.xlsx';
        $excelHelper = new ExcelHelper();
        $excelHelper->exportExcel($this->headers, $list, $file);
    }


    public function getFields()
    {
        $newFields = [];
        foreach ($this->fields as $field) {
            $newFields[] = $field['name'];
            $this->headers[$field['header']] = $field['type'];
        }

        $field = '{' . implode(',', $newFields) . '}';
        return $field;
    }
}
