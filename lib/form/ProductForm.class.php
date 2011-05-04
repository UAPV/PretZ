<?php

/**
 * Product form.
 *
 * @package    pretz
 * @subpackage form
 * @author     Your name here
 */
class ProductForm extends BaseProductForm
{
  public function configure()
  {
      
      /* fields use in the form */

       $this->setWidget('product_categ_list', new sfWidgetFormPropelChoice(array(
                      'model' => 'Category',
                      'order_by' => array('Name','asc'),
                      'multiple' => true,
                      'expanded' => true)
                    ));

      if($this->getObject()->getBarcode() == null)
      {
          $test = ProductQuery::getLastId();
          $this->setWidget('barcode',new sfWidgetFormInputHidden(array(),array(
                          'value' => $test
                )));
      }
      else
          $this->setWidget('barcode',new sfWidgetFormInputHidden(array(),array()));

      $this->setWidget('name',new sfWidgetFormInput());

      $this->setWidget('description', new sfWidgetFormTextarea());
      $this->setWidget('comments', new sfWidgetFormTextarea());

      $this->setWidget('state', new sfWidgetFormInputHidden());
      $this->setWidget('retourPrevu', new sfWidgetFormInputHidden());
      
      // validation of the string content and error messages
      $this->setValidator('name',new sfValidatorString(
                 array('min_length' => 2,'required' => true),
                 array('required' => 'le champ nom est obligatoire',
                       'min_length' => 'le nom doit saisir au moins 3 caractères')
                 ));
      
      $this->setValidator('description', new sfValidatorString(
                 array('max_length' => 1000, 'required' => false),
                 array('max_length' => 'La description ne doit pas dépasser 1000 caractères')
                 ));
      
      $this->setValidator('comments', new sfValidatorString(
                 array('max_length' => 1000, 'required' => false),
                 array('max_length' => 'Le commentaire ne doit pas dépasser 1000 caractères')
                 ));

      $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);
  }
}
