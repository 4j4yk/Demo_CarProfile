<?php
namespace Demo\CarProfile\Block;

use Demo\CarProfile\Api\CarsApi;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;
class CarForm extends Template

{
    /**
     * @var string
     */
    protected $_template = 'Demo_CarProfile::cars.phtml';


    public function __construct(
        CarsApi $carsApi,
        Context $context,
        Session $customerSession,
        SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        array $data = []
    ) {
        $this->carsApi = $carsApi;
        $this->customerSession = $customerSession;
        $this->subscriberFactory = $subscriberFactory;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        parent::__construct($context, $data);
    }

    public function getCarsList()
    {
        return $this->carsApi->getList()['cars'] ?: [];
    }

    public function getToken()
    {
        return $this->carsApi->getList()['your-token'] ?? '';
    }

    /**
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSavedCar()
    {
        $customerId = $this->customerSession->getCustomerId();
        $car = $this->customerRepository->getById($customerId)->getCustomAttribute('customer_car');
        if($car->getValue()) {
        return $this->carsApi->getById($car->getValue(), $this->getToken()) ?? '';
        }
    }
    /**
     * Return the save action Url.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl('mycar/index/save');
    }
}
