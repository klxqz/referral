<?php

class shopReferralPluginFrontendSavePaymentController extends waJsonController {

    protected $statuses = array(
        'new' => 'Новый',
    );

    public function execute() {
        $id = waRequest::post('id');
        $status = waRequest::post('status');
        $data = waRequest::post('data');
        $amount = waRequest::post('amount');
        $referral_model = new shopReferralPluginModel();
        $refferal_payments_model = new shopReferralPluginPaymentsModel();

        $result = $refferal_payments_model->getById($id);
        if ($result && isset($this->statuses[$status])) {
            $payment = array(
                'status' => $status,
                'data' => $data,
                'amount' => $amount,
            );

            $refferal_payments_model->updateById($id, $payment);
            $this->response['status'] = $this->statuses[$status];
            $this->response['data'] = $data;
            $this->response['amount'] = $amount;
        } else {
            $this->errors = 'Ошибка. Запись не найдена';
        }
    }

}
