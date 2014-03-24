<?php

class shopReferralPluginBackendDeleteReferralsController extends waJsonController {

    public function execute() {
        $referral_ids = waRequest::post('referral_ids', array());
        $referral_model = new shopReferralPluginModel();

        if ($referral_ids) {
            $referral_model->deleteByField('contact_id',$referral_ids);
        }
        $this->response['message'] = 'OK';
    }
    

}
