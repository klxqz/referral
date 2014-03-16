<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendAddTransactionController extends waJsonController {

    public function execute() {
        $referral_id = waRequest::post('referral_id');
        if (!$referral_id) {
            $this->errors = 'Ошибка. Пользователь не найден.';
        } else {
            $amount = waRequest::post('amount');
            $comment = waRequest::post('comment');
            $date = waRequest::post('date');
            $referral_model = new shopReferralPluginModel();
            $data = array(
                'amount' => $amount,
                'contact_id' => $referral_id,
                'comment' => $comment,
                'date' => $date,
            );
            $referral_model->insert($data);
        }
    }

}
