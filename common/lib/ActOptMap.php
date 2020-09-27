<?php

namespace app\modules\common\lib;

/**
 * action对应的opt
 *
 * Class ActOptMap
 * @package app\modules\common\lib
 */
class ActOptMap
{
    public static function getMap()
    {
        return [
            'fb' => [
                'auth' => [
                    'comment' => 'FACEBOOK',
                    'save-auth' => [
                        'comment' => 'SAVE_AUTH',
                    ],
                    'deauthorize' => [
                        'comment' => 'DEAUTHORIZE'
                    ]
                ],
                'oe' => [
                    'comment' => 'OPEN_ACCOUNT',
                    'update' => [
                        'comment' => 'UPDATE_OE',
                    ],
                    'create' => [
                        'comment' => 'CREATE_OE'
                    ]
                    ],
                'management-account' => [
                    'comment' => 'MANAGEMENT_ACCOUNT',
                    'create' => [
                        'comment' => 'CREATE_MANAGEMENT_ACCOUNT'
                    ]
                ]
            ],
            'sys' => [
                'admin/company' => [
                    'comment' => 'ADMIN_COMPANY_SETTING',
                    'save' => [
                        'comment' => 'MODIFY_COMPANY_INFORMATION'
                    ]
                ],
                'admin/group' => [
                    'comment' => 'ADMIN_GROUP_SETTING',
                    'add' => [
                        'comment' => 'ADD_GROUP'
                    ],
                    'update' => [
                        'comment' => 'UPDATE_GROUP'
                    ],
                    'del' => [
                        'comment' => 'DELETE_GROUP'
                    ],
                    'remove-user' => [
                        'comment' => 'REMOVE_MEMBER'
                    ],
                    'update-user' => [
                        'comment' => 'UPDATE_MEMBER'
                    ],
                    'add-user' => [
                        'comment' => 'JOIN_COMPANY'
                    ],
                ],
                'admin/role' => [
                    'comment' => 'ADMIN_ROLE_SETTING',
                    'add' => [
                        'comment' => 'ADD_ROLE'
                    ],
                    'del' => [
                        'comment' => 'DELETE_ROLE'
                    ],
                    'update' => [
                        'comment' => 'UPDATE_ROLE'
                    ],
                    'update-user-role' => [
                        'comment' => 'UPDATE_USER_ROLE'
                    ],
                    'save-role-auth' => [
                        'comment' => 'UPDATE_ROLE_AUTH'
                    ]
                ],
                'user/passport' => [
                    'comment' => 'ADMIN_USER_PASSPORT',
                    'login' => [
                        'comment' => 'USER_LOGIN'
                    ]
                ],
                'user/profile' => [
                    'comment' => 'ADMIN_USER_SETTING',
                    'update-profile' => [
                        'comment' => 'UPDATE_USER_INFORMATION'
                    ],
                    'update-password' => [
                        'comment' => 'UPDATE_PASSWORD'
                    ],
                    'bind-phone' => [
                        'comment' => 'BIND_PHONE'
                    ],
                    'bind-email' => [
                        'comment' => 'BIND_EMAIL'
                    ],
                ],
            ],
            'shoplazza' => [
                'auth' => [
                    'comment' => 'SHOPLAZZA',
                    'bind-email' => [
                        'comment' => 'BIND_EMAIL',
                    ]
                ]
            ],
            'payment' => [
                'recharge-order' => [
                    'comment' => 'RECHARGE',
                    'update' => [
                        'comment' => 'UPDATE_RECHARGE',
                    ],
                    'test-update' => [
                        'comment' => 'TEST_UPDATE_RECHARGE',
                    ],
                    'create' => [
                        'comment' => 'CREATE_RECHARGE'
                    ]
                ]
            ],
            'shopify' => [
                'web-hook' => [
                    'comment' => 'SHOPIFY',
                    'customer-create' => [
                        'comment' => 'CUSTOMER_CREATE',
                    ],
                    'customer-redact' => [
                        'comment' => 'CUSTOMER_REDACT',
                    ],
                    'shop-redact' => [
                        'comment' => 'SHOP_REDACT',
                    ],
                    'delete-shop' => [
                        'comment' => 'DELETE_SHOP',
                    ],
                    'test-uninstall' => [
                        'comment' => 'TEST_UNINSTALL',
                    ]
                ]
            ],
            'tiktok' => [
                'open-account' => [
                    'comment' => 'OPEN_ACCOUNT',
                    'create' => [
                        'comment' => 'TIKTOK_OPEN_ACCOUNT_CREATE',
                    ],
                    'update' => [
                        'comment' => 'TIKTOK_OPEN_ACCOUNT_UPDATE',
                    ]
                ]
            ],
            'google' => [
                'open-account' => [
                    'comment' => 'OPEN_ACCOUNT',
                    'create' => [
                        'comment' => 'GOOGLE_OPEN_ACCOUNT_CREATE',
                    ],
                    'update' => [
                        'comment' => 'GOOGLE_OPEN_ACCOUNT_UPDATE',
                    ]
                ]
            ]
        ];
    }
}
