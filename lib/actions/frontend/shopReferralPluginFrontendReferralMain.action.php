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

            $promo['description'] = str_replace('{$referral_id}', $referral_id, $promo['description']);
            $promo['description'] = str_replace('{$coupon_code}', $ref_coupon['code'], $promo['description']);

            $promo['ref_coupon'] = $ref_coupon['code'];
        }
        unset($promo);


        $template_path = wa()->getDataPath('plugins/referral/templates/printform/FrontendDescription.html', false, 'shop', true);
        if (!file_exists($template_path)) {
            $template_path = wa()->getAppPath('plugins/referral/templates/printform/FrontendDescription.html', 'shop');
        }

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
