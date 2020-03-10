<?php
namespace Omneo\Relay\Controller\Product;

class Image extends \Magento\Framework\App\Action\Action 
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
    

    private function getProductImage($product){
        $size = $this->request->getParam('size');
        if(!$size || $size == 1){
            $size = 400;
        }

        $helper = $this->om->get('\Magento\Catalog\Helper\Image');
        $image = $helper->init($product, 'product_page_main')
            ->setImageFile($product->getImage()) 
            ->keepFrame(false)
            ->resize($size);

        return $image;
    }

    private function getImagePath($image){
        $imageUrl = $image->getUrl();
        $imageArray = explode('pub', $imageUrl)[1];
        $directory = $this->om->get('\Magento\Framework\Filesystem\DirectoryList');
        $imagePath = $directory->getpath('pub').explode('pub', $imageUrl)[1];

        return $imagePath;
    }


    public function execute() 
    {
        $product = null;
        try{
            $sku = $this->request->getParam('sku');
            $product = $this->getProductBySku($sku);

            $image = $this->getProductImage($product);
            $imagePath = $this->getImagePath($image);
            $imageFile = file_get_contents($imagePath);

            $imageType = 'image/'.pathinfo($imagePath)['extension'];
            
            $rawResponse = $this->om->get('\Magento\Framework\Controller\Result\RawFactory');
            $response = $rawResponse->create();

            $response->setHeader('Content-Type', $imageType);
            $response->setHeader('Content-Length', filesize($imagePath));
            $response->setContents($imageFile);

            return $response;
        }catch(\Exception $e){
            // Consider adding a fallback
            $this->getResponse()->setStatusCode(404);
            return;
        }
    }
}