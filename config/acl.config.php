<?php
return array (
  'ADM' => 
  array (
    'Admin\\Controller\\Category' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'change-status' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\Customer' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\Index' => 
    array (
      'index' => 'allow',
    ),
    'Admin\\Controller\\Product' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'change-status' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\Role' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\Supplier' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'change-status' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\User' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'edit' => 'allow',
      'edit-profile' => 'allow',
    ),
    'Admin\\Controller\\Zone' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'change-status' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\QtyDetails' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'change-status' => 'allow',
      'edit' => 'allow',
    ),
  ),
  'WRK' => 
  array (
    'Admin\\Controller\\Category' => 
    array (
      'index' => 'deny',
      'add' => 'deny',
      'change-status' => 'deny',
      'delete' => 'deny',
      'edit' => 'deny',
    ),
    'Admin\\Controller\\Customer' => 
    array (
      'index' => 'allow',
      'add' => 'allow',
      'edit' => 'allow',
    ),
    'Admin\\Controller\\Index' => 
    array (
      'index' => 'allow',
    ),
    'Admin\\Controller\\Role' => 
    array (
      'index' => 'deny',
      'add' => 'deny',
      'edit' => 'deny',
    ),
    'Admin\\Controller\\User' => 
    array (
      'index' => 'deny',
      'add' => 'deny',
      'edit' => 'deny',
    ),
    'Admin\\Controller\\Zone' => 
    array (
      'index' => 'deny',
      'add' => 'deny',
      'change-status' => 'deny',
      'edit' => 'deny',
    ),
  ),
);
