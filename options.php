<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\HttpApplication;
use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\SiteTemplateTable;
use Bazarow\DecorateUrSite\Settings\FormOptionsBuilder;
Loc::loadMessages(__FILE__);
$request = HttpApplication::getInstance()->getContext()->getRequest();
$module_id = htmlspecialcharsbx($request["mid"] != "" ? $request["mid"] : $request["id"]);
Loader::includeModule($module_id);
CModule::IncludeModule("iblock");
// options
$allOptions[] = array(
    'header',
    Loc::getMessage('BO_DUS_OPTIONS_TAB_HEADER')
);
$allOptions[] = array(
    'source_script',
    Loc::getMessage('BO_DUS_SCRIPT_OPTION'),
    array(
        'selectbox'
    ),
	false
);
$allOptions[] = array(//А можно ли сделать это покрасивее. Вынести параметр с дефолтными изображениями в отдельный файл ---> DONE (вынесено в FormOptionsBuilder)
    'source_defaultImage',
    Loc::getMessage('BO_DUS_IMAGE_DEFAULT'),
    array(
        'file'
    ),
	true,
	FormOptionsBuilder::forDefaultImages($module_id)
);
$allOptions[] = array(
    'source_image',
    Loc::getMessage('BO_DUS_IMAGE'),
    array(
        'file'
    ),
	false
);
$allOptions[] = array(
    'source_activity',
    Loc::getMessage('BO_DUS_ACTIVITY'),
    array(
        'checkbox'
    ),
	false
);
$allOptions[] = array(
    'source_snow',
    Loc::getMessage('BO_DUS_SNOW'),
    array(
        'checkbox'
    ),
	true
);
$allOptions[] = array(
    'source_fix',
    Loc::getMessage('BO_DUS_FIXED'),
    array(
        'checkbox'
    ),
	false
);
$allOptions[] = array(
    'source_angle',
    Loc::getMessage('BO_DUS_ANGLE_POSITION'),
    array(
        'radio'
    ),  
	false
);
// tabs
$tabControl = new \CAdmintabControl('tabControl', array(
	array('DIV' => 'edit_main', 'TAB' => Loc::getMessage('BO_DUS_OPTIONS_TAB_MAIN'), 'ICON' => ''),
    array('DIV' => 'edit1', 'TAB' => Loc::getMessage('BO_DUS_OPTIONS_TAB_NY'), 'ICON' => ''),
    array('DIV' => 'edit2', 'TAB' => Loc::getMessage('BO_DUS_OPTIONS_TAB_FEB'), 'ICON' => ''),
    array('DIV' => 'edit3', 'TAB' => Loc::getMessage('BO_DUS_OPTIONS_TAB_MARCH'), 'ICON' => ''),
));

// post save
if($_SERVER['REQUEST_METHOD'] == "POST" && $Update.$Apply.$RestoreDefaults <> '' && check_bitrix_sessid())
{
	foreach($tabControl->tabs as $tab) {
		foreach ($allOptions as $arOption)
		{
			if ($arOption[0] == 'header')
			{
				continue;
			}
			$name = $arOption[0].'_'.$tab['DIV'];
			$val = $_POST[$name];
		
			if (isset($_POST[$name."_del"]) && !is_array($val)) {
				\CFile::Delete($val);
				\COption::setOptionString($module_id, $name, '');
			}
			if($arOption[2][0] == "checkbox" && $val != "Y")
			{
				$val = "N";
			} 
			//написать скрипт который уберет активность у остальных чекбоксов source_activity при выборе одного из них. <-- сделано через js
			elseif($arOption[2][0] == "file" && !empty($val))
			{
				if(!$arOption[3]) {
					$val = \CIBlock::makeFileArray($val);
					$fileID = \CFile::SaveFile($val, $module_id, true);
					if ($fileID) {					
						\COption::setOptionString($module_id, $name, $fileID);
					} 
				} else {
					\COption::setOptionString($module_id, $name, $val);
				}	
			}
			elseif ($arOption[2][0] == 'selectbox')
			{
				$val = '';
				if (isset($$name))
				{
					for ($j=0; $j<count($$name); $j++)
					{
						if (trim(${$name}[$j]) <> '')
						{
							$val .= ($val <> '' ? ',':'') . trim(${$name}[$j]);
						}
					}
				}	
			}
			
			if($arOption[2][0] == "checkbox" || $arOption[2][0] == 'selectbox' || $arOption[2][0] == 'radio') 
			{
				\COption::setOptionString($module_id, $name, $val);
			}
		}
	}
	\Bitrix\Main\Data\Cache::createInstance()->CleanDir('/bazarow.decorateursite/');
	
	LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANG);
}
?>
    <link rel="stylesheet" href="<?= '/bitrix/css/' . $module_id . '/admin_styles.css' ?>">
    <script>
        if (!window.jQuery) {
            document.write("<script src='//code.jquery.com/jquery-3.5.1.min.js' ><\/script>");
        }
    </script>
    <script src="<?= '/bitrix/js/' . $module_id . '/admin_js.js' ?>"></script>
    <form method="post" action="<?= $APPLICATION->GetCurPage()?>?mid=<?= urlencode($module_id)?>&amp;lang=<?= LANGUAGE_ID?>" id="options"><?
        $tabControl->Begin();
		foreach($tabControl->tabs as $tabKey => $tab) {
			$tabControl->BeginNextTab();
			foreach($allOptions as $Option):
            if ($Option[0] == 'header')
            {
                ?>
                <tr class="heading">
                    <td colspan="2">
                        <?= $Option[1];?>
                    </td>
                </tr>
                <?if (isset($Option[2])):?>
					<tr>
						<td></td>
						<td>
							<?
							echo BeginNote();
							echo $Option[2];
							echo EndNote();
							?>
						</td>
					</tr>
				<?endif;?>
                <?
                continue;
            }
			$val = \COption::GetOptionString($module_id, $Option[0].'_'.$tab['DIV']);
			$type = $Option[2];
            ?>
			<?if (str_contains($Option[0], 'source')):?>
                <tr>
                    <td valign="top" width="100%"><?
                        if ($type[0]=='checkbox' && $tab['DIV'] != "edit_main")
                        {
							if($Option[3] && $tab['DIV'] != "edit1")//if IT IS snow option and IS NOT first tab -> continue
								continue;?>
							<input type="checkbox" name="<?echo htmlspecialcharsbx($Option[0]).'_'.$tab['DIV']?>" id="<?echo htmlspecialcharsbx($Option[0]).'_'.$tab['DIV']?>" value="Y"<?if($val=="Y")echo" checked";?>>
                            <?echo '<label for="' . \htmlspecialcharsbx($Option[0]).'_'.$tab['DIV'].'">'.$Option[1].'</label>';
                        } else if ($type[0]=='file' && $tab['DIV'] != "edit_main") {?> 
							<?if($Option[3]):?>
								<p class="addalt-btn"><?=Loc::getMessage('BO_DUS_IMAGE_DEFAULT')?></p>
								<div>
									<img src="<?=$Option[4][$tabKey]?>"/>
									<input type="hidden" name="<?=htmlspecialcharsbx($Option[0])."_".$tab['DIV']?>" value="<?=$Option[4][$tabKey]?>">
									<!-- Это изображение должно быть индивидуально для каждой вкладки праздника
										соответственно оно зависит от текущего таба, но при этом должно быть опцией, 
										чтобы передать картинку дальше в обработку (include or main)
										
										Если пользватель добавляет свою картинку, она должна заменять дефолтное
										(нужно добавить условие if)
										
										
										-> $tabKey - ключ текущего таба, который соответствует ключу подходящего этому табу изображению
									-->
								</div>
							<?else:?>
								<p class="addalt-btn"><?=Loc::getMessage('BO_DUS_IMAGE_CHOISE')?></p>
								<div>
								<?php if (class_exists('\Bitrix\Main\UI\FileInput', true)) {
									echo \Bitrix\Main\UI\FileInput::createInstance([
										"name" => htmlspecialcharsbx($Option[0])."_".$tab['DIV'],
										"id" => "file_".$tab['DIV'],
										"description" => true,
										"upload" => true,
										"allowUpload" => "I",
										"medialib" => true,
										"fileDialog" => true,
										"cloud" => true,
										"delete" => true,
										"maxCount" => 1,
									])->show($val > 0 ? $val : 0);
								} else {
									echo CFileInput::Show($Option[0].'_'.$tab['DIV'], ($val > 0 ? $val : 0),
										[
											"IMAGE" => "Y",
											"PATH" => "Y",
											"FILE_SIZE" => "Y",
											"DIMENSIONS" => "Y",
											"IMAGE_POPUP" => "Y",
											//                                    "MAX_SIZE" => array(
											//                                        "W" => COption::GetOptionString("iblock", "detail_image_size"),
											//                                        "H" => COption::GetOptionString("iblock", "detail_image_size"),
											//                                    ),
										], [
											'upload' => true,
											'medialib' => true,
											'file_dialog' => true,
											'cloud' => true,
											'del' => true,
											'description' => true,
										]
									);
								}?>
								</div>	
							<?endif;?>							
						<?} else if ($type[0]=='selectbox' && $tab['DIV'] == "edit_main") {

							$rowType = FormOptionsBuilder::forExecutionType();
							$currValue = explode(',', $val);
							?>
							<select name="<?echo htmlspecialcharsbx($Option[0]).'_'.$tab['DIV']?>[]">
								<?foreach($rowType as $type) {
									if (in_array($type['VAL'], $currValue)) {
                                        $sel = ' selected="selected"';
                                    } elseif($type['DEFAULT'] && empty($currValue)) {
										$sel = ' selected="selected"';
                                    } else {										
                                        $sel = '';
                                    }?>
									<option value="<?=$type['VAL']?>"<?=$sel?>><?=$type['TEXT']?></option>
								<?}?>
							</select>
						<?} else if($type[0]=='radio' && $tab['DIV'] != "edit_main" && $tab['DIV'] != "edit1") {?>
							<?$radioPos = FormOptionsBuilder::forAnglePosition();
							echo '<p>'.$Option[1].'</p>';
							
							foreach($radioPos as $pos) {
								//echo $val;
								//echo $pos['VAL'];
									if ($pos['VAL'] == $val) {
                                        $check = ' checked="checked"';	
                                    } elseif($pos['DEFAULT'] && empty($val)) {
										$check = ' checked="checked"';
									} else {
										$check = '';	
									}
                                   ?>
									<input type="radio" name="<?echo htmlspecialcharsbx($Option[0]).'_'.$tab['DIV']?>" value="<?=$pos['VAL']?>"<?=$check?>><?=$pos['TEXT']?>
								<?}?>
						<?}?>
					</td>
                </tr>
            <?endif;?>
        <?endforeach;
		}
        $tabControl->Buttons();
        ?>
		<input type="submit" name="Update" value="<?=Loc::getMessage('BO_DUS_OPTIONS_INPUT_SAVE')?>" title="<?=Loc::getMessage('BO_DUS_OPTIONS_INPUT_SAVE_BACK')?>" class="adm-btn-save">
        <input type="submit" name="Apply" value="<?=Loc::getMessage('BO_DUS_OPTIONS_INPUT_APPLY')?>" title="<?=Loc::getMessage('BO_DUS_OPTIONS_INPUT_APPLY_STAY')?>">
        <input type="submit" name="default" value="<? echo(Loc::GetMessage("BO_DUS_OPTIONS_INPUT_DEFAULT")); ?>" />
		<?=bitrix_sessid_post();?>
		<?$tabControl->End();?>
    </form>
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"][name*="source_activity"]');
			
			checkboxes.forEach(function(checkbox) {
				checkbox.addEventListener('change', function() {
					if (this.checked) {
						// Снимаем галочки с других чекбоксов
						checkboxes.forEach(function(other) {
							if (other !== checkbox) other.checked = false;
						});
					}
				});
			});
		});
	</script>