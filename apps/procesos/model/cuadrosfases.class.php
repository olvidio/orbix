<?php
namespace procesos\model;

use web\Desplegable;

class CuadrosFases {
    
    /**
     * permisions. array amb els diferents tipos de permisos i el seu numero.
     *
     * @var array
     */
    protected $permissions = array();
    /**
     * acciones posibles. 
     *
     * @var array
     */
    protected $aOpcionesAction;
    /**
     * acciones posibles. 
     *
     * @var object
     */
    protected $oDesplAccion;
    /**
     * opciones seleccionadas
     *
     * @var object
     */
    protected $oFases;
  
    public function __construct() {

        //$this->permissions = $this->generarArrayTraducido();
        $oAcciones = new PermAccion();
        $this->aOpcionesAction = $oAcciones->lista_array();
        $oDesplAccion = new Desplegable('',$this->aOpcionesAction,'',false);
        
        $this->oDesplAccion = $oDesplAccion;
        
    }
	
    function getPermissions() {
        return $this->permissions;
    }
    function setPermissions($permissions) {
        $this->permissions = $permissions;
    }
    function getoFases() {
        return $this->oFases;
    }
    function setoFases($oFases) {
        $this->oFases = $oFases;
    }
    
    public function lista_tiene_txt($oFases) {
        $txt = '';
        foreach ($oFases as $id_fase=>$iAccion ){
            $txt .= empty($txt)? '' : '<br>'; 
            $nom_fase = array_search($id_fase, $this->permissions);
            if(empty($nom_fase)) {
                continue;
            }
            $nom_accion = $this->aOpcionesAction[$iAccion];
            $txt .= $nom_fase;
            $txt .= ' => ';
            $txt .= $nom_accion;
        }
        return $txt;
    }
    
    /**
     * dibuja una lista de checkbox
     *
     */
    public function cuadros_check($nomcamp,$fases_csv){
        
       $oFases = $this->getoFases();
        
        
        $txt = '<table class="semi">';
        $txt .= '<tr><th>';
        $txt .= _("fases");
        $txt .= '</th><th>';
        $txt .= _("permiso");
        $txt .= '</th></tr>';
        
        foreach($this->permissions as $nom=>$id_fase) {
            $camp=$nomcamp."[$id_fase]";
            $this->oDesplAccion->setNombre($camp);
            if($accion = $oFases->$id_fase) {
                $this->oDesplAccion->setOpcion_sel($accion);
            } else {
                $this->oDesplAccion->setOpcion_sel(0);
            }
    
            $txt .= '<tr>';
            //$txt .="<td><input type=\"Checkbox\" id=\"$camp\" name=\"$camp\" value=\"$id_fase\" $chk>$nom</td>";
            $txt .="<td>$nom</td>";
            $txt .= '<td>'.$this->oDesplAccion->desplegable().'</td>';
            $txt .= '</tr>';
        }
        $txt .= '</table>';
        return $txt;
    }
    
}