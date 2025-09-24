<?php
use Gordeeva\DecorateUrSite\DecorateUrSite;
use \Bitrix\Main\Config\Option;
use Gordeeva\DecorateUrSite\Helper;

global $APPLICATION;
$module_id = 'gordeeva.decorateursite';

CModule::AddAutoloadClasses($module_id, [
    'Gordeeva\\DecorateUrSite\\Helper' => 'lib/Helper.php',
    'Gordeeva\\DecorateUrSite\\DecorateUrSite' => 'lib/DecorateUrSite.php',
    'Gordeeva\\DecorateUrSite\\Settings\\FormOptionsBuilder' => 'lib/settings/FormOptionsBuilder.php',
    'Gordeeva\\DecorateUrSite\\EventHandlers\\MainEvents' => 'lib/eventhandlers/MainEvents.php'
]);
$APPLICATION->SetAdditionalCSS("/bitrix/css/$module_id/main.css");
if (COption::GetOptionString($module_id, 'source_script_edit_main') == 'js' && (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') && strpos($_SERVER['REQUEST_URI'], 'bitrix/') === FALSE && php_sapi_name() != "cli"): ?>
	<?
	$optValues = Helper::getAllNeedOptions($module_id);
	if (!empty($optValues)) {
		DecorateUrSite::drawDecorations($optValues);
	}
	?>
<?endif;?>