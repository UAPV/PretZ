<?php

/**
 * Permet d'afficher le widget d'autocompletion avec la ressource par defaut (ldpa) ( -- Depreciated -- )
 *
 * @param string $inputName
 * @param string $searchedField
 * @param string $returnedField
 * @param string $suggestMainValue
 * @param string $suggestSecondValue
 * @param string $class
 * @param string $size
 * @param string $placeHolder
 * @return string
 */
function uapvAutocompleteInput ($inputName, 
        $searchedField = "name",
        $returnedField = "uid",
        $suggestMainValue = "cn",
        $suggestSecondValue = "affectation",
        $class = "uapv_form_extra_input",
        $size = "20",
        $placeHolder = "Tapez un nom" )
{
    $w =  new uapvWidgetFormLdapAutocompleter (
            $options = array(
            'search_attr' =>$searchedField,
            'returned_attr' => $returnedField,
            'suggest_mainValue' => $suggestMainValue,
            'suggest_secondValue' => $suggestSecondValue ),
            $attributes = array(
            'class' => $class,
            'size' => $size,
            "placeholder" => $placeHolder)
            ) ;
    return $w->render($inputName);
}



/**
 * Permet d'afficher le widget d'autocompletion ldap
 *
 * @param string $inputName
 * @param string $searchedField
 * @param string $returnedField
 * @param string $suggestMainValue
 * @param string $suggestSecondValue
 * @param string $class
 * @param string $size
 * @param string $placeHolder
 * @param string $group voir uapvFormExtraUtils ou le README.mkd
 * @return string
 */
function uapvLdapAutocompleteInput($inputName,
        $searchedField = "name",
        $returnedField = "uid",
        $suggestMainValue = "cn",
        $suggestSecondValue = "affectation",
        $class = "uapv_form_extra_input uapv_ac_pers",
        $size = "20",
        $placeHolder = "Tapez un nom" ,
        $groupe = 'personnels')
{
    $w =  new uapvWidgetFormLdapAutocompleter (
            $options = array(
            'search_attr' =>$searchedField,
            'returned_attr' => $returnedField,
            'suggest_mainValue' => $suggestMainValue,
            'suggest_secondValue' => $suggestSecondValue,
            'groupe' => $groupe),
            $attributes = array(
            'class' => $class,
            'size' => $size,
            "placeholder" => $placeHolder)
            ) ;

    $response = sfContext::getInstance()->getResponse();
    foreach ($w->getJavascripts() as $file)
      $response->addJavascript($file, sfWebResponse::LAST);
    foreach ($w->getStylesheets() as $file => $null)
      $response->addStylesheet($file, sfWebResponse::FIRST);

    return $w->render($inputName);
}

function uapvAutocompleteEnseignement ($name, $value = null, $options = array(), $attributes = array())
{
  $widget = new uapvWidgetFormAppigEnseignementAutocompleter ($options, $attributes);

  $response = sfContext::getInstance()->getResponse();
  foreach ($widget->getJavascripts() as $file)
    $response->addJavascript($file, sfWebResponse::LAST);
  foreach ($widget->getStylesheets() as $file => $null)
    $response->addStylesheet($file, sfWebResponse::FIRST);

  return $widget->render  ($name, $value);
}
?>
