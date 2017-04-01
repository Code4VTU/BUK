<?php
namespace VT_AIR\Traits;


trait ServiceLocatorTraits
{
    protected $config=null;
    public function setServiceLocator($config)
    {
        if($this->config===null)
            $this->config= $config;
    }
    public function getServiceLocator()
    {
        return $this->config;
    }

    protected function getConfigVar($key=null,$val=null)
    {
        $c=$this->getConfig();
        return (!isset($c[$key][$val])?'':$c[$key][$val]);
    }
}