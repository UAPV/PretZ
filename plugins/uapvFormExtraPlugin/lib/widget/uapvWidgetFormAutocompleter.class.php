<?php

/**
 * TODO: doc
 *
 */

class uapvWidgetFormAutocompleter extends sfWidgetFormJQueryAutocompleter
{
    protected function configure($options = array(), $attributes = array())
    {
        $this->addRequiredOption('url');
        $this->addRequiredOption('search_attr');     // champ à tester
        $this->addOption('returned_attr', 'uid');    // champ retourné par le formulaire
        $this->addOption('suggest_mainValue', 'cn'); // champ à afficher dans le input et dans les lignes de suggestion
        $this->addOption('suggest_secondValue');     // champ affiché dans la deuxieme ligne de la suggestion
        $this->addOption('groupe');     // groupePersonn ldap, pour la recherche LDAP  voir uapvFormExtraUtils

        parent::configure($options, $attributes) ;
    }

    public function render($name, $value = null, $attributes = array(), $errors = array())
    {
        $visibleValue = $this->getOption('value_callback') ? call_user_func($this->getOption('value_callback'), $value) : $value;

        // On recupere les options du widget
        $suggest_mainValue =  $this->getOption('suggest_mainValue');
        $suggest_secondValue =  $this->getOption('suggest_secondValue');    
        $autocompleteId = $this->generateId('autocomplete_'.$name);
        $url = $this->getOption('url'); 
        $config = $this->getOption('config');
        $search_attr = $this->getOption('search_attr');
        $returned_attr = $this->getOption('returned_attr');
        $groupe = $this->getOption('groupe');
        $inputId = $this->generateId($name);

        // Si config est un array on le transforme en json
        if (is_array ($config))
          $config = json_encode ($config);

        // On definie ce qui va être affiché dans la suggestion (construction du code en javascript)
        $suggestedItem_js= 'data[key].'.$suggest_mainValue;
        if (! empty ($suggest_secondValue)) // si une variable a été définie on l'affiche (liste des variables devant avoir été définie dans app.yml)
            $suggestedItem_js = "'<div class=\"autocompleteMainValue_div\">'+
            data[key].$suggest_mainValue +'</div>'+
            '<div id=\"autocomplete_user_$suggest_secondValue\"  class=\"autocomplete_user_$suggest_secondValue autocomplete_div\">'+data[key].$suggest_secondValue+'</div>'";


        $this->setAttribute ('class', $this->getAttribute ('class').' uapv_form_extra_input');

        return $this->renderTag('input', array('type' => 'hidden', 'name' => $name, 'value' => $value)).
        sfWidgetFormInput::render('autocomplete_'.$name, $visibleValue, array_merge($this->attributes, $attributes), $errors).
        <<<HEREDOC
<script type="text/javascript">

  jQuery(document).ready(function() {
    jQuery("#$autocompleteId")
    .autocomplete('$url', jQuery.extend({}, {
        dataType: 'json',
        autoFill: false,
        width: 200,
        scrollHeight: 400,
        parse:    function(data) {
          var parsed = [];
          for (key in data) {
            parsed[parsed.length] = {
              'data' :  [$suggestedItem_js, data[key].$returned_attr], //propositions et valeur du hidden
              value: key,                             
              result: data[key].$suggest_mainValue // valeur du champ
            };
          }
          return parsed;
        }
      }, $config, {extraParams : { attr : "$search_attr", returned_attr: "$returned_attr", groupe: "$groupe" }      // !! Paramatres supplémentaire ajoutés a q et limit et timestamp et envoyés a l'action
    }))
    .result(function(event, data) {
      jQuery("#$inputId").val(data[1]); }) // remplit le hidden
    .change(function() {
      if (jQuery(this).val() == '') // Si autocomplete vide on remet le hidden à zéro
        jQuery("#$inputId").val('');
    });
  });
</script>
HEREDOC;
    }

    public function getStylesheets()
    {
        return array('/uapvFormExtraPlugin/css/JqueryAutocomplete.css' => 'all') ;
    }

    public function getJavaScripts()
    {
        return array('/uapvFormExtraPlugin/js/jquery.autocomplete.min.js') ;
    }
}
