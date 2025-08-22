<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;
use Gordeeva\DecorateUrSite;
Loc::loadMessages(__FILE__);
class gordeeva_decorateursite extends CModule {
    public function __construct(){
        if(file_exists(__DIR__."/version.php")){
            $arModuleVersion = array();
            include(__DIR__."/version.php");
            $this->MODULE_ID            = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION       = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME          = Loc::getMessage("BO_DUS_NAME");
            $this->MODULE_DESCRIPTION  = Loc::getMessage("BO_DUS_DESCRIPTION");
            $this->PARTNER_NAME     = Loc::getMessage("BO_DUS_PARTNER_NAME");
            $this->PARTNER_URI      = Loc::getMessage("BO_DUS_PARTNER_URI");
        }
        return false;
    }

    public function DoInstall(){
        global $APPLICATION;
        if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){
            $this->InstallFiles();
            $this->InstallDB();
            ModuleManager::registerModule($this->MODULE_ID);
			$this->InstallEvents();
        }else{
            $APPLICATION->ThrowException(
                Loc::getMessage("BO_DUS_INSTALL_ERROR_VERSION")
            );
        }
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("BO_DUS_INSTALL_TITLE")." \"".Loc::getMessage("BO_DUS_NAME")."\"",
            __DIR__."/step.php"
        );
        return false;
    }

    public function InstallFiles(){
        // copy js files
        if(!CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/assets/js", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/".$this->MODULE_ID, true, true)){
            throw new Exception(Loc::getMessage("ERRORS_CREATE_DIR",array('#DIR#'=>'bitrix/js')));
			return false;
        }
        if(!CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/local/modules/".$this->MODULE_ID."/install/assets/css", $_SERVER["DOCUMENT_ROOT"]."/bitrix/css/".$this->MODULE_ID, true, true)){
            throw new Exception(Loc::getMessage("ERRORS_CREATE_DIR",array('#DIR#'=>'bitrix/css')));
            return false;
        }
        return true;
    }
    public function InstallDB(){
        return false;
    }
    public function InstallEvents(){
		RegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "Gordeeva\DecorateUrSite\EventHandlers\MainEvents", "decorateSite", "100");
		return false;
	}
    public function DoUninstall(){
        global $APPLICATION;
		//parent::DoUninstall();
        $this->UnInstallFiles();
        $this->UnInstallDB();
		$this->UnInstallEvents();
		\Bitrix\Main\Data\Cache::createInstance()->CleanDir('/gordeeva.decorateursite/');
        ModuleManager::unRegisterModule($this->MODULE_ID);
        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("BO_DUS_UNINSTALL_TITLE")." \"".Loc::getMessage("BO_DUS_NAME")."\"",
            __DIR__."/unstep.php"
        );
        return false;
    }
    public function UnInstallFiles(){
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/js/'.$this->MODULE_ID) && !DeleteDirFilesEx( '/bitrix/js/'.$this->MODULE_ID )){
            throw new Exception(Loc::getMessage("ERRORS_DELETE_FILE",array('#FILE#'=>'bitrix/js/'.$this->MODULE_ID)));
        }
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/css/'.$this->MODULE_ID) && !DeleteDirFilesEx( '/bitrix/css/'.$this->MODULE_ID )){
            throw new Exception(Loc::getMessage("ERRORS_DELETE_FILE",array('#FILE#'=>'bitrix/css/'.$this->MODULE_ID)));
        }
        return true;
    }
    public function UnInstallDB(){
        Option::delete($this->MODULE_ID);
        return false;
    } 
	public function UnInstallEvents(){
        UnRegisterModuleDependences("main", "OnBeforeEndBufferContent", $this->MODULE_ID, "Gordeeva\DecorateUrSite\EventHandlers\MainEvents", "decorateSite");
        return false;
    }
}