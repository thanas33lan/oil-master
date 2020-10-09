<?php
namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;
use Zend\Db\Sql\Where;

class SupplierTable extends AbstractTableGateway {

    protected $table = 'supplier';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    public function fetchAllState()
    {
        return $this->select(array('supplier_status' =>'active'))->toArray();
    }

    public function saveSupplierDetails($params)
    {
        # save Supplier details code...
        $common = new CommonService();
        $sessionLogin = new Container('user');
        $data = array(
            'supplier_code'     => $params['scode'],
            'supplier_name'     => $params['sname'],
            'supplier_type'     => $params['stype'],
            'supplier_mobile'   => $params['smobile'],
            'supplier_gst_no'   => $params['gstNo'],
            'supplier_address'  => $params['saddress'],
            'supplier_status'   => $params['status']
        );
        if(isset($params['supplierId']) && $params['supplierId'] != ''){
            $data['modified_on'] = $common->getDateTime();
            $data['modified_by'] = $sessionLogin->userId;
            /* To get supplier id as the last inserted id */
            $lastInsertId = base64_decode($params['supplierId']);
            $this->update($data,array('id' => $lastInsertId));
        }else{
            $data['created_on'] = $common->getDateTime();
            $data['created_by'] = $sessionLogin->userId;
            $this->insert($data);
            $lastInsertId = $this->lastInsertValue;
        }
        if(isset($_FILES['image']['name']) && trim($_FILES['image']['name'])!=''){
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "supplier") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "supplier")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "supplier",0777);
            }
            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "supplier" . DIRECTORY_SEPARATOR . "" . $lastInsertId;
            if (!file_exists($pathname) && !is_dir($pathname)) {
                mkdir($pathname,0777);
            }
            $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = $common->generateRandomString(4,'alphanum'). "." . $extension;
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $pathname. DIRECTORY_SEPARATOR.$imageName)){
                $this->update(array('supplier_image' => $imageName),array("id" =>$lastInsertId));
            }
        }
        return $lastInsertId;
    }

    public function fetchAllActiveSupplier()
    {
        # fetching active Supplier list...
        return $this->select(array('supplier_status' => 'active'))->toArray();
    }

    public function changeStatusBysupplierId($params)
    {
        # change the status of the Supplier...
        return $this->update(array('supplier_status' => $params['status']),array('id'=>base64_decode($params['id'])));
    }

    public function fetchSupplierListInGrid($parameters, $acl)
    {
        $aColumns = array('supplier_code', 'supplier_name', 'supplier_type', 'supplier_image', 'supplier_status');
        $sLimit = "";
        if (isset($parameters['iDisplayStart']) && $parameters['iDisplayLength'] != '-1') {
            $sOffset = $parameters['iDisplayStart'];
            $sLimit = $parameters['iDisplayLength'];
        }

        $sOrder = "";
        if (isset($parameters['iSortCol_0'])) {
            for ($i = 0; $i < intval($parameters['iSortingCols']); $i++) {
                if ($parameters['bSortable_' . intval($parameters['iSortCol_' . $i])] == "true") {
                    $sOrder .= $aColumns[intval($parameters['iSortCol_' . $i])] . " " . ($parameters['sSortDir_' . $i]) . ",";
                }
            }
            $sOrder = substr_replace($sOrder, "", -1);
        }

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
        # Query
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from($this->table);

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
        if ($acl->isAllowed($role, 'Admin\Controller\Supplier', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        if ($acl->isAllowed($role, 'Admin\Controller\Supplier', 'change-status')) {
            $changeStatus = true;
        } else {
            $changeStatus = false;
        }

        foreach ($rResult as $aRow) {
            $row = array();$updateLink = '';$changeStatusLink = '';
            $pathname = DIRECTORY_SEPARATOR . 'uploads'. DIRECTORY_SEPARATOR . "supplier" . DIRECTORY_SEPARATOR . "" . $aRow['id'] . DIRECTORY_SEPARATOR . $aRow['supplier_image'];

            $row[] = $aRow['supplier_code'];
            $row[] = ucwords($aRow['supplier_name']);
            $row[] = ucwords($aRow['supplier_type']);
            $row[] = '<img src="'.$pathname.'" class="img-responsive" title="'.ucwords($aRow['supplier_name']).'" style=" width: 50%; ">';
            $row[] = ucwords($aRow['supplier_status']);

            if($update){
                $updateLink = '<a href="/admin/supplier/edit/' . base64_encode($aRow['id']) . '" class="btn btn-sm btn-outline-info" style="margin-left: 2px;" title="Edit Supplier of '.ucwords($aRow['supplier_name']).'"><i class="far fa-edit"></i> Edit</a>';
            }
            if($changeStatus){
                $statusTxt = (isset($aRow['supplier_status']) && $aRow['supplier_status'] == 'active')?"inactive":"active";
                $changeStatusLink = '<a href="javascript:void(0);" onclick="changeStatus(\''.base64_encode($aRow['id']).'\',\''.$statusTxt.'\')" class="btn btn-sm btn-outline-dark" style="margin-left: 2px;" title="Change status of '.ucwords($aRow['supplier_name']).'"><i class="fa fa-exchange-alt"></i> Change Status</a>';
            }
            if($changeStatus || $update){
                $row[] = $updateLink . $changeStatusLink;
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchSupplierById($id)
    {
        # fetch Supplier by id...
        return $this->select(array('id' => $id))->current();
    }

    public function fetchLastInsertedId()
    {
        # code...
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from($this->table)->columns(array('id'))->order('id DESC')->limit(1);
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $lastId =  $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        return $lastId['id'];
    }
}
?>