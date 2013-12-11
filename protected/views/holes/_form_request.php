<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'request-form',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->createUrl("holes/requestform", array("id"=>$hole->ID)),
	'htmlOptions'=> array ('onsubmit'=>"document.getElementById('pdf_form').style.display='none';",'name'=>"requestForm"),
));

$usermodel=Yii::app()->user->userModel;
$model=new RequestForm;
//			$mytype=$hole->type->findAll("id=:id",array(':id'=>$hole->TYPE_ID));
$mytype=array();
if($first==1){
	$mytype['ru']=$hole->type->findByPk(array("id"=>$hole->TYPE_ID,"lang"=>"ru"));
	$mytype['ua']=$hole->type->findByPk(array("id"=>$hole->TYPE_ID,"lang"=>"ua"));
}else{
	$mytype['ru']=$hole->type->findByPk(array("id"=>13,"lang"=>"ru"));
	$mytype['ua']=$hole->type->findByPk(array("id"=>13,"lang"=>"ua"));
	
	$auth['ru']=$req->auth_ru;
	$auth['ua']=$req->auth_ua;
	$mytype['ru']->name=$auth['ru']->name;
	$mytype['ua']->name=$auth['ua']->name;
}

$region=$hole->region();
$region->id;
$choices=array();
if($first==1){
	$choices['ua']=$hole->getAllAuth($region,$mytype['ua'],"ua");
	$choices['ru']=$hole->getAllAuth($region,$mytype['ru'],"ru");
}else{

	$choices['ua']=$auth['ua']->parents("ua");
	$choices['ru']=$auth['ru']->parents("ru");
}
$usermodel=Yii::app()->user->userModel;
?>
<style>
#requestForm input{
	width:150px;
}
</style>
<script>
	function authChange(auth, lang){
		if(auth.value!=0){
			$.post("/holes/getauth",{"auth":auth.value,"lang":lang}, function(data){
				eval('data2='+data); 
				$("#"+lang+"_to_address").val(data2['address']);
				$("#"+lang+"_to_name").val(data2['name']);
				$("#"+lang+"_to_index").val(data2['index']);
				});
		}
	}

	function lChange(lng,btn){
		$(".lnbtn").removeClass("selbt")
		btn.className="lnbtn selbt";
		lang.value=lng;
		if(lng=="ru"){
			ua_form.style['display']="none";
			ru_form.style['display']="inline";
		}else{
			ua_form.style['display']="inline";
			ru_form.style['display']="none";
		}
	}
</script>
<table>
<tr><td>
<input type="hidden" name="lang" id="lang" value="ua">
<input type="hidden" name="hole_type" value="<?= $hole->TYPE_ID?>">

<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_LANG',array(),null,'uk_ua')?></label>/<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_LANG',array(),null,'ru')?></label>
</td>
<td><?= CHtml::button("Українською", Array('class'=>'lnbtn selbt', 'name'=>'uaBtn', 'onClick'=>'lChange("ua",this)'))." ".CHtml::button("По-русски", Array('class'=>'lnbtn', 'name'=>'ruBtn', 'onClick'=>'lChange("ru",this)')) ?>
</td></tr>
</table>
<table id="ua_form"">
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_DEFECT_TYPE',array(),null,'uk_ua')?></label></td>
<td><?= $mytype['ua']->name;?>
</td></tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_AUTHORITY',array(),null,'uk_ua')?></label>
</td>
<td>
<select onClick="authChange(this,'ua')" name="ua_auth">
	<option value="0">. . .
<?php foreach($choices['ua'] as $choice) {?>
	<option value="<?= $choice->id?>"><?=$choice->name?>
<?php } ?>
</select>
</td>
</tr>
<tr>
<td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_NAME',array(),null,'uk_ua')?></label>
</td>
<td><input id="ua_to_name" name="ua_to_name"></div></td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_ADDRESS',array(),null,'uk_ua')?></label>
</td>
<td><input id="ua_to_address" name="ua_to_address"></td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_INDEX',array(),null,'uk_ua')?></label>
</td>
<td><input id="ua_to_index" name="ua_to_index"></td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_FROM',array(),null,'uk_ua')?></label>
</td>
<td>
<input name="ua_from" value="<?= $usermodel->relProfile->request_from ? $usermodel->relProfile->request_from : $usermodel->last_name.' '.$usermodel->name.' '.$usermodel->second_name ?>">
</td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_POSTADDRESS',array(),null,'uk_ua')?></label>
</td>
<td>
<input name="ua_postaddress" value="<?=$usermodel->relProfile->request_address ? $usermodel->relProfile->request_address : ''?>">
</td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE',array(),null,'uk_ua')?></label>
</td>
<td>
<input name="ua_signature" value="<?= $usermodel->relProfile->request_signature ? $usermodel->relProfile->request_signature : $usermodel->last_name.' '.substr($usermodel->name, 0, 2).($usermodel->name ? '.' : '').' '.substr($usermodel->second_name, 0, 2).($usermodel->second_name ? '.' : '') ?>">
</td>
</tr>
</table>
<table id="ru_form" style="display:none">
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_DEFECT_TYPE',array(),null,'ru')?></label> 
<td><?= $mytype['ru']->name;?>
</td></tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_AUTHORITY',array(),null,'ru')?></label> 
</td>
<td>
<select onClick="authChange(this,'ru')" name="ru_auth">
	<option>. . .
<?php foreach($choices['ru'] as $choice) {?>
	<option value="<?= $choice->id?>"><?=$choice->name?>
<?php } ?>
</select>
</td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_NAME',array(),null,'ru')?></label> 
</td>
<td><input id="ru_to_name" name="ru_to_name"></div></td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_ADDRESS',array(),null,'ru')?></label> 
</td>
<td><input id="ru_to_address" name="ru_to_address"></div></td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_TO_INDEX',array(),null,'ru')?></label> 
</td>
<td><input id="ru_to_index" name="ru_to_index"></div></td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_FROM',array(),null,'ru')?></label> 
</td>
<td>
<input name="ru_from" value="<?= $usermodel->relProfile->request_from ? $usermodel->relProfile->request_from : $usermodel->last_name.' '.$usermodel->name.' '.$usermodel->second_name ?>">
</td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_POSTADDRESS',array(),null,'ru')?></label>
</td>
<td>
<input name="ru_postaddress" value="<?= $usermodel->relProfile->request_address ? $usermodel->relProfile->request_address : '' ?>">
</td>
</tr>
<tr><td>
<label><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_SIGNATURE',array(),null,'ru')?></label> 
</td>
<td>
<input name="ru_signature" value="<?= $usermodel->relProfile->request_signature ? $usermodel->relProfile->request_signature : $usermodel->last_name.' '.substr($usermodel->name, 0, 2).($usermodel->name ? '.' : '').' '.substr($usermodel->second_name, 0, 2).($usermodel->second_name ? '.' : '') ?>">
</td>
</tr>
</table>
<table>
<?php
if($first==1){
	$pictures=$hole->pictures_fresh;
}else{
	$pictures=$answ->files_img;
}
if(count($pictures)): ?>
<script>
function setPic(id){
	var a = $("#chpk_"+id);
	var tic = $("#tic_"+id);
	if(a.prop('checked')){
		a.prop('checked', false);
		tic.hide();
		$('#pc').text($(".form_pics input:checkbox:checked").length);
	}else{
		a.prop('checked', true);
		tic.show();
		$('#pc').text($(".form_pics input:checkbox:checked").length);
	}
}
function picSelect(){
	if($(".form_pics").css("display")=="none"){
		a = $(".form_pics input:checkbox");
		$(".form_pics .tic").show();
		for(i=0;i<a.length;i++){a[i].checked=true;}
		$('#pc').text($(".form_pics input:checkbox:checked").length);
		$(".form_pics").show();
	}else{
		$(".form_pics").hide();
	}
}
</script>
<tr><td colspan=2><?= Yii::t('holes_view', 'HOLE_REQUEST_FORM_PHOTO', array("{0}"=>"<span id='pc'>".count($pictures)."</span>")) ?>
<a href="#" onClick="picSelect()"><?=Yii::t('holes_view', 'HOLE_REQUEST_FORM_PHOTO_BUTTON') ?></a>
</td></tr>
<tr><td colspan=2 class="form_pics">
<div id=attach>
<ul>
<?php
	foreach($pictures as $picture){
		if($first==1){
			echo "<li><input name='chpk[".$picture->id."]' id='chpk_".$picture->id."' type=checkbox checked><a href='#' onClick=setPic(".$picture->id.")><img class='t_pic' width=100px src='".$picture->small."' id='".$picture->id."'><img class='tic' src='/images/tic.png' id='tic_".$picture->id."'></a></li>\n";
		}else{
			echo "<li><input name='chpk[".$picture->id."]' id='chpk_".$picture->id."' type=checkbox checked><a href='#' onClick=setPic(".$picture->id.")><img class='t_pic' width=100px src='".$hole->request_gibdd->answer->filesFolder.'/'.$picture->file_name."' id='".$picture->id."'><img class='tic' src='/images/tic.png' id='tic_".$picture->id."'></a></li>\n";
		}
	}
?>
</ul>
</div>
</td></tr>
<?php endif; ?>
<tr><td>
<input type="submit" name="print" value="PDF"></td><td><input type="submit" name="print" value="HTML">
</td></tr>
</table>
<?php $this->endWidget(); ?>

