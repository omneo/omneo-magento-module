<?php 
namespace Omneo\Relay\Utils;
use Magento\Framework\App\ObjectManager;
use Omneo\Relay\Utils\Request;
class ID {
	protected $logger;
	private $tenant;
	private $token;
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
        $om = ObjectManager::getInstance(); 
		$model = $om->get('Magento\Variable\Model\Variable');

		$this->tenant = $model->loadByCode('omneo_tenant')->getPlainValue();
		$this->token = $model->loadByCode('omneo_id_token')->getPlainValue();
		$this->logger = $logger;
    }

	function post(string $id) {
		try{
			if(!$this->tenant || !$this->token){
				throw new \Exception('Omneo ID URL or Token not configured');
			}

			$url = 'https://api.'.$this->tenant.'.getomneo.com/id/api/v1/auth/token';
			$headers = ['Authorization: Bearer '.$this->token];
			$payload = ['id' => $id, 'id_handle' => 'magento_id'];

			$request = new Request($this->logger);
			$response = $request->post($url, $payload, $headers);

			return $response;
        }catch(\Exception $e){
			$this->logger->debug($e);
			return null;
        }
	}
}

