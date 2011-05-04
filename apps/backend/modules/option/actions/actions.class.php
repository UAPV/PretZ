<?php

require_once dirname(__FILE__).'/../lib/optionGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/optionGeneratorHelper.class.php';

/**
 * option actions.
 *
 * @package    pretz
 * @subpackage option
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class optionActions extends autoOptionActions
{
    public function executeIndex(sfWebRequest $request)
    {
    }

    /*
     * Check if the auto-mail option is activated.
     * If it's, option is disabled.
     */
    public function executeAjaxMail(sfWebRequest $request)
    {
        $option = new Option();

        $respons['active'] = OptionQuery::mailIsActive();

        //mail option is checked
        if(!OptionQuery::mailIsActive())
        {
            $option->setAutomail('1');
        }
        else
        {
            $option->setAutomail('0');
        }

        $option->save();
        $respons['error'] = false;

        return $this->returnJSON($respons);
    }

    /*
     * Add a new template for the software
     */
    public function executeAddTemplate(sfWebRequest $request)
    { 
        $fond = $request->getParameter("fond");
        $menu = $request->getParameter("menu");
        $nomS = $request->getParameter("nomS");
        $url = $request->getParameter("url");
        $nomT = $request->getParameter("nomT");
        $couleurMess = $request->getParameter("couleurMess");

        if($fond == "" || $menu == "" || $nomT == "")
        {
            $response = "Veuillez remplir tous les champs pour la couleur svp";
        }
        else
        {
            $exist = TemplateQuery::create()
                ->filterByNameTemplate($nomT)
                ->find();
 
            // no template with this name exists
            if(count($exist) == 0)
            {
                $template = new Template();
                $template->setBackColor($fond);
                $template->setMenuColor($menu);
                $template->setNameTemplate($nomT);
                $template->setNameStructure($nomS);
                $template->setColorMessage($couleurMess);

                 //we verify if the logo can be downloaded
                if(isset($_FILES['logoUpload']))
                {
                    $logo = $_FILES['logoUpload'];

                    $tmp = $logo['tmp_name'];
                    $directory = dirname(__FILE__).'/../lib';

                    list($largeur, $hauteur, $type, $attr) = getimagesize($tmp);
                    if($type==1 || $type==2 || $type==3 || $type==6)
                    {
                       if(is_dir($directory))
                       {
                           if(move_uploaded_file($tmp, $directory."/".$logo['name']))
                                $template->setLogo($logo['name']);
                           else
                                $template->setLogo($directory);
                       }
                       else
                            $template->setLogo("FAUX !");
                    }
                    $template->setUrl($url);
                    $template->save();
                }
                $response = "Le template a bien ete ajoute";
            }
            else
                $response = "Ce template existe deja avec ce nom";
        }

        return $this->redirect("option/index?error=".urlencode($response)."&nomT=".urlencode($nomT)."&nomS=".urlencode($nomS)."&fond=".urlencode($fond)."&menu=".urlencode($menu)."&url=".urlencode($url)."&mess=".urlencode($couleurMess));
    }


    public function returnJSON($data)
    {
        $json = json_encode($data);

        if (sfContext::getInstance()->getConfiguration()->isDebug () && !$this->getRequest()->isXmlHttpRequest())
        {
          $this->getContext()->getConfiguration()->loadHelpers('Partial');

          $json = get_partial('global/json', array('data' => $data));

        } else {
          $this->getResponse()->setHttpHeader('Content-type', 'application/json');

    }

    return $this->renderText($json);

  }

  /*
   * Add an ip address to access to PretZ
   */
  public function executeAjaxAddIp(sfWebRequest $request)
  {
      $address = $request->getParameter('ip');

      $exist = OptionIpQuery::create()
        ->filterByAddress($address)
        ->find();

      // we check if this ip doesn't exist
      if(count($exist) == 0)
      {
          $ip = new OptionIp();
          $ip->setAddress($address);
          $ip->save();

          $respons['error'] = false;
      }
      else
          $respons['error'] = true;
      
      return $this->returnJSON($respons);
  }

  /*
   * Change template fot this one
   */
  public function executeAjaxChangeTemplate(sfWebRequest $request)
  {
      $template = $request->getParameter('template');

      $templateNew = TemplateQuery::create()
                    ->filterById($template)
                    ->findOne();
      
      $templateOld = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne();

      // if these two templates are different
      if($templateNew->getId() != $templateOld->getId())
      {
          if(count($templateOld) > 0)
          {
              $templateNew->setActive('1');
              $templateNew->save();

              $templateOld->setActive('0');
              $templateOld->save();

              $respons['error'] = false;
          }
      }
      else
          $respons['error'] = true;
      
      return $this->returnJSON($respons);
  }
}
