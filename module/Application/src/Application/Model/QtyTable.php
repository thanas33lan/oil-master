<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Debug\Debug;

class QtyTable extends AbstractTableGateway {

    protected $table = 'qty_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function fetchAllActiveQty()
    {
        return $this->select(array('qty_status' => 'active'))->toArray();
    }
    
}
