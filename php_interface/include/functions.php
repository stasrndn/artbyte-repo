<?php

/**
 * Очищает кеш в указанной папке
 * @param $userId
 * @return void
 */
function cacheCleanDirByUserId($userId) {
    if ($userId) {
        $cache = \Bitrix\Main\Data\Cache::createInstance();
        $cache->cleanDir('/s1/highload_blocks/user_addresses/user_' . $userId);
    }
}