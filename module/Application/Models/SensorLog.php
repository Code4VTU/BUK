<?php

namespace Application\Models;

use Zend\Db\Adapter\AdapterAwareInterface;
use VT_AIR\Config\ConfigInterface;
use VT_AIR\Traits\SqlUtil;
use VT_AIR\Config\ConfigInterfaceTrait;

class SensorLog implements AdapterAwareInterface, ConfigInterface
{
    public $limitTreshold = 5;
    use ConfigInterfaceTrait;
    use SqlUtil;

    public function getSensors() {
        $select = $this->getSqlInstance()->select()->from('sensors');
        return $this->getResultSetFromSelect($select);
    }
    
    public function getSensorData($sensorId) {
        $thresholds = $this->getTypeTresholds();
        $select = $this->getSqlInstance()->select()->from(array('s' => 'sensors'));
        $select->join(array('sl' => 'log'), "s.sensor_id=sl.sensor_id", array("data", "ts"), "left");
        $select->where->equalTo("s.sensor_id", $sensorId);
        $select->order("sl.ts DESC");
        $select->limit($this->limitTreshold);
        $resultRows = $this->getResultSetFromSelect($select)->toArray();
        $sumValues = 0;
        $divider = count($resultRows);
        foreach ($resultRows as $row) { $sumValues += $row['data']; }
        $lastRow = array_pop($resultRows);
        $lastRow['data'] = $sumValues/$divider;
        $lastRow['colorClass'] = $lastRow['data'] > $thresholds[$lastRow['type']] ? "redPin" : "greenPin";
        $lastRow['warn'] = $lastRow['data'] > $thresholds[$lastRow['type']] ? true : false;
        return $lastRow;
    }
    
    public function getTypeTresholds() {
        $select = $this->getSqlInstance()->select()->from('types');
        $result = $this->getResultSetFromSelect($select);
        return $this->rsToList($result, 'type_id','limit_treshold');
    }
    
    public function putPseudoData() {
        $sensors = $this->getSensors();
        foreach ($sensors as $sensor) {
            
        }
    }
    }

}