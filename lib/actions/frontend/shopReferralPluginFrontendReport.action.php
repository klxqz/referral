<?php

class shopReferralPluginFrontendReportAction extends shopFrontendAction {

    public function execute() {
        $referral_id = wa()->getUser()->getId();
        $referral_model = new shopReferralPluginModel();
        $referral_transactions = $referral_model->getTransactionList($referral_id);
        $balance = $referral_model->getReferralBalance($referral_id);
        $this->view->assign('referral_transactions', $referral_transactions);
        $this->view->assign('breadcrumbs', self::getBreadcrumbs());
        $this->view->assign('balance', $balance);
        $this->getResponse()->setTitle('Отчет');
    }
    
    public static function getBreadcrumbs() {
        $result = shopReferralPluginFrontendAction::getBreadcrumbs();
        $result[] = array(
            'name' => 'Партнерская программа',
            'url' => wa()->getRouteUrl('shop/frontend/'),
        );
        return $result;
    }

}
