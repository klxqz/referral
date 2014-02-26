<?php

class shopReferralPluginFrontendAction extends shopFrontendAction {

    public function execute() {
        $promo_model = new shopPromoPluginModel();
        $promos = $promo_model->getAll();
        $this->view->assign('promos', $promos);
    }

}
