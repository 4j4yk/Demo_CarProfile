<?php declare(strict_types=1);

namespace Demo\CarProfile\Controller\Index;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Save extends Action implements HttpPostActionInterface
{

    public const CUSTOMER_CAR = 'customer_car';
    public function __construct(
        protected Context $context,
        protected Session $customerSession,
        protected Validator $formKeyValidator,
        protected StoreManagerInterface $storeManager,
        protected CustomerRepository $customerRepository,
    ) {
        parent::__construct($context, $customerSession);
    }

    public function execute(): ResultInterface|ResponseInterface
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->_redirect('customer/account/');
        }

        $customerId = $this->customerSession->getCustomerId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your car.'));
        } else {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $storeId = (int)$this->storeManager->getStore()->getId();
                $customer->setStoreId($storeId);
                $customAttributeValue = $this->getRequest()->getParam('customer_car', null);
                $customer->setCustomAttribute('customer_car', $customAttributeValue);
                $this->customerRepository->save($customer);
                $this->messageManager->addSuccessMessage(__('Your car has been saved.'));
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving your car.'));
            }
        }
        return $this->_redirect('customer/account/');
    }
}
