<?php 
namespace Omneo\Relay\Utils;
use Magento\Framework\App\ObjectManager;
class Request {
	protected $logger;
    public function __construct(\Psr\Log\LoggerInterface $logger)
    {
		$this->logger = $logger;
    }

	public function call(string $method = "POST", string $url, $body = null, array $customHeaders = []) 
	{
		try{
			$ch = curl_init();   
			curl_setopt($ch, CURLOPT_URL, $url);                                                                     
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method); 
			
			$headers = ['Content-Type: application/json'];
			$headers = array_merge($headers, $customHeaders);

			if($body){
				$payload = json_encode($body);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);  
				$headers[] = 'Content-Length: ' . strlen($payload);
			}
                                                               
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);			   

			$response = curl_exec($ch);
			$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			
			return [
				'status' => $status,
				'body' => json_decode($response, true)
			];
        }catch(\Exception $e){
			$this->logger->debug($e);
			return [
				'status' => 500,
				'body' => ''
			];
        }
	}

	public function post(string $url, $body = null, array $headers = [])
	{
		return $this->call('POST', $url, $body, $headers);
	}
}

