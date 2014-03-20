<?php

class shopReferralPluginFrontendAction extends shopFrontendAction {

    public function execute() {
        $coupon_model = new shopCouponModel();
        $ref_coupon_model = new shopReferralPluginCouponsModel();
        $promo_model = new shopReferralPluginPromoModel();
        $promos = $promo_model->getAll();
        $referral_id = wa()->getUser()->getId();
        $domain = wa()->getRouting()->getDomain(null, true);
        foreach ($promos as &$promo) {
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
            
            
            $coupon = $coupon_model->getById($promo['coupon_id']);
            $url = 'http://' . $domain . wa()->getDataUrl('plugins/referral/promos/' . $promo['img'], true, 'shop');
            $html = '<a href="' . $promo['url'] . '?referral_id=' . $referral_id . '&coupon_code=' . $ref_coupon['code'] . '"><img src="' . $url . '" /></a>';
            $promo['code'] = $html;
            $promo['ref_coupon'] = $ref_coupon['code'];
        }
        unset($promo);
        $this->view->assign('promos', $promos);
        $this->view->assign('breadcrumbs', self::getBreadcrumbs());
        $this->getResponse()->setTitle('Партнерская программа');
    }

    public static function getBreadcrumbs() {
        return array(
            array(
                'name' => _w('My account'),
                'url' => wa()->getRouteUrl('/frontend/my'),
            ),
        );
    }

}
