<?php

class shopReferralPlugin extends shopPlugin {

    public function backendMenu() {
        if ($this->getSettings('status') || 1) {
            $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected"' : 'class="no-tab"') . '>
                        <a href="?plugin=referral">Партнерская программа</a>
                    </li>';
            return array('core_li' => $html);
        }
    }

    public function frontendMy() {
        $url = wa()->getAppUrl(null, false) . 'my/referral/';
        $html = '<a href="' . $url . '">Партнерская программа</a>';
        return $html;
    }

    public function frontendHead() {
        if ($referral_id = waRequest::get('referral_id', 0) && !$this->getReferralId()) {
            $this->setReferralId($referral_id);
        }

        if ($coupon_code = waRequest::get('coupon')) {
            $data = wa()->getStorage()->get('shop/checkout', array());
            $data['coupon_code'] = $coupon_code;
            wa()->getStorage()->set('shop/checkout', $data);
            wa()->getStorage()->remove('shop/cart');
        }

        //если пользователь быль неавторизирован, а затем авторизировался, тогда пересохраняем реферала в базу
        if (wa()->getUser()->isAuth() && wa()->getStorage()->read('referral_id') && !wa()->getUser()->get('referral_id')) {
            $referral_id = wa()->getStorage()->read('referral_id');
            $this->setReferralId($referral_id);
        }
    }

    protected function setReferralId($referral_id) {
        if (wa()->getUser()->isAuth()) {
            $contact = wa()->getUser();
            $contact->set('referral_id', $referral_id);
            $contact->save();
        } else {
            $session = wa()->getStorage();
            $session->write('referral_id', $referral_id);
        }
    }

    protected function getReferralId() {
        $referral_id = 0;
        $session = wa()->getStorage();
        if (wa()->getUser()->isAuth()) {
            $contact = wa()->getUser();
            if ($contact->get('referral_id')) {
                $referral_id = $contact->get('referral_id');
            } elseif ($referral_id = $session->read('referral_id')) {
                $this->setReferralId($referral_id);
            }
        } else {
            $referral_id = $session->read('referral_id');
        }
        return $referral_id;
    }

    public static function formatValue($c, $curr = null) {
        static $currencies = null;
        if ($currencies === null) {
            if ($curr) {
                $currencies = $curr;
            } else {
                $curm = new shopCurrencyModel();
                $currencies = $curm->getAll('code');
            }
        }

        if ($c['type'] == '$FS') {
            return _w('Free shipping');
        } else if ($c['type'] === '%') {
            return waCurrency::format('%0', $c['value'], 'USD') . '%';
        } else if (!empty($currencies[$c['type']])) {
            return waCurrency::format('%0{s}', $c['value'], $c['type']);
        } else {
            // Coupon of unknown type. Possibly from a plugin?..
            return '';
        }
    }

    public static function isEnabled($c) {
        $result = $c['limit'] === null || $c['limit'] > $c['used'];
        return $result && ($c['expire_datetime'] === null || strtotime($c['expire_datetime']) > time());
    }

}
