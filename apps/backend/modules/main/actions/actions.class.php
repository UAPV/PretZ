<?php

/**
 * main actions.
 *
 * @package    pretz
 * @subpackage main
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mainActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
  }

  /*
   * Clean up connection table.
   * Remove all connection in the table.
   */
  public function executeAjaxCache(sfWebRequest $request)
  {
        $connects = ConnectionQuery::create()
                ->find();

        // foreach connection to the database
        foreach($connects as $con)
        {
          //destroy his basket and put state of the product to '0'
          $basket = LendQuery::create()
            ->filterByIduser($con->getIdUser())
            ->findOne();

          if(count($basket) > 0)
          {
              $prodLend = LendProductQuery::create()
                    ->filterByIdLend($basket->getId())
                    ->find();

              foreach($prodLend as $prod)
              {
                  $product = ProductQuery::create()
                            ->filterById($prod->getIdproduct())
                            ->findOne();

                  if(count($product) > 0)
                  {
                    $product->setState('0');
                    $product->save();
                  }

               $prod->delete();
              }

              $basket->delete();
              }
              
           $con->delete();
        }

        $respons['error'] = false;

        return $this->returnJSON($respons);
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
}
