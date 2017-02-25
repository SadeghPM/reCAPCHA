<?php
namespace Sadeghpm\Recapcha;

class GoogleReCapcha {

	private $error_codes = [
		"missing-input-secret"   => "The secret parameter is missing.",
		"invalid-input-secret"   => "The secret parameter is invalid or malformed.",
		"missing-input-response" => "The response parameter is missing.",
		"invalid-input-response" => "The response parameter is invalid or malformed.",
	];

	public function __construct($secret) {
		$this->secret = $secret;
	}
	/**
	 * Google reCAPCHA middleware for slim
	 *
	 * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
	 * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
	 * @param  callable                                 $next     Next middleware
	 *
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function __invoke($request, $response, $next) {
		$this->post = $request->getParsedBody();
		if ($this->verify()) {
			$response = $next($request, $response);
			return $response;
		} else {
			return $response->withStatus(403)->withJson([
				'ok'          => false,
				'description' => $this->formatErrors(),
				'result'      => [],
			]);
		}
	}

	public function verify() {
		$this->googleResponse = $this->getGoogle();
		return $this->googleResponse['success'];
	}

	private function getGoogle() {
		$post = [
			'secret'   => $this->secret,
			'response' => empty($this->post['g-recaptcha-response']) ? "" : $this->post['g-recaptcha-response'],
		];

		$ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$response = curl_exec($ch);
		curl_close($ch);
		return json_decode($response, true);
	}

	private function formatErrors() {
		$stringError = '';
		foreach ($this->googleResponse['error-codes'] as $code) {
			$stringError .= $this->error_codes[$code] . "\n";
		}
		return $stringError;
	}
}
