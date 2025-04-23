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
}