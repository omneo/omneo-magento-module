<?php 
namespace Omneo\Relay\Utils;
use Magento\Framework\App\ObjectManager;
class Request {
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

	function post(string $event = "unknown", array $data = []) {
		try{
			if(!$this->url || !$this->secret){
				throw new \Exception('Omneo Webhook URL or Secret not configured');
			}
	
			$payload = json_encode($data);
			$ch = curl_init();   
			curl_setopt($ch, CURLOPT_URL, $this->url);                                                                     
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);                                                                  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
				'Content-Type: application/json',                                                                                
				'Content-Length: ' . strlen($payload),
				"X-Magento-Event: ".$event,
				"X-Magento-Signature: ".hash_hmac('sha256', $payload, $this->secret)                                                           
			));       
			
			return curl_exec($ch);
        }catch(\Exception $e){
            $this->logger->debug($e);
        }
	}
}

