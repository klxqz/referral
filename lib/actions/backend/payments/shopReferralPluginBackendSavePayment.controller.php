<?php

class shopReferralPluginBackendSavePaymentController extends waJsonController {

    protected $statuses = array(
        'new' => 'Новый',
        'processing' => 'В обработке',
        'complete' => 'Выполнен',
        'cancel' => 'Отменен',
    );

    public function execute() {
        $id = waRequest::post('id');
        $status = waRequest::post('status');
        $referral_model = new shopReferralPluginModel();
        $refferal_payments_model = new shopReferralPluginPaymentsModel();

        $result = $refferal_payments_model->getById($id);
        if ($result && isset($this->statuses[$status])) {
            if ($result['status'] == 'complete') {
                $data = array(
                    'amount' => (-1) * $result['amount'],
                    'contact_id' => $result['contact_id'],
                    'comment' => 'Выплата №' . $result['id'],
                    'date' => waDateTime::date('Y-m-d H:i:s'),
                );
                $referral_model->insert($data);
            }

            $refferal_payments_model->updateById($id, array('status' => $status));
            if ($status != $result['status']) {
                $result['status_txt'] = $this->statuses[$status];
                shopReferralPlugin::sendStatusNotification($result['contact_id'], $result);
            }
            $this->response['status'] = $this->statuses[$status];
        } else {
            $referral_id = waRequest::post('referral_id');
            $amount = waRequest::post('amount');
            $data = waRequest::post('data');
            $payment = array(
                'data' => $data,
                'amount' => $amount,
                'contact_id' => $referral_id,
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'status' => 'new',
            );
            $refferal_payments_model = new shopReferralPluginPaymentsModel();
            $payment_id = $refferal_payments_model->insert($payment);
            $data = array(
                'amount' => (-1) * $amount,
                'contact_id' => $referral_id,
                'comment' => 'Выплата №' . $payment_id,
                'date' => waDateTime::date('Y-m-d H:i:s'),
            );
            $referral_model->insert($data);
        }
    }

}
