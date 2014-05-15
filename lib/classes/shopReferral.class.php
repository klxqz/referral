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

    public static function multiReferralPayment($referral_id, $amount, $order_id = null) {
        $referral_model = new shopReferralPluginModel();
        $contact = new waContact($referral_id);
        $c_referral_id = $contact->get('referral_id', 'default');

        $comment = '';
        if ($order_id) {
            $comment = 'Начисление. Заказ №' . shopHelper::encodeOrderId($order_id);
        }

        if ($c_referral_id) {
            $c_amount = shopReferralPlugin::getReferralAmount($amount);
            if ($c_amount > 0.01) {
                $data = array(
                    'contact_id' => $referral_id,
                    'date' => waDateTime::date('Y-m-d H:i:s'),
                    'amount' => $amount - $c_amount,
                    'comment' => $comment,
                    'order_id' => $order_id,
                );
                $referral_model->insert($data);
                shopReferralPlugin::sendReferralNotification($referral_id, $data);
                self::multiReferralPayment($c_referral_id, $c_amount, $order_id);
            }
        } else {
            $data = array(
                'contact_id' => $referral_id,
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'amount' => $amount,
                'comment' => $comment,
                'order_id' => $order_id,
            );
            $referral_model->insert($data);
            shopReferralPlugin::sendReferralNotification($referral_id, $data);
        }
    }
    
    

    public static function multiReferralRefund($order_id) {
        $referral_model = new shopReferralPluginModel();
        $transactions = $referral_model->getByField('order_id', $order_id, true);
        foreach ($transactions as $transaction) {
            $referral_id = $transaction['contact_id'];
            $amount = (-1) * $transaction['amount'];
            $comment = 'Возврат. Заказ №' . shopHelper::encodeOrderId($params['order_id']);

            $data = array(
                'contact_id' => $referral_id,
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'amount' => $amount,
                'comment' => $comment,
            );
            $referral_model->insert($data);
            shopReferralPlugin::sendReferralNotification($referral_id, $comment);
        }
    }

}
