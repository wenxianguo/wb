<?php
namespace app\modules\sys\services\options;

use app\modules\common\models\sys\SysCountryModel;
use app\modules\common\services\BaseService;


class CountryService extends BaseService
{

    /**
     * 获取国家列表
     * @param array $fields
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getCountryList(array $fields)
    {
        $fieldsArr = [];
        $fieldsList = ['country_code_two', 'country_code_three', 'cn_name', 'en_name'];
        foreach ($fields as $fieldItem) {
            if (in_array($fieldItem, $fieldsList)) {
                $fieldsArr[] = $fieldItem;
            }
        }
        return SysCountryModel::find()->asArray()
            ->select($fieldsArr)
            ->orderBy('country_code_two desc, id asc')
            ->all();
    }

}