<? 
$this->pageTitle=Yii::app()->name . ' - '.$model->name.' - Справочник ГАИ ';
$this->title=CHtml::link('Справочник ГАИ', Array('index')).' > '.$model->name;
?>
	<?php
		$auth=$model->auth_ru->condition("type=:type",array(":type"=>2));
		if ($auth) : ?>
		<div class="news-detail  sprav-detail">
		<?php $this->renderPartial('_view_gibdd', array('data'=>$auth)); ?>
		</div>
		<br/><br/>
	<?php endif; ?>
