<?php

class shopReferralPluginBackendTransferController extends waJsonController {

    public function execute() {
        $transfer = waRequest::post('transfer');

        try {
            if ($transfer['amount'] <= 0) {
                throw new Exception('Сумма перевода должна быть больше нуля');
            }

            switch ($transfer['transfer']) {
                case 'webasyst':
                    $this->webasystTransfer($transfer);
                    break;
                case 'wa-bonuses':
                    $this->waBonusesTransfer($transfer);
                    break;
            }
        } catch (Exception $ex) {
            $this->errors = $ex->getMessage();
        }
    }

    public function webasystTransfer($transfer) {
        $affiliate_model = new shopAffiliateTransactionModel();
        $referral_model = new shopReferralPluginModel();
        $data = array(
            'amount' => (-1) * $transfer['amount'],
            'contact_id' => $transfer['referral_id'],
            'comment' => $transfer['comment'],
            'date' => waDateTime::date('Y-m-d H:i:s'),
        );
        $referral_model->insert($data);
        $affiliate_model->applyBonus($transfer['referral_id'], $transfer['amount'], null, $transfer['comment']);
    }

    public function waBonusesTransfer($transfer) {

        if (!class_exists('shopBonusesPluginModel')) {
            throw new Exception('Плагин "Бонусы за покупку" не установлен"');
        }
        $bonus_model = new shopBonusesPluginModel();
        $referral_model = new shopReferralPluginModel();
        $data = array(
            'amount' => (-1) * $transfer['amount'],
            'contact_id' => $transfer['referral_id'],
            'comment' => $transfer['comment'],
            'date' => waDateTime::date('Y-m-d H:i:s'),
        );
        $referral_model->insert($data);

        if ($sb = $bonus_model->getByField('contact_id', $transfer['contact_id'])) {
            $exist_bonus = $this->getUnburnedBonus($transfer['contact_id']);
            $data = array(
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'bonus' => $bonus + $exist_bonus,
            );
            $bonus_model->updateById($sb['id'], $data);
        } else {
            $data = array(
                'contact_id' => $order['contact_id'],
                'date' => waDateTime::date('Y-m-d H:i:s'),
                'bonus' => $bonus,
            );
            $bonus_model->insert($data);
        }
    }

    private function getUnburnedBonus($contact_id) {
        $bonus_model = new shopBonusesPluginModel();
        $sb = $bonus_model->getByField('contact_id', $contact_id);
        if ($sb) {
            if ($this->isBurn($sb)) {
                $bonus_model->updateById($sb['id'], array('bonus' => 0));
                return 0;
            } else {
                return $sb['bonus'];
            }
        } else {
            return false;
        }
    }

}
