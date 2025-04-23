<?php
use \Bitrix\Main\Config\Option;
global $APPLICATION;
$module_id = 'bazarow.decorateursite';

CModule::AddAutoloadClasses($module_id, [
    'Bazarow\\DecorateUrSite\\Helper' => 'lib/Helper.php',
    'Bazarow\\DecorateUrSite\\DecorateUrSite' => 'lib/DecorateUrSite.php',
    'Bazarow\\DecorateUrSite\\Settings\\FormOptionsBuilder' => 'lib/settings/FormOptionsBuilder.php',
    'Bazarow\\DecorateUrSite\\EventHandlers\\MainEvents' => 'lib/eventhandlers/MainEvents.php'
]);
//\Bazarow\DecorateUrSite\EventHandlers\MainEvents::registerHandlers(); <-- не подходит, так как сначала должен быть зарегистрирован модуль. Такая регистрация события подходит для использования в сторонних компонентах, других модулях при предварительном подключении модуля: CModule::IncludeModule('bazarow.decorateursite');
$APPLICATION->SetAdditionalCSS("/bitrix/css/$module_id/main.css");
//$APPLICATION->AddHeadScript("/bitrix/css/$module_id/main.js");
if (COption::GetOptionString($module_id, 'source_script_edit_main') == 'js' && (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') && strpos($_SERVER['REQUEST_URI'], 'bitrix/') === FALSE && php_sapi_name() != "cli"): ?>
	<?
	$optValues = \Bazarow\DecorateUrSite\Helper::getAllNeedOptions($module_id);	// get actual parameters (options)
	// Выводим найденные связанные значения
	if (!empty($optValues)) {
		\Bazarow\DecorateUrSite\DecorateUrSite::drawDecorations($optValues);	 // draw decorations	
	}
	?>
<?endif;?>