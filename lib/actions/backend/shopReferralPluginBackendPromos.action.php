<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendPromosAction extends waViewAction {

    public function execute() {
        $promo_model = new shopPromoPluginModel();
        $promos = $promo_model->getAll();
        $this->view->assign('promos', $promos);
    }

}
