<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendContactFormController extends waJsonController {

    public function execute() {
        $id = (int) waRequest::post('id');
        if ($id) {
            $referral_model = new shopReferralPluginModel();
            $form = shopHelper::getCustomerForm($id);
            $this->response['html_form'] = $form->html();
            $balance = $referral_model->getReferralBalance($id);
            $this->response['balance'] = shop_currency_html($balance);
            $this->response['date'] = waDateTime::date('Y-m-d H:i:s');
        }
    }

}
