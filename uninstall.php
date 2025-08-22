<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$module_id = 'gordeeva.decorateursite';
// Отписка от событий
if (CModule::IncludeModule($module_id)) {
    try {
        // 1. Отписываем D7-обработчики
        \Gordeeva\DecorateUrSite\EventHandlers\MainEvents::unregisterHandlers();
        // 2. Удаляем legacy-обработчики (если есть)
        $eventManager = \Bitrix\Main\EventManager::getInstance();
        $eventManager->unRegisterEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            $module_id,
            '\Gordeeva\DecorateUrSite\Main',
            'decorateSite'
        );   
        // 3. Очищаем настройки
        \Bitrix\Main\Config\Option::delete($module_id);    
        // 4. Чистим кэш
        $GLOBALS['CACHE_MANAGER']->CleanAll();
    } catch (\Exception $e) {
        // Логирование ошибок
        \CEventLog::Add([
            'SEVERITY' => 'ERROR',
            'AUDIT_TYPE_ID' => 'MODULE_UNINSTALL',
            'MODULE_ID' => $module_id,
            'DESCRIPTION' => $e->getMessage(),
        ]);
    }
}