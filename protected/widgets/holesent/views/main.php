<div id="holesent" name="holesent" style="display:none">
<table>
	<tr><td>Каким образом доставлена жалоба?</td><td></tr>
<tr><td>
	<?= CHtml::link('Занес лично', "javascript:void(0)",array('class'=>"declarationBtn",'onClick'=>'btnz = $(".declarationBtn"); for(i=0;i<btnz.length;i++){ btnz[i].style["font-weight"]="";};this.style["font-weight"]="bold";mailtype.value="1";subwd.style["display"]="inline";wd.style["display"]="inline";rcptform.style["display"]="none"')) ?><br>
	<?= CHtml::link('Простым письмом', "javascript:void(0)",array('class'=>"declarationBtn",'onClick'=>'btnz = $(".declarationBtn"); for(i=0;i<btnz.length;i++){ btnz[i].style["font-weight"]="";};this.style["font-weight"]="bold";mailtype.value="2";subwd.style["display"]="inline";wd.style["display"]="inline";rcptform.style["display"]="none"')) ?><br>
	<?= CHtml::link('Заказным письмом', "javascript:void(0)",array('class'=>"declarationBtn",'onClick'=>'btnz = $(".declarationBtn"); for(i=0;i<btnz.length;i++){ btnz[i].style["font-weight"]="";};this.style["font-weight"]="bold";subwd.style["display"]="none";wd.style["display"]="inline";rcptform.style["display"]="inline";')) ?>
</td>
	<form name="simple" action="/holes/sent/<?=$hole->ID?>/" method="POST" onSubmit="if(when.value.length<8){ $('#when').effect('highlight', {}, 3000); when.focus(); return false;}">
	<input type="hidden" name="mailtype" id="mailtype">
	<tr><td><div style="display:none" id="wd" name="wd">Когда:<br><input type="date" max="<?= date('Y-m-d',time()) ?>" name="when" id="when"><br><input id="subwd" name="subwd" type="submit" value="OК"></div></td></tr>
	</form>
</tr>
	<tr>
<td><div style="display:none" name="rcptform" id="rcptform">
	<form method="POST" action="/holes/sent/<?=$hole->ID?>" onSubmit="if(rcpt.value.length<13){ $('#rcpt').effect('highlight', {}, 3000); rcpt.focus(); return false;}else if(when.value.length<8){ $('#when').effect('highlight', {}, 3000); when.focus(); return false;}else{when2.value=when.value;}">
	Введите штрих-кодовый идентификатор(ШКИ):<input type="text" onkeyup="this.value=this.value.replace(/[^\d]/,'')" name="holesent[rcpt]" id="rcpt"><br>
	Пример штрих-кодового идентификатора<br><a target="_blank" href="/images/rcpt.jpg" ><img width=150px src="/images/rcpt.jpg"></a><br>
	<input name="when2" id="when2" type="hidden"><br/>
	<input type="checkbox" checked onChange="if(this.checked){nomail.style['display']='inline'}else{nomail.style['display']='none'}" name="holesent[mailme]" id="mailme"> уведомить о доставке по эл. почте.<br>
	<div id="nomail" >
<?php
	if(!strlen(Yii::app()->user->email)){
		echo "Введите ваш email <input name='nomail'>";
	}
?>
	</div>
	<input type="submit" value="ОК"></div>
	</form>
</td></tr>

</table>
</div>
