<?php

/**
 * Ce widget sert à compléter un champs désignant un enseignement
 *
 */

class uapvWidgetFormAppigEnseignementAutocompleter extends uapvWidgetFormPropelAutocompleter
{
  protected function configure ($options = array(), $attributes = array())
  {
    $this->addRequiredOption('an');

    parent::configure($options, $attributes);

    $this->setAttribute ('class', $this->getAttribute ('class').' uapv_ac_enseignement');
    $this->setAttribute ('placeholder', 'Code de l\'enseignement');

    $this->setOption ('search_attr',         'Code'); // champ à tester
    $this->setOption ('returned_attr',       'Code'); // champ retourné par le formulaire
    $this->setOption ('suggest_mainValue',   'NomComplet'); // champ à afficher dans le input et dans les lignes de suggestion
    $this->setOption ('model',               'Enseignement');
    $this->setOption ('url', sfContext::getInstance()->getController()->genUrl ('autocomplete/appigEnseignement'));
    $this->setOption ('config', '{width: 500, max: 100}'); // Besoin de largeur pour afficher code + libellé
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

    return EnseignementQuery::create()->findByCode ($value, $this->getOption ('an'));
  }
}
