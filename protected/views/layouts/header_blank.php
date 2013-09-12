<?php $this->beginContent('//layouts/main'); ?>
<div class="head">
   <div class="container">
      <div class="lCol">
         <?php echo CHtml::link(CHtml::image(Yii::app()->request->baseUrl."/images/logo.png", $this->pageTitle), "/", array('class'=>'logo', 'title'=>Yii::t('template','GOTO_MAIN'))); ?>
      </div>
      <h1><?php echo $this->title; ?></h1>
   </div>
</div>
<!--<br clear="all" />-->
<div class="mainCols">
	<?php echo $content; ?>	
</div>
		
<?php $this->endContent(); ?>