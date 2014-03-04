<?php

class shopReferralPluginBackendDeleteTransactionController extends waJsonController {

    public function execute() {
        $id = waRequest::post('id');
        $referral_model = new shopReferralPluginModel();

        $result = $referral_model->getById($id);
        if ($result) {
            $referral_model->deleteById($id);
        } else {
            $this->errors = 'Ошибка. Запись не найдена';
        }
    }

}
