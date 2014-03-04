<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginModel extends waModel {

    protected $table = 'shop_referral';

    public function getReferrals() {
        $sql = "SELECT * , sum( `amount` ) AS `total_amount`, `wc`.`name`
                FROM `" . $this->table . "` as `sr`
                LEFT JOIN `wa_contact` as `wc` ON `sr`.`contact_id` = `wc`.`id`
                GROUP BY `contact_id`";
        return $this->query($sql)->fetchAll();
    }

}
