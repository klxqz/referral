<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendLayout extends shopBackendLayout {

    public function execute() {
        parent::execute();
        $this->assign('page', 'referral');
    }

}
