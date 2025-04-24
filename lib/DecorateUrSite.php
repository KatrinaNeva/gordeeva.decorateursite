<?namespace Bazarow\DecorateUrSite;
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
		<?if(isset($options['snow']) && $options['snow'] == 'Y'):?>
			document.addEventListener('DOMContentLoaded', function() {
			// Создаем контейнер для снежинок
			const snowflakesContainer = document.createElement('div');
			snowflakesContainer.style.position = 'fixed';
			snowflakesContainer.style.top = '0';
			snowflakesContainer.style.left = '0';
			snowflakesContainer.style.width = '100%';
			snowflakesContainer.style.height = '100%';
			snowflakesContainer.style.pointerEvents = 'none';
			snowflakesContainer.style.zIndex = '9999';
			document.body.appendChild(snowflakesContainer);

			// Количество снежинок (можно настроить)
			const snowflakesCount = 50;

			// Создаем снежинки
			for (let i = 0; i < snowflakesCount; i++) {
			  createSnowflake();
			}

			function createSnowflake() {
			  const snowflake = document.createElement('div');
			  snowflake.innerHTML = '❄'; // Можно заменить на ✦, ✿ или использовать SVG
			  snowflake.style.position = 'absolute';
			  snowflake.style.userSelect = 'none';
			  snowflake.style.opacity = Math.random() * 0.7 + 0.3; // Разная прозрачность
			  snowflake.style.fontSize = `${Math.random() * 10 + 10}px`; // Разный размер
			  snowflake.style.color = '#ffffff'; // Белый цвет (можно изменить)

			  // Начальная позиция (случайная по горизонтали, выше экрана)
			  const startX = Math.random() * window.innerWidth;
			  const startY = -50;
			  snowflake.style.left = `${startX}px`;
			  snowflake.style.top = `${startY}px`;

			  // Скорость и анимация
			  const animationDuration = Math.random() * 10 + 5; // Разная скорость падения
			  const driftAmount = Math.random() * 50 - 25; // Случайное смещение вбок

			  snowflake.style.transition = `transform ${animationDuration}s linear, top ${animationDuration}s linear`;

			  snowflakesContainer.appendChild(snowflake);

			  // Запуск анимации
			  function animateSnowflake() {
				// Новые координаты (ниже экрана + случайный сдвиг)
				const endY = window.innerHeight + 50;
				const endX = startX + driftAmount;

				// Применяем анимацию
				setTimeout(() => {
				  snowflake.style.top = `${endY}px`;
				  snowflake.style.transform = `translateX(${driftAmount}px)`;
				}, 10);

				// После завершения анимации возвращаем снежинку наверх
				setTimeout(() => {
				  snowflake.style.transition = 'none';
				  snowflake.style.top = `${-50}px`;
				  snowflake.style.transform = 'translateX(0)';
				  // Небольшая задержка перед повторной анимацией
				  setTimeout(() => {
					snowflake.style.transition = `transform ${animationDuration}s linear, top ${animationDuration}s linear`;
					animateSnowflake();
				  }, 50);
				}, animationDuration * 1000);
			  }

			  // Запускаем анимацию с небольшой задержкой для разного времени падения
			  setTimeout(animateSnowflake, Math.random() * 5000);
			}
		  });
		<?endif;?>
		var parentElem = document.body;
		parentElem.insertBefore(div, parentElem.firstChild);
	</script>	
	<?}
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
            $decor .= <<<HTML
                <script>
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
                            snowflake.innerHTML = '❄';
                            
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
                </script>
            HTML;
        }
        return $decor;
    }
}