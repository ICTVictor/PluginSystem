<?php

class Plugin {

    private $_RefObject;
    private $_Class = '';
    private $_PluginName;

    public function __construct($RefObject, $PluginName) {
        $this->_Class = get_class($RefObject);
        $this->_RefObject = $RefObject;
        $this->_PluginName = $PluginName;
    }

    public function __set($sProperty,$mixed) {
        $sPlugin = $this->_Class . '_' . $sProperty . '_setEvent';
        if (is_callable($sPlugin)) {
            $mixed = call_user_func_array($sPlugin, (array) $mixed);
        }   
        $this->_RefObject->$sProperty = $mixed;
    }

    public function __get($sProperty) {
        $asItems = (array) $this->_RefObject;
        $mixed = $asItems[$sProperty];
        $sPlugin = $this->_Class . '_' . $sProperty . '_getEvent';
        if (is_callable($sPlugin)) {
            $mixed = call_user_func_array($sPlugin, (array) $mixed);
        }   
        return $mixed;
    }

    public function __call($sMethod,$mixed) {
        $sPlugin = $this->_Class . '_' .  $sMethod . '_beforeEvent';
        if (is_callable(array($this->_PluginName,$sPlugin))) {
            $mixed = call_user_func_array(array($this->_PluginName,$sPlugin), (array) $mixed);
        }
        if ($mixed != 'BLOCK_EVENT') {
            call_user_func_array(array($this->_RefObject, $sMethod), (array) $mixed);
            $sPlugin = $this->_Class . '_' . $sMethod . '_afterEvent';
            if (is_callable($sPlugin)) {
                call_user_func_array(array($this->_PluginName,$sPlugin), (array) $mixed);
            }       
        } 
    }

}

?>