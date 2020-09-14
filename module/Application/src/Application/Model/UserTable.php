<?php

namespace Application\Model;

use Zend\Session\Container;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Expression;
use Application\Service\CommonService;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Countries
 *
 * @author amit
 */
class UserTable extends AbstractTableGateway
{

    protected $table = 'user';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function addUser($param)
    {
        $logincontainer = new Container('user');
        $common = new CommonService();
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $password = sha1($param['password'] . $configResult["password"]["salt"]);
        $data = array(
            'user_name'         => $param['userName'],
            'gender'            => $param['gender'],
            'alternate_email'   => $param['alternateEmail'],
            'address'           => $param['address'],
            'email'             => $param['email'],
            'password'          => $password,
            'contact_no'        => $param['mobileNumber'],
            'alternate_contact' => $param['alternateNumber'],
            'role'              => $param['role'],
            'added_on'          => $common->getDateTime(),
            'user_status'       => $param['status'],
            'added_by'          => $logincontainer->userId
        );
        // \Zend\Debug\Debug::dump($data);die;
        $this->insert($data);
        return $this->lastInsertValue;
    }

    public function updateUser($param)
    {
        $userId = base64_decode($param['userId']);
        $data = array(
            'user_name'         => $param['userName'],
            'gender'            => $param['gender'],
            'alternate_email'   => $param['alternateEmail'],
            'address'           => $param['address'],
            'email'             => $param['email'],
            'contact_no'        => $param['mobileNumber'],
            'alternate_contact' => $param['alternateNumber']
        );
        if (isset($param['role']) && $param['role'] != '') {
            $data['role'] = $param['role'];
        }
        if (isset($param['status']) && $param['status'] != '') {
            $data['user_status'] = $param['status'];
        }
        if (isset($param['password']) && $param['password'] != '') {
            $config = new \Zend\Config\Reader\Ini();
            $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
            $passsword = sha1($param["password"] . $configResult["password"]["salt"]);
            $data['password'] = $passsword;
        }
        return $this->update($data, array("user_id" => $userId));
    }

    public function fetchUserList($parameters, $acl)
    {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = array('user_name', 'gender', 'email', 'role_name', "DATE_FORMAT(added_on,'%d-%b-%Y')", 'contact_no', 'user_status');
        $orderColumns = array('user_name', 'gender', 'email', 'role_name', 'added_on', 'contact_no', 'user_status');
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
        $sQuery = $sql->select()->from(array('u' => 'user'))
        ->join(array('r' => 'roles'), 'r.role_id=u.role', array('role_name'));

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

        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery); // Get the string of the Sql, instead of the Select-instance 
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE);
        /* Data set length after filtering */
        $sQuery->reset('limit');
        $sQuery->reset('offset');
        $fQuery = $sql->getSqlStringForSqlObject($sQuery);
        $aResultFilterTotal = $dbAdapter->query($fQuery, $dbAdapter::QUERY_MODE_EXECUTE);
        $iFilteredTotal = count($aResultFilterTotal);

        /* Total data set length */
        $iTotal = $this->select(array("user_id!='daemon'"))->count();


        $output = array(
            "sEcho" => intval($parameters['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );
        $sessionLogin = new Container('user');
        $role = $sessionLogin->roleCode;
        if ($acl->isAllowed($role, 'Admin\Controller\User', 'edit')) {
            $update = true;
        } else {
            $update = false;
        }
        foreach ($rResult as $aRow) {
            $row = array();
            $row[] = ucwords($aRow['user_name']);
            $row[] = ucwords($aRow['gender']);
            $row[] = $aRow['email'];
            $row[] = ucwords($aRow['role_name']);
            $row[] = $aRow['contact_no'];
            $row[] = date('d-M-Y H:s A',strtotime($aRow['added_on']));
            $row[] = ucwords($aRow['user_status']);
            if($update){
                $row[] = '<a href="/admin/user/edit/' . base64_encode($aRow['user_id']) . '" class="btn btn-default" style="margin-right: 2px;" title="Edit"><i class="far fa-edit"></i> Edit</a>';
            }
            $output['aaData'][] = $row;
        }
        return $output;
    }

    public function getUser($userId)
    {
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $sQuery = $sql->select()->from(array('u' => 'user'))
            ->join(array('r' => 'roles'), 'r.role_id=u.role', array('role_name'))
            ->where(array('u.user_id' => $userId));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        return $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
    }

    public function loginProcess($params){
        $dbAdapter = $this->adapter;
        $sql = new Sql($dbAdapter);
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $passsword = sha1($params["password"] . $configResult["password"]["salt"]);
        $logincontainer = new Container('user');
        $alertcontainer = new Container('alert');

        $sQuery = $sql->select()->from(array('u'=>'user'))->columns(array('user_id','user_name','user_status'))
        ->join(array('r'=>'roles'),'r.role_id=u.role',array('role_code'))
        ->where(array('user_name'=>$params['username'],'password'=>$passsword));
        $sQueryStr = $sql->getSqlStringForSqlObject($sQuery);
        $rResult = $dbAdapter->query($sQueryStr, $dbAdapter::QUERY_MODE_EXECUTE)->current();
        if(isset($rResult['user_status']) && $rResult['user_status'] == "active"){
            $logincontainer->userId = $rResult['user_id'];
            $logincontainer->userName = $rResult['user_name'];
            $logincontainer->roleCode = $rResult['role_code'];
            return 'admin-home';
        }else if(isset($rResult['user_status']) && $rResult['user_status'] != "active"){
            $alertcontainer->alertMsg = 'You are not activated. Please contact your administrator!';
            return 'admin-login';
        }else{
            $alertcontainer->alertMsg = 'Please check your login credentials!';
            return 'admin-login';
        }
    }

    public function modifyUserPassword($params)
    {
        $userLogincontainer = new Container('userLoginContainer');
        $userId = $userLogincontainer->userId;
        $config = new \Zend\Config\Reader\Ini();
        $configResult = $config->fromFile(CONFIG_PATH . '/custom.config.ini');
        $passsword = sha1($params["newpassword"] . $configResult["password"]["salt"]);
        $data = array('password' => $passsword);
        return $this->update($data, array('user_id' => $userId));
    }

    public function fetchActiveUsers()
    {
        return $this->select(array('user_status' => 'active'))->toArray();
    }
}