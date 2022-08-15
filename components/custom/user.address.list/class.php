<?php

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\ORM\Data\DataManager;

class UserAddressListComponent extends \CBitrixComponent
{
    /**
     * Время кеширования по умолчанию
     */
    private const CACHE_TIME = 3600;

    /**
     * ID highload-блока по умолчанию
     */
    private const HL_BLOCK_ID = 1;

    /**
     * ID текущего пользователя
     * @var int|null
     */
    private $currentUserId = null;

    /**
     * Директория кеша пользователя
     * @var string|null
     */
    private $cacheDir = SITE_ID . '/highload_blocks/user_addresses/user_';


    /**
     * Выполняет код компонента
     * @throws LoaderException
     */
    public function executeComponent()
    {
        if (!is_null($this->currentUserId = $this->getCurrentUserId()) && $this->checkModules()) {
            $this->includeComponentLang('class.php');
            $this->getData();
        }

        $this->includeComponentTemplate();
    }

    /**
     * Подготавливает параметры компонента
     * @param $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        if (!isset($arParams['SHOW_ACTIVE'])) {
            $arParams['SHOW_ACTIVE'] = 'N';
        }

        if (!isset($arParams['HL_BLOCK_ID'])) {
            $arParams['HL_BLOCK_ID'] = self::HL_BLOCK_ID;
        }

        if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = self::CACHE_TIME;
        }

        return $arParams;
    }

    /**
     * Проверяет установку различных модулей,
     * необходимых для работы компонента
     * @return bool
     * @throws LoaderException
     */
    private function checkModules(): bool
    {
        if (!Loader::includeModule('highloadblock')) {
            ShowError(Loc::getMessage('HIGHLOADBLOCK_MODULE_NOT_INSTALLED'));
            return false;
        }

        return true;
    }

    /**
     * Возвращает id текущего авторизованного пользователя
     * @return int|null
     */
    private function getCurrentUserId():? int
    {
        return CurrentUser::get()->getId();
    }

    /**
     * Возвращает класс сущности highload-блока
     * @return DataManager|string|void
     */
    private function getEntityClass()
    {
        try {
            $hlData = HL\HighloadBlockTable::getById($this->arParams['HL_BLOCK_ID'])->fetch();
            $compiledEntity = HL\HighloadBlockTable::compileEntity($hlData);

            return $compiledEntity->getDataClass();
        } catch (Exception $exception) {
            ShowError($exception->getMessage());
        }
    }

    /**
     * Собирает и кеширует данные для текущего пользователя
     * @return void
     */
    private function getData(): void
    {
        try {
            $entityClass = $this->getEntityClass();

            $cache = Cache::createInstance();
            $cacheTime = $this->arParams['CACHE_TIME'];
            $cacheId = serialize($this->cacheDir . $this->currentUserId);
            $cacheDir = $this->cacheDir . $this->currentUserId;

            if ($cache->initCache($cacheTime, $cacheId, $cacheDir)) {
                $this->arResult = $cache->getVars();
                return;
            }

            if ($cache->startDataCache($cacheTime, $cacheId, $cacheDir)) {
                $arFilter = ['UF_USER_ID' => $this->currentUserId];

                if ($this->arParams['SHOW_ACTIVE'] === 'Y') {
                    $arFilter = array_merge($arFilter, ['UF_ACTIVE' => true]);
                }

                $result = $entityClass::getList([
                    'select' => ['*'],
                    'filter' => $arFilter
                ])->fetchAll();

                if ($result) {
                    $this->arResult['ADDRESSES'] = $result;
                    $cache->endDataCache($this->arResult);
                } else {
                    $cache->abortDataCache();
                }
            }

        } catch (Exception $exception) {
            ShowError($exception->getMessage());
        }
    }
}
