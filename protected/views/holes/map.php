<?php 
$this->pageTitle=Yii::app()->name.' :: '.Yii::t('template', 'MAP_DEFECT');
$this->title = Yii::t('template', 'MAP_DEFECT');
//echo CHtml::tag('h1', array(), Yii::t('template', 'MAP_DEFECT'));
$form=$this->beginWidget('CActiveForm',Array(
	'id'=>'map-form',
	'enableAjaxValidation'=>false,
)); 
?>

<div class="filterCol filterStatus">
   <p class="title"><?php echo Yii::t('template', 'SHOW_DEFECTS_STATE')?></p>
   <?php foreach ($model->allstatesMany as $alias=>$name) : ?>
      <label>
         <span class="<?php echo $alias; ?>">
            <input id="chn0" name="Holes[STATE][]" type="checkbox"  value="<?php echo $alias; ?>" />
         </span>
         <ins><?php echo $name; ?></ins>
      </label>
   <?php endforeach; ?>	
</div>

<div class="filterCol filterType">
   <p class="title"><?php echo Yii::t('template', 'SHOW_DEFECTS_TYPE')?></p>
   <?php foreach ($types as $i=>$type) : ?>
      <label class="col2">
         <input id="ch0" name="Holes[type][]" type="checkbox" value="<?php echo $type->id; ?>"/>
         <ins class="<?php echo $type->alias; ?>">
            <?php echo $type->getName(); ?>
         </ins>
      </label>
   <?php endforeach; ?>
   <input id="MAPLAT" name="MAPLAT" type="hidden" value="" />
   <input id="MAPZOOM" name="MAPZOOM" type="hidden" value="" />
</div>

<div class="submit">
   <input type="submit" name="button" id="button" value="<?php echo Yii::t('template', 'SHOW')?>" />
   <input type="reset" name="reset" id="reset_button" value="<?php echo Yii::t('template', 'CLEAR')?>" type="button" />
</div>

<?php $this->endWidget(); ?>			
</div>
<div class="mainCols">			
   <div class="bx-yandex-search-layout">
      <div class="bx-yandex-search-form">
         <form id="search_form_MAP_DzDvWLBsil" name="search_form_MAP_DzDvWLBsil" onsubmit="jsYandexSearch_MAP_DzDvWLBsil.searchByAddress(this.address.value); return false;">
            <p><?php echo Yii::t('template', 'ENTER_ADDRES_FOR_SEARCH')?></p>
            <input type="text" id="address_inp" name="address" class="textInput" value="" style="width: 300px;" />
			   <input type="submit" value="<?php echo Yii::t('template', 'SEARCH')?>" />
			   <a style="display:none;" id="clear_result_link" href="#" onclick="clearSerchResults('MAP_DzDvWLBsil', JCBXYandexSearch_arSerachresults); document.getElementById('address_inp').value=''; return false;"><?php echo Yii::t('template', 'CLEAR')?></a>
         </form>
      </div>
      <div class="bx-yandex-search-results" id="results_MAP_DzDvWLBsil">
      </div>
      <div class="bx-yandex-view-layout">
         <div class="bx-yandex-view-map">

         <?php
				$this->widget('application.extensions.ymapmultiplot.YMapMultiplot', array(
						'key'=>$this->mapkey,
					   'id' => 'BX_YMAP_MAP_DzDvWLBsil',//id of the <div> container created
					   'address' =>  Array(), //Array of AR objects
					   'width'=>'100%',
					   'height'=>'600px',						   
				  ));
         ?>
         </div>
      </div>
      <img src="/images/map_shadow.jpg" class="mapShadow" alt="" />
   </div>
</div>
	
<script type="text/javascript">
   history.navigationMode = 'compatible';
   $(document).ready( function(){
      init_MAP_DzDvWLBsil();
   });
</script>
