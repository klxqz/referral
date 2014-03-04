<?php

class shopReferralPluginBackendSaveTransactionController extends waJsonController {

    public function execute() {
        $id = waRequest::post('id');
        $amount = waRequest::post('amount');
        $comment = waRequest::post('comment');
        $date = waRequest::post('date');
        $referral_model = new shopReferralPluginModel();

        $result = $referral_model->getById($id);
        if ($result) {
            $referral_model->updateById($id, array('amount' => $amount, 'date' => $date, 'comment' => $comment));
            $this->response['amount'] = shop_currency_html($amount);
            $this->response['date'] = waDateTime::format('humandatetime',$date);
            $this->response['comment'] = $comment;
        } else {
            $this->errors = 'Ошибка. Запись не найдена';
        }
    }

}
