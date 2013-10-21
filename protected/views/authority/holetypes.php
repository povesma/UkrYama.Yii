<?php
/* @var $this HoleTypesController */
/* @var $model HoleTypes */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'hole-types-holetypes-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// See class documentation of CActiveForm for details on this,
	// you need to use the performAjaxValidation()-method described there.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<p class="err required"><?= $err ?></p>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
		<?php echo $form->error($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'lang'); ?>
		<?php echo $form->textField($model,'lang'); ?>
		<?php echo $form->error($model,'lang'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'alias'); ?>
		<?php echo $form->textField($model,'alias'); ?>
		<?php echo $form->error($model,'alias'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name'); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
<table>
<?php 
	$htypes = $model->findAll();
	foreach($htypes as $type){
		echo "<tr><td>".$type['id']."</td><td>".$type['lang']."</td><td>".$type['alias']."</td><td>".$type['name']."</td></tr>";
	}
?>
</table>
