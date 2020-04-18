<?php
namespace procesos\model;
use permisos\model as permisos;
use permisos\model\PermisosActividades;
class PermAfectados extends permisos\Xpermisos {
  public static $classname = "CuadrosPermActiv";
  public $permissions;
  
  public function __construct($iaccion=0) {
      $this->iaccion = $iaccion;
      $this->permissions = PermisosActividades::Afecta;
  }
}