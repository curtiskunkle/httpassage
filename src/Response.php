<?php 
namespace QuickRouter;

/**
 * Router response object
 */
class Response  {

	/**
	 * HTTP status code
	 * @var integer
	 */
	protected $code = 200;

	/**
	 * Most recent matched route name
	 * @var string
	 */
	protected $matchedRoute = "";

	/**
	 * Array of all matched routes (in the case of subrouters)
	 * @var array
	 */
	protected $matchedRoutes = [];

	/**
	 * Array of messages
	 * @var array
	 */
	protected $messages = [];

	/**
	 * Response data
	 * @var array
	 */
	public $data = [];

	/**
	 * Set the HTTP status code
	 */
	public function setCode($code) {
		$this->code = (int)$code;
		return $this;
	}

	/**
	 * Get the current HTTP status code
	 * @return int
	 */
	public function getCode() {return $this->code;}

	/**
	 * Get the most recent matched route
	 * @return string
	 */
	public function getMatchedRoute() {return $this->matchedRoute;}

	/**
	 * Get all matched routes
	 * There will be multiple matched routes in the case of subrouters
	 * @return array
	 */
	public function getAllMatchedRoutes() {return $this->matchedRoutes;}

	/**
	 * Return if this response matched a route
	 * @return bool
	 */
	public function hasMatch() {return !empty($this->matchedRoute);}

	/**
	 * Sets the current matched route and adds the route to the array of all matched routes
	 */
	public function setMatchedRoute($route) {
		$this->matchedRoutes[] = $route;
		$this->matchedRoute = $route;
		return $this;
	}

	/**
	 * Set a message on the response
	 * @param string $message 
	 * @param string $type    
	 */
	public function setMessage($message, $type = "error") {
		$this->messages[] = [
			"message" => $message,
			"type" => $type,
		];
	}

	/**
	 * Get the messages array
	 */
	public function getMessages() {return $this->messages;}

	/**
	 * Get the formatted response to send to the client
	 */
	public function getResponseData() {
		return  [
			"data" => $this->data,
			"messages" => $this->messages,
		];
	}

	/**
	 * Sets the HTTP status code and outputs the response in the provided format
	 * @param  string $format text or json
	 */
	public function respond($format = "json") {
		http_response_code($this->code);

		if ($format === "text") {
			header("Content-type: text/plain");
			echo "<pre>"; print_r($this->getResponseData()); echo "</pre>";
			die;
		}

		//default to json
		header("Content-type: application/json");
		echo json_encode($this->getResponseData(), JSON_UNESCAPED_UNICODE);
	}
}