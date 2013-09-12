<?php
$this->pageTitle=Yii::app()->name . ' :: Мои ямы';

Yii::app()->clientScript->registerScript('select_holes','			
   function selectHoles(arr,del){
      jQuery.ajax({
         "type":"POST",
         "beforeSend":function(){
				$("#holes_select_list").empty();
				$("#holes_select_list").addClass("loading");		
         },
         "complete":function(){
            $("#holes_select_list").removeClass("loading");
			},
         "url":"'.CController::createUrl("selectHoles").'?del="+del,"cache":false,"data":"holes="+arr,
			"success":function(html){
			   jQuery("#holes_select_list").html(html);
			}
      });				
   }						
', CClientScript::POS_HEAD);

Yii::app()->clientScript->registerScript('check_holes','
   checkInList();	
   var scroller = new StickyScroller("#holes_select_list",{
      start: 270,
      end: 200000,
      interval: 300,
      range: 100,
      margin: 50
	});
			
   scroller.onNewIndex(function(index){
      $("#scrollbox").html("Index " + index);
	});
					
   var opacity = .25;
   var fadeTime = 500;
   var current;				
   
   scroller.onScroll(function(index){                        
		//alert(index);
	});
', CClientScript::POS_READY);

$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	//'method'=>'get',
	'id'=>'holes_selectors',
)); 	
		
echo $form->dropDownList($model, 'TYPE_ID', HoleTypes::getList(), array('prompt'=>Yii::t('template', 'DEFECTTYPE'))); 
echo $form->dropDownList($model, 'STATE', $model->Allstates, array('prompt'=>Yii::t('template', 'DEFECTSTATE'))); 
echo $form->dropDownList($model, 'showUserHoles', array(1=>Yii::t('template', 'MY_DEFECTS'), 2=>Yii::t('template', 'DEFECTS_WITH_MY_REQUESTS'))); 
echo CHtml::submitButton(Yii::t('template', 'SEARCH')); 
echo '<br/>';

$this->endWidget(); ?>

	
<div class="lCol">
	<div  class="select-all-wrap">
		<?php echo CHtml::checkBox('selectAll', false, Array('id'=>'selectAll','class'=>'state_check')); ?>
      <?php echo CHtml::label(Yii::t('template', 'SELECT_ALL'), 'selectAll'); ?>
	</div>
	<div id="holes_select_list">
	<?php 
      $selected=$user->getState('selectedHoles', Array());
      if ($selected || $user->userModel->selected_holes_lists){
         $this->renderPartial('_selected', Array('gibdds'=>$selected ? GibddHeads::model()->with('holes')->findAll('holes.id IN ('.implode(',',$selected).')') : Array(),'user'=>$user->userModel));
		}
   ?>
	</div> 
</div>

<div class="rCol">
   <div class="pdf_form" id="pdf_form" style="display: none; left:auto;">
      <a href="#" onclick="var c=document.getElementById('pdf_form');if(c){c.style.display=c.style.display=='block'?'none':'block';}return false;" class="close">&times;</a>
      <div id="gibdd_form"></div>				
   </div>
				
   <?php 
   $this->widget('zii.widgets.CListView', array(
   	'id'=>'holes_list',
   	'ajaxUpdate'=>true,
   	'dataProvider'=>$model->userSearch(),
   	'itemView'=>'_view',
   	'itemsTagName'=>'ul',
   	'cssFile'=>Yii::app()->request->baseUrl.'/css/holes_list.css',
   	'itemsCssClass'=>'holes_list',
   	'summaryText'=>false,
   	'viewData'=>Array('showcheckbox'=>true, 'user'=>$user),
   	'afterAjaxUpdate'=> 'function(id){
   		checkInList();
     }',
   )); 
   ?>
</div>


