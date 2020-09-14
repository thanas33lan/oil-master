<?php
namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;


class CategoryTable extends AbstractTableGateway {

    protected $table = 'category';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    public function fetchAllState()
    {
        return $this->select(array('category_status' =>'active'))->toArray();
    }

    public function saveCategoryDetails($params)
    {
        // \Zend\Debug\Debug::dump($params);die;
        # save category details code...
        $common = new CommonService();
        $sessionLogin = new Container('user');
        $data = array(
            'category_name'     => $params['name'],
            'category_slug'     => $params['slug'],
            'description'       => $params['description'],
            'parent_id'         => (isset($params['parentId']) && trim($params['parentId']) != '')?base64_decode($params['parentId']):0,
            'category_status'   => $params['status']
        );
        
        if(isset($params['categoryId']) && $params['categoryId'] != ''){
            $data['modified_on'] = $common->getDateTime();
            $data['modified_by'] = $sessionLogin->userId;
            $this->update($data,array('category_id' => base64_decode($params['categoryId'])));
            $lastInsertId = base64_decode($params['categoryId']);
        }else{
            $data['created_on'] = $common->getDateTime();
            $data['created_by'] = $sessionLogin->userId;
            $this->insert($data);
            $lastInsertId = $this->lastInsertValue;
        }

        
        if(isset($_FILES['image']['name']) && trim($_FILES['image']['name'])!=''){
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "category") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "category")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "category",0777);
            }
    
            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR . "category_" . $lastInsertId;
            if (!file_exists($pathname) && !is_dir($pathname)) {
                mkdir($pathname,0777);
            }
            $extension = strtolower(pathinfo(UPLOAD_PATH . DIRECTORY_SEPARATOR . $_FILES['image']['name'], PATHINFO_EXTENSION));
            $imageName = $common->generateRandomString(4,'alphanum'). "." . $extension;
            if(move_uploaded_file($_FILES["image"]["tmp_name"], $pathname. DIRECTORY_SEPARATOR.$imageName)){
                $this->update(array('image' => $imageName),array("category_id" =>$lastInsertId));
            }
        }

        return $lastInsertId;
    }

    public function fetchAllActiveCategory()
    {
        # fetching active category list...
        $categoryList = $this->select(array('category_status' => 'active'))->toArray();
        $data = array();
        foreach($categoryList as $aRow){
            $categoryName = array();
            if($aRow['parent_id'] > 0){
                $parentId = $aRow['parent_id'];
                do{
                    $result = $this->select(array('category_id' => $parentId))->current();
                    if($result){
                        $categoryName []=  ucwords($result['category_name']);
                        $parentId = $result['parent_id'];

                    }
                }while($result && $result['parent_id'] == 0);
            }
            $categoryName[] = ucwords($aRow['category_name']);
            $data[] = array(
                'category_id'   => $aRow['category_id'],
                'category_name' => implode(' -> ',$categoryName),
                'category_slug' => $aRow['category_slug'],
                'description'   => $aRow['description'],
                'parent_id'     => $aRow['parent_id']
            );
        }
        // \Zend\Debug\Debug::dump($data);
        return $data;
    }

    public function deleteByCategoryId($id)
    {
        # remove the category...
        return $this->delete(array('category_id'=>base64_decode($id)));
    }

    public function changeStatusByCategoryId($params)
    {
        # change the status of the category...
        return $this->update(array('category_status' => $params['status']),array('category_id'=>base64_decode($params['id'])));
    }

    public function fetchCategoryListInGrid($parameters, $acl)
    {
        $aColumns = array('category_name', 'description', 'category_status');
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
        if ($acl->isAllowed($role, 'Admin\Controller\Category', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        /* if ($acl->isAllowed($role, 'Admin\Controller\Category', 'delete')) {
            $delete = true;
        } else {
            $delete = false;
        } */
        if ($acl->isAllowed($role, 'Admin\Controller\Category', 'change-status')) {
            $changeStatus = true;
        } else {
            $changeStatus = false;
        }

        foreach ($rResult as $key=>$aRow) {
            $row = array();$updateLink = '';$changeStatusLink = '';$deleteLink = '';$categoryName = array();$result = false;
            if($aRow['parent_id'] > 0){
                $parentId = $aRow['parent_id'];
                do{
                    $result = $this->select(array('category_id' => $parentId))->current();
                    if($result){
                        $categoryName []=  ucwords($result['category_name']);
                        $parentId = $result['parent_id'];

                    }
                }while($result && $result['parent_id'] == 0);
            }
            $categoryName[] = ucwords($aRow['category_name']);
            $row[] = implode(' <i class="fa fa-arrow-right"></i> ',$categoryName);
            $row[] = $aRow['description'];
            $row[] = ucwords($aRow['category_status']);
            if($update){
                $updateLink = '<a href="/admin/category/edit/' . base64_encode($aRow['category_id']) . '" class="btn btn-sm btn-outline-info" style="margin-left: 2px;" title="Edit category of '.ucwords($aRow['category_name']).'"><i class="far fa-edit"></i> Edit</a>';
            }
            /* if($delete){
                $deleteLink = '<a href="javascript:void(0);" onclick="deleteCategory(\''.base64_encode($aRow['category_id']).'\')" class="btn btn-sm btn-outline-danger" style="margin-left: 2px;" title="Delete category of '.ucwords($aRow['category_name']).'"><i class="far fa-trash-alt"></i> Delete</a>';
            } */
            if($changeStatus){
                $statusTxt = (isset($aRow['category_status']) && $aRow['category_status'] == 'active')?"inactive":"active";
                $changeStatusLink = '<a href="javascript:void(0);" onclick="changeStatus(\''.base64_encode($aRow['category_id']).'\',\''.$statusTxt.'\')" class="btn btn-sm btn-outline-dark" style="margin-left: 2px;" title="Change status of '.ucwords($aRow['category_name']).'"><i class="fa fa-exchange-alt"></i> Change Status</a>';
            }
            if($changeStatus || $delete || $update){
                $row[] = $updateLink . $deleteLink . $changeStatusLink;
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchCategoryById($id)
    {
        # fetch category by id...
        return $this->select(array('category_id' => $id))->current();
    }
    
    public function fetchCategoryByIdInLinked($id)
    {
        # fetch category by id...
        $result =  $this->select(array('category_id' => $id))->current();
        if($result['parent_id'] > 0){
            $name = array();
            $parentId = $result['parent_id'];
            do{
                $result = $this->select(array('id' => $parentId))->current();
                if($result){
                    $name []=  ucwords($result['category_name']);
                    $parentId = $result['parent_id'];
                }
            }while($result && $result['parent_id'] == 0);
        } else{
            return array(
                'name'          => $result['category_name'],
                'slug'          => $result['category_slug'],
                'description'   => $result['description'],
                'image'         => $result['image']
            );
        }
    }
}
?>