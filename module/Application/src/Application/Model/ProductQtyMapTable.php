<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Debug\Debug;

class ProductQtyMapTable extends AbstractTableGateway {

    protected $table = 'product_qty_map';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function fetchMappedQtyByProductId($id)
    {
        # Query
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('pqm' => $this->table))
        ->join(array('q' => 'qty_details'),'pqm.qty_id=q.qty_id',('*'))
        ->where(array('product_id' => $id, 'qty_status' => 'active'));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        return $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
}
