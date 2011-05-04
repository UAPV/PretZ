<?php

require_once dirname(__FILE__).'/../lib/productGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/productGeneratorHelper.class.php';

/**
 * product actions.
 *
 * @package    pretz
 * @subpackage product
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12474 2008-10-31 10:41:27Z fabien $
 */
class productActions extends autoProductActions
{
    protected function processForm(sfWebRequest $request, sfForm $form)
    {
        parent::processForm($request,$form);

        $form->getObject()->setBarcode('product');
        $form->save();
    }

    /*
     * Allow to print barcode of one product
     */
    public function executeMonPDF(sfWebRequest $request)
    {
        $id = $request->getParameter('id');

        // on définit le répertoire des polices
        define('FPDF_FONTPATH',dirname(__FILE__).'/../../../../../lib/tools/fpdf16/font/');

        // on charge la classe PDF
        $this->pdf = new PDF_Code39();

        // on ajoute une page au document
        $this->pdf->AddPage();

        // j'écris un texte en arial 11 gras sur fond blanc
        $barcode = ProductQuery::getBarcode($id);
        
        $this->pdf->Code39(90, 15, $barcode);

        // on exporte le fichier
        $this->pdf->Output('Code-barres_prod'.$id.'.pdf','D');

        // on définit la classe du div contenant le PDF
        $this->classe = "pdf";

        // on appelle le template pdfSuccess.php
        $this->setTemplate('pdf');

    }

    /*
     * Allow to print barcode of all products
     */
    public function executeAllPDF(sfWebRequest $request)
    {
        // on définit le répertoire des polices
        define('FPDF_FONTPATH',dirname(__FILE__).'/../../../../../lib/tools/fpdf16/font/');

        // on charge la classe PDF
        $this->pdf = new PDF_Code39();

        $max = 25;
        $height = 15;
        $width = 15;
        $cpt = 1;
        $products = ProductQuery::create()->find();

        $this->pdf->AddPage();

        //tant qu'on a des produits dans la base, on continue d'imprimer leur code-barre
        foreach($products as $product)
        {
            //si on atteint le maximum de produits sur la page, on en crée une nouvelle
            if($cpt%$max == 0)
            {
                // on ajoute une page au document
                $this->pdf->AddPage();
                $cpt = 1;
                $height = 15;
                $width = 15;
            }

            $this->pdf->Code39($width, $height, $product->getBarcode(),$product->getName());
            if($cpt%3 == 0)
            {
                $width = 15;
                $height += 35;
            }
            else
                $width += 75;

            $cpt++;
        }

        // on exporte le fichier
        $this->pdf->Output('Code-barres.pdf','D');

        // on définit la classe du div contenant le PDF
        $this->classe = "pdf";

        // on appelle le template pdfSuccess.php
        $this->setTemplate('pdf');

    }
}
