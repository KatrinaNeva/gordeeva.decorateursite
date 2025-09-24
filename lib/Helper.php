<?namespace Gordeeva\DecorateUrSite;
use \Bitrix\Main\Context;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Main\Data\TaggedCache;
use \Bitrix\Main\FileTable;
class Helper {
	/**
     * Возвращает путь до картинки.
     * Ресайзит картинку, если нужно
     * @param int $imageId
     * @return string
     */
    public function getImagePathById($imageId)
    {
        $imagePath = '';
        if (!$imagePath) {
            $fileData = FileTable::getById($imageId)->fetch();
            if ($fileData) {
                $imagePath = '/upload/' . $fileData['SUBDIR'] . '/' . $fileData['FILE_NAME'];
            }
        }
        return $imagePath;
    }
    /**
     * Возвращает установленные действительные настройки.
     * @param int $mi
     * @return array
     */
	public static function getAllNeedOptions($mi) {
		$allOptions = Option::getForModule($mi);
		$relatedValues = [];
		foreach ($allOptions as $key => $value) {
			if (strpos($key, 'source_activity_edit') === 0 && $value === 'Y') {
				$suffix = substr($key, strlen('source_activity_'));
				foreach ($allOptions as $subKey => $subValue) {
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
        $cache->noOutput();
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
            $taggedCache->startTagCache($cacheDir);
            $taggedCache->registerTag($cacheTag);
            $taggedCache->endTagCache();
            $cache->endDataCache($data);
        }
        return $data;
    }
}