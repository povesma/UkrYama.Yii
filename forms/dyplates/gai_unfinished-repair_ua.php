<html>
<head>
<title></title>
</head>
<body>
<table>
<tr>
	<td></td>
	<td class="head" colspan=2><p>Кому: <?= $to_name ?><br>Куди: <?= $to_address ?><br>Від: <?= $from_name ?><br>Адреса <?= $from_address ?></p></td>
</tr>
<tr>
<td class="ref"><div class="ref">Вих. №<?= $ref ?> від <?= $date ?> р.</div></td><td>&nbsp;</td>
</tr>
<tr><td colspan=2 class="v-spacer"></td></tr>
<tr><td colspan=2 class="title">
<div>Заява</div>
</td></tr>
<tr><td colspan=2 class="smv-spacer"></td></tr>
<tr><td colspan=2 class="a-body">
<div>
<b><?= $when ?> р.</b> 
мною на территорії дороги за адресою: <b><?= $where ?></b>  був виявлена ділянка дороги, що знаходиться в стані ремонту,
яка не огороджена відповідно до діючих правил та норм. Так, на цій ділянці зрізано частину асфальтового покриття. Зрізання асфальтового 
покриття є складовою частиною ремонту дороги, початку якого передує огородження місця робіт. На місці виконання робіт відсутні
робітники, безпека руху не забезпечена. Такими діями особи, що розпочали цей ремонт, вчинили адміністративне
правопорушення, визначене ч. 3 ст. 140 КУпАП.<br>
Окрім того, руйнування дороги, що утворилися, не  відповідають вимогам Державного стандарту України ДСТУ 3587-97 — 
«Автомобільні дороги, вулиці та залізничні переїзди. Вимоги до експлуатаційного стану». Ці недоліки є небезпечними для
життя та здоров'я учасників дорожнього руху та вимагають негайного вжиття заходів з забезпечення безпеки руху.<br><br>
Вказані недоліки порушують моє право на безпечні умови дорожнього руху, гарантоване ст. 14 Закону "Про дорожній рух".<br><br>

Відповідно до ч. 10 ст. 52-1 Закону Украіни «Про дорожній рух» та ч. 12 ст. 10 Закону «Про міліцію», на ДАІ покладено 
обов'язок забезпечення 
безпеки дорожнього руху, державний контроль, в тому числі шляхом проведення перевірок за дотриманням законів, правил і нормативів
в сфері дорожнього руху. Також відповідно до ч. 1, ст. 6 КУпАП, органи виконавчої влади та органи місцевого самоврядування розробляють та 
здійснюють заходи, спрямовані на запобігання адміністративним правопорушенням, виявлення та усунення причин та умов, 
що до них призводять.<br>
Відповідно до п. 3 ч. 1 ст. 10 Закону «Про міліцію», міліція зобов'язана приймати та реєструвати заяви та повідомлення про 
адміністративні праворушення та вчасно  приймати рішення за ними.<br><br>
В зв'язку з викладеним, керуючись ст. 40 Конституції Україні, <b>прошу:</b><br>
<ol>
<li>Прийняти та зареєструвати цю заяву як повідомлення про адміністративне правопорушення за ст. 140 КУпАП, 
негайно організувати перевірку вказаних фактів, розпочати провадження у справі про адміністративне правопорушення.</li>
<li>Вжити заходів для забезпечення безпечного експлуатційного стану вказаної дороги (обмежити або заборонити рух, 
встановити засоби регулювання тощо).</li>
<li>Направити мені копії актів обстеження, протоколів, постанов, вимог, приписів тощо, підготовлених в результаті перевірки і провадження
у справі за вказаним у заяві фактом (в порядку законодавства про доступ до публічної інформації).</li>
<li>Про результати розгляду цієї заяви та про вжиті заходи прошу повідомити мені письмово.</li>
</ol>
</div>
</td></tr>
<tr><td colspan=2 class="smv-spacer"></td></tr>
<tr>
	<td class="date"><?= $date ?> г.</td>
	<td class="init"><?= $init ?></td>
</tr>
<tr><td colspan=2 class="v-spacer"></td></tr>
<?php if($c_photos): ?>
<tr><td class="attach">Додаю: <?= $c_photos ?> фото.</td></tr>
<?php endif;?>
</table>
<?php if($c_photos): ?>
<pagebreak />
<table>
<?= $files ?>
</table>
<?php endif;?>
</body>
</html>