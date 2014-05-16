<?php

/**
 * @author wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'shop_referral_promo' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'name' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'description' => array('text'),
        'coupon_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'url' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'img' => array('varchar', 255, 'null' => 0, 'default' => ''),
        'enabled' => array('tinyint', 1, 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => array('id'),
            'coupon_id' => 'coupon_id',
        ),
    ),
    'shop_referral' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'contact_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'location' => array('varchar', 32, 'null' => 0, 'default' => ''),
        'date' => array('datetime', 'null' => 0),
        'amount' => array('decimal', '15,4', 'null' => 0),
        'comment' => array('text'),
        'order_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => array('id'),
            'contact_id' => 'contact_id',
            'location' => 'location',
        ),
    ),
    'shop_referral_payments' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'contact_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'date' => array('datetime', 'null' => 0),
        'amount' => array('decimal', '15,4', 'null' => 0),
        'data' => array('text'),
        'status' => array('enum', "'new','processing','complete','cancel'", 'null' => 0, 'default' => 'new'),
        ':keys' => array(
            'PRIMARY' => array('id'),
            'contact_id' => 'contact_id',
        ),
    ),
    'shop_referral_coupons' => array(
        'contact_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'promo_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'code' => array('varchar', 32, 'null' => 0, 'default' => ''),
        ':keys' => array(
            'contact_id' => 'contact_id',
            'promo_id' => 'promo_id',
            'code' => 'code',
        ),
    ),
);
