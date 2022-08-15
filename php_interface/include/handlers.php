<?php

use Bitrix\Main\Entity\Event;
use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

/**
 * Обработчик перед добавлением записи в highload-блоке
 */
$eventManager->addEventHandler('', 'UserAddressesOnBeforeAdd', 'onBeforeAddUserAddresses');
function onBeforeAddUserAddresses(Event $event) {
    $userId = $event->getParameter('fields')['UF_USER_ID']['VALUE'];
    cacheCleanDirByUserId($userId);
}

/**
 * Обработчик перед обновлением записи в highload-блоке
 */
$eventManager->addEventHandler('', 'UserAddressesOnBeforeUpdate', 'onBeforeUpdateUserAddresses');
function onBeforeUpdateUserAddresses(Event $event)
{
    $userId = $event->getParameter('fields')['UF_USER_ID']['VALUE'];
    cacheCleanDirByUserId($userId);
}