<?php
namespace Omneo\Relay\View\Model;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Session\SessionManagerInterface;

class Shapes implements \Magento\Framework\View\Element\Block\ArgumentInterface{
    protected $httpContext;
    protected $session;

    public function __construct(Context $httpContext, \Magento\Customer\Model\Session $session)
    {
        $this->httpContext = $httpContext;
        $this->session = $session;
    }

    public function getIdData()
    {
        return [
            'token' => $this->session->getIdToken(),
            'expiry' => $this->session->getIdExpiry()
        ];
    }
}