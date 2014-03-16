<?php

/**
 * @author Коробов Николай wa-plugins.ru <support@wa-plugins.ru>
 * @link http://wa-plugins.ru/
 */
return array(
    'status' => array(
        'title' => 'Статус',
        'description' => '',
        'value' => '1',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            '0' => 'Выключен',
            '1' => 'Включен',
        )
    ),
    'referral_percent' => array(
        'title' => 'Процент начисляемый рефералу',
        'description' => 'Процент от суммы заказа, для начисления на персональный счет реферала, привлекшего клиента.',
        'value' => '20',
        'control_type' => waHtmlControl::INPUT,
    ),
    'including_delivery' => array(
        'title' => 'Учитывать доставку',
        'description' => 'Расчет начислений рефералу производится с учетом стоимости доставки в заказе.',
        'value' => '0',
        'control_type' => waHtmlControl::CHECKBOX,
    ),
    'order_hook' => array(
        'title' => 'Статус заказа',
        'description' => 'Статус заказа, при установке которого осуществляется начисление рефералу',
        'value' => 'complete',
        'control_type' => waHtmlControl::SELECT,
        'options' => array(
            'complete' => 'Выполнен',
            'pay' => 'Оплачен',
        )
    )
);
