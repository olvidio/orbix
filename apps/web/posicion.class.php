<?php
namespace web;
use core;

class Posicion {
	
	/**
	 * id_div de Posicion
	 *
	 * @var string
	 */
	private $sid_div;
	/**
	 * Url de Posicion
	 *
	 * @var string
	 */
	private $surl;
	/**
	 * Bloque de Posicion
	 *
	 * @var string
	 */
	private $sbloque;
	/**
	 * parametros de Posicion
	 *
	 * @var array
	 */
	private $aParametros = array ();

	/* CONSTRUCTOR ------------------------------ */
	function __construct($php_self='',$vars=array()) {
		$this->surl = $php_self;
		$this->sbloque = 'main';
		$this->setParametros($vars);
	}
	
	/**
	 * coloca el cursor de posicion n posiciones atras.
	 *
	 * @var n número de posiciones a retroceder.
	 */
	public function go($n=0) {
		$aPosition = end($_SESSION['position']);
		for ($i=0; $i < $n; $i++) {
			$aPosition = prev($_SESSION['position']);
		}
		//print_r($_SESSION['position']);
		$this->surl = $aPosition['url'];
		$this->sbloque = $aPosition['bloque'];
		$this->aParametros = $aPosition['parametros'];
	}

	public function recordar() {
		//echo "<script>history.pushState({state:'new'},'New State','?new');</script>";
		// evitar que sea muy grande
		$this->limitar(20);

		$aPosition = array('url'=>$this->surl,'bloque'=>$this->sbloque,'parametros'=>$this->aParametros);
		$_SESSION['position'][] = $aPosition;
	}

	public function atras() {
		// puede ser que no haya donde volver
		if (empty($_SESSION['position'])) {
			return '';
		}
		$id_div = $this->getId_div();
		$id_div = empty($id_div)? 'ir_atras' : $id_div;
		$aPosition = end($_SESSION['position']);
		$aParam = $aPosition['parametros'];
		$url = $aPosition['url'];
		$sparametros = Hash::add_hash($aParam,$url);

		$html = '<div id="'.$id_div.'" style="display: none;">';
		$html .= '	<form id="go">';
		$html .= '	url: <input id="url" type="text" value="' . $url .'" size=70><br>';
		$html .= '	parametros: <input id="parametros" type="text" value="' . $sparametros . '" size=70><br>';
		$html .= '	bloque: <input id="id_div" type="text" value="' . $aPosition['bloque'] . '" size=70>';
		$html .= '</form>';
		$html .= '</div>';
		return $html;
	}
	public function atras2() {
		$aPosition = end($_SESSION['position']);
		$aParam = $aPosition['parametros'];
		$url = $aPosition['url'];
		$sparametros = Hash::add_hash($aParam,$url);

		$html = '<div style="display: none;">';
		$html .= '<form id="go">';
		$html .= '	<input id="url" type="hidden" value="' . $url .'" size=70>';
		$html .= '	<input id="parametros" type="hidden" value="' . $sparametros . '" size=70>';
		$html .= '	<input id="id_div" type="hidden" value="' . $aPosition['bloque'] . '" size=70>';
		$html .= '</form>';
		$html .= '</div>';
		$html .= "<img onclick=fnjs_ir_a() src=".core\ConfigGlobal::$web_icons.'/flechas/left.gif border=0 height=40>';
		return $html;
	}

	private function limitar($n=10) {
		//if (isset($_SESSION['position']) & is_array($_SESSION['position']) & (count($_SESSION['position']) > 2*$n)) {
		if (isset($_SESSION['position'])) { // No sé poruqe no deja poner todo junto
			if(is_array($_SESSION['position']) & (count($_SESSION['position']) > 2*$n)) {
				array_splice($_SESSION['position'], -$n); // negativo empieza por el final.
			}
		}
	}

	/**
	 * estableix el valor de l'atribut id_div de Posicion
	 *
	 * @param string id_div
	 */
	function setId_div($id_div) {
		$this->sid_div = $id_div;
	}
	/**
	 * Recupera l'atribut id_div de Posicion
	 *
	 * @return string id_div
	 */
	function getId_div() {
		return $this->sid_div;
	}
	/**
	 * estableix el valor de l'atribut url de Posicion
	 *
	 * @param string url
	 */
	function setUrl($url) {
		$this->surl = $url;
	}
	/**
	 * Recupera l'atribut url de Posicion
	 *
	 * @return string url
	 */
	function getUrl() {
		return $this->surl;
	}
	/**
	 * estableix el valor de l'atribut bloque de Posicion
	 *
	 * @param string sbloque
	 */
	function setBloque($bloque) {
		$this->sbloque = $bloque;
	}
	/**
	 * Recupera l'atribut bloque de Posicion
	 *
	 * @return string sbloque
	 */
	function getBloque() {
		return $this->sbloque;
	}

	/**
	 * estableix el valor de tots els atributs parametros de Posicion que se li passen en un array
	 *
	 * @param array aVars
	 */
	public function setParametros($aVars) {

		foreach ($aVars as $key=>$value) {
			$this->aParametros[$key] = $value;
		}
	}
	/**
	 * recupera el valor de tots del parametre
	 *
	 * @param string nomParametre
	 */
	public function getParametro($nomParametre) {
		$valParametre = empty($this->aParametros[$nomParametre])? '' : $this->aParametros[$nomParametre];
		return $valParametre;
	}

	/**
	*
	* Esta función sirve para ir a una página. Típico al acabar un procedimiento.
	*
	* versión Ajax.
	*
	* la variable $go_to puede contener sólo el nombre de la página o también el <div> 
	* donde se quiere la página. (separado por un '|'): pagina|div. ¡OJO!, cuando se pasa
	* una consulta con concatenaciones (||) es un lio. 
	*
	* busca la página en el directorio actual. Para usar una referencia absoluta a una página,
	* el $go_to deberia empezar por '#'.
	*/
	public function ir_a($go_to) {
		if ($go_to=='atras') { return self::atras(); }
		$url='';
		$parametros='';
		$frame='';
		$go=strtok($go_to,"@");
		/*
		if ($go=="session" && isset($_SESSION['session_go_to']) ) {
			$g=strtok("@");

			empty($_SESSION['session_go_to'][$g]['pag'])? $pagina='' : $pagina=$_SESSION['session_go_to'][$g]['pag'];
			empty($_SESSION['session_go_to'][$g]['dir_pag'])? $dir_pag='' : $dir_pag=$_SESSION['session_go_to'][$g]['dir_pag'];
			empty($_SESSION['session_go_to'][$g]['target'])? $frame ='' : $frame = $_SESSION['session_go_to'][$g]['target'];
			// separo la url de los parametros
			if ($p=strpos($pagina,"?") || $p=strpos($pagina,"%3F") ) { //"%3F" es "?" cuando está encode) 
				$pagina=substr($pagina,0,$p);
				$parametros=substr($pagina,$p+1)."&go_to=$go_to";
			} else {
				$parametros="go_to=$go_to";
			}
			if (!empty($dir_pag)) {$dire=$dir_pag; } else {$dire=system("pwd"); }
			$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
			if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
			
			$url=core\ConfigGlobal::getWeb().$path."/".$pagina;
			
			// mas parámetros que pueden estar registrados en la session:
			foreach($_SESSION['session_go_to'][$g] as $clave => $valor) {
				if ($clave!="dir_pag" && $clave!="pag" && $clave!="target" ) {
					$parametros.="&$clave=$valor";
				}
			}

		} else {
		*/
			$pag_sin_param = '';
			$pagina=urldecode($go_to);
			$pagina=strtok($pagina,"|");
			$frame = strtok("|");
			//echo "frame: $frame<br>";

			$_error_txt = "pagina1: $pagina<br>";
			// separo la url de los parametros
			$p=strpos($pagina,'?');
			if ($p !== false ) {
				$pag_sin_param=substr($pagina,0,$p);
				$parametros=substr($pagina,$p+1);
				$_error_txt .= "pag sin param: $pag_sin_param<br>";
				$_error_txt .= "param: $parametros<br>";
			} else {
				$pag_sin_param=$pagina;
			}
			$posi=strpos($parametros,"condicion=");
			if ($posi===false) {
				//$cond=substr($parametros,$posi);
			} else {
				$cond1=substr($parametros,$posi+10);
				//para asegurar que no tiene barras \
				$cond1=stripslashes($cond1);	
				$_error_txt .= "cond1: $cond1<br>";
				$cond2=urlencode($cond1);
				$_error_txt .= "cond2: $cond2<br>";
				$parametros=str_replace($cond1,$cond2,$parametros);
			}
			
			$pagina=$pag_sin_param; // quito la doble barra
			if (strpos($pagina,core\ConfigGlobal::getWeb()) !== false ) { // Si es una referencia absoluta
				$url=$pagina;
			} else {
				$_error_txt .= "pagina2: $pagina<br>";
				$dire=getcwd();
				$_error_txt .= "dire: $dire<br>";
				$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
				$_error_txt .= "path: $path<br>";
				if (substr($path,-1)!='/'){$path.='/';} // me aseguro de que acabe en "/"
				$_error_txt .= "path2: $path<br>";
				$pagina=str_replace ($path,"",$pagina); //si la dirección ya es absoluta, la quito
				$_error_txt .= "pagina3: $pagina<br>";
				//echo "directorio: $dire, path: $path<br>";
				//echo "pagina: $pagina<br>";
				if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
				if (substr($pagina,0,2)=='./'){$pagina=substr($pagina,1);}
				if (substr($pagina,0,3)=='../') { //quito un directorio de $path
					$path = preg_replace('/\w+\/?$/', '', $path);
					$pagina=substr($pagina,3);
				}
				$_error_txt .= "pagina4: $pagina<br>";
				//echo "pagina2: $pagina<br>";
				$url=core\ConfigGlobal::getWeb().$path.$pagina;
			}
			/*
			if (core\ConfigGlobal::mi_id_usuario() == 443) {
				echo "hola dani<br>";
				echo $_error_txt;
			}
			*/
		//}
		if (empty($frame)) $frame="main";

		// passarlo a array para usar la funcion add_hash
		$aParam = array();
		foreach (explode('&',$parametros) as $param) {
			$aa = explode('=',$param);
			$aParam[$aa[0]] = empty($aa[1])? '' : $aa[1];
		}
		$parametros = Hash::add_hash($aParam,$url);
		?>
		<div id="ir_a" style="display: none;">
			<form id="go">
			url: <input id="url" type="text" value="<?= $url ?>" size=70><br>
			parametros: <input id="parametros" type="text" value="<?= $parametros ?>" size=70><br>
			bloque: <input id="id_div" type="text" value="<?= $frame ?>" size=70>
			</form>
		</div>
		<?php
	}

	/**
	*
	* Esta función sirve para ir a una página. desde un link, a traves de java: location.
	* El parámetro $form sirve para indicar si se pone una direccion absoluta (http:...) o una relativa al $web (es para el caso del action de un formulario).
	*
	*/
	public function link_a($go_to,$form='') {
		$go=strtok($go_to,"@");
		if ($go=="session" && isset($_SESSION['session_go_to']) ) {
			$g=strtok("@");
			empty($_SESSION['session_go_to'][$g]['pag'])? $pagina='' : $pagina=$_SESSION['session_go_to'][$g]['pag']."?go_to=$go_to";
			empty($_SESSION['session_go_to'][$g]['dir_pag'])? $dir_pag='' : $dir_pag=$_SESSION['session_go_to'][$g]['dir_pag'];
			empty( $_SESSION['session_go_to'][$g]['target'])? $frame ='' : $frame = $_SESSION['session_go_to'][$g]['target'];
			if (!empty($dir_pag)) {$dire=$dir_pag; } else { $dire=getcwd(); }
			$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
			// quito la barra inicial del path (si existe).
			if (substr($path,0,1)=='/'){$path=substr($path,1);}
			if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
			//para mantener el id de la session
			if (!empty($form)) {
				$location="$path/$pagina";
			} else {
				if (strstr($pagina,"?")){
					$pagina.='&PHPSESSID='.session_id();
				} else {
					$pagina.='?PHPSESSID='.session_id();
				}
				$location="'http:".core\ConfigGlobal::getWeb()."/".$path."/".$pagina."'";
			}
			return $location;
		} else {
			$pagina=urldecode($go_to);
			$pagina=str_replace (core\ConfigGlobal::getWeb(),"",$pagina); //si la dirección ya es absoluta, la quito
			$dire=getcwd();
			$path= str_replace (core\ConfigGlobal::$directorio, "", $dire);
			if (substr($path,-1)!='/'){$path.='/';} // me aseguro de que acabe en "/"
			$pagina=str_replace ($path,"",$pagina); //si la dirección ya es absoluta, la quito
			//echo "directorio: $dire, path: $path<br>";
			//echo "pagina: $pagina<br>";
			if (substr($pagina,0,1)=='#'){$pagina=substr($pagina,1); $path="";}
			if (substr($pagina,0,2)=='./'){$pagina=substr($pagina,1);}
			if (substr($pagina,0,3)=='../') { //quito un directorio de $path
				$path = preg_replace('/\w+\/?$/', '', $path);
				$pagina=substr($pagina,3);
			}
			//echo "pagina2: $pagina<br>";
			// separo la url de los parametros
			if ($p=strpos($pagina,'?') ) { //"%3F" es "?" cuando está encode) 
				$pag=substr($pagina,0,$p);
				// no arrastro el goto, no sé porque estba aqui.
				$parametros=substr($pagina,$p+1);
				//$parametros=substr($pagina,$p+1)."&go_to=$go_to";
				//echo "param: $parametros<br>";
			} else {
				$pag=$pagina;
				//$parametros="go_to=$go_to";
			}
			$posi=strpos($parametros,"condicion=");
			if ($posi===false) {
				//$cond=substr($parametros,$posi);
			} else {
				$cond1=substr($parametros,$posi+10);
				//para asegurar que no tiene barras \
				$cond1=stripslashes($cond1);	
				//echo "cond1: $cond1<br>";
				$cond2=urlencode($cond1);
				//echo "cond2: $cond2<br>";
				$parametros=str_replace($cond1,$cond2,$parametros);
			}
			$url=core\ConfigGlobal::getWeb().$path.$pag;
			return "'".$url."?".$parametros."'";
		}
	}
}
?>
