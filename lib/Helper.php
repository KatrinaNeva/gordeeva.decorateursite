<?namespace Bazarow\DecorateUrSite;
use \Bitrix\Main\Context;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Data\TaggedCache;
class Helper {//Класс должен иметь то же наименование, что и файл с этим классом!!!
	/**
     * Возвращает путь до картинки.
     * Ресайзит картинку, если нужно
     * @param int $imageId
     * @return string
     */
    public function getImagePathById($imageId)
    {
        //$settingsResize = \dev2funModuleOpenGraphClass::getInstance()->getSettingsResize();
        $imagePath = '';
        /* if ($settingsResize['ENABLE'] === 'Y' && (!empty($settingsResize['WIDTH']) || !empty($settingsResize['HEIGHT']))) {
            if (empty($settingsResize['TYPE'])) $settingsResize['TYPE'] = BX_RESIZE_IMAGE_PROPORTIONAL;
            $arImage = \CFile::ResizeImageGet($imageId, [
                'width' => (!empty($settingsResize['WIDTH']) ? $settingsResize['WIDTH'] : 99999),
                'height' => (!empty($settingsResize['HEIGHT']) ? $settingsResize['HEIGHT'] : 99999),
            ], $settingsResize['TYPE']);
            if ($arImage) {
                $imagePath = $arImage['src'];
            }
        } */
        if (!$imagePath) {
            $imagePath = \CFile::GetPath($imageId);
        }
        /* if ($imagePath) {
            $oModule = \dev2funModuleOpenGraphClass::getInstance();
            $prefix = '';
            if (!preg_match('#^(http|https)#', $imagePath)) {
                $prefix = $oModule->getProtocol() . $oModule->getHost();
            }
            $imagePath = $prefix . $imagePath;
        } */
        return $imagePath;
    }
    /**
     * Возвращает установленные действительные настройки.
     * @param int $mi
     * @return array
     */
	public static function getAllNeedOptions($mi) { //не стоит ли передавать в метод в качестве аргумента опции? или правильно как сейчас в методе получать их с помощью Option::getForModule($mi)?? нид ту синк
		$allOptions = Option::getForModule($mi);
		$relatedValues = [];
		// Перебираем все элементы массива
		foreach ($allOptions as $key => $value) {
			// Проверяем, содержит ли ключ 'source_activity_edit' и значение равно 'Y'
			if (strpos($key, 'source_activity_edit') === 0 && $value === 'Y') {
				// Извлекаем суффикс (например, 'edit1')
				$suffix = substr($key, strlen('source_activity_')); // Получаем 'edit1', 'edit2' и т.д.
				//echo "Найдена активная запись с суффиксом: $suffix\n";
				// Теперь ищем все ключи с этим суффиксом
				foreach ($allOptions as $subKey => $subValue) {
					// Проверяем, заканчивается ли ключ на наш суффикс (edit1, edit2 и т.д.)
					if (strpos($subKey, $suffix) !== false && $subKey !== $key) {
						$parts = explode('_', $subKey);
						if (count($parts) >= 3) {
							$subKey = $parts[1];
						}
						if(($subKey == 'defaultImage' && !empty($allOptions['source_image_'.$suffix])) || empty($allOptions['source_'.$subKey.'_'.$suffix])) continue;
						$relatedValues['suffix'] = $suffix;
						if($subKey == 'defaultImage' && empty($allOptions['source_image_'.$suffix])) {
							$relatedValues['image'] = $subValue;
						} elseif($subKey == 'image') {
							$relatedValues[$subKey] = (new self())->getImagePathById($subValue);
						} elseif($subKey == 'fix') {
							$relatedValues[$subKey] = $subValue == 'Y' ? 'fixed' : 'absolute';
						} else {	
							$relatedValues[$subKey] = $subValue;
						}
					}
				}
			}
		}
		return $relatedValues;
	}
    /**
     * Кеширует и возвращает закешированные установленные действительные настройки.
     * @param int $mi
     * @return array
     */
	public static function cacheData($mi){
        $data = [];
        $cache = Cache::createInstance();
        $taggedCache = new TaggedCache();
        $cacheKey = md5(__METHOD__ . Context::getCurrent()->getSite());
        $cacheDir = '/'.$mi.'/';
        if ($cache->initCache(3600, $cacheKey, $cacheDir)) {
            $data = $cache->getVars();
        } elseif ($cache->startDataCache()) {
            $optValues = self::getAllNeedOptions($mi);
            $cacheTag = $optValues['suffix'];
            if (!empty($optValues)) {
                foreach($optValues as $k => $val) {
                    $data[$k] = $val;
                }
            }
            // Устанавливаем тег
            $taggedCache->startTagCache($cacheDir);
            $taggedCache->registerTag($cacheTag);
            $taggedCache->endTagCache();
            $cache->endDataCache($data);
        }
        return $data;
    }
}