<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendPromosAction extends waViewAction {

    public function execute() {
        $promo_model = new shopReferralPluginPromoModel();
        $coupon_model = new shopCouponModel();
        $curm = new shopCurrencyModel();
        $currencies = $curm->getAll('code');
        $promos = $promo_model->getAll();
        foreach ($promos as &$promo) {
            $coupon = $coupon_model->getById($promo['coupon_id']);
            $coupon['enabled'] = shopReferralPlugin::isEnabled($coupon);
            $coupon['hint'] = shopReferralPlugin::formatValue($coupon, $currencies);
            $promo['coupon'] = $coupon;
        }
        $this->view->assign('promos', $promos);
    }

}
