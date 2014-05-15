<?php

return array(
    'name' => 'Партнерская программа',
    'description' => 'Средство рекламы и привлечения конечного клиента.',
    'vendor' => '985310',
    'version' => '1.0.0',
    'img' => 'img/referral.png',
    'shop_settings' => true,
    'frontend' => true,
    'handlers' => array(
        'backend_menu' => 'backendMenu',
        'frontend_my_nav' => 'frontendMyNav',
        'frontend_head' => 'frontendHead',
        'order_action.complete' => 'orderActionComplete',
        'order_action.pay' => 'orderActionPay',
        'order_action.refund' => 'orderActionRefund',
        'order_action.create' => 'orderActionCreate',
        'frontend_checkout' => 'frontendCheckout',
    ),
);
//EOF
