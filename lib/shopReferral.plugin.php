<?php

class shopReferralPlugin extends shopPlugin {

    public static $plugin_id = array('shop', 'referral');
    public static $locations = array(
        'webasyst' => 'Партнерская программа(Вебасист)',
        'wa-bonuses' => 'Плагин "Бонусы за покупку"',
    );

    public function backendMenu() {
        if ($this->getSettings('status')) {
            $html = '<li ' . (waRequest::get('plugin') == $this->id ? 'class="selected"' : 'class="no-tab"') . '>
                        <a href="?plugin=referral">Партнерская программа</a>
                    </li>';
            return array('core_li' => $html);
        }
    }

    public function frontendMyNav() {
        if ($this->getSettings('status')) {
            $html = '<a title="' . $this->getSettings('frontend_description') . '" href="' . wa()->getRouteUrl('shop/frontend/referralMain') . '">' . $this->getSettings('frontend_name') . '</a>';
            return $html;
        }
    }

    public function frontendCheckout($param) {
        if ($this->getSettings('status') && $param['step'] == 'success') {

            $template_path = wa()->getDataPath('plugins/referral/templates/tpls/FrontendCheckout.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/referral/templates/tpls/FrontendCheckout.html', 'shop');
            }
            $view = wa()->getView();
            if (wa()->getUser()->isAuth()) {
                $referral = wa()->getUser();
                if ($promo_id = $this->getSettings('success_promo')) {
                    $promo_model = new shopReferralPluginPromoModel();
                    $promo = $promo_model->getById($promo_id);
                    shopReferral::initReferralCoupon($promo, $referral['id']);
                    $view->assign('promo', $promo);
                }
                $view->assign('referral', $referral);
            }

            $view->assign('frontend_description', $this->getSettings('frontend_description'));
            $view->assign('frontend_name', $this->getSettings('frontend_name'));
            $html = $view->fetch($template_path);
            return $html;
        }
    }

    public function frontendHead() {
        if (!$this->getSettings('status')) {
            return false;
        }
        if (($coupon_code = waRequest::request('coupon_code')) && ($coupon_code != wa()->getStorage()->get('coupon_code'))) {
            $ref_coupon_model = new shopReferralPluginCouponsModel();
            $promo = $ref_coupon_model->getShopPromoByCouponCode($coupon_code);

            if ($promo && $promo['enabled']) {
                //установка реферала по купону
                if (($referral_id = $promo['contact_id']) && !$this->getReferralId()) {
                    $this->setReferralId($referral_id);
                }

                $coupm = new shopCouponModel();
                if ($promo['coupon_id'] && ($coupon = $coupm->getById($promo['coupon_id']))) {
                    $data = wa()->getStorage()->get('shop/checkout', array());
                    $data['coupon_code'] = $coupon['code'];
                    wa()->getStorage()->set('shop/checkout', $data);
                    wa()->getStorage()->set('coupon_code', $coupon_code);
                    wa()->getStorage()->remove('shop/cart');
                    wa()->getResponse()->redirect(waRequest::server('REQUEST_URI'));
                }
            }
        }

        if (($referral_id = waRequest::get('referral_id', 0)) && !$this->getReferralId()) {
            $this->setReferralId($referral_id);
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

    public function orderActionCreate($param) {

        if (($referral_id = $this->getReferralId()) && !wa()->getUser()->isAuth()) {
            $contact = new waContact($param['contact_id']);
            $contact->set('referral_id', $referral_id);
            $contact->save();
        }
    }

    public function orderActionComplete($params) {
        if ($this->getSettings('status') && $this->getSettings('order_hook') == 'complete') {
            $this->orderAction($params);
        }
    }

    public function orderActionPay($params) {
        if ($this->getSettings('status') && $this->getSettings('order_hook') == 'pay') {
            $this->orderAction($params);
        }
    }

    protected function orderAction($params) {
        $order_model = new shopOrderModel();
        $order = $order_model->getById($params['order_id']);
        $contact = new waContact($order['contact_id']);
        $referral_id = $contact->get('referral_id', 'default');
        $total = $order['total'] - $order['shipping'];
        if ($this->getSettings('including_delivery')) {
            $total = $order['total'];
        } else {
            $total = $order['total'] - $order['shipping'];
        }
        $amount = shopReferral::getReferralAmount($total);
        $def_currency = wa('shop')->getConfig()->getCurrency(true);
        $amount = shop_currency($amount, $order['currency'], $def_currency, false);

        shopReferral::multiReferralPayment($referral_id, $amount, $params['order_id']);
    }

    public static function sendAdminNotification($payment) {

        $general = wa('shop')->getConfig()->getGeneralSettings();
        $to = $general['email'];

        $template_path = wa()->getDataPath('plugins/referral/templates/printform/AdminMessage.html', false, 'shop', true);
        if (!file_exists($template_path)) {
            $template_path = wa()->getAppPath('plugins/referral/templates/printform/AdminMessage.html', 'shop');
        }
        $referral = new waContact($payment['contact_id']);

        $view = wa()->getView();
        $view->assign('referral', $referral);
        $view->assign('payment', $payment);
        $notification = $view->fetch($template_path);

        $message = new waMailMessage('Партнерская программа', $notification);
        $message->setTo($to);
        $message->send();
    }

    public static function sendReferralNotification($referral_id, $transaction) {
        $referral = new waContact($referral_id);
        $email = $referral->get('email', 'default');
        if ($email) {
            $general = wa('shop')->getConfig()->getGeneralSettings();
            $template_path = wa()->getDataPath('plugins/referral/templates/printform/ReferralMessage.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/referral/templates/printform/ReferralMessage.html', 'shop');
            }
            $view = wa()->getView();
            $view->assign('referral', $referral);
            $view->assign('transaction', $transaction);
            $notification = $view->fetch($template_path);
            $message = new waMailMessage('Партнерская программа', $notification);
            $message->setTo($email);
            $from = $general['email'];
            $message->setFrom($from, $general['name']);
            $message->send();
        }
    }

    public static function sendStatusNotification($referral_id, $payment) {
        $referral = new waContact($referral_id);
        $email = $referral->get('email', 'default');
        if ($email) {
            $general = wa('shop')->getConfig()->getGeneralSettings();
            $template_path = wa()->getDataPath('plugins/referral/templates/printform/StatusMessage.html', false, 'shop', true);
            if (!file_exists($template_path)) {
                $template_path = wa()->getAppPath('plugins/referral/templates/printform/StatusMessage.html', 'shop');
            }
            $view = wa()->getView();
            $view->assign('referral', $referral);
            $view->assign('payment', $payment);
            $notification = $view->fetch($template_path);
            $message = new waMailMessage('Партнерская программа', $notification);
            $message->setTo($email);
            $from = $general['email'];
            $message->setFrom($from, $general['name']);
            $message->send();
        }
    }

    public function orderActionRefund($params) {
        if ($this->getSettings('status')) {
            shopReferral::multiReferralRefund($params['order_id']);
        }
    }
}
