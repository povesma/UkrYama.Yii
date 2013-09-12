<?php
$this->title = Yii::t('holes_view', 'HOLE_GIBDDREPLY');
$this->pageTitle=Yii::app()->name . ' :: '.$this->title;
?>
<h1><?php echo $this->title; ?></h1>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>Array ('enctype'=>'multipart/form-data'),
)); ?>
<?php echo $form->errorSummary($answer); ?>

	<!-- левая колоночка -->
	<div class="lCol main_section">
		<!-- тип дефекта -->
	<?php foreach ($models as $model) : ?>
		<div class="f">
			<p class="type <?php echo $model->type->alias ?>" style="padding-left: 30px;"><?php echo $model->type->getName()?></p>
         <p class="address"><?= CHtml::encode($model->ADDRESS) ?></p>
			<?php echo $model->COMMENT1; ?><br/>
		</div>
	<?php endforeach; ?>
		
		<!-- Дата обнаружения -->
		<div class="f clearfix">
		<?php echo $form->label($answer,'date'); ?>
      <?php echo CHtml::textField('answerdate', date(C_DATEFORMAT, $answer->date)); ?>
		<?php echo $form->error($answer,'date'); ?>
		</div>
      <script> $('#answerdate').datepicker({dateFormat: '<?php  echo C_DATEFORMAT_JS ?>'});</script>
      

		<!-- фотки -->
		<div class="f clearfix">
			<?php echo $form->label($answer,'uppload_files'); ?>
			<?php $this->widget('CMultiFileUpload',array('accept'=>'gif|jpg|png|pdf|txt', 'model'=>$answer, 'attribute'=>'uppload_files', 'htmlOptions'=>array('class'=>'mf'), 'denied'=>Yii::t('mf','Невозможно загрузить этот файл'),'duplicate'=>Yii::t('mf','Файл уже существует'),'remove'=>Yii::t('mf','удалить'),'selected'=>Yii::t('mf','Файлы: $file'),)); ?>						
		</div>
            
		<!-- анкета -->
		<div class="f chekboxes">
			<?php echo $form->labelEx($answer,'results'); ?>
			<?php echo $form->checkBoxList($answer,'results',CHtml::listData( HoleAnswerResults::model()->findAll(Array('order'=>'ordering','condition'=>'published=1')), 'id', 'name' ),Array('attributeitem' => 'id', 'template'=>'{input}{label}')); ?>
			<?php echo $form->error($answer,'results'); ?>
		</div>
		
	<div class="f">		
		<div class="bx-yandex-view-layout">
			<div class="bx-yandex-view-map">
			<div id="ymapcontainer" class="ymapcontainer"></div>
			<?php foreach ($models as $i=>$model) $jsplacemarks.="
				var point = new YMaps.GeoPoint({$model->LONGITUDE},{$model->LATITUDE});
                points[{$i}]=point;
				placemarks[{$i}] = new YMaps.Placemark(point, { hideIcon: false, hasBalloon: false });
				map.addOverlay(placemarks[{$i}]);
				"; ?>
				
			<?php Yii::app()->clientScript->registerScript('initmap',<<<EOD
				var map = new YMaps.Map(YMaps.jQuery("#ymapcontainer")[0]);
				map.enableScrollZoom();
				map.setCenter(new YMaps.GeoPoint({$models[0]->LONGITUDE},{$models[0]->LATITUDE}), 14);
				var bounds = new Array();
				var placemarks = new Array();
				var points = new Array();
				{$jsplacemarks}
				 bounds = new YMaps.GeoCollectionBounds(points);
				map.setBounds(bounds);  
				
EOD
,CClientScript::POS_READY);
?>
			</div>
		</div>
		<img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
	</div>		
		
		
		<!-- камент -->    
		<div class="f">
			<?php echo $form->labelEx($answer,'comment'); ?>
			<?php echo $form->textArea($answer,'comment',array('rows'=>4, 'cols'=>30)); ?>
			<?php echo $form->error($answer,'comment'); ?>
		</div>

	</div>
	<!-- /правая колоночка -->
	<div class="addSubmit">
		<div class="container">
			<div class="btn" onclick="$(this).parents('form').submit();">
				<a class="addFact"><i class="text"><?php echo Yii::t('template', 'SEND')?></i><i class="arrow"></i></a>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->
