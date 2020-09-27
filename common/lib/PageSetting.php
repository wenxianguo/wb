<?php

namespace app\modules\common\lib;

class PageSetting
{
    // 主页的类型
    private $type = [
        'Business',
        'Brand'
    ];

    // 主页的名称
    private $name = [
        'Healthy club',
        'Keto life',
        'Keto club',
        'Keep fit',
        'Love life',
        'No more gym',
        'Popular diet',
        'Diet on the way',
        'What is keto',
        'Slim lady',
        'Gym and diet',
        'Diet club',
        'Healthy lifestyle',
        'Healthy eating',
        'Health and fitness',
        'Fitness talk',
        'Fitness club',
        'Healthy foods',
        'Weight loss tips',
        'How to lose weight',
        'Latest diet news',
        'Weight loss plan',
        'Diet queen',
        'Weight management',
        'Slim couture',
    ];

    // 主页的类别
    private $category = [
        'camera',
        'photo',
        'just for fun'
    ];

    // 头像
    private $avatar = [
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/1.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/2.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/3.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/4.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/5.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/6.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/7.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/8.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/9.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/10.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/11.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/12.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/13.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/14.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/15.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/16.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_avatar/17.jpg'
    ];

    // 封面照片
    private $coverPhoto = [
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/1.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/2.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/3.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/4.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/5.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/6.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/7.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/8.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/9.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/10.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/11.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/12.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/13.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/14.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/15.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/16.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/17.jpg',
        'https://fbcc-image.oss-ap-southeast-1.aliyuncs.com/page_cover_photo/18.jpg'
    ];

    /**
     * 随机获取一个组合
     *
     * @return array
     */
    public function getRand()
    {
        return [
            'type' => ($this->type)[mt_rand(0, count($this->type) - 1)],
            'name' => ($this->name)[mt_rand(0, count($this->name) - 1)],
            'category' => ($this->category)[mt_rand(0, count($this->category) - 1)],
            'avatar' => ($this->avatar)[mt_rand(0, count($this->avatar) - 1)],
            'cover_photo' => ($this->coverPhoto)[mt_rand(0, count($this->coverPhoto) - 1)],
        ];
    }
}
