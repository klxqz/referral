<?php

class shopReferralPluginFrontendReferralReportAction extends shopFrontendAction {

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopReferralPlugin::$plugin_id, 'status') || !$app_settings_model->get(shopReferralPlugin::$plugin_id, 'enable_report')) {
            throw new waException(_ws("Page not found"), 404);
        }
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

        $app_settings_model = new waAppSettingsModel();
        $result = shopReferralPluginFrontendReferralMainAction::getBreadcrumbs();
        $result[] = array(
            'name' => $app_settings_model->get(shopReferralPlugin::$plugin_id, 'frontend_name'),
            'url' => wa()->getRouteUrl('shop/frontend/referralMain'),
        );
        return $result;
    }

    private function implodeNames($items) {
        $names = array();
        foreach ($items as $item) {
            $names[] = $item['name'];
        }
        return implode(', ', $names);
    }

}
