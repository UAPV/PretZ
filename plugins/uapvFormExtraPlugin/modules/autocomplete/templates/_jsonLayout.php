<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>JSON response</title>
</head>
<body>
  <br />
  data dump:<br />
  <pre>
    <?php print_r($sf_data->getRaw('data')); ?>
  </pre>

  json:<br />
  <pre>
    <?php echo json_encode($sf_data->getRaw('data')); ?>
  </pre>
</body>
</html>

