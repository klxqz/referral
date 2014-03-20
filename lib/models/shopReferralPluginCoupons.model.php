<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginCouponsModel extends waModel {

    protected $table = 'shop_referral_coupons';

    public static function generateCode() {
        $alphabet = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $result = '';
        while (strlen($result) < 8) {
            $result .= $alphabet{mt_rand(0, strlen($alphabet) - 1)};
        }
        return $result;
    }

    public function getShopPromoByCouponCode($code) {
        $sql = "SELECT *
                FROM `shop_referral_coupons` AS `src`
                LEFT JOIN `shop_referral_promo` AS `srp` ON `src`.`promo_id` = `srp`.`id`
                WHERE `src`.`code` = '" . $this->escape($code) . "'";
        return $this->query($sql)->fetch();
    }

}
