<?php

/**
 * Lend form.
 *
 * @package    pretz
 * @subpackage form
 * @author     Your name here
 */
class LendForm extends BaseLendForm
{
  public function configure()
  {

    /* unset of some fields of the table Lend */
    unset(
            $this['idProduct'],
            $this['idUser'],
            $this['idAdmin'],
            $this['created_at'],
            $this['updated_at']
          );

    $this->setWidget('product',new sfWidgetFormInputText());

    // Validation for barcode
    $this->validatorSchema['product'] = new sfValidatorString(array('required' => true, 'min_length' => 6, 'max_length' => 6));

   /* $this->widgetSchema['product']->setOption('renderer_class', 'sfWidgetFormPropelJQueryAutocompleter');
    $this->widgetSchema['product']->setOption('renderer_options', array(
      'model' => 'Product',
      'url'   => $this->getOption('url'),
));*/

  }
}
