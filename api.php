<?php

abstract class Api
{

    protected $method = ''; //POST

    public $requestUri = [];
    public $requestParams = [];

    protected $action = '';

    public function __construct() 
	{
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        $this->requestUri = explode('/', trim($_SERVER['REQUEST_URI'],'/'));
        $this->requestParams = $_REQUEST;
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    public function run()
	{
        if(array_shift($this->requestUri) !== 'api'){
            throw new RuntimeException('API Not Found', 404);
        }
        $this->action = $this->getAction();
        if (method_exists($this, $this->action)) {
            return $this->{$this->action}();
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    protected function response($data, $status = 500) 
	{
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }

    protected function getAction()
    {  
		$request = explode('/', $_SERVER['REQUEST_URI']);	
        $method = $this->method;
        switch ($method) {
            case 'POST':
				if($request[2] == 'table'){
					return 'tableAction';
				} elseif ($request[2] == 'sessionSubscribe') {
					return 'sessionSubscribeAction';
				}
                break;
            default:
                return null;
        }
    }
}