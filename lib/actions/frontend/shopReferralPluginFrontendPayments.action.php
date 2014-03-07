<?php

class shopReferralPluginFrontendPaymentsAction extends shopFrontendAction {

    public function execute() {
        $payment = waRequest::post('payment');
        $refferal_payments_model = new shopReferralPluginPaymentsModel();
        if ($payment) {
            $payment['contact_id'] = wa()->getUser()->getId();
            $payment['date'] = waDateTime::date('Y-m-d H:i:s');
            $payment['status'] = 'new';
            $refferal_payments_model->insert($payment);
        }
        $payments = $refferal_payments_model->order('date DESC')->fetchAll();
        $this->view->assign('payments', $payments);
        $this->view->assign('breadcrumbs', self::getBreadcrumbs());
        $this->getResponse()->setTitle('Выплаты');
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
