<?php

class PipelineDealsClient {
	private $api_key;
	private $last_response_headers;

	public function __construct($api_key) {
		$this->api_key = $api_key;
	}


	public function call($method, $path, $params=array())
	{
		$url = "https://api-v2.pipelinedeals.com/$path?api_key={$this->api_key}";
		$payload = in_array($method, array('POST','PUT')) ? stripslashes(json_encode($params)) : array();
		$request_headers = in_array($method, array('POST','PUT')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();

		$response = $this->curlHttpApiRequest($method, $url, $payload, $request_headers);
		$response = json_decode($response, true);

		if (isset($response['errors']) or ($this->last_response_headers['http_status_code'] >= 400))
			throw new ClientApiException($method, $path, $params, $this->last_response_headers, $response);

		return (is_array($response) and (count($response) > 0)) ? array_shift($response) : $response;
	}

	private function curlHttpApiRequest($method, $url, $payload='', $request_headers=array())
	{
		$ch = curl_init($url);
		$this->curlSetopts($ch, $method, $payload, $request_headers);
		$response = curl_exec($ch);
		$errno = curl_errno($ch);
		$error = curl_error($ch);
		curl_close($ch);

		if ($errno) throw new ClientCurlException($error, $errno);

		list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
		$this->last_response_headers = $this->curlParseHeaders($message_headers);

		return $message_body;
	}

	private function curlSetopts($ch, $method, $payload, $request_headers)
	{
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'HAC');
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);

		if ('GET' == $method)
		{
			curl_setopt($ch, CURLOPT_HTTPGET, true);
		}
		else
		{
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
			if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
			if (!empty($payload))
			{
				if (is_array($payload)) $payload = http_build_query($payload);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $payload);
			}
		}
	}

	private function curlParseHeaders($message_headers)
	{
		$header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
		$headers = array();
		list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
		foreach ($header_lines as $header_line)
		{
			list($name, $value) = explode(':', $header_line, 2);
			$name = strtolower($name);
			$headers[$name] = trim($value);
		}

		return $headers;
	}
}

class ClientCurlException extends Exception { }
class ClientApiException extends Exception
{
	protected $method;
	protected $path;
	protected $params;
	protected $response_headers;
	protected $response;

	function __construct($method, $path, $params, $response_headers, $response)
	{
		$this->method = $method;
		$this->path = $path;
		$this->params = $params;
		$this->response_headers = $response_headers;
		$this->response = $response;

		parent::__construct($response_headers['http_status_message'], $response_headers['http_status_code']);
	}

	function getMethod() { return $this->method; }
	function getPath() { return $this->path; }
	function getParams() { return $this->params; }
	function getResponseHeaders() { return $this->response_headers; }
	function getResponse() { return $this->response; }
}
