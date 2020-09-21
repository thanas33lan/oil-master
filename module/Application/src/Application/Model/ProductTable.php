<?php
namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Application\Service\CommonService;
use Application\Model\UserProductTable;
use Zend\Db\Sql\Where;

class ProductTable extends AbstractTableGateway {

    protected $table = 'product';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    public function saveProductDetails($params)
    {
        # save Product details code...
        $productQtyMapDb = new ProductQtyMapTable($this->adapter);
        $lastInsertId = 0;
        $common = new CommonService();
        $sessionLogin = new Container('user');
        $data = array(
            'category_id'   => base64_decode($params['categoryId']),
            'name'          => $params['name'],
            'slug'          => $params['slug'],
            'description'   => $params['description'],
            'purchase_price'=> $params['purchasePrice'],
            'dealer_price'  => $params['dealerPrice'],
            'mrp_price'     => $params['mrpPrice'],
            'tax'           => $params['tax'],
            'hsn'           => $params['hsn'],
            'sku'           => $params['sku'],
            'option_colour' => $params['optColour'],
            'option_grade'  => $params['optGrade'],
            'option_type'   => $params['optType'],
            'remainder_qty' => $params['remainderQty'],
            'status'        => $params['status']
        );
        if(isset($params['productId']) && $params['productId'] != ''){
            $data['modified_on'] = $common->getDateTime();
            $data['modified_by'] = $sessionLogin->userId;
            $lastInsertId = base64_decode($params['productId']);
            $this->update($data,array('id' => $lastInsertId));
            /* Delete mapping if already exist */
            $productQtyMapDb->delete(array('product_id' => $lastInsertId));
        }else{
            $data['created_on'] = $common->getDateTime();
            $data['created_by'] = $sessionLogin->userId;
            $this->insert($data);
            $lastInsertId = $this->lastInsertValue;

        }
        if(!empty($params['productQtyMap'])){
            foreach($params['productQtyMap'] as $mapId){
                $productQtyMapDb->insert(array(
                    'product_id'=> $lastInsertId,
                    'qty_id'    => base64_decode($mapId)
                ));
            }
        }
        // \Zend\Debug\Debug::dump($lastInsertId);die;

        if(isset($_FILES['image']['name']) && trim($_FILES['image']['name'])!=''){
            if (!file_exists(UPLOAD_PATH . DIRECTORY_SEPARATOR . "Product") && !is_dir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "Product")) {
                mkdir(UPLOAD_PATH . DIRECTORY_SEPARATOR . "Product",0777);
            }
            $pathname = UPLOAD_PATH . DIRECTORY_SEPARATOR . "Product" . DIRECTORY_SEPARATOR . "" . $lastInsertId;
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

    public function fetchAllActiveProduct()
    {
        # fetching active Product list...
        $sessionLogin = new Container('user');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('p' => $this->table))
        ->Where(array('p.status' => 'active'));
        if($sessionLogin->roleCode != 'ADM'){
            $sQuery = $sQuery->join(array('upm' => 'user_product_map'),'p.id=upm.product_id',array());
            $sQuery = $sQuery->where(array('upm.user_id' => $sessionLogin->userId));
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $productList = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        foreach($productList as $key=>$aRow){
            $qQuery = $sql->select()->from(array('pqm' => 'product_qty_map'))->columns(array('pqm.product_id'))
            ->join(array('q' => 'qty_details'),'pqm.qty_id=q.qty_id',array('qty_id','qty_name','qty_status'))
            ->where(array('qty_status' => 'active'));
            $qQueryStr = $sql->getSqlStringForSqlObject($qQuery);
            $qtyList = $dbAdapter->query($qQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
            $productList[$key]['qtyDetails'] = $qtyList;
        }
        return $productList;
    }

    public function changeStatusByproductId($params)
    {
        # change the status of the Product...
        return $this->update(array('status' => $params['status']),array('id'=>base64_decode($params['id'])));
    }

    public function fetchProductListInGrid($parameters, $acl)
    {
        $sessionLogin = new Container('user');
        $categoryDb = new CategoryTable($this->adapter);
        $aColumns = array('c.category_name', 'name', 'option_grade', 'description', 'status');
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
        $sQuery = $sql->select()->from(array('p' => $this->table))
        ->join(array('c' => 'category'),'p.category_id=c.category_id',array('category_name', 'category_slug', 'parent_id'));
        if($sessionLogin->roleCode != 'ADM'){
            $sQuery = $sQuery->join(array('upm' => 'user_product_map'),'p.id=upm.product_id',array());
            $sQuery = $sQuery->where(array('upm.user_id' => $sessionLogin->userId));
        }
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
        if ($acl->isAllowed($role, 'Admin\Controller\Product', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        if ($acl->isAllowed($role, 'Admin\Controller\Product', 'change-status')) {
            $changeStatus = true;
        } else {
            $changeStatus = false;
        }

        foreach ($rResult as $aRow) {
            $row = array();$updateLink = '';$changeStatusLink = '';$productName = array();$result = false;
            if($aRow['parent_id'] > 0){
                $parentId = $aRow['parent_id'];
                do{
                    $result = $categoryDb->select(array('category_id' => $parentId))->current();
                    if($result){
                        $categoryName []=  ucwords($result['category_name']);
                        $parentId = $result['parent_id'];

                    }
                }while($result && $result['parent_id'] == 0);
            }
            $categoryName[] = ucwords($aRow['category_name']);
            $row[] = implode(' <i class="fa fa-arrow-right"></i> ',$categoryName);
            $row[] = ucwords($aRow['name']);
            $row[] = ucwords($aRow['option_grade']);
            $row[] = $aRow['description'];
            $row[] = ucwords($aRow['status']);
            if($update){
                $updateLink = '<a href="/admin/product/edit/' . base64_encode($aRow['id']) . '" class="btn btn-sm btn-outline-info" style="margin-left: 2px;" title="Edit Product of '.ucwords($aRow['name']).'"><i class="far fa-edit"></i> Edit</a>';
            }
            if($changeStatus || $update){
                $statusTxt = (isset($aRow['status']) && $aRow['status'] == 'active')?"inactive":"active";
                $changeStatusLink = '<a href="javascript:void(0);" onclick="changeStatus(\''.base64_encode($aRow['id']).'\',\''.$statusTxt.'\')" class="btn btn-sm btn-outline-dark" style="margin-left: 2px;" title="Change status of '.ucwords($aRow['name']).'"><i class="fa fa-exchange-alt"></i> Change Status</a>';
                $row[] = $updateLink . $changeStatusLink;
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function fetchProductById($id)
    {
        # fetch Product by id...
        $sessionLogin = new Container('user');
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('p' => $this->table))
        ->Where(array('p.id' => $id));
        if($sessionLogin->roleCode != 'ADM'){
            $sQuery = $sQuery->join(array('upm' => 'user_product_map'),'p.id=upm.product_id',array());
            $sQuery = $sQuery->where(array('upm.user_id' => $sessionLogin->userId));
        }
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $productList = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        /* To get qty details */
        $qQuery = $sql->select()->from(array('pqm' => 'product_qty_map'))->columns(array('product_id'))
        ->join(array('q' => 'qty_details'),'pqm.qty_id=q.qty_id',array('qty_id','qty_name','qty_status'))
        ->where(array('qty_status' => 'active', 'pqm.product_id' => $id));
        $qQueryStr = $sql->getSqlStringForSqlObject($qQuery);
        $qtyList = $dbAdapter->query($qQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->toArray();
        $productList['qtyDetails'] = $qtyList;
        
        return $productList;
    }
}
?>