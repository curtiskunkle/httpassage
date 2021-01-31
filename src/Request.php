<?php 
namespace QuickRouter;

/**
 * Object representing an HTTP request
 */
class Request  {
	public $method = "";
	public $contentType = "";
	public $isCLI = false;
	public $routeParams = [];
	public $queryParams = [];
	public $rawInput = "";
	public $body = [];
	public $session = [];
	public $cookie = [];
	public $files = [];

	public function __construct() {
		$this->isCLI = PHP_SAPI === "cli";
		$this->method = !empty($_SERVER["REQUEST_METHOD"]) ? $_SERVER["REQUEST_METHOD"] : "";
		$this->queryParams = $_GET;
		$this->rawInput = file_get_contents("php://input");
		$this->contentType = !empty($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : "";
		//@todo remove when urlencoded body parse middleware done
		$this->body = $_POST;

		$this->session = isset($_SESSION) ? $_SESSION : [];
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
	}

	public function getContentType() {
		return strtolower(trim(explode(";", $this->contentType)[0]));
	}
}