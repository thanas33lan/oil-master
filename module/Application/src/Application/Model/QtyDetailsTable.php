<?php
namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;

class QtyDetailsTable extends AbstractTableGateway {

    protected $table = 'qty_details';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function saveQtyDetails($params)
    {
        # save Qty details code...
        $lastInsertId = 0;
        $common = new CommonService();
        $sessionLogin = new Container('user');
        $data = array(
            'qty_name'   => $params['name'],
            'qty_status' => $params['status']
        );
        if(isset($params['qtyId']) && $params['qtyId'] != ''){
            $data['modified_on'] = $common->getDateTime();
            $data['modified_by'] = $sessionLogin->userId;
            $lastInsertId = base64_decode($params['qtyId']);
            $this->update($data,array('qty_id' => $lastInsertId));
        }else{
            $data['created_on'] = $common->getDateTime();
            $data['created_by'] = $sessionLogin->userId;
            $this->insert($data);
            $lastInsertId = $this->lastInsertValue;
        }
        return $lastInsertId;
    }

    public function fetchAllActiveQty()
    {
        # fetching active Qty list...
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('qd' => $this->table))
        ->Where(array('qd.qty_status' => 'active'));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        return $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
    }

    public function changeStatusByQtyId($params)
    {
        # change the status of the Qty...
        return $this->update(array('qty_status' => $params['status']),array('qty_id'=>base64_decode($params['id'])));
    }

    public function fetchQtyListInGrid($parameters, $acl)
    {
        $sessionLogin = new Container('user');
        $aColumns = array('qty_name', 'qty_status', 'created_on', 'user_name');
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
        $sQuery = $sql->select()->from(array("q" => $this->table))
        ->join(array('u' => 'user'),'q.created_by=u.user_id',array('user_name'));

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
        // die($sQueryStr);
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
        if ($acl->isAllowed($role, 'Admin\Controller\QtyDetails', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        if ($acl->isAllowed($role, 'Admin\Controller\QtyDetails', 'change-status')) {
            $changeStatus = true;
        } else {
            $changeStatus = false;
        }

        foreach ($rResult as $aRow) {
            $row = array();$updateLink = '';$changeStatusLink = '';
            $row[] = ucwords($aRow['qty_name']);
            $row[] = ucwords($aRow['qty_status']);
            $row[] = date('d-M-Y H:i:a',strtotime($aRow['created_on']));
            $row[] = ucwords($aRow['user_name']);
            if($update){
                $updateLink = '<a href="/admin/qty/edit/' . base64_encode($aRow['qty_id']) . '" class="btn btn-sm btn-outline-info" style="margin-left: 2px;" title="Edit Qty details of '.ucwords($aRow['qty_name']).'"><i class="far fa-edit"></i> Edit</a>';
            }
            if($changeStatus || $update){
                $statusTxt = (isset($aRow['qty_status']) && $aRow['qty_status'] == 'active')?"inactive":"active";
                $changeStatusLink = '<a href="javascript:void(0);" onclick="changeStatus(\''.base64_encode($aRow['qty_id']).'\',\''.$statusTxt.'\')" class="btn btn-sm btn-outline-dark" style="margin-left: 2px;" title="Change status of '.ucwords($aRow['qty_name']).'"><i class="fa fa-exchange-alt"></i> Change Status</a>';
                $row[] = $updateLink . $changeStatusLink;
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchQtyById($id)
    {
        # fetch Qty by id...
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from($this->table)
        ->Where(array('qty_id' => $id));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        return $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
    }
}
?>