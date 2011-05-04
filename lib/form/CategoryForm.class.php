<?php

/**
 * Category form.
 *
 * @package    pretz
 * @subpackage form
 * @author     Your name here
 */
class CategoryForm extends BaseCategoryForm
{
  public function configure()
  {
       $this->useFields(array('name','description'));

       $this->setWidget('logo',new sfWidgetFormInputFile());
       $file_path = sfConfig::get('sf_upload_dir');
       
       $this->setValidator('logo',new sfValidatorFile(
                array('required' => false, 'max_size' => 30000, 'mime_types' => 'web_images', 'path' => $file_path)
               ));
  }
}
