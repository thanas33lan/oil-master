<?php
namespace Application\Model;

use Zend\Config\Factory;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Resource\GenericResource;
use Zend\Permissions\Acl\Role\GenericRole;

/**
 * Description of Acl
 *
 * @author amit
 */
class Acl extends ZendAcl {
    public function __construct($resourceList,$rolesList) {
        foreach ($resourceList as $res) {
            if (!$this->hasResource($res['resource_id'])) {
                $this->addResource(new GenericResource($res['resource_id']));
            }
        }

        foreach ($rolesList as $rol) {
            if (!$this->hasRole($rol['role_code'])) {
                $this->addRole(new GenericRole($rol['role_code']));
            }
        }

        $config = Factory::fromFile(CONFIG_PATH . DIRECTORY_SEPARATOR . "acl.config.php");

        foreach ($config as $role => $resource) {
            if (!$this->hasRole($role)) {
                $this->addRole(new GenericRole($role));
            }
            foreach ($resource as $resource => $permission) {
                foreach ($permission as $privilege => $permission) {
                    $this->$permission($role, $resource, $privilege);
                }
            }
        }
    }

}
