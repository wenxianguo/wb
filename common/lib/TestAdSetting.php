<?php

namespace app\modules\common\lib;

class TestAdSetting
{
    private $media = [
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/1.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/2.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/3.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/4.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/5.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/6.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/7.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/8.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/9.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/10.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/11.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/12.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/13.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/14.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/15.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/16.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/17.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/18.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/19.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/test_ad_photo/20.jpg'
    ];

    private $link = [
        'alibaba.com',
        'amazon.com',
        'ebay.com',
        'globalsources.com'
    ];

    public function getRand()
    {
        return [
            'media' => ($this->media)[mt_rand(0, count($this->media) - 1)],
            'link' => ($this->link)[mt_rand(0, count($this->link) - 1)],
        ];
    }
}
