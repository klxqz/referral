<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendAddPromoAction extends waViewAction {

    protected $default = array(
        'enabled' => '1',
        'name' => 'Новый баннер',
        'url' => '',
    );

    public function execute() {
        $id = waRequest::get('id');
        $promo_model = new shopPromoPluginModel();
        $promo = $promo_model->getById($id);
        if (!$promo) {
            $promo = $this->default;
        }

        $curm = new shopCurrencyModel();
        $currencies = $curm->getAll('code');

        $coupm = new shopCouponModel();
        $coupons = $coupm->order('id DESC')->fetchAll('code');
        foreach ($coupons as &$c) {
            $c['enabled'] = shopReferralPlugin::isEnabled($c);
            $c['hint'] = shopReferralPlugin::formatValue($c, $currencies);
        }
        unset($c);

        $order_model = new shopOrderModel();
        $count_new = $order_model->getStateCounters('new');

        $this->view->assign(array(
            'coupons' => $coupons,
            'order_count_new' => $count_new
        ));

        $this->view->assign('promo', $promo);
    }

}
