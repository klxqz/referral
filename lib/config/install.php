<?php

$plugin_id = array('shop', 'referral');
$app_settings_model = new waAppSettingsModel();
$app_settings_model->set($plugin_id, 'status', '1');
$app_settings_model->set($plugin_id, 'referral_percent', '5');
$app_settings_model->set($plugin_id, 'including_delivery', '0');
$app_settings_model->set($plugin_id, 'order_hook', 'complete');
$app_settings_model->set($plugin_id, 'send_referral_message', '1');
$app_settings_model->set($plugin_id, 'send_admin_message', '1');
$app_settings_model->set($plugin_id, 'send_status_message', '1');

$fields = waContactFields::getAll();
if (!isset($fields['referral_id'])) {
    $field = new waContactStringField('referral_id', 'referral_id', array('app_id' => 'shop'));
    waContactFields::createField($field);
    waContactFields::enableField($field, 'person');
}
