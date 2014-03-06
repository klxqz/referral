<?php

class shopReferralPluginFrontendReportAction extends shopFrontendAction {

    public function execute() {
        $referral_id = wa()->getUser()->getId();
        $referral_model = new shopReferralPluginModel();
        $order_items_model = new shopOrderItemsModel();
        $referral_transactions = $referral_model->getTransactionList($referral_id);
        foreach ($referral_transactions as &$transaction) {
            $items = $order_items_model->getItems($transaction['order_id']);
            $transaction['products_str'] = $this->implodeNames($items);
        }
        unset($transaction);
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
    
    private function implodeNames($items){
        $names = array();
        foreach($items as $item) {
            $names[] = $item['name'];
        }
        return implode(', ', $names);
    }

}
