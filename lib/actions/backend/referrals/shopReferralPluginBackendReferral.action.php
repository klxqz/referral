<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendReferralAction extends waViewAction {

    

    public function execute() {
        $referral_id = waRequest::get('referral_id', 0);
        $referral_model = new shopReferralPluginModel();
        $referral_transactions = $referral_model->getTransactionList($referral_id);
        $contact = new waContact($referral_id);
        $this->view->assign('transfers', shopReferralPlugin::$locations);
        $this->view->assign('referral_transactions', $referral_transactions);
        $this->view->assign('contact', $contact);
        $this->view->assign('referral_id', $referral_id);
        $this->view->assign('datenow', waDateTime::date('Y-m-d H:i:s'));
    }

}
