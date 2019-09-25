<?php
	require_once "{$_SERVER["DOCUMENT_ROOT"]}/lib/Request.php";

	abstract class Router {
		public static $Server;
		public static $LastRequest;

		public static function SetServer($server) {
			Router::$Server = $server;
		}

		public static function GetRequestURI() {
			return Router::$Server["REQUEST_URI"];
		}

		public static function GetVerb() {
			return Router::$Server["REQUEST_METHOD"];
		}

		public static function GetParams() {
			return explode("/", substr(Router::$Server["PATH_INFO"], 1));	// Removes the preceding "/" via substr()
		}

		public static function GetQuery() {
			if(isset(Router::$Server["QUERY_STRING"])) {
				$temps = explode("&", Router::$Server["QUERY_STRING"]);
				$queries = [];
				foreach($temps as $temp) {
					$t = explode("=", $temp);
					$queries[$t[0]] = $t[1];
				}

				return $queries;
			}

			return [];
		}

		public static function CheckRoute($request, $route) {
			if($route[0] === "/") {
				$route = substr($route, 1);
			}
			$route = explode("/", $route);

			if(sizeof($route) !== sizeof($request->Params)) {
				return false;
			}

			foreach($route as $i => $r) {
				if(substr($r, 0, 1) === ":") {
					// NOOP
				} else {
					if($request->Params[$i] !== $r) {
						return false;
					}
				}
			}

			return true;
		}
		
		public static function GrabRequest($route) {
			return Router::$LastRequest;
		}

		public static function Route($verbs, $route, $callback = NULL) {
			Router::$LastRequest = new Request($route, Router::GetRequestURI(), Router::GetVerb(), Router::GetParams(), Router::GetQuery());
			if(Router::CheckRoute(Router::$LastRequest, $route) && is_callable($callback)) {
				$callback(Router::$LastRequest);
			}
		}

		public static function Get($route, $callback) {
			Router::Route("GET", $route, $callback);
		}
		public static function QuickGet($route, $URI, $preload = NULL) {
			Router::$LastRequest = new Request($route, Router::GetRequestURI(), Router::GetVerb(), Router::GetParams(), Router::GetQuery());
			$ViewBag = NULL;
			$Request = Router::$LastRequest;
			if(Router::CheckRoute(Router::$LastRequest, $route) && is_callable($preload)) {
				$ViewBag = $preload(Router::$LastRequest);
			}
			Router::Route("GET", $route, (function() use ($URI, $Request, $ViewBag) {
				require_once "{$_SERVER["DOCUMENT_ROOT"]}/routes/{$URI}.php";
			}));
		}

		public static function SimpleRoute($paths, $invoke) {
			$Route = new Route(Router::GetVerb(), Router::GetParams(), Router::GetQuery());
			
			$queries = [];

			if($paths[0] === "/") {
				$paths = substr($paths, 1);
			}
			$paths = explode("/", $paths);

			if(sizeof($path) !== sizeof($GetParams)) {
				return false;
			}
			
			foreach($paths as $i => $path) {
				if(substr($path, 0, 1) === ":") {
					// Query Variable
					$queries[substr($path, 1)] = $GetParams[$i];
				}
			}

			// echo "<pre>";
			// print_r($route);
			// echo "<br />";
			// print_r(Router::GetVerb());
			// echo "<br />";
			// print_r(Router::GetParams());
			// echo "<br />";
			// print_r(Router::GetQuery());
			// echo "</pre>";
		}
	}
?>