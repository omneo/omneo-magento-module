<?php
 
namespace Omneo\Relay\Observer\Order;
 
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

use Omneo\Relay\Utils\Request;
class Data implements ObserverInterface
{

    protected $logger;
    protected $om;
    protected $customerModel;

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
        $order = $observer->getEvent()->getData('order');

        try{
            $product = $observer->getOrder();
        }catch(\Exception $e){}


        if(!$order){
            return $this;
        }

        try{
            $payload = json_decode($order->toJson(), true);

            $payload['items'] = [];
            foreach ($order->getItems() as $i=>$item) {
                $payload['items'][] = json_decode($item->toJson(), true);
            }

            $payment = json_decode($order->getPayment()->toJson(), true);
            $payload['payment'] = $payment;

            $payload['addresses'] = [];
            foreach ($order->getAddresses() as $i=>$item) {
                $payload['addresses'][] = json_decode($item->toJson(), true);
            }

            $payload['extension_attributes'] = [];
            foreach ($order->getExtensionAttributes() as $i=>$item) {
                $payload['extension_attributes'][] = json_decode($item->toJson(), true);
            }

            $payload['status_histories'] = [];
            foreach ($order->getStatusHistories() as $i=>$item) {
                $payload['status_histories'][] = json_decode($item->toJson(), true);
            }          

            $request = new Request($this->logger);
            $response = $request->post('order.updated', $payload);
        }catch(\Exception $e){
            $this->logger->debug($e);
        }

        return $this;
    }
}