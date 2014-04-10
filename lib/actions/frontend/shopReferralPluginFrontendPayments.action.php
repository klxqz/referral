<?php

class shopReferralPluginFrontendPaymentsAction extends shopFrontendAction {

    protected $all_statuses = array(
        'new' => 'Новый',
        'processing' => 'В обработке',
        'complete' => 'Выполнен',
        'cancel' => 'Отменен',
    );
    protected $user_statuses = array(
        'new' => 'Новый',
    );

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        if (!$app_settings_model->get(shopReferralPlugin::$plugin_id, 'status')) {
            throw new waException(_ws("Page not found"), 404);
        }
        $payment = waRequest::post('payment');
        $refferal_payments_model = new shopReferralPluginPaymentsModel();
        if ($payment) {
            $payment['contact_id'] = wa()->getUser()->getId();
            $payment['date'] = waDateTime::date('Y-m-d H:i:s');
            $payment['status'] = 'new';
            $refferal_payments_model->insert($payment);
            shopReferralPlugin::sendAdminNotification($payment);
        }
        $payments = $refferal_payments_model->where('contact_id = ' . wa()->getUser()->getId())->order('date DESC')->fetchAll();
        $this->view->assign('all_statuses', $this->all_statuses);
        $this->view->assign('user_statuses', $this->user_statuses);
        $this->view->assign('payments', $payments);
        $this->view->assign('breadcrumbs', self::getBreadcrumbs());
        $this->getResponse()->setTitle('Выплаты');
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

}
