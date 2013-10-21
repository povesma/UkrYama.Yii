<? 
$this->pageTitle=Yii::app()->name . ' - '.$model->name_full.' - Справочник ГАИ ';
$this->title=CHtml::link('Справочник ГАИ', Array('index')).' > '.$model->name_full;
?>
<?php
			if(Yii::app()->user->getLanguage()=="ru"){
 ?>
<?php if ($model->gibdd_ru) : ?>
<div class="news-detail  sprav-detail">
<?php $this->renderPartial('_view_gibdd', array('data'=>$model->gibdd_ru)); ?>	  		
</div>
<br/><br/>
<?php endif; ?>			
<?php 			}elseif(Yii::app()->user->getLanguage()=="ua"){ ?>
<?php if ($model->gibdd_ua) : ?>
<div class="news-detail  sprav-detail">
<?php $this->renderPartial('_view_gibdd', array('data'=>$model->gibdd_ua)); ?>	  		
</div>
<br/><br/>
<?php endif; ?>			

<?php } ?>
<?php if ($model->prosecutor) : ?>
<div class="news-detail  sprav-detail">
				<h2><?php echo $model->prosecutor->gibdd_name; ?></h2>
				<?php echo $model->prosecutor->preview_text; ?><div style="clear:both"></div>
		 				
		</div>
<?php endif; ?>		

<?php if (!Yii::app()->user->isGuest) : ?>
<br/><br/><br/>
<?php echo CHtml::link('Добавить территориальный отдел ГАИ', array('add'), array('class'=>'button')); ?>
<?php endif; ?>
<?php if(Yii::app()->user->getLanguage()=="ru"){ ?>
<?php if ($model->gibdd_local_ru) : ?>
<br/><br/>
<h2>Территориальные отделы ГАИ :</h2>
<?php foreach ($model->gibdd_local_ru as $data) : ?>
<div class="news-detail  sprav-detail">
				<?php $this->renderPartial('_view_gibdd', array('data'=>$data)); ?>		 				
		</div>
<br/><br/>		
<?php endforeach; ?>				
<?php endif; ?>
<?php }elseif(Yii::app()->user->getLanguage()=="ua"){ ?>
<?php if ($model->gibdd_local_ru) : ?>
<br/><br/>
<h2>Территориальные отделы ГАИ :</h2>
<?php foreach ($model->gibdd_local_ru as $data) : ?>
<div class="news-detail  sprav-detail">
				<?php $this->renderPartial('_view_gibdd', array('data'=>$data)); ?>		 				
		</div>
<br/><br/>		
<?php endforeach; ?>				
<?php endif; ?>
<?php } ?>
