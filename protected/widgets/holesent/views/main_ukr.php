<div id="holesent" name="holesent" style="display:none">
<div>Это форма бла-бла-бла разъяснение</div>
<table>
	<tr><td>Каким письмом было отправлено?</td><td></tr>
<tr><td>
	<?= CHtml::link('Занес лично', array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")) ?><br>
	<?= CHtml::link('Простым', array('sent', 'id'=>$hole->ID),array('class'=>"declarationBtn")) ?><br>
	<?= CHtml::link('Заказным', "#",array('class'=>"declarationBtn",'onClick'=>'rcptform.style["display"]="inline";')) ?>
</td>
</tr>
	<tr>
<td><div style="display:none" name="rcptform" id="rcptform">Пример чека<br><a target="_blank" href="/images/rcpt.png" ><img width=100px src="/images/rcpt.png"></a><br>
	<form method="POST" action="/holes/sent/<?=$hole->ID?>">
	Введите номер чека:<input type="number" name="holesent[rcpt]" id="rcpt"><br> 
	<input type="checkbox" onChange="if(this.checked){nomail.style['display']='inline'}else{nomail.style['display']='none'}" name="holesent[mailme]" id="mailme"> уведомить о доставке по эл. почте.<br>
	<div id="nomail" style="display:none">
<?php
	if(!strlen(Yii::app()->user->email)){
		echo "Введите ваш email <input name='nomail'>";
	}
?>
	</div>
	<input type="submit" value="Отправить"></div>
	</form>
</td></tr>

</table>
</div>
