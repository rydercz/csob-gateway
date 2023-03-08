<?php declare(strict_types = 1);

namespace SlevomatCsobGateway\Call\OneClick;

use SlevomatCsobGateway\Api\ApiClient;
use SlevomatCsobGateway\Call\ResponseExtensionHandler;
use SlevomatCsobGateway\Crypto\SignatureDataFormatter;

class EchoOneClickRequest
{

	/** @var ResponseExtensionHandler[] */
	private array $extensions = [];

	public function __construct(private string $merchantId, private string $origPayId)
	{
	}

	public function send(ApiClient $apiClient): EchoOneClickResponse
	{
		$requestData = [
			'merchantId' => $this->merchantId,
			'origPayId' => $this->origPayId,
		];

		$response = $apiClient->post(
			'oneclick/echo',
			$requestData,
			new SignatureDataFormatter([
				'merchantId' => null,
				'origPayId' => null,
				'dttm' => null,
			]),
			new SignatureDataFormatter(EchoOneClickResponse::encodeForSignature()),
			$this->extensions,
		);

		/** @var mixed[] $data */
		$data = $response->getData();
		$data['extensions'] = $response->getExtensions();

		return EchoOneClickResponse::createFromResponseData($data);
	}

	public function registerExtension(string $name, ResponseExtensionHandler $extensionHandler): void
	{
		$this->extensions[$name] = $extensionHandler;
	}

}
