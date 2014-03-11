<?php

class shopReferralPluginBackendSavePaymentController extends waJsonController {

    protected $statuses = array(
        'new' => 'Новый',
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
            if ($result['status'] == 'new' && $status == 'complete') {
                $data = array(
                    'amount' => (-1) * $result['amount'],
                    'contact_id' => $result['contact_id'],
                    'comment' => 'Выплата №' . $result['id'],
                    'date' => waDateTime::date('Y-m-d H:i:s'),
                );
                $referral_model->insert($data);
            }

            $refferal_payments_model->updateById($id, array('status' => $status));
            $this->response['status'] = $this->statuses[$status];
        } else {
            $this->errors = 'Ошибка. Запись не найдена';
        }
    }

}
