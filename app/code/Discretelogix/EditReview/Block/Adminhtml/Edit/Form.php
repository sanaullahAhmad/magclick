<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Adminhtml Review Edit Form
 */
namespace Discretelogix\EditReview\Block\Adminhtml\Edit;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{

//protected $_scopeConfig;

/**
 * Constructor
 */
// public function __construct(
//     \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
//     // ...any other injected classes the class depends on...
// ) {
//   $this->_scopeConfig = $scopeConfig;
//   // Remaining constructor logic...
// }

// protected $_scopeConfig;

//     public function __construct(
//     \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
//     ) {
//         $this->_scopeConfig = $scopeConfig;
//         parent::__construct();
//     }


    /**
     * Prepare edit review form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        //echo $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('dxeditreview/general/enable');
        echo "asdf";



        
        // \Magento\Framework\App\ConfigInterface
        
        // echo $this->_scopeConfig->getValue('dxeditreview/general/enable', \Magento\Framework\App\ConfigInterface);

        //echo $this->_objectManager->create('Discretelogix\EditReview\Helper\Data')->getConfig('dxeditreview/general/enable');

        //echo $this->_scopeConfig->getValue('dxeditreview/general/enable', \Magento\Framework\App\ConfigInterface);

        //\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,

		$review = $this->_coreRegistry->registry('review_data');
        $product = $this->_productFactory->create()->load($review->getEntityPkValue());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'review/*/save',
                        [
                            'id' => $this->getRequest()->getParam('id'),
                            'ret' => $this->_coreRegistry->registry('ret')
                        ]
                    ),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'review_details',
            ['legend' => __('Review Details'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'product_name',
            'note',
            [
                'label' => __('Product'),
                'text' => '<a href="' . $this->getUrl(
                    'catalog/product/edit',
                    ['id' => $product->getId()]
                ) . '" onclick="this.target=\'blank\'">' . $this->escapeHtml(
                    $product->getName()
                ) . '</a>'
            ]
        );

        try {
            $customer = $this->customerRepository->getById($review->getCustomerId());
            $customerText = __(
                '<a href="%1" onclick="this.target=\'blank\'">%2 %3</a> <a href="mailto:%4">(%4)</a>',
                $this->getUrl('customer/index/edit', ['id' => $customer->getId(), 'active_tab' => 'review']),
                $this->escapeHtml($customer->getFirstname()),
                $this->escapeHtml($customer->getLastname()),
                $this->escapeHtml($customer->getEmail())
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerText = ($review->getStoreId() == \Magento\Store\Model\Store::DEFAULT_STORE_ID)
                ? __('Administrator') : __('Guest');
        }

        $fieldset->addField('customer', 'note', ['label' => __('Author'), 'text' => $customerText]);

        $fieldset->addField(
            'summary-rating',
            'note',
            [
                'label' => __('Summary Rating'),
                'text' => $this->getLayout()->createBlock(
                    \Magento\Review\Block\Adminhtml\Rating\Summary::class
                )->toHtml()
            ]
        );

        $fieldset->addField(
            'detailed-rating',
            'note',
            [
                'label' => __('Detailed Rating'),
                'required' => true,
                'text' => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                    \Magento\Review\Block\Adminhtml\Rating\Detailed::class
                )->toHtml() . '</div>'
            ]
        );

        $fieldset->addField(
            'status_id',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
                'name' => 'status_id',
                'values' => $this->_reviewData->getReviewStatusesOptionArray()
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->hasSingleStore()) {
            $field = $fieldset->addField(
                'select_stores',
                'multiselect',
                [
                    'label' => __('Visibility'),
                    'required' => true,
                    'name' => 'stores[]',
                    'values' => $this->_systemStore->getStoreValuesForForm()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                \Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element::class
            );
            $field->setRenderer($renderer);
            $review->setSelectStores($review->getStores());
        } else {
            $fieldset->addField(
                'select_stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $review->setSelectStores($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'nickname',
            'text',
            ['label' => __('Nickname'), 'required' => true, 'name' => 'nickname']
        );
		
		$fieldset->addField(
            'created_at',
            'text',
            ['label' => __('Created At'), 'required' => true, 'name' => 'created_at']
        );

        $fieldset->addField(
            'title',
            'text',
            ['label' => __('Summary of Review'), 'required' => $this->getConfigdx('dxeditreview/general/enable'), 'name' => 'title']
        );

        $fieldset->addField(
            'detail',
            'textarea',
            ['label' => __('Review'), 'required' => true, 'name' => 'detail', 'style' => 'height:24em;']
        );

        $form->setUseContainer(true);
        $form->setValues($review->getData());

        // $fieldset->addField(
        //     'detail123',
        //     'textarea',
        //     [
        //         'label' => __('Review'),
        //         'required' => 0,
        //         'name' => 'detail123',
        //         'style' => 'height:24em;'
        //           // ,
        //           // 'value' => $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('dxeditreview/general/enable')
        //          // 'value' => $this->_scopeConfig->getValue('dxeditreview/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        //      ]
        // );

        $this->setForm($form);
        //return parent::_prepareForm();
       
        //return parent::_prepareForm();
    }

    private function getConfigdx($path) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('core_config_data'); //gives table name with prefix

        //Select Data from table
        $sql = "Select value FROM " . $tableName ." where path = '$path' ";
        //$result = $connection->fetchAll($sql); // gives associated array, table fields as 
        $result = $connection->fetchOne($sql);
        return $result;
    }
}
