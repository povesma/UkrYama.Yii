<?php
$requests=$hole->requests_user;
if(count($requests)>0){
	$req=$requests[count($requests)-1];
}
$param="auth_ru";
$this->title = Yii::t('holes_view', 'HOLE_REPLY').$req->auth_ru->name;
$this->pageTitle=Yii::app()->name . ' :: '.$this->title;
?>
<h1><?php echo $this->title; ?></h1>

