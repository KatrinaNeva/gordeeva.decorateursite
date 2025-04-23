$(document).ready(function () {
	$('.my').change(function() {
    if ($(this).val() != '') $(this).parent().prev().text('Выбрано файлов: ' + $(this)[0].files.length);
    else $(this).parent().prev().text('Выберите файлы');
});

})