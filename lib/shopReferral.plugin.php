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

}
