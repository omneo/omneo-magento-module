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

        // print_r($order->toArray());

        // var_dump($order->getData());

        $formattedOrder = json_decode($order->toJson());
        $items = $order->getItems();
        $formattedItems = [];

        foreach ($items as $i=>$item) {
            print_r(get_class_methods($item));
        }
        // print_r($formattedOrder);
        // var_dump($order->getItems());
        // print_r(get_class_methods($order->getItems()));
        die();

        
        // $this->logger->debug(implode($order->toArray(), ', '));
        // $this->logger->debug($order->getData());

        die();

        try{
            $data = array();                                                                    
            $request = new Request($this->logger);
            $response = $request->post('order.updated', $order->__toArray());
        }catch(\Exception $e){
            $this->logger->debug($e);
        }

        return $this;
    }
}