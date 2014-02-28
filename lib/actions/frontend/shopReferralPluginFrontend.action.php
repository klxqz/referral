<?php

class shopReferralPluginFrontendAction extends shopFrontendAction {

    public function execute() {
        $promo_model = new shopPromoPluginModel();
        $promos = $promo_model->getAll();
        $this->view->assign('promos', $promos);
    }
    
    public static function getBreadcrumbs()
    {
        return array(
            array(
                'name' => _w('My account'),
                'url' => wa()->getRouteUrl('/frontend/my'),
            ),
        );
    }

}
