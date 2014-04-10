<?php

class shopReferralPluginSettingsAction extends waViewAction {

    protected $printforms = array(
        'FrontendDescription' => array(
            'name' => 'Ваш текст на странице в личном кабинете',
            'path' => 'plugins/referral/templates/printform/FrontendDescription.html',
            'change_tpl' => false,
        ),
        'ReferralMesssage' => array(
            'name' => 'Уведомление рефералу о начисление бонусов',
            'path' => 'plugins/referral/templates/printform/ReferralMessage.html',
            'change_tpl' => false,
        ),
        'AdminMesssage' => array(
            'name' => 'Уведомление администратору о заявке на выплату',
            'path' => 'plugins/referral/templates/printform/AdminMessage.html',
            'change_tpl' => false,
        ),
        'StatusMesssage' => array(
            'name' => 'Уведомление администратору о изменение статуса выплаты',
            'path' => 'plugins/referral/templates/printform/StatusMessage.html',
            'change_tpl' => false,
        ),
    );
    protected $plugin_id = array('shop', 'referral');

    public function execute() {
        $app_settings_model = new waAppSettingsModel();
        $settings = $app_settings_model->get($this->plugin_id);
        foreach ($this->printforms as &$printform) {
            $template_path = wa()->getDataPath($printform['path'], false, 'shop', true);
            if (file_exists($template_path)) {
                $printform['change_tpl'] = true;
            } else {
                $template_path = wa()->getAppPath($printform['path'], 'shop');
            }
            $printform['content'] = file_get_contents($template_path);
        }
        unset($printform);
        $this->view->assign('settings', $settings);
        $this->view->assign('printforms', $this->printforms);
    }

}
