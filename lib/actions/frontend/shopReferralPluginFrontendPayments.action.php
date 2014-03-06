<?php

class shopReferralPluginFrontendPaymentsAction extends shopFrontendAction {

    public function execute() {
        $contact = wa()->getUser();

        $data = waRequest::post('data');
        if ($data) {
            $contact->set('referral_data', $data);
        }

        $this->view->assign('data', $contact->get('referral_data'));
        $this->view->assign('breadcrumbs', self::getBreadcrumbs());
        $this->getResponse()->setTitle('Реквизиты');
    }

    public static function getBreadcrumbs() {
        $result = shopReferralPluginFrontendAction::getBreadcrumbs();
        $result[] = array(
            'name' => 'Партнерская программа',
            'url' => wa()->getRouteUrl('shop/frontend/'),
        );
        return $result;
    }

}
