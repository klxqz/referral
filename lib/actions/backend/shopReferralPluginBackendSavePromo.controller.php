<?php

class shopReferralPluginBackendSavePromoController extends waJsonController {

    public function execute() {

        $promo_post = waRequest::post('promo');
        $promo_model = new shopReferralPluginPromoModel();

        $promo = $promo_model->getById($promo_post['id']);

        $file = waRequest::file('img');
        if ($file->uploaded()) {
            $image_path = wa()->getDataPath('plugins/referral/promos/', 'shop');
            $name = $this->uniqueName($image_path, $file->extension);
            try {
                $file->waImage()->save($image_path . $name);
                $this->response['preview'] = wa()->getDataUrl('plugins/referral/promos/' . $name, true, 'shop');
                if ($promo && $promo['img'] && file_exists(wa()->getDataPath('plugins/referral/promos/' . $promo['img'], true, 'shop'))) {
                    @unlink(wa()->getDataPath('plugins/referral/promos/' . $promo['img'], true, 'shop'));
                }
                $promo_post['img'] = $name;
            } catch (Exception $e) {

                $this->setError($e->getMessage());
            }
        }

        if ($promo) {
            $promo_model->updateById($promo_post['id'], $promo_post);
        } else {
            $lastInsertId = $promo_model->insert($promo_post);
            $this->response['id'] = $lastInsertId;
        }

        $this->response['message'] = 'OK';
    }

    protected function uniqueName($path, $file_extension) {
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        do {
            $name = '';
            for ($i = 0; $i < 10; $i++) {
                $n = rand(0, strlen($alphabet) - 1);
                $name .= $alphabet{$n};
            }
            $name .= '.' . $file_extension;
        } while (file_exists($path . $name));

        return $name;
    }

}
