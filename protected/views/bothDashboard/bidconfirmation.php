<div class="row">
    <div class="col-md-12">
        <h2 class="top-title"><?php echo Yii::t('string', "Bid Design Preview"); ?></h2>
    </div><!--col-md-12-->
</div>
<?php if (Yii::app()->user->hasFlash('success')): ?>
    <br/>
    <div role="alert" class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?php echo Yii::app()->user->getFlash('success'); ?>
    </div>
<?php endif; ?>
<div class="postDetail">
    <?php
    if ($model->uploaded_file != '')
        {
        if ($this->getFileExtention($model->uploaded_file) == 'stl')
            {
            ?>
            <div id="div" class="containersrr">
                <div class="row">
                    <div class="col-md-12">
                        <div id="view3d" style="text-align: center;">
                            <!-- HTML5 Canvas -->
                            <a id="image_save_url" href="<?php echo Yii::app()->createUrl('site/CanvasToImage'); ?>"></a>
                            <canvas data-uploadDir="jobs" data-href="<?php echo Yii::app()->request->getBaseUrl(true) . "/upload/jobs/" . $model->uploaded_file; ?>"  data-rawImageName="<?php echo $model->uploaded_file; ?>"  id="cv" width="555" height="308"></canvas>
                            <script>
                                $(document).ready(function() {
                                    viewer3d('cv');
                                });
                            </script>
                        </div>
                        <style>
                            .table {
                                /*width: 155%;*/
                                max-width: 400% !important;
                            }
                        </style>
                        <!-- Render type selection menu -->
                        <table class="table">
                            <tr>
                                <td>
                                    <div class="btn-group dropup">
                                        <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                            <span class="glyphicon glyphicon glyphicon-cog"></span> Options <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="#wireframe" class="rendermode">Wireframe</a></li>
                                            <li><a href="#point" class="rendermode">Points</a></li>
                                            <li><a href="#flat" class="rendermode">Filled</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            }
        else
            {
            ?>
            <img src="<?php echo Yii::app()->request->getBaseUrl(true) . "/upload/jobs/" . $model->uploaded_file; ?>">
            <?php
            }
        }
    ?>

    <div class="clearfix"></div>

    <div class="postDetailsP">        
        <p><b>Price: </b>$<?php echo isset($model->price) ? $model->price : ''; ?></p>
    </div>
    <br/>
    <div> 
        <a href="<?php echo Yii::app()->createUrl('bothDashboard/bidConfirmationActions', ['id' => $model->id]); ?>?type=confirm" class="btn btn-success btn-xs pull-left" >
            <span class="glyphicon glyphicon-confirm"></span> Confirm 
        </a>
        <a href="<?php echo Yii::app()->createUrl('bothDashboard/bidConfirmationActions', ['id' => $model->id]); ?>?type=cancel" class="btn btn-danger btn-xs pull-left" >
            <span class="glyphicon glyphicon-confirm"></span> Cancel 
        </a>
    </div>
    <div class="clearfix"></div>
</div>
<a id="dataJson" href="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/data.json"); ?>"></a>
<!-- 
CSS and Javascript library definitions.
-->
<link rel="stylesheet" src="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/css/bootstrap.min.css"); ?>">
<link rel="stylesheet" src="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/css/bootstrap-theme.min.css"); ?>">
<link rel="stylesheet" src="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/css/style.css"); ?>">

<script src="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/js/jsc3d.js"); ?>"></script>
<script src="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/js/jsc3d.console.js"); ?>"></script>

<script src="<?php echo Assets::themeUrl("vendor/codecanyon-3222913-stl3d-viewer/dist/js/object_viewer.js"); ?>"></script>