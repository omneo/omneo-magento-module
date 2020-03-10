<?php
 
namespace Omneo\Relay\Observer\Customer;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Omneo\Relay\Utils\Request;
class Address implements ObserverInterface
{

    protected $logger;
    protected $om;
    protected $customerModel;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerModel
    )
    {
        $this->logger = $logger;
        $this->om = \Magento\Framework\App\ObjectManager::getInstance(); 
        $this->customerModel = $customerModel;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {

        $address = $observer->getCustomerAddress();
        $customerId = $address->getCustomerId();
        $customer = $this->customerModel->getById($address->getCustomerId());
    
        if(!$customer){
            return $this;
        }

        try{                                                                  
            $request = new Request($this->logger);
            $response = $request->post('profile.updated', $customer->__toArray());
        }catch(\Exception $e){
            $this->logger->debug($e);
        }

        return $this;
    }
}