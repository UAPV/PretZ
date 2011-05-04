<?php

/**
 * Ce widget sert à compléter un champs désignant une personne du ldap
 *
 *
 */

class uapvWidgetFormAppigPersAutocompleter extends uapvWidgetFormPropelAutocompleter
{
  protected function configure ($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    
    $this->setAttribute ('class', $this->getAttribute ('class').' uapv_ac_pers');

    // Modification des valeurs par défaut (l'utilisateur pourra toujours les changer)
    $this->setOption ('search_attr',         'Nom');
    $this->setOption ('returned_attr',       'Pers');
    $this->setOption ('suggest_mainValue',   'NomComplet'); 
    $this->setOption ('model',               'Personnel');
    $this->setOption ('url', sfContext::getInstance()->getController()->genUrl ('autocomplete/appigPers'));
  }

}
