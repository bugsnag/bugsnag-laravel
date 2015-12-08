<?php namespace Bugsnag\BugsnagLaravel;
    
use Bugsnag_Client;
    
class BugsnagLaravelClient extends Bugsnag_Client 
{
    public function resetBugsnagConfig($config) {
        
        $this->config = new \Bugsnag_Configuration();
        foreach($config as $k => $v) {
            $k = $this->_snakeToCamel($k);
            $this->config->{$k} = $v;
        }
        $this->diagnostics = new \Bugsnag_Diagnostics($this->config);
    }
    
    private function _snakeToCamel($val) {  
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $val))));
    }
}
