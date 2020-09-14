<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Debug\Debug;

class UserProductMapTable extends AbstractTableGateway {

    protected $table = 'user_product_map';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function fetchMappedProductByUserId($id)
    {
        # Query
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('upm' => $this->table))
        ->join(array('p' => 'product'),'upm.product_id=p.product_id',('*'))
        ->where(array('upm.user_id' => $id, 'p.status' => 'active'));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        return $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }
}
