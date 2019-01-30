<?php

class Shopware_Controllers_Backend_ShareBasket extends Shopware_Controllers_Backend_ExtJs
{
    public function getAttributesAction()
    {
        /** @var \Shopware\Bundle\AttributeBundle\Service\CrudService $crud */
        $crud = $this->container->get('shopware_attribute.crud_service');
        $table = $crud->getList('s_order_details_attributes');

        $data = [];
        foreach ($table as $fields) {
            $columnName = $fields->getColumnName();

            if ($columnName !== 'id' && $columnName !== 'detailID') {
                $data[] = [
                    'id' => $fields->getId(),
                    'name' => $columnName,
                ];
            }
        }

        $this->view->assign([
            'data' => $data,
            'total' => count($data),
        ]);
    }
}
