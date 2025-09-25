<?namespace Gordeeva\DecorateUrSite;
use \Bitrix\Main\Config\Option;
class DecorateUrSite {
    /**
     * Отрисовывает украшение сайта на основе установленных действительных настроек
     * @param array $options
     * @return void
     */
	public static function drawDecorations($options) {?>
		<script>
            //alert('check it');
            var div = document.createElement('div');
            <?if(isset($options['angle'])):?>
                var divInner = document.createElement('div');
                div.className = "site-decor angled";
                divInner.className = "site-decor-inner";
                div.classList.add('angled-' + '<?=$options['angle']?>');
                divInner.style.backgroundImage = 'url(<?=$options['image']?>)';
                div.appendChild(divInner);
            <?else:?>
                div.className = "site-decor";
                div.style.backgroundImage = 'url(<?=$options['image']?>)';
            <?endif;?>
            div.style.position = '<?=$options['fix']?>';
            var parentElem = document.body;
            parentElem.insertBefore(div, parentElem.firstChild);
        </script>
        <?if(isset($options['snow']) && $options['snow'] == 'Y') {
            echo self::drawSnowScript();
        }
	}

    /**
     * Отрисовывает украшение сайта на основе кешированных установленных действительных настроек
     * @param array $options
     * @return string
     */
    public static function drawDecorationsCached($options) {
	    $angled = isset($options['angle']);
	    $pos = $angled ? $options['angle'] : '';
        $angledClasses = $angled ? ' angled angled-'.$pos.'' : '';
	    $image = $options['image'];
	    $fix = $options['fix'];
	    $decor = '';

        $decor .= '<div class="site-decor' . $angledClasses . '" style="position: ' . $fix . ';' . (!$angled ? " background-image: url('" . $image . "')" : "") . '">';
        $decor .= $angled
            ? '<div class="site-decor-inner" style="background-image: url(' . $image . ')"></div>'
            : '';
        $decor .= '</div>';?>

        <?if(isset($options['snow']) && $options['snow'] == 'Y') {
            $decor .= self::drawSnowScript();
        }
        return $decor;
    }
    /**
     * Отрисовывает скрипт со снежинками общий для всех типов отрисовки
     * @return string
     */
    private static function drawSnowScript(){
        return "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.createElement('div');
                container.style.cssText = [
                    'position: fixed;',
                    'top: 0;',
                    'left: 0;',
                    'width: 100%;',
                    'height: 100%;',
                    'pointer-events: none;',
                    'z-index: 9999;'
                ].join(' ');
                document.body.appendChild(container);
        
                const SNOWFLAKES_COUNT = 50;
                const BASE_SIZE = 10;
                const ANIMATION_OFFSET = 50;
        
                function createSnowflake() {
                    const snowflake = document.createElement('div');
                    snowflake.innerHTML = '&#10052;';
        
                    // Вычисление параметров
                    const opacity = Math.random() * 0.7 + 0.3;
                    const size = Math.random() * 10 + BASE_SIZE;
                    const startX = Math.random() * window.innerWidth;
                    const duration = Math.random() * 10 + 5;
                    const drift = Math.random() * 50 - 25;
        
                    // Настройка стилей
                    snowflake.style.cssText = [
                        'position: absolute;',
                        'user-select: none;',
                        'opacity: ' + opacity + ';',
                        'font-size: ' + size + 'px;',
                        'color: #ffffff;',
                        'left: ' + startX + 'px;',
                        'top: -' + ANIMATION_OFFSET + 'px;',
                        'transition: transform ' + duration + 's linear, top ' + duration + 's linear;'
                    ].join(' ');
        
                    container.appendChild(snowflake);
        
                    const animate = () => {
                        // Фаза падения
                        setTimeout(() => {
                            snowflake.style.top = (window.innerHeight + ANIMATION_OFFSET) + 'px';
                            snowflake.style.transform = 'translateX(' + drift + 'px)';
                        }, 10);
        
                        // Сброс позиции
                        setTimeout(() => {
                            snowflake.style.transition = 'none';
                            snowflake.style.top = '-' + ANIMATION_OFFSET + 'px';
                            snowflake.style.transform = 'translateX(0)';
        
                            // Перезапуск анимации
                            setTimeout(() => {
                                snowflake.style.transition = [
                                    'transform ' + duration + 's linear',
                                    'top ' + duration + 's linear'
                                ].join(',');
                                animate();
                            }, 50);
                        }, duration * 1000);
                    };
        
                    setTimeout(animate, Math.random() * 5000);
                }
        
                // Инициализация снежинок
                for (let i = 0; i < SNOWFLAKES_COUNT; i++) {
                    createSnowflake();
                }
            });
        </script>";
    }
}