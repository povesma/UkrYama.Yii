<?php
$data= array("to_name" => "Some name",
"to_address"=>"Some adress",
"from_name"=>"FromName",
"from_address"=>"From Address",
"when"=>"10th of somemonth 1922",
"where"=>"SomeStreetName St.",
"date"=>"29th Of Todays 1599",
"init"=>"E.S. Lastname",
"c_photos"=>"5",
"files"=>"images");

$html = "";

$f=fopen("protected/views/forms/gail.ru.tpl.html","r");
while(!feof($f)){
	$line = fgets($f);
	foreach(array_keys($data) as $el){
		if(!strpos($line, $el)===FALSE){
			$line=str_replace("\${".$el."}",$data[$el],$line);
		}
	}
	$html=$html.$line;
}
$f.fclose();
include("protected/vendors/mpdf57/mpdf.php");
 $mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10);
 $mpdf->charset_in = 'utf-8';

 $stylesheet = file_get_contents('./css/form_pdf.css'); /*подключаем css*/
 $mpdf->WriteHTML($stylesheet, 1);

 $mpdf->list_indent_first_level = 0; 
 $mpdf->WriteHTML($html, 2);
 $mpdf->Output('form_out.pdf', 'I');

