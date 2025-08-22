<?php
namespace Gordeeva\DecorateUrSite\Settings;  // <-- Namespace с заглавных
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
class FormOptionsBuilder
{
    /**
     * Возвращает типы основных настроек, регилирующих способ отрисовки украшения
     * @return array
     */
    public static function forExecutionType(): array
    {
        return $types = array(
			0 => array(
				'VAL' => 'js',
				'TEXT' => Loc::getMessage('BO_DUS_SELECT_JS'),
				'DEFAULT' => true
			), 
			1 => array(
				'VAL' => 'php',
				'TEXT' => Loc::getMessage('BO_DUS_SELECT_PHP')
			)
		);
    }
    /**
     * Возвращает типы расположения картинки для праздников 8 марта и 23 февраля
     * @return array
     */
    public static function forAnglePosition(): array
    {
        return $pos = array(
			'left' => array(
				'VAL' => 'left',
				'TEXT' => Loc::getMessage('BO_DUS_ANGLE_LEFT')
			),
			'right' => array(
				'VAL' => 'right',
				'TEXT' => Loc::getMessage('BO_DUS_ANGLE_RIGHT'),
				'DEFAULT' => true
			)
		);
    }
    /**
     * Возвращает картинки по умолчанию для украшения сайта
     * @return array
     */
	public static function forDefaultImages(string $mi): array 
	{
		// Получить путь к папке images // А можно ли сделать это покрасивее. Вынести параметр с дефолтными изображениями в отдельный файл
		$docRoot = $_SERVER['DOCUMENT_ROOT'];
		$fullPath = Loader::getLocal('modules/'.$mi.'/install/assets/images/'); // Полный путь
		$moduleImgsPath = str_replace($docRoot, '', $fullPath);
		return array(
			1 => $moduleImgsPath.'new_year.png',
			2 => $moduleImgsPath.'23f.png',
			3 => $moduleImgsPath.'8m.png'
		);
	}
}