<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginPaymentsModel extends waModel {

    protected $table = 'shop_referral_payments';

    public function getReferralPayments() {
        $sql = "SELECT `srp`.*,`wc`.`name`
                FROM `" . $this->table . "` as `srp`
                LEFT JOIN `wa_contact` as `wc` ON `srp`.`contact_id` = `wc`.`id`";
        return $this->query($sql)->fetchAll();
    }

}
