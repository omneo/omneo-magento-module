<?php
namespace Omneo\Relay\Controller\Product;

class Detail extends \Magento\Framework\App\Action\Action 
{
    protected $request;
    protected $om;
    
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->request = $request;  
        $this->om = \Magento\Framework\App\ObjectManager::getInstance(); 
        parent::__construct($context);
    }
    
    private function getProductBySku($sku)
	{
        $model = $this->om->get('\Magento\Catalog\Model\ProductRepository');
		return $model->get($sku);
    }

    public function execute() 
    {
        $product = null;
        try{
            $sku = $this->request->getParam('sku');
            $product = $this->getProductBySku($sku);

            if(!$product){
                throw new Exception('No product found for SKU: '.$sku);
            }

            return $this->_redirect($product->getProductUrl());
        }catch(\Exception $e){
            // Consider adding a fallback
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }
}