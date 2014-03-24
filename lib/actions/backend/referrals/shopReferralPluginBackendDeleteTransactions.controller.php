<?php

class shopReferralPluginBackendDeleteTransactionsController extends waJsonController {

    public function execute() {
        $transaction_ids = waRequest::post('transaction_ids', array());
        $referral_model = new shopReferralPluginModel();

        if ($transaction_ids) {
            $referral_model->deleteByField('id',$transaction_ids);
        }
        $this->response['message'] = 'OK';
    }
    

}
