<?
$this->title = Yii::t("template", "MANUALS_GIBDD");
$this->pageTitle = Yii::app()->name . ' :: '.$this->title;
$this->layout='//layouts/header_blank';
?>

<div class="news-list sprav-list">
<?php foreach ($model as $subj) : ?>
	<p class="news-item">
<?php
			if(Yii::app()->user->getLanguage()=="ru"){
?>
		<?php if (Yii::app()->user->isModer && $subj->gibdd_local_not_moderated_ru) : ?>
		<?php echo CHtml::link('('.($subj->region_num < 10 ? '0'.$subj->region_num : $subj->region_num).') '.CHtml::encode($subj->name_full."($subj->gibdd_local_not_moderated_ru)"),Array('view','id'=>$subj->id), Array('style'=>'color:red;')); ?><br />
		<?php else : ?>
		<?php echo CHtml::link('('.($subj->region_num < 10 ? '0'.$subj->region_num : $subj->region_num).') '.CHtml::encode($subj->name_full),Array('view','id'=>$subj->id)); ?><br />
		<?php endif; ?>

<?php
			}elseif(Yii::app()->user->getLanguage()=="ua"){
?>
		<?php if (Yii::app()->user->isModer && $subj->gibdd_local_not_moderated_ua) : ?>
		<?php echo CHtml::link('('.($subj->region_num < 10 ? '0'.$subj->region_num : $subj->region_num).') '.CHtml::encode($subj->name_full."($subj->gibdd_local_not_moderated_ua)"),Array('view','id'=>$subj->id), Array('style'=>'color:red;')); ?><br />
		<?php else : ?>
		<?php echo CHtml::link('('.($subj->region_num < 10 ? '0'.$subj->region_num : $subj->region_num).') '.CHtml::encode($subj->name_full),Array('view','id'=>$subj->id)); ?><br />
		<?php endif; ?>

<?php
			}
?>
	</p>
<?php endforeach; ?>				
</div>
