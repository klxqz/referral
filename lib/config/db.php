<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'shop_promo' => array(
        'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
        'name' => array('varchar', 255, 'null' => 0, 'default' => ''),
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
        'date' => array('datetime', 'null' => 0),
        'amount' => array('decimal', '15,4', 'null' => 0),
        'comment' => array('text'),
        'order_id' => array('int', 11, 'null' => 0, 'default' => '0'),
        'data' => array('text'),
        ':keys' => array(
            'PRIMARY' => array('id'),
            'contact_id' => 'contact_id',
        ),
    ),
);
