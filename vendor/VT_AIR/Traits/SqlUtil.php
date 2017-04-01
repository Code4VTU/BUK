<?php
namespace VT_AIR\Traits;

//use Zend\Authentication\AuthenticationService;
use Zend\Db\ResultSet\ResultSet;

trait SqlUtil
{
    protected $sqlInst = null;
    protected $dbAdapter = null;
    protected $_lang = 'bg';
    protected $translator;
    protected $serviceLocator;
    
    protected static $auth;

    
    public function setDbAdapter(\Zend\Db\Adapter\Adapter $dbAdapter)
    {
    	if($this->dbAdapter===null){
    		$this->dbAdapter =$dbAdapter;
    		$this->setSqlInstance($dbAdapter);    		
    	}
    }

    public function getDbAdapter()
    {
    	return $this->dbAdapter;
    }
    public function setTranslator($translator)
    {
    	if($this->translator===null){
    		$this->translator =$translator;
    	}
    }
    public function getTranslator()
    {
    		return $this->translator;
    }
    public function setSqlInstance($dbAdapter)
    {
    	if($this->sqlInst=== null)
    		$this->sqlInst = new  \Zend\Db\Sql\Sql($dbAdapter);
    }
    public function getSqlInstance()
    {
    	return $this->sqlInst;
    }
    
    protected function getResultSetFromSelect($select)
    {
    	try{
    	$result = $this->getSqlInstance()->prepareStatementForSqlObject($select)->execute();
    	$resultSet = new ResultSet();
    	$resultSet->initialize($result)->buffer();
    	
    		
    	} catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
    		$message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
    		throw new \Exception("<p>SQL error "   . $message . "<br/>");
    		
    	}catch (\Exception $e) {
    		throw new \Exception("<p>General Error: " . $e->getMessage() . "<br/>");
    	}
    
    	return $resultSet;
    }
    
    protected function getRowFromSelect($select)
    {
    	$resArray=array();
    	try{
    		$result = $this->getSqlInstance()->prepareStatementForSqlObject($select)->execute();
    		$resultSet = new ResultSet();
    		$resultSet->initialize($result);
    		if($resultSet->count()) {
    			$resArray=$rowData = $resultSet->current()->getArrayCopy();
    		}
    
    	} catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
    		$message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
    		throw new \Exception("<p>SQL error "   . $message . "<br/>");
    
    	}catch (\Exception $e) {
    		throw new \Exception("<p>General Error: " . $e->getMessage() . "<br/>");
    	}
    
    	return $resArray;
    }
   
    protected function safeInsert($execSql, $ignoreLastkey = false) 
    {
    	if ($execSql instanceof \Zend\Db\Sql\Insert ){
    		try{
    			$lastKey=$this->getSqlInstance()->prepareStatementForSqlObject($execSql)->execute()->getGeneratedValue();
    		} catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
    			$message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
    			throw new \Exception("<p>SQL error "   . $message . "<br/>");
    		}catch (\Exception $e) {
    			throw new \Exception( "<p>General Error: " . $e->getMessage() . "<br/>");
    		}
    	}
    	// В някои случаи нямаме autoincrement и тук гърми ако проверяваме само $lastKey (пример ent_position_competency)
    	if (empty($lastKey) && ! $ignoreLastkey){throw new \Exception( '<h1>Any Error</h1><pre>'.$execSql->getSqlString($this->getDbAdapter()->getPlatform())."</pre>");} //log the error and redirect to error page and display predefined
    	return $lastKey;
    }
    
    protected function safeUpdate($execSql) //need safeInsert and safeUodate - different exceptions and no result check
    {
    	
    	if ($execSql instanceof \Zend\Db\Sql\Update) {
    		try{
    			$result=$this->getSqlInstance()->prepareStatementForSqlObject($execSql)->execute()->getAffectedRows();
    		} catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
    			$message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
    			throw new \Exception("SQL error "   . $message );
    		}catch (\Exception $e) {
    			throw new \Exception( "General Error: " . $e->getMessage() );
    		}
    	}
    	return $result;
    }
    
    protected function safeDelete($execSql) //need safeInsert and safeUodate - different exceptions and no result check
    {
    	if ($execSql instanceof \Zend\Db\Sql\Delete) {
    		try{
    			$result=$this->getSqlInstance()->prepareStatementForSqlObject($execSql)->execute()->getAffectedRows();
    		} catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
    			$message = $e->getPrevious() ? $e->getPrevious()->getMessage() : $e->getMessage();
    			throw new \Exception("<p>SQL error "   . $message . "<br/>");
    		}catch (\Exception $e) {
    			throw new \Exception( "<p>General Error: " . $e->getMessage() . "<br/>");
    		}
    	}
    	return $result;
    }
    protected function rsToList($recordSet,$keyCol,$valCol)
    {
    	$resultArray = array();
    	if ($recordSet instanceof \Zend\Db\ResultSet\ResultSet) {
    		foreach($recordSet->toArray() as $key=>$value)
    			$resultArray[$value[$keyCol]] = $value[$valCol];
    	}
    	return $resultArray;
    }
    
    protected function rsToArray($recordSet,$valCol)
    {
    	$resultArray = array();
    	if ($recordSet instanceof \Zend\Db\ResultSet\ResultSet) {
    		foreach($recordSet->toArray() as $key=>$value)
    			$resultArray[] = $value[$valCol];
    	}
    	return $resultArray;
    }
    protected function dumpSql($select)
    {
    	$sql = $select->getSqlString($this->getDbAdapter()->getPlatform());
    	echo '<p><pre>'.$sql . "</pre>\n";
    }
    
    protected function dumpResultSet($rs)
    {
    	echo '<p><b>Records: '.$rs->count() . "</b><p>";
    	foreach ($rs as $key=>$val){
    		var_dump($val);	echo '<br>'	;
    	}
    }
    
    protected function entUserListSelect()
    {
    	$select=$this->getSqlInstance()->select()->from ('acl_user_enterprise')->columns(array('user_id'));
    	$select->where->equalto("acl_user_enterprise.enterprise_id", $this->getEnterpriseId())
    		->where->equalto('status',1);
    	return $select;
    }
} 
