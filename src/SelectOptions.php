<?php
namespace Logikos\Forms;

use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\MetaData;
use Phalcon\Mvc\Model\Exception;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Db\Column;

class SelectOptions extends Plugin {
  use \Logikos\Events\EventsAwareTrait;
  
  /**
   * String modelname - must implement Phalcon\Mvc\Model
   * @var string \Namespace\Modelname
   */
  protected $modelname;
  
  protected $selection = [];
  
  protected $conditions = [];
  
  protected $order;
  
  protected $binds = [];
  
  protected $limit;
  
  protected $offset = 0;
  
  protected $page;
  
  protected $searchstr;
  
  protected $result;
  
  /**
   * @var Array
   */
  protected $match;
  
  public function __construct($modelname=null,$options=null) {
    if ($modelname)
      $this->setModelName($modelname);
    
    if ($options)
      $this->options($options);
  }
  
  public function options($options) {
    if (!is_array($options) && !is_object($filter))
      throw new \InvalidArgumentException("Argument must be an array or object");
    
    foreach($options as $k=>$v) {
      switch($k) {
        case 'limit'  : $this->setLimit($v);     break;
        case 'offset' : $this->setOffset($v);    break;
        case 'page'   : $this->setPage($v);      break;
        case 'search' : $this->setSearchStr($v); break;
        case 'match'  : $this->setMatch($v);     break;
        case 'select' : $this->setSelect($v);    break;
      }
    }
    return $this;
  }
  
  /**
   * Get filtered results
   * use beforeSelectOptionsCriteriaExecute($query) method in the model
   * to add any model specific conditions such as company and plant constraints etc
   * @return Array
   */
  public function result() {
    /* @var $criteria \Phalcon\Mvc\Model\Criteria */
    
    $criteria = $this->newCriteria();
    
    if (count($this->selection))
      $criteria->columns($this->selection);
    
    if ($this->match)
      $this->addConditionsFromArray($this->match);
    
    if ($this->searchstr && $text=$this->getSelect('text'))
      $this->addconditions(
          $text." LIKE :searchstr:",
          ['searchstr'=>"%{$this->searchstr}%"]
      );
    
    if (is_array($this->conditions) && count($this->conditions))
      foreach ($this->conditions as $condition=>$bind)
        $criteria->andWhere($condition,$bind);
      
    if ($this->getOrder())
      $criteria->orderBy($this->getOrder());
    
    
    // limit/offset
    if ($this->offset)
      $criteria->limit($this->limit,$this->offset);
    
    elseif ($this->page)
      $criteria->limit($this->limit,($this->page-1) * $this->limit);
    
    elseif ($this->limit)
      $criteria->limit($this->limit);
    
    
    $this->_fireEvent('beforeCriteriaExecute',$criteria);
    $resultset = $criteria->execute();
    
    $this->_fireEvent('beforeResultsetToArray',$resultset);
    $result    = $resultset->toArray();
    
    $modified  = $this->_fireEvent('AfterResultsetToArray',$result);
    
    $this->result = is_array($modified) ? $modified : $result;
    return $this->result;
  }
  
  /**
   * when result() is called this causes an andWhere(conditions,$binds)
   * @param string $conditions
   * @param array  $binds
   * @return \Logikos\Forms\SelectOptions
   */
  public function addConditions($conditions,$binds=null) {
    $this->conditions[$conditions] = $binds;
    return $this;
  }
  
  /**
   * Set Modelname - must implement Phalcon\Mvc\ModelInterface
   * @param string $modelName \Namespace\Modelname implements Phalcon\Mvc\ModelInterface
   * @throws \Phalcon\Mvc\Model\Exception
   */
  public function setModelName($modelName) {
    
    if (!class_exists($modelName))
      throw new Exception("Model '{$modelName}' could not be found");
    
    $model = new \ReflectionClass($modelName);
    
    if (!$model->implementsInterface('\Phalcon\Mvc\ModelInterface'))
      throw new Exception("'{$modelName}' must be an instance of Phalcon\Mvc\ModelInterface");
    
    $this->modelname = $modelName;
    return $this;
  }
  
  public function getSelect($alias=null) {
    if (is_null($alias))
      return $this->selection;
    if (isset($this->selection[$alias]))
      return $this->selection[$alias];
    return null;
  }
  /**
   * columns is an array where the values are selected and optionaly aliased to the key
   * so you could do something like this:
   *   [ 'col1', 'col2', 'somealias'=>"concat(col3,' - ',col4)" ]
   * @param array $columns
   */
  public function setSelect(array $columns) {
    $this->selection = $columns;
    return $this;
  }
  public function addSelect(array $columns) {
    $this->selection += $columns;
    return $this;
  }
  public function setIdColumn($select) {
    $this->addSelect(['id'=>$select]);
    return $this;
  }
  public function setTextColumn($select) {
    $this->addSelect(['text'=>$select]);
    return $this;
  }
  
  public function setOrder($order) {
    $this->order = $order;
    return $this;
  }
  public function getOrder() {
    return $this->order ?: $this->getSelect('text');
  }
  
  public function setLimit($int) {
    $int = (int) $int;
    if ($int < 1)
      throw \OutOfRangeException('Argument must be a positive integer');
    $this->limit = $int;
    return $this;
  }
  
  public function setOffset($int) {
    $int = (int) $int;
    if ($int < 0)
      throw \OutOfRangeException('Argument must be a positive integer or zero');
    $this->offset = $int;
    return $this;
  }
  
  public function setPage($int) {
    $int = (int) $int;
    if ($int < 1)
      throw \OutOfRangeException('Argument must be a positive integer');
    $this->page = $int;
    return $this;
  }
  
  public function setSearchStr($string) {
    $this->searchstr = $string;
    return $this;
  }
  
  public function setMatch($array) {
    $this->match = (array) $array;
    return $this;
  }
  public function addMatch($item,$value) {
    $this->match[$item] = $value;
    return $this;
  }
  

  /**
   * @param array $data
   * @param string $operator
   * @return \Phalcon\Mvc\Model\Criteria
   */
  protected function newCriteria() {
    if (!$this->modelname)
      throw new Exception('No valid model has been set');
    
    $criteria = new Criteria();
    
    $criteria
      ->setModelName($this->modelname)
      ->setDI($this->getDI());
    
    return $criteria;
  }
  
  public function addConditionsFromArray(array $data,$operator='AND') {
    /* @var $metaData \Phalcon\Mvc\Model\MetaData */
    
    if (class_exists($this->modelname)) {
      $model      = new $this->modelname;
      $metaData   = $model->getDI()->getShared('modelsMetadata');
      $dataTypes  = $metaData->getDataTypes($model);
      $columnMap  = $metaData->getReverseColumnMap($model);
    }
    $conditions = [];
    $bind       = [];

    foreach ($data as $field => $value) {      
      $attribute = !empty($columnMap[$field])
          ? $columnMap[$field]
          : $field;
      
      $slug = !empty($dataTypes[$attribute])
          ? $attribute
          : uniqid('slug_');
      
      if (!is_null($value) && $value !== '') {
        
        if (is_array($value)) {
          $conditions[] = $field.' IN({'.$slug.':array})';
          $bind[$slug]  = $value;
          continue;
        }
        
        $type = !empty($dataTypes[$attribute])
            ? $dataTypes[$attribute]
            : 'unknown';
        
        switch ($type) {
          // use LIKE condition for VARCHAR and TEXT
          case Column::TYPE_VARCHAR :
          case Column::TYPE_TEXT    : {
            $conditions[] = $field." LIKE :{$slug}:";
            $bind[$slug]  = "%{$value}%";
            break;
          }
          
          // not sure what kind of field this is, perhaps it is an expression
          // or perhaps no model was defined to lookup the type...
          case 'unknown' : {
            // if value starts or ends with % we will use LIKE without adding %'s
            // else use =
            if (trim($value,'%') != $value) {
              $conditions[] = $field." LIKE :{$slug}:";
              $bind[$slug]  = "%{$value}%";
            }
            else {
              $conditions[] = "{$field}=:{$slug}:";
              $bind[$slug]  = $value;
            }
            break;
          }
          
          // use = condition for all other types includeing CHAR
          default : {
            $conditions[] = "{$field}=:{$slug}:";
            $bind[$slug]  = $value;
            break;
          }
        } // END switch($type)
      }
    } // END foreach ($data as $field => $value)

    $this->addConditions(
        join(" {$operator} ", $conditions),
        $bind
    );
    
  }
}