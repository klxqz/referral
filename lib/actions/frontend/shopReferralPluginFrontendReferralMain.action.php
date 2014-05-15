<?php

class shopReferralPluginFrontendReferralMainAction extends shopFrontendAction {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopReferralPlugin::$plugin_id, 'status')) {
            throw new waException(_ws("Page not found"), 404);
        }

        $coupon_model = new shopCouponModel();
        $ref_coupon_model = new shopReferralPluginCouponsModel();
        $promo_model = new shopReferralPluginPromoModel();
        $promos = $promo_model->getAll();
        $referral_id = wa()->getUser()->getId();
        $domain = wa()->getRouting()->getDomain(null, true);
        foreach ($promos as &$promo) {
            shopReferral::initReferralCoupon($promo,$referral_id);
        }
        unset($promo);


        $template_path = wa()->getDataPath('plugins/referral/templates/tpls/FrontendDescription.html', false, 'shop', true);
        if (!file_exists($template_path)) {
            $template_path = wa()->getAppPath('plugins/referral/templates/tpls/FrontendDescription.html', 'shop');
        }
        
        $this->view->assign('enable_report', $app_settings_model->get(shopReferralPlugin::$plugin_id, 'enable_report'));
        $this->view->assign('enable_payments', $app_settings_model->get(shopReferralPlugin::$plugin_id, 'enable_payments'));
        $this->view->assign('frontend_name', $app_settings_model->get(shopReferralPlugin::$plugin_id, 'frontend_name'));
        
        $this->view->assign('FrontendDescription', $template_path);
        $this->view->assign('promos', $promos);
        $this->view->assign('breadcrumbs', self::getBreadcrumbs());
        $this->getResponse()->setTitle($app_settings_model->get(shopReferralPlugin::$plugin_id, 'frontend_name'));
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
