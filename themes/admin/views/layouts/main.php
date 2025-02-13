<?php /* @var $this Controller */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>        
        <meta name="language" content="en" />       
        <link href='<?php echo Yii::app()->baseUrl; ?>/themes/admin/assets/icon/favicon.ico' rel="shortcut icon" type="image/x-icon"/>
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <style>.errorMessage{color: red;}</style>
    </head>
    <body class="<?php echo isset($this->bodyClass) ? $this->bodyClass : '' ?>">
        <?php echo $content; ?>
    </body>
</html>