<?php

$fields = waContactFields::getAll();
if (!isset($fields['referral_id'])) {
    $field = new waContactStringField('referral_id', 'referral_id', array('app_id' => 'shop'));
    waContactFields::createField($field);
    waContactFields::enableField($field, 'person');
}
