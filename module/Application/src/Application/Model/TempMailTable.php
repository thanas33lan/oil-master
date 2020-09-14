<?php
namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Session\Container;
use Zend\Db\Sql\Expression;
use Application\Service\CommonService;

class TempMailTable extends AbstractTableGateway {

    protected $table = 'temp_mail';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function insertTempMail($message,$fromMail,$toMail,$cc,$bcc,$subject,$fromName,$attachment=null){
         $data = array(
                'message' => $message,
                'from_mail' => $fromMail,
                'to_email' => $toMail,
                'cc' => $cc,
                'bcc' => $bcc,
                'subject' => $subject,
                'from_full_name' => $fromName,
                'attachment' => $attachment
            );
            if(trim($toMail)!=""){
                $this->insert($data);
            }
            return $this->lastInsertValue;
    }
    
    public function deleteTempMail($id){
         $this->delete(array('temp_id = ' . $id));
    }
    
    public function updateTempMailStatus($id){
        
        $data = array(
                'status' => 'not-sent'
            );
        $this->update($data,array('temp_id='.$id));
    }
}
    