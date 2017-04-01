<?php 
namespace VT_AIR\Config;


trait ConfigInterfaceTrait
{
	protected $config=null;
	public function setConfig($config)
	{
		if($this->config===null)
			$this->config= $config;
	}
	public function getConfig()
	{
		return $this->config;
	}
	
	protected function getConfigVar($key=null,$val=null)
	{
		$c=$this->getConfig();
		return (!isset($c[$key][$val])?'':$c[$key][$val]);
	}
}
