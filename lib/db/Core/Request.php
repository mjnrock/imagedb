<?php
	class Request {
		public $URI;
		public $Verb;
		public $Params;
		public $Variables;
		public $Query;

		public function __construct($route, $uri, $verb, $params, $queries) {
			$this->URI = $uri;
			$this->Verb = $verb;
			$this->Params = $params;
			$this->Variables = $this->GetVariables($route);
			$this->Query = $queries;
		}

		public function GetVariables($route) {
			if($route[0] === "/") {
				$route = substr($route, 1);
			}
			$route = explode("/", $route);

			if(sizeof($route) !== sizeof($this->Params)) {
				return [];
			}
			
			$vars = [];
			foreach($route as $i => $r) {
				if(substr($r, 0, 1) === ":") {
					$vars[substr($r, 1)] = $this->Params[$i];
				}
			}

			return $vars;
		}
	}
?>