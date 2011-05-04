<?php

/**
 * Product filter form.
 *
 * @package    pretz
 * @subpackage filter
 * @author     Your name here
 */
class ProductFormFilter extends BaseProductFormFilter
{
  public function configure()
  {
      $this->widgetSchema['created_at'] = new sfWidgetFormDate(array(
            'format' => '%day% / %month% / %year%',
          ));
  }
}
