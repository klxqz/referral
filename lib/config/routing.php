<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'my/referral/' => array(
        'module' => 'frontend',
        'plugin' => 'referral',
        'action' => '',
        'secure' => true,
    ),
    'my/referral/report/' => array(
        'module' => 'frontend',
        'plugin' => 'referral',
        'action' => 'report',
        'secure' => true,
    ),
    'my/referral/payments/' => array(
        'module' => 'frontend',
        'plugin' => 'referral',
        'action' => 'payments',
        'secure' => true,
    ),
    'my/referral/savepayment/' => array(
        'module' => 'frontend',
        'plugin' => 'referral',
        'action' => 'savePayment',
        'secure' => true,
    ),
);
