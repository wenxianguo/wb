<?php

namespace app\modules\common\lib;

class Excel
{
    /**
     * csv导出数据
     * @param $filename
     * @param array $header
     * @param array $data
     */
    public static function arrayToCsvDownload($filename, array $header, array $data)
    {
        // 不限制脚本执行时间以确保导出完成
        set_time_limit(0);
        // 输出Excel文件头，可把user.csv换成你要的文件名
        header('Content-Disposition: attachment;filename="'.$filename.'.csv"');

        // 打开PHP文件句柄，php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        fwrite($fp, "\xEF\xBB\xBF");

        // 输出Excel列名信息
        foreach ($header as $i => $v) {
            // CSV的Excel支持GBK编码，一定要转换，否则乱码
            $header[$i] = mb_convert_encoding($v,'UTF-8');
        }

        // 将数据通过fputcsv写到文件句柄
        fputcsv($fp, $header);

        foreach ($data as $dataKey => $dataV){
            $index = 0;
            $params = [];
            foreach ($dataV as $i => $v) {
                $v = mb_convert_encoding($v,'UTF-8');
                $params[$index++] = $v;
            }
            fputcsv($fp, $params);
        }
        exit;
    }
}
