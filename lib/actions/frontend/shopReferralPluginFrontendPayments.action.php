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
        $payment = waRequest::post('payment');
        $refferal_payments_model = new shopReferralPluginPaymentsModel();
        if ($payment) {
            $payment['contact_id'] = wa()->getUser()->getId();
            $payment['date'] = waDateTime::date('Y-m-d H:i:s');
            $payment['status'] = 'new';
            $refferal_payments_model->insert($payment);
        }
        $payments = $refferal_payments_model->where('contact_id = ' . wa()->getUser()->getId())->order('date DESC')->fetchAll();
        $this->view->assign('all_statuses', $this->all_statuses);
        $this->view->assign('user_statuses', $this->user_statuses);
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
