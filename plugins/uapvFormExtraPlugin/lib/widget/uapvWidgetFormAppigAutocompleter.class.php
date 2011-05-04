<?php

/**
 * Ce widget sert à compléter un champs venant d'une base propel
 *
 * Code issue de sfWidgetFormPropelJQueryAutocompleter
 */

class uapvWidgetFormPropelAutocompleter extends uapvWidgetFormAutocompleter
{
  /**
   * @see sfWidget
   */
  public function __construct($options = array(), $attributes = array())
  {
    $options['value_callback'] = array($this, 'toString');

    parent::__construct($options, $attributes);
  }

  /**
   * Configures the current widget.
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormJQueryAutocompleter
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('method', '__toString');

    parent::configure($options, $attributes);
  }

  /**
   * Returns the text representation of a foreign key.
   *
   * @param string $value The primary key
   */
  protected function toString($value)
  {
    if ($value === null)
      return '';

    $class = constant($this->getOption('model').'::PEER');

    $criteria = new Criteria();
    $object = call_user_func(array($class, 'retrieveByPK'), $value);

    $method = $this->getOption('method');

    if (!method_exists($this->getOption('model'), $method))
    {
      throw new RuntimeException(sprintf('Class "%s" must implement a "%s" method to be rendered in a "%s" widget', $this->getOption('model'), $method, __CLASS__));
    }

    return !is_null($object) ? $object->$method() : '';
  }
}

