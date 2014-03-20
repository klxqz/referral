<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendCouponsAction extends waViewAction {

    protected $statuses = array(
        'new' => 'Новый',
        'processing' => 'В обработке',
        'complete' => 'Выполнен',
        'cancel' => 'Отменен',
    );

    public function execute() {
        $refferal_payments_model = new shopReferralPluginPaymentsModel();
        $payments = $refferal_payments_model->getReferralPayments();
        $this->view->assign('payments', $payments);
        $this->view->assign('statuses', $this->statuses);
    }

}
