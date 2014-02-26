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

        $this->view->assign('promo', $promo);
    }

}
