<?php

class shopReferral {

    public static function initReferralCoupon(&$promo, $referral_id) {
        $coupon_model = new shopCouponModel();
        $ref_coupon_model = new shopReferralPluginCouponsModel();
        $ref_coupon = $ref_coupon_model->getByField(array('promo_id' => $promo['id'], 'contact_id' => $referral_id));
        if (!$ref_coupon) {
            $data = array(
                'contact_id' => $referral_id,
                'promo_id' => $promo['id'],
                'code' => shopReferralPluginCouponsModel::generateCode(),
            );
            $ref_coupon_model->insert($data);
        }
        $ref_coupon = $ref_coupon_model->getByField(array('promo_id' => $promo['id'], 'contact_id' => $referral_id));

        $domain = wa()->getRouting()->getDomain(null, true);
        $coupon = $coupon_model->getById($promo['coupon_id']);
        $url = 'http://' . $domain . wa()->getDataUrl('plugins/referral/promos/' . $promo['img'], true, 'shop');
        $html = '<a href="' . $promo['url'] . '?referral_id=' . $referral_id . '&coupon_code=' . $ref_coupon['code'] . '"><img src="' . $url . '" /></a>';
        $promo['code'] = $html;

        $promo['description'] = str_replace('{$referral_id}', $referral_id, $promo['description']);
        $promo['description'] = str_replace('{$coupon_code}', $ref_coupon['code'], $promo['description']);

        $promo['ref_coupon'] = $ref_coupon['code'];
    }

    public static function multiReferralPayment($referral_id, $amount, $order_id = null, $comment = null, $level = 1) {
        $referral_model = new shopReferralPluginModel();
        $contact = new waContact($referral_id);
        $c_referral_id = $contact->get('referral_id', 'default');

        $app_settings_model = new waAppSettingsModel();
        $number_levels = $app_settings_model->get(shopReferralPlugin::$plugin_id, 'number_levels');
        if ($c_referral_id && $level < $number_levels) {
            $c_amount = shopReferral::getReferralAmount($amount, $level + 1);
            if ($c_amount > 0.01) {
                self::multiReferralPayment($c_referral_id, $c_amount, $order_id, $comment, $level + 1);
                $amount -= $c_amount;
            }
        }

        if (!$comment && $order_id) {
            switch ($level) {
                case 1:
                    $comment = 'Бонус за привлеченного друга';
                    break;
                case 2:
                    $comment = 'Бонус за заказ, привлеченный Вашим другом';
                    break;
                default:
                    $comment = 'Начисление. Заказ №' . shopHelper::encodeOrderId($order_id);
            }
        }

        self::referralPayment($referral_id, $amount, $order_id, $comment);
    }

    public static function referralPayment($referral_id, $amount, $order_id = null, $comment = null) {
        $app_settings_model = new waAppSettingsModel();
        $direct_transfer = $app_settings_model->get(shopReferralPlugin::$plugin_id, 'direct_transfer');
        switch ($direct_transfer) {
            case 'webasyst':
                self::webasystTransferPayment($referral_id, $amount, $order_id, $comment);
                break;
            case 'wa-bonuses':
                self::waBonusesTransferPayment($referral_id, $amount);
                break;
        }

        $referral_model = new shopReferralPluginModel();
        $data = array(
            'contact_id' => $referral_id,
            'location' => $direct_transfer,
            'date' => waDateTime::date('Y-m-d H:i:s'),
            'amount' => $amount,
            'comment' => $comment,
            'order_id' => $order_id,
        );
        $referral_model->insert($data);
        shopReferralPlugin::sendReferralNotification($referral_id, $data);
    }

    public static function webasystTransferPayment($referral_id, $amount, $order_id = null, $comment = null) {
        $affiliate_model = new shopAffiliateTransactionModel();
        $affiliate_model->applyBonus($referral_id, $amount, $order_id, $comment);
    }

    public static function waBonusesTransferPayment($referral_id, $amount) {

        if (!class_exists('shopBonusesPluginModel')) {
            throw new Exception('Плагин "Бонусы за покупку" не установлен');
        }
        $bonus_model = new shopBonusesPluginModel();

        if ($sb = $bonus_model->getByField('contact_id', $referral_id)) {
            $exist_bonus = $this->getUnburnedBonus($referral_id);
            $data = array(
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'bonus' => $amount + $exist_bonus,
            );
            $bonus_model->updateById($sb['id'], $data);
        } else {
            $data = array(
                'contact_id' => $referral_id,
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'bonus' => $amount,
            );
            $bonus_model->insert($data);
        }
    }

    public static function multiReferralRefund($order_id) {
        $referral_model = new shopReferralPluginModel();
        $transactions = $referral_model->getByField('order_id', $order_id, true);
        foreach ($transactions as $transaction) {
            $location = $transaction['location'];
            $referral_id = $transaction['contact_id'];
            $amount = (-1) * $transaction['amount'];
            $comment = 'Возврат. Заказ №' . shopHelper::encodeOrderId($order_id);

            switch ($transaction['location']) {
                case 'webasyst':
                    self::webasystTransferPayment($referral_id, $amount, $order_id, $comment);
                    break;
                case 'wa-bonuses':
                    self::waBonusesTransferPayment($referral_id, $amount);
                    break;
            }

            $data = array(
                'contact_id' => $referral_id,
                'location' => $location,
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'amount' => $amount,
                'comment' => $comment,
                'order_id' => $order_id,
            );
            $referral_model->insert($data);
            shopReferralPlugin::sendReferralNotification($referral_id, $comment);
        }
    }

    public static function getReferralAmount($total, $level = 1) {
        $app_settings_model = new waAppSettingsModel();
        $referral_percent = $app_settings_model->get(shopReferralPlugin::$plugin_id, 'referral_percent');

        $referral_level_percents = $app_settings_model->get(shopReferralPlugin::$plugin_id, 'referral_level_percents', null);
        if ($referral_level_percents) {
            $referral_level_percents = json_decode($referral_level_percents, true);
        } else {
            $referral_level_percents = array();
        }

        if (isset($referral_level_percents[$level]) && $referral_level_percents[$level]) {
            return $total * $referral_level_percents[$level] / 100.0;
        } else {
            return $total * $referral_percent / 100.0;
        }
    }

}
