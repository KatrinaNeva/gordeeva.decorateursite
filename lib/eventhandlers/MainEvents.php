<?php
namespace Bazarow\DecorateUrSite\EventHandlers;
use \Bitrix\Main\Localization;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\EventManager;
use \Bazarow\DecorateUrSite;

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
		$module_id = 'bazarow.decorateursite';
		if((empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') && strpos($_SERVER['REQUEST_URI'], 'bitrix/') === FALSE && php_sapi_name() != "cli" && Option::get($module_id, 'source_script_edit_main') != 'js'){

            $optValues = DecorateUrSite\Helper::cacheData($module_id);
			if (!empty($optValues)) {


                // включаем буферизацию вывода, все идет в отдельный буфер
                ob_start();
                global $APPLICATION;
// вызываем компонент, который формирует список блок случайных элементов инфоблока
                //print_r($optValues);
// выключаем буферизацию и помечаем этот контент меткой «random-elements»
                $APPLICATION->AddViewContent('drawDecorations', ob_get_clean());


				echo DecorateUrSite\DecorateUrSite::drawDecorationsCached($optValues);
			}
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			//DONE --> В первую очередь разобраться, почему НЕ выводится выбранное значение ОПЦИИ. Option::get ПУСТОЙ - why??? --> ОБРАЩАЛАСЬ К НЕВЕРНОМУ $name, ЗАБЫВ ПРО ТАБЫ
			//
			//FOR PHP (CACHE)+JS VARIATION
			//
			//Добавить таблицу в базе данных при установке - index.php
			//Добавить классы для работы с данными настроек - обработка и запись в БД - как минимум 2 класса
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			//
			//FOR ONLY JS VARIATION
			//
			//Обработка пока что только в файле include.php
			//
			// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!	
        }
    } 
 }