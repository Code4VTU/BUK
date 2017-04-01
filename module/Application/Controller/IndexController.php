<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $sensorModel = $this->getServiceLocator()->get("SensorLog");
        $sensors = $sensorModel->getSensors()->toArray();
        $sensorInfo = array();
        foreach ($sensors as $sensor) {
            $sensorInfo[$sensor['sensor_id']] = [
                "latitude" => $sensor['latitude'],
                "longitude" => $sensor['longitude'],
                "colorClass" => "greenPin",
                "warn" => true    
            ];
        }
        $viewModel = new ViewModel();
        $viewModel->markers = json_encode($sensorInfo, false);
        return $viewModel;
    }
    
    public function refreshAction() {
        $sensorModel = $this->getServiceLocator()->get("SensorLog");
        $sensors = $sensorModel->getSensors()->toArray();
        $result = array();
        foreach ($sensors as $key => $sensorRow) {
            $result[$key] = $sensorModel->getSensorData($sensorRow['sensor_id']);
        }
        echo json_encode($result);
        return $this->response;
    }
    
    
    public function FillDataAction() {
        
    }
}
