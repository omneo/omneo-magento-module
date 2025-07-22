<?php

namespace Omneo\Relay\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omneo\Relay\Utils\Webhook;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\Data\AddressInterface;

class AddressDelete implements ObserverInterface
{
    protected $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->logger->debug('Omneo Relay: AddressDelete Observer constructor called for AFTER event.');
    }

    public function execute(Observer $observer)
    {
        $event = $observer->getEvent();
        if (!$event) {
            $this->logger->warning('Omneo Relay: Event object is null, cannot retrieve data for AFTER event.');
            return $this; // Cannot proceed without event data
        }

        /** @var AddressInterface|null $address */
        $address = null;

        if (is_object($event->getData('data_object')) && method_exists($event->getData('data_object'), 'getId')) {
            $address = $event->getData('data_object');
        } elseif (is_object($observer->getData('customer_address')) && method_exists($observer->getData('customer_address'), 'getId')) {
            $address = $observer->getData('customer_address');
        }

        if (!$address || !($address instanceof AddressInterface) && !method_exists($address, 'getId')) {
            $this->logger->warning('Omneo Relay: Final attempt failed. Could not retrieve a valid address object from customer_address_delete_before event. Type received: ' . (is_object($address) ? get_class($address) : gettype($address)));
            return $this;
        }

        try {
            $request = new Webhook($this->logger);
            $addressData = $address->toArray(); // Convert the address object to an array
            $response = $request->post('address.deleted', $addressData);

        } catch (\Exception $e) {
            $this->logger->error('Omneo Relay: Error firing webhook for address deletion in AFTER event: ' . $e->getMessage());
            $this->logger->debug($e);
        }

        $this->logger->debug('Omneo Relay: AddressDelete Observer execute method finished for AFTER event.');
        return $this;
    }
}
