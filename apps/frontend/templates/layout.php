<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>PretZ</title>
    <?php include_javascripts() ?>
    <?php include_stylesheets() ?>
  </head>
  <?php $file = TemplateQuery::create()
                    ->filterByActive('1')
                    ->findOne();
  ?>
  <body>
    <div class="contentText" style="background-color: <?php echo $file->getBackColor(); ?>;">
       <?php echo $sf_content ?>
    </div>
  </body>
</html>
