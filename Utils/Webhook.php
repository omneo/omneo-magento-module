<?php 
namespace Omneo\Relay\Utils;
use Magento\Framework\App\ObjectManager;
use Omneo\Relay\Utils\Request;
class Webhook {
	protected $logger;
	private $url;
	private $secret;
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $om = ObjectManager::getInstance(); 
		$model = $om->get('Magento\Variable\Model\Variable');

		$this->url = $model->loadByCode('omneo_webhook_url')->getPlainValue();
		$this->secret = $model->loadByCode('omneo_webhook_secret')->getPlainValue();
		$this->logger = $logger;
    }

	function post(string $event = "unknown", array $payload = []) {
		try{
			if(!$this->url || !$this->secret){
				throw new \Exception('Omneo Webhook URL or Secret not configured');
			}

			$url = $this->url;
			$headers = [
				"X-Magento-Event: ".$event,
				"X-Magento-Signature: ".hash_hmac('sha256', json_encode($payload), $this->secret)
			];

			$request = new Request($this->logger);
			$response = $request->post($url, $payload, $headers);
			
			return $response;
        }catch(\Exception $e){
			$this->logger->debug($e);
			return null;
        }
	}
}

