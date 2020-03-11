<?php
 
namespace Omneo\Relay\Observer\Layout;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omneo\Relay\Utils\ID;
class Shapes implements ObserverInterface
{
    protected $customerSession;
    protected $logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Session $customerSession
    ){
        $this->customerSession = $customerSession;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $session = $this->customerSession;

        if (!$session->isLoggedIn()) {
            return $this;
        }

        // Check if ID token already exists and is valid
        $token = $session->getIdToken();
        $expiry = $session->getIdExpiry();

        // $this->logger->debug("ID DATA ".$token.' - '.$expiry.' - '.time());

        // Bail if token request errored and within retry period
        if($token == 'error' && $expiry < time()){
            return $this;
        }

        if(!$token || $expiry < time()){
            try{
                $id = $session->getCustomerId();
                $this->logger->debug('Request Omneo ID Customer Token: '.$id);
                
                $request = new ID($this->logger);
                $response = $request->post($id);
                if(!$response){throw new \Exception('Unknown Omneo ID Request Error');}

                if($response['status'] < 400){
                    $token = $response['body']['data']['token'];
                    $session->setIdToken($token);

                    $expiry = $response['body']['data']['exp'];
                    $session->setIdExpiry($expiry); 
                }else{
                    if($response['status'] == 404){
                        // Profile not in Omneo or doesn't match
                        // Consider adding additional checks here on email etc.
                        // Could land here for new signups
                    }

                    throw new \Exception("Omneo ID Error ".$response['status'].' for customer '.$id);
                }
            }catch(\Exception $e){
                // Don't retry token fetch for 2 mins
                $session->setIdExpiry(time() + 120);
                $session->setIdToken('error');
                $this->logger->debug($e);
                return $this;
            }
        }
        

        $layout = $observer->getLayout();
        $layout->getUpdate()->addHandle('shapes'); 

        return $this;
    }   
}