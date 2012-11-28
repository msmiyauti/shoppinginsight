<?php

class RedFeet_Taxvat_Model_Form extends Mage_Customer_Model_Form
{
    
    
    /**
     * Validate data array and return true or array of errors
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data)
    {
        $errors = array();
        foreach ($this->getAttributes() as $attribute) {
            if ($this->_isAttributeOmitted($attribute)) {
                continue;
            }
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setExtractedData($data);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
            
            if ($attribute->getIsUnique()) {
//                die($attribute->getAttributeCode());
                $cid = $this->getEntity()->getData('entity_id'); //get current customer id
                $cli = Mage::getModel('customer/customer')
                ->getCollection()
                ->addAttributeToFilter($attribute->getAttributeCode(), $data[$attribute->getAttributeCode()]);
                //->addFieldToFilter('customer_id',   array('neq' => $cid)); //exclude current user from results  //###### not working......
                $flag=0;
                foreach ($cli as $customer) {
                     $dataid=$customer->getId();
                     if ($dataid != $cid) //if the value is from another customer_id
                        $flag |= 1;  //we found a dup value
                }
                
                if ($flag) {
                    $label = $attribute->getStoreLabel();
                    $errors = array_merge($errors, array( Mage::helper('customer')->__('"%s" already used!',$label)));
                }
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

}