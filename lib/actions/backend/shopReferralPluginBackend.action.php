<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
class shopReferralPluginBackendAction extends waViewAction {

    public function execute() {
        $this->setLayout(new shopReferralPluginBackendLayout());
    }

}
