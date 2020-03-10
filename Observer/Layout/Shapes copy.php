<?php
 
namespace Omneo\Relay\Observer\Layout;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class Shapes implements ObserverInterface
{
    protected $customerSession;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ){
        $this->customerSession = $customerSession;
    }

    public function execute(Observer $observer)
    {
        $session = $this->customerSession;
        if (!$session->isLoggedIn()) {
            return $this;
        }

        

        echo $session->getCustomerId();
            // $customerSession->getCustomer();
            // $customerSession->getCustomerData();
        
        echo $session->getCustomer()->getName();  // get  Full Name
        echo $session->getCustomer()->getEmail(); // get Email

        die();
        // $customerSession->setMyValue('test');
        // $customerSession->getMyValue();


        // $layout = $observer->getLayout();
        // $layout->getUpdate()->addHandle('catalog_product_view_customlayout'); 

        return $this;
    }   
}