<?php
namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;
use Application\Model\UserZoneTable;
use Zend\Db\Sql\Where;

class ZoneTable extends AbstractTableGateway {

    protected $table = 'zone';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    public function fetchAllState()
    {
        return $this->select(array('status' =>'active'))->toArray();
    }

    public function saveZoneDetails($params)
    {
        # save Zone details code...
        $userZoneMapDb = new UserZoneTable($this->adapter);
        $common = new CommonService();
        $sessionLogin = new Container('user');
        $data = array(
            'name'          => $params['name'],
            'slug'          => $params['slug'],
            'description'   => $params['description'],
            'parent_zone'   => (isset($params['parentId']) && trim($params['parentId']) != '')?base64_decode($params['parentId']):0,
            'status'        => $params['status']
        );
        if(isset($params['zoneId']) && $params['zoneId'] != ''){
            $data['modified_on'] = $common->getDateTime();
            $data['modified_by'] = $sessionLogin->userId;
            $this->update($data,array('id' => base64_decode($params['zoneId'])));
            $lastInsertId = base64_decode($params['zoneId']);
            $userZoneMapDb->delete(array('zone_id' => $lastInsertId));
            if(isset($params['userId']) && count($params['userId']) > 0){
                foreach($params['userId'] as $user){
                    $userZoneMapDb->insert(array('zone_id' => $lastInsertId, 'user_id' => base64_decode($user)));
                }
            }
        }else{
            $data['created_on'] = $common->getDateTime();
            $data['created_by'] = $sessionLogin->userId;
            $this->insert($data);
            $lastInsertId = $this->lastInsertValue;
            if(isset($params['userId']) && count($params['userId']) > 0){
                foreach($params['userId'] as $user){
                    $userZoneMapDb->insert(array('zone_id' => $lastInsertId, 'user_id' => base64_decode($user)));
                }
            }
        }

        if(isset($_FILES['image']['name']) && trim($_FILES['image']['name'])!=''){
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "zone") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "zone")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "zone",0777);
            }
            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "zone" . DIRECTORY_SEPARATOR . "" . $lastInsertId;
            if (!file_exists($pathname) && !is_dir($pathname)) {
                mkdir($pathname,0777);
            }
            $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = $common->generateRandomString(4,'alphanum'). "." . $extension;
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $pathname. DIRECTORY_SEPARATOR.$imageName)){
                $this->update(array('image' => $imageName),array("id" =>$lastInsertId));
            }
        }
        return $lastInsertId;
    }

    public function fetchAllActiveZone()
    {
        # fetching active Zone list...
        $sessionLogin = new Container('user');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('z' => $this->table))
        ->Where(array('z.status' => 'active'));
        if($sessionLogin->roleCode != 'ADM'){
            $sQuery = $sQuery->join(array('uzm' => 'user_zone_map'),'z.id=uzm.zone_id',array());
            $sQuery = $sQuery->where(array('uzm.user_id' => $sessionLogin->userId));
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $zoneList = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        $data = array();
        foreach($zoneList as $aRow){
            $ZoneName = array();
            if($aRow['parent_zone'] > 0){
                $parentId = $aRow['parent_zone'];
                do{
                    $result = $this->select(array('id' => $parentId))->current();
                    if($result){
                        $ZoneName []=  ucwords($result['name']);
                        $parentId = $result['parent_zone'];

                    }
                }while($result && $result['parent_zone'] == 0);
            }
            $ZoneName[] = ucwords($aRow['name']);
            $data[] = array(
                'id'            => $aRow['id'],
                'name'          => implode(' -> ',$ZoneName),
                'slug'          => $aRow['slug'],
                'description'   => $aRow['description'],
                'parent_zone'   => $aRow['parent_zone']
            );
        }
        return $data;
    }

    public function changeStatusByZoneId($params)
    {
        # change the status of the Zone...
        return $this->update(array('status' => $params['status']),array('id'=>base64_decode($params['id'])));
    }

    public function fetchZoneListInGrid($parameters, $acl)
    {
        $aColumns = array('name', 'description', 'status');
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
        if ($acl->isAllowed($role, 'Admin\Controller\Zone', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        if ($acl->isAllowed($role, 'Admin\Controller\Zone', 'change-status')) {
            $changeStatus = true;
        } else {
            $changeStatus = false;
        }

        foreach ($rResult as $aRow) {
            $row = array();$updateLink = '';$changeStatusLink = '';$ZoneName = array();$result = false;
            if($aRow['parent_zone'] > 0){
                $parentId = $aRow['parent_zone'];
                do{
                    $result = $this->select(array('id' => $parentId))->current();
                    if($result){
                        $ZoneName []=  ucwords($result['name']);
                        $parentId = $result['parent_zone'];

                    }
                }while($result && $result['parent_zone'] == 0);
            }
            $ZoneName[] = ucwords($aRow['name']);
            $row[] = implode(' <i class="fa fa-arrow-right"></i> ',$ZoneName);
            $row[] = $aRow['description'];
            $row[] = ucwords($aRow['status']);
            if($update){
                $updateLink = '<a href="/admin/zone/edit/' . base64_encode($aRow['id']) . '" class="btn btn-sm btn-outline-info" style="margin-left: 2px;" title="Edit Zone of '.ucwords($aRow['name']).'"><i class="far fa-edit"></i> Edit</a>';
            }
            if($changeStatus){
                $statusTxt = (isset($aRow['status']) && $aRow['status'] == 'active')?"inactive":"active";
                $changeStatusLink = '<a href="javascript:void(0);" onclick="changeStatus(\''.base64_encode($aRow['id']).'\',\''.$statusTxt.'\')" class="btn btn-sm btn-outline-dark" style="margin-left: 2px;" title="Change status of '.ucwords($aRow['name']).'"><i class="fa fa-exchange-alt"></i> Change Status</a>';
            }
            if($changeStatus || $update){
                $row[] = $updateLink . $changeStatusLink;
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchZoneById($id)
    {
        # fetch Zone by id...
        $userZoneMapDb = new UserZoneTable($this->adapter);
        $result = $this->select(array('id' => $id))->current();
        $userZoneResult = $userZoneMapDb->select(array('zone_id' => $id))->toArray();
        foreach($userZoneResult as $user){
            $result['map'][] =  $user['user_id'];   
        }
        // \Zend\Debug\Debug::dump($result);die;
        return $result;
    }
}
?>