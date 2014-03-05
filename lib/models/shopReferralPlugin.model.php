<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginModel extends waModel {

    protected $table = 'shop_referral';

    public function getReferralBalance($referral_id) {
        $sql = "SELECT * , sum( `amount` ) AS `total_amount`, `wc`.`name`
                FROM `" . $this->table . "` as `sr`
                LEFT JOIN `wa_contact` as `wc` ON `sr`.`contact_id` = `wc`.`id`
                WHERE `contact_id` = '" . (int) $referral_id . "'
                GROUP BY `contact_id`";
        $result = $this->query($sql)->fetch();
        return $result['total_amount'];
    }

    public function getReferrals() {
        $sql = "SELECT * , sum( `amount` ) AS `total_amount`, `wc`.`name`
                FROM `" . $this->table . "` as `sr`
                LEFT JOIN `wa_contact` as `wc` ON `sr`.`contact_id` = `wc`.`id`
                GROUP BY `contact_id`";
        return $this->query($sql)->fetchAll();
    }

    public function getTransactionList($referral_id) {
        $sql = "SELECT `sr`.*,
                `so`.`create_datetime` as `order_date`,
                `so`.`total` as `order_total`,
                `so`.`currency` as `order_currency`,
                `so`.`shipping` as `order_shipping`,
                `so`.`discount` as `order_discount`
                FROM `" . $this->table . "` as `sr`
                LEFT JOIN `shop_order` as `so` ON `sr`.`order_id` = `so`.`id`
                WHERE `sr`.`contact_id` = '" . (int) $referral_id . "'
                ORDER BY `date` DESC";
        return $this->query($sql)->fetchAll();
    }

}
