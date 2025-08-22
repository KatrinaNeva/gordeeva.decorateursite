<?php
use Gordeeva\DecorateUrSite\DecorateUrSite;
use \Bitrix\Main\Config\Option;
global $APPLICATION;
$module_id = 'gordeeva.decorateursite';

CModule::AddAutoloadClasses($module_id, [
    'Gordeeva\\DecorateUrSite\\Helper' => 'lib/Helper.php',
    'Gordeeva\\DecorateUrSite\\DecorateUrSite' => 'lib/DecorateUrSite.php',
    'Gordeeva\\DecorateUrSite\\Settings\\FormOptionsBuilder' => 'lib/settings/FormOptionsBuilder.php',
    'Gordeeva\\DecorateUrSite\\EventHandlers\\MainEvents' => 'lib/eventhandlers/MainEvents.php'
]);
//\Gordeeva\DecorateUrSite\EventHandlers\MainEvents::registerHandlers(); <-- не подходит, так как сначала должен быть зарегистрирован модуль. Такая регистрация события подходит для использования в сторонних компонентах, других модулях при предварительном подключении модуля: CModule::IncludeModule('gordeeva.decorateursite');
$APPLICATION->SetAdditionalCSS("/bitrix/css/$module_id/main.css");
if (COption::GetOptionString($module_id, 'source_script_edit_main') == 'js' && (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') && strpos($_SERVER['REQUEST_URI'], 'bitrix/') === FALSE && php_sapi_name() != "cli"): ?>
	<?
	$optValues = \Gordeeva\DecorateUrSite\Helper::getAllNeedOptions($module_id);	// get actual parameters (options)
	// Выводим найденные связанные значения
	if (!empty($optValues)) {
		DecorateUrSite::drawDecorations($optValues);	 // draw decorations
	}
	?>
<?endif;?>