<?php

class shopReferralPluginBackendDeletePromosController extends waJsonController {

    public function execute() {
        $promo_ids = waRequest::post('promo_ids', array());
        $promo_model = new shopReferralPluginPromoModel();

        if ($promo_ids) {
            foreach ($promo_ids as $id) {
                $promo = $promo_model->getById($id);
                if ($promo['img'] && file_exists(wa()->getDataPath('plugins/referral/promos/' . $promo['img'], true, 'shop'))) {
                    @unlink(wa()->getDataPath('plugins/referral/promos/' . $promo['img'], true, 'shop'));
                }
                $promo_model->deleteById($id);
            }
        }
        $this->response['message'] = 'OK';
    }
    

}
