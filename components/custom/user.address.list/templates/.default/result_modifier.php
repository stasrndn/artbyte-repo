<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// Преобразовать данные для грида
if (!empty($arResult['ADDRESSES'])) {
    $tmp = [];

    foreach ($arResult['ADDRESSES'] as $arItem) {
        $tmp[]['data'] = [
            'ID' => $arItem['ID'],
            'ADDRESS' => $arItem['UF_USER_ADDRESS'],
            'ACTIVE' => ($arItem['UF_ACTIVE']) ? 'Да' : 'Нет'
        ];
    }

    $arResult['ADDRESSES'] = $tmp;
    unset($tmp);
}