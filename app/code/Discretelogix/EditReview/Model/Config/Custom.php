<?php
namespace Discretelogix\EditReview\Model\Config;

class Custom implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Optional')],
            ['value' => 1, 'label' => __('Required')]
        ];
    }
}