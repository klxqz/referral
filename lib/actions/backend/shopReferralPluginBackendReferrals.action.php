<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendReferralsAction extends waViewAction {

    public function execute() {
        
        $referral_model = new shopReferralPluginModel();
        $referrals = $referral_model->getReferrals();
        $this->view->assign('referrals', $referrals);
    }

}
