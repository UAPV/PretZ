<?php

/**
 * Ce widget sert à compléter un champs désignant une personne du ldap
 *
 *
 */

class uapvWidgetFormLdapAutocompleter extends uapvWidgetFormAutocompleter
{
  protected function configure ($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->setAttribute ('class', $this->getAttribute ('class').' uapv_ac_pers');
      
    // Modification des valeurs par défaut (l'utilisateur pourra toujours les changer)
    $this->setOption('value_callback', array ($this, 'toString'));
    $this->setOption('search_attr', 'cn');
    $this->setOption('returned_attr', 'uid');
    
    $this->setOption('groupe', 'personnels');
    $this->setOption('url', sfContext::getInstance()->getController()->genUrl ('autocomplete/ldap'));   // url de l'action à exécuter
  }

  /**
   * Returns the text representation of a foreign key.
   *
   * @param string $value The primary key
   */
  protected function toString ($value)
  {
    if ($value === null || $value == '')
      return '';

    $user = uapvProfileFactory::find ($value);
    return ($user === null ?
       $value : $user->get ($this->getOption('suggest_mainValue'), $value));
  }
}
