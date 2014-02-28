<?php

class shopReferralPluginFrontendAction extends shopFrontendAction {

    public function execute() {
        $coupon_model = new shopCouponModel();
        $promo_model = new shopPromoPluginModel();
        $promos = $promo_model->getAll();
        $referral_id = wa()->getUser()->getId();
        $domain = wa()->getRouting()->getDomain(null, true);
        foreach ($promos as &$promo) {
            $coupon = $coupon_model->getById($promo['coupon_id']);
            $url = 'http://' . $domain . wa()->getDataUrl('plugins/referral/promos/' . $promo['img'], true, 'shop');
            $html = '<a href="' . $promo['url'] . '?referral_id=' . $referral_id . '&coupon=' . $coupon['code'] . '"><img src="' . $url . '" /></a>';
            $promo['code'] = $html;
        }
        unset($promo);
        $this->view->assign('promos', $promos);
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
