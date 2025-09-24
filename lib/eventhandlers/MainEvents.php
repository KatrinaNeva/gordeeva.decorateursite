<?php
namespace Gordeeva\DecorateUrSite\EventHandlers;
use \Bitrix\Main\Localization;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\EventManager;
use \Gordeeva\DecorateUrSite;

global $APPLICATION;
Localization\Loc::loadMessages(__FILE__);
class MainEvents{
    public static function registerHandlers()
    {
        EventManager::getInstance()->addEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            [__CLASS__, 'decorateSite']
        );
    }
    public static function unregisterHandlers()
    {
        EventManager::getInstance()->removeEventHandler(
            'main',
            'OnBeforeEndBufferContent',
            [__CLASS__, 'decorateSite']
        );
    }

    /**
     * Отрисовывает украшение сайта на основе закешированных установленных действительных настроек
     * @return void
     */
    public static function decorateSite(){
		$module_id = 'gordeeva.decorateursite';
		if((empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') && strpos($_SERVER['REQUEST_URI'], 'bitrix/') === FALSE && php_sapi_name() != "cli" && Option::get($module_id, 'source_script_edit_main') != 'js'){
            $optValues = DecorateUrSite\Helper::cacheData($module_id);
            if (!empty($optValues)) {
                global $APPLICATION;
                $content = DecorateUrSite\DecorateUrSite::drawDecorationsCached($optValues);
                $APPLICATION->AddViewContent('drawDecorations', $content);
            }
        }
    } 
 }