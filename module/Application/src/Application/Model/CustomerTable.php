<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Application\Service\CommonService;

use function GuzzleHttp\json_decode;

class CustomerTable extends AbstractTableGateway
{

    protected $table = 'customer';
    protected $primary = 'customer_id';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function addCustomer($param)
    {
        $customerZoneDb = new CustomerZoneTable($this->adapter);
        $logincontainer = new Container('user');
        $common = new CommonService();
        $data = array(
            'serial_no'         => $param['serialNo'],
            'first_name'        => $param['firstName'],
            'last_name'         => $param['lastName'],
            'gender'            => $param['gender'],
            'email'             => $param['email'],
            'phone'             => $param['mobileNumber'],
            'alternate_phone'   => $param['alternateNumber'],
            'street_address'    => $param['address'],
            'address_line2'     => $param['address2'],
            'city'              => $param['city'],
            'state'             => base64_decode($param['state']),
            'pincode'           => $param['pincode'],
            'recceiver_name'    => $param['receiverName'],
            'customer_status'   => $param['customerStatus'],
            'added_by'          => $logincontainer->userId,
            'added_on'          => $common->getDateTime()
        );
        $this->insert($data);
        $lastInsertedId = $this->lastInsertValue;
        if(isset($param['zoneId']) && count($param['zoneId']) > 0){
            foreach($param['zoneId'] as $zone){
                $customerZoneDb->insert(array('customer_id' => $lastInsertedId, 'zone_id' => base64_decode($zone)));
            }
        }
        
        
        if(isset($_FILES['image']['name']) && trim($_FILES['image']['name'])!=''){
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer",0777);
            }
    
            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer" . DIRECTORY_SEPARATOR . "customer_" . $lastInsertedId;
            if (!file_exists($pathname) && !is_dir($pathname)) {
                mkdir($pathname,0777);
            }
            $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = $common->generateRandomString(4,'alphanum'). "." . $extension;
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $pathname. DIRECTORY_SEPARATOR.$imageName)){
                $this->update(array('image' => $imageName),array("customer_id" =>$lastInsertedId));
            }
        }
        return $lastInsertedId;
    }

    public function updateCustomer($param)
    {
        $customerZoneDb = new CustomerZoneTable($this->adapter);
        $common = new CommonService();
        $customerId = base64_decode($param['customerId']);
        $data = array(
            'serial_no'         => $param['serialNo'],
            'first_name'        => $param['firstName'],
            'last_name'         => $param['lastName'],
            'gender'            => $param['gender'],
            'email'             => $param['email'],
            'phone'             => $param['mobileNumber'],
            'alternate_phone'   => $param['alternateNumber'],
            'street_address'    => $param['address'],
            'address_line2'     => $param['address2'],
            'city'              => $param['city'],
            'state'             => base64_decode($param['state']),
            'pincode'           => $param['pincode'],
            'recceiver_name'    => $param['receiverName'],
            'customer_status'   => $param['customerStatus']
        );
        $this->update($data, array("customer_id" => $customerId));
        $customerZoneDb->delete(array('customer_id' => $customerId));
        if(isset($param['zoneId']) && count($param['zoneId']) > 0){
            foreach($param['zoneId'] as $zone){
                $customerZoneDb->insert(array('customer_id' => $customerId, 'zone_id' => base64_decode($zone)));
            }
        }
        if(isset($_FILES['image']['name']) && trim($_FILES['image']['name'])!=''){
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer",0777);
            }
    
            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "customer" . DIRECTORY_SEPARATOR . "customer_" . $customerId;
            if (!file_exists($pathname) && !is_dir($pathname)) {
                mkdir($pathname,0777);
            }
            $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = $common->generateRandomString(4,'alphanum'). "." . $extension;
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $pathname. DIRECTORY_SEPARATOR.$imageName)){
                $this->update(array('image' => $imageName),array("customer_id" =>$customerId));
            }
        }
        
        return $customerId;
    }

    public function fetchCustomerList($parameters, $acl)
    {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = array('serial_no', 'first_name', 'phone','recceiver_name','customer_status');
        $orderColumns = array('serial_no', 'first_name', 'phone','recceiver_name','customer_status');
        /*
         * Paging
         */
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        /*
         * Ordering
         */

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $orderColumns[intval($parameters['iSortCol_' . $i])] . " " . ($parameters['sSortDir_' . $i]) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */

        $sWhere = "";
        if (isset($parameters['sSearch']) && $parameters['sSearch'] != "") {
            $searchArray = explode(" ", $parameters['sSearch']);
            $sWhereSub = "";
            foreach ($searchArray as $search) {
                if ($sWhereSub == "") {
                    $sWhereSub .= "(";
                } else {
                    $sWhereSub .= " AND (";
                }
                $colSize = count($aColumns);

                for ($i = 0; $i < $colSize; $i++) {
                    if ($i < $colSize - 1) {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' OR ";
                    } else {
                        $sWhereSub .= $aColumns[$i] . " LIKE '%" . ($search) . "%' ";
                    }
                }
                $sWhereSub .= ")";
            }
            $sWhere .= $sWhereSub;
        }

        /* Individual column filtering */
        for ($i = 0; $i < count($aColumns); $i++) {
            if (isset($parameters['bSearchable_' . $i]) && $parameters['bSearchable_' . $i] == "true" && $parameters['sSearch_' . $i] != '') {
                if ($sWhere == "") {
                    $sWhere .= $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                } else {
                    $sWhere .= " AND " . $aColumns[$i] . " LIKE '%" . ($parameters['sSearch_' . $i]) . "%' ";
                }
            }
        }

        /*
         * SQL queries
         * Get data to display
         */
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('c' => 'customer'))
        ->join(array('u' => 'user'), 'c.added_by=u.role', array('user_name'));

        if (isset($sWhere) && $sWhere != "") {
            $sQuery->where($sWhere);
        }

        if (isset($sOrder) && $sOrder != "") {
            $sQuery->order($sOrder);
        }

        if (isset($sLimit) && isset($sOffset)) {
            $sQuery->limit($sLimit);
            $sQuery->offset($sOffset);
        }

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $iTotal = $this->select()->count();


        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        $sessionLogin = new Container('user');
        $role = $sessionLogin->roleCode;
        if ($acl->isAllowed($role, 'Admin\Controller\Customer', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        if ($acl->isAllowed($role, 'Admin\Controller\Customer', 'edit')) {
            $setDisabled = "";
        }else{
            $setDisabled = "disabled";
        }

        foreach ($rResult as $key=>$aRow) {
            $row = array();
            if($aRow['customer_status'] != 'active'){
                $setDisabled = "disabled";
            }else{
                $setDisabled = "";
            }
            /* $row[] = '
                <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                    <input type="checkbox" class="custom-control-input customerSMS" id="customerSMS'.($key + 1).'" name="customerSMS[]" value="' . base64_encode($aRow['customer_id']) . '" onclick="checkAll(this,\'child\')" '.$setDisabled.'>
                    <label class="custom-control-label" for="customerSMS'.($key + 1).'"></label>
                </div>
            '; */
            $row[] = $aRow['serial_no'];
            $row[] = ucwords($aRow['first_name']) .' '.  ucwords($aRow['last_name']);
            $row[] = $aRow['phone'];
            $row[] = ucwords($aRow['recceiver_name']);
            $row[] = ucwords($aRow['customer_status']);
            if($update){
                $row[] = '<a href="/admin/customer/edit/' . base64_encode($aRow['customer_id']) . '" class="btn btn-default" style="margin-right: 2px;" title="Edit"><i class="far fa-edit"></i> Edit</a>';
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchCustomerById($customerId)
    {
        $customerZoneDb = new CustomerZoneTable($this->adapter);
        $result = $this->select(array('customer_id'=>$customerId))->current();
        $zoneResult = $customerZoneDb->select(array('customer_id'=>$customerId))->toArray();
        foreach($zoneResult as $zone){
            $result['map'][] = $zone['zone_id'];
        }
        return $result;
        
    }
    
    public function fetchTotalCount()
    {
        return $this->select(array('customer_status'=>'active'))->count();
    }
    
    public function fethUniqueId()
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $query = $sql->select()->from($this->table)->columns(array($this->primary))->order('customer_id DESC');
        $queryStr = $sql->getSqlStringForSqlObject($query);
        return $dbAdapter->query($queryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
    }
    
    public function sendSMSDetails($param)
    {
        $status = 0;
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $client = new \GuzzleHttp\Client();
        $alertContainer = new Container('alert');
        $logincontainer = new Container('user');
        $common = new CommonService();

        if(isset($param['customerId']) && $param['customerId'] != ""){
            $ids = explode(',',$param['customerId']);
            foreach($ids as $id){
                $cusResult = $this->select(array('customer_id'=> base64_decode($id),'customer_status'=>'active'))->current();
                if($cusResult){
                    $result = $client->post($configResult['api']['link'], [
                        'form_params' => [
                            'senderid'     => $configResult['api']['senderid'],
                            'secret'       => $configResult['api']['secret'],
                            'Message'      => urlencode('Hi, '.$cusResult['first_name'].' '.$cusResult['last_name'].'   '.$param['message'].'   Your Serial No :'.$cusResult['serial_no'].'  All Proceeds will support the building construction of CSI IMMANUEL CHURCH, PERUNGUDI, CH-96'),
                            'phone'        => (isset($cusResult['phone']) && $cusResult['phone'] != "")?$cusResult['phone']:$cusResult['alternate_phone'],
                            'apikey'       => $configResult['api']['key'],
                            'usetype'      => $configResult['api']['type'],
                        ]
                    ]);
                    $response = $result->getBody()->getContents();
                    $json = json_decode($response);
                    // \Zend\Debug\Debug::dump($json);die;
                    if($json->status == 'success'){
                        $alertContainer->alertMsg = 'Message send successfully, Remaining COST -'.$json->balacne;
                        $status = 1;
                        $this->update(array(
                            'message' => $param['message'],
                            'message_status' => 'send',
                            'send_by' => $logincontainer->userId,
                            'send_on' => $common->getDateTime()
                        ),array('customer_id'=>$cusResult['customer_id']));
                    }else{
                        $alertContainer->alertMsg = $json->message.', Error -'.$json->code;
                        $alertContainer->alertFlag = 'error';
                        $this->update(array(
                            'message' => $param['message'],
                            'message_status' => 'not-send',
                            'send_by' => $logincontainer->userId,
                            'send_on' => $common->getDateTime()
                        ),array('customer_id'=>$cusResult['customer_id']));
                    }
                }
            }
        }
        return $status;
    }
}
