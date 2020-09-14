<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;

class UserZoneTable extends AbstractTableGateway {

    protected $table = 'user_zone_map';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
}
?>