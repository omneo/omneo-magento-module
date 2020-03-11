<?php
 
namespace Omneo\Relay\Observer\Product;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Omneo\Relay\Utils\Webhook;
class Data implements ObserverInterface
{

    protected $logger;
    protected $om;

    public function __construct(
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->om = \Magento\Framework\App\ObjectManager::getInstance(); 
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getData('product');

        try{
            $product = $observer->getProduct();
        }catch(\Exception $e){}


        if(!$product){
            return $this;
        }

        try{                                                                 
            $request = new Webhook($this->logger);
            $response = $request->post('product.updated', $product->__toArray());
        }catch(\Exception $e){
            $this->logger->debug($e);
        }

        return $this;
    }
}