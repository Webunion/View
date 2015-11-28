<?php namespace Webunion\View;

class View
{
	private $path;
	private $layout;
	private $page;
	private $data = array();
	private $dataFix = array();
	
	protected $appViewData = array();
	protected $appViewDataFix = array();

	public function __construct( $path ){
		$this->path = $path;
        $this->loadLayout();
        $this->loadPage();
	}
	
	public function loadLayout( $file = 'default' ){
		$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
		$file = $this->path . DIRECTORY_SEPARATOR . 'layouts'. DIRECTORY_SEPARATOR . $file.'.php';
		if( is_file( $file) ){
			$this->layout = file_get_contents( $file );
		}
		else{
			throw new \Exception('Layout not find');
		}
	}
	
	public function loadPage( $file = 'default' ){
		$file = str_replace('/', DIRECTORY_SEPARATOR, $file);
		$file = $this->path . DIRECTORY_SEPARATOR . 'pages'. DIRECTORY_SEPARATOR . $file.'.php';
		if( is_file( $file) ){
			$this->page = file_get_contents( $file );
		}
		else{
			throw new \Exception('View not find');
		}
	}

	//Insere variaveis no array appViewData para exibir na View
    public function setVar($var, $content = null, $method = false){
        if(!is_array($var)){
			if(!($method)){
				$this->data["$var"] = $content;
			}
			else{
				array_key_exists($var, $this->data) ? $this->data["$var"] .= $content : $this->data["$var"] = $content;
			}
		}
		else{
			foreach($var AS $key=>$value){
				if(is_null($method)){
					$this->data["$key"] = $value;
				}
				else{
					array_key_exists($key, $this->data) ? $this->data["$key"] .= $value : $this->data["$key"] = $value;
				}
			}
		}
	}
	
	//Insere variaveis no array appViewDataFix para substituir na View
	public function setFixVars($var, $content = null, $method = false){
      	if(!is_array($var)){
			if(!($method)){
				$this->dataFix["$var"] = $content;
			}
			else{
				array_key_exists($var, $this->dataFix) ? $this->dataFix["$var"] .= $content : $this->dataFix["$var"] = $content;
			}
		}
		else{
			foreach($var AS $key=>$value){
				if(is_null($method)){
					$this->dataFix["$key"] = $value;
				}
				else{
					array_key_exists($key, $this->dataFix) ? $this->dataFix["$key"] .= $value : $this->dataFix["$key"] = $value;
				}
			}
		}
   }

	//Substiui marcacoes na View diretamente, precisa instanciar a view antes de chamar este metodo
    public function replaceVars($var, $content){
        $this->layout 	= str_replace("{#$var#}", $content, $this->layout);
		$this->page 	= str_replace("{#$var#}", $content, $this->page);
	}
	
	//Substitui variaveis da view/layout (marcadas com tag) por dados do appViewDataFix
	public function replaceFixVars(){
		if(!empty($this->dataFix) AND is_array($this->dataFix)){
			foreach($this->dataFix AS $key=>$value){
				$this->layout 	= str_replace('{#'.$key.'#}', $value, $this->layout);
				$this->page 	= str_replace('{#'.$key.'#}', $value, $this->page);
			}		
		}	
	}

	//Limpa variaveis na usadas na view e layout (maracads com tag)
	protected function clearUnusedVars(){
        $this->layout = preg_replace('[{#(.*)#}]', "", $this->layout);
        $this->page = preg_replace('[{#(.*)#}]', "", $this->page);
    }

	public function render( $page = null, $data = array() ){
		//Substituo os dados armazenados em appViewDataFix desta nos layout e view
		$this->replaceFixVars();
		
		if( !is_null($page) && !empty($page) ){
			$this->loadPage( $page );			
		}
		
		//Transforma os dados passados no array deste metodo em variaveis locais
		if(!empty($data) AND is_array($data)){extract($data, EXTR_PREFIX_SAME, 'view');}
		
		//Transforma os dados armazenados em appViewData desta classe em variaveis locais
		if(!empty($this->data)){extract($this->data, EXTR_PREFIX_SAME, 'view');}
		
		//Limpo variaveis que estao na view mas nao usadas (marcadas com tag)
		$this->clearUnusedVars();

		//renderizo o código php na view e salvo na variavel appPage
		ob_start();
		eval('?>'.$this->page);
		$appPage = ob_get_contents();
		ob_end_clean();
		
		//renderizo o código php do layout (incluindo a view) e imprimo tudo
		ob_start();
		eval('?>'.$this->layout);
		$retorno = ob_get_contents();
		ob_end_clean();
		
		return $retorno;
   }
      
   //ENCODE AN ARRAY TO AN XML STRING
	public static function encodeXML($data, $node = null, $header = false, $att=''){
		$xml = '';
		if(($header)){
			$xml .= '<?xml version="1.0" encoding="utf-8"?>'."\n";	
		}
		$xml .= (!is_null($node) AND !is_int($node)) ? '<'.trim($node.' '.$att).'>'."\n" : '';
			if( array_key_exists(0, $data) ){
				//$xml .= $data[0]."\n";
				//unset($data[0]);
			}
			foreach($data as $key => $val){
				if(is_array($val) || is_object($val)){
					$att = '';
					if(array_key_exists('attr:', $val)){
						foreach($val['attr:'] AS $k=>$v){
							$att .= $k.'="'.$v.'" ';
						}
						unset($val['attr:']);
					}
					if(count($val) == 0){
						$xml .= '<'.trim($key.' '.$att).' />'."\n";
					}
					else{
						$xml .= self::encodeXML($val, $key, false, $att);
					}
					$att = '';
				}
				else{
					//PROBLEMA DO ZERO ESTÁ AQUI NO EMPTY
					$xml .= ($val == '' || is_null($val)) ? '<'.trim($key.''.$att).' />'."\n" : "<$key>" . htmlspecialchars($val) . "</$key>\n";
				}
			}
		$xml .= (!is_null($node) AND !is_int($node)) ? '</'.$node.'>'."\n" : '';
		return $xml;
	}
	
	//ENCODE AN ARRAY TO JSON STRING
	public static function encodeJSON($data, $node = false, $callBack = false){
		$cIni = ($callBack) ?  '/**/'.$callBack.'('  : null; 
		$cFim = ($callBack) ?  ');'  : null;
			
		if( $node ){
			$data = [ $node=>$data ];				
		}			
		return $cIni.json_encode($data).$cFim;
	}
}