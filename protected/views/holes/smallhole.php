<style>
.btn-addhole {cursor:pointer;}
.btn-map {cursor:pointer;}
.btn-defect {cursor:pointer;}
#addhole .center{text-align:center;}
#type_lbl {height:20px;}
#map-canvas {height:90%;width:100%;}
#big_map {position:fixed;height:100%;width:95%;display:none;float:center;z-index: 999;}
.btn-map {cursor:pointer; text-decoration:underline;}
.ok-btn {float:right}
</style>
<div id="addhole" style="">
<div id="big_map" style="background-color:#ccc">
	<div class="google-search-form" style="padding-bottom: 0px;">
		<input width="700px" id="target" type="text" placeholder="Пошук">
		<input type="button" class="ok-btn" onClick="big_map.style['display']='none'" value="OK">
	</div>
	<div id="map-canvas"></div>
	<input type="button" class="ok-btn" onClick="big_map.style['display']='none'" value="OK">
</div>
<form enctype="multipart/form-data" method="POST" target="postYama" name="yamaForm" id="yamaForm" action="http://ukryama.com/holes/smallhole">
<!-- Персональные данные -->
<table id="addyama" border=0 style="border: 0px; border-top: 0px; border-bottom: 0px; border-right: 0px;">
<tr border=0><td border=0 colspan=3>Email: <input name="umail" id="umail"> Им'я: <input name="uname" id="uname"></td></tr>
<!-- Карта/адрес -->
<tr><td colspan=3><span class="" onClick="">Адреса:</span> <input id="haddress" name="haddress" onClick="big_map.style['display']='inline';initialize('<?= $model->LONGITUDE ?>', '<?= $model->LATITUDE ?>')">
<input type="hidden" name="poslat" id="poslat"><input type="hidden" name="poslon" id="poslon">
<!-- Тип дефекта -->

<select name="deftype">
<option value="0">-==Тип дефекту==-
<?php
$defects = HoleTypes::model()->findAll('published=:stat and lang=:lang',array(':stat'=>1,':lang'=>"ua"));
foreach($defects as $defect):
?>
<option value="<?= $defect->id ?>"><?= $defect->name ?>
<?php endforeach;?>
</select>
</tr>
<!-- Фотографи -->
<tr><td valign="top">Фото дефекту:</td><td>
<?php $this->widget('CMultiFileUpload', array('accept'=>'gif|jpg|png|jpeg', 'model'=>$model, 'attribute'=>'upploadedPictures', 'htmlOptions'=>array('class'=>'mf multi'), 'denied'=>Yii::t('mf','Неможливо завантажити цей файл'),'duplicate'=>Yii::t('mf','Файл вже iснує'),'remove'=>Yii::t('mf','видалити'),'selected'=>Yii::t('mf','Файли: $file'),)); ?>
</td><td rowspan=2><a href=http://ukryama.com/><img border=0 width="70px" src="http://ukryama.com/images/logo.png"></td></tr>
<tr><td><input type="submit" value="Надiслати" onClick="addyama.style['display']='none';finalPage.style['display']='inline'"></td></tr>
</table>
</form>
<table id="finalPage" style="display:none">
<tr><td>Дефект завантажено на сайт УкрЯма. Вам відправлено електронного листа з посиланням для підтвердження 
адреси електронної пошти. Будь ласка, підтвердіть її для продовження роботи з дефектом. Дякуемо!</td></tr>
</table>
<iframe id="postYama" name="postYama" src="" style="width:0;height:0;border:0px solid #fff;"></iframe>
</div>
