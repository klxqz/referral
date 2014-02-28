<?php

return array(
    'name' => 'Партнерская программа',
    'description' => 'Список последних добавленных продуктов',
    'vendor' => '985310',
    'version' => '1.0.0',
    'img' => 'img/referral.png',
    'shop_settings' => true,
    'frontend' => true,
    'handlers' => array(
        'backend_menu' => 'backendMenu',
        'frontend_my' => 'frontendMy',
        'frontend_head' => 'frontendHead',
    ),
);
//EOF
