<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;

class CustomerZoneTable extends AbstractTableGateway {

    protected $table = 'customer_zone_map';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
}
?>