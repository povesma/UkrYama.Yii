<style>
#map-canvas {
	height: 500px;
	width: 700px;
}
</style>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'holes-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('enctype'=>'multipart/form-data'),
)); 
echo $form->errorSummary($model); ?>
	<!-- правая колоночка -->
	<div class="rCol side_section"> 
		<ul class="add_steps clear">
			<li class="step_1 clear">
				<div class="step_number clear">
					<span class="clear">1</span>
				</div>
				<p><?php echo Yii::t('template', 'HOW_ADD_STEP1')?></p>
			</li>
			<li class="step_2 clear">
				<div class="step_number clear">
					<span class="clear">2</span>
				</div>
				<p><?php echo Yii::t('template', 'HOW_ADD_STEP2')?></p>
			</li>
			<li class="step_3 clear">
				<div class="step_number clear">
					<span class="clear">3</span>
				</div>
				<p><?php echo Yii::t('template', 'HOW_ADD_STEP3')?></p>
			</li>
		</ul>
	</div>
	<!-- /правая колоночка -->
	<script type="text/javascript">
		$(document).ready( function(){

			$('.defect_type li input').click(function(){
				$('.defect_type li').removeClass('checked');
				$(this).parent('li').addClass('checked');
			});

		});
	</script>

	<!-- левая колоночка -->
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script>
var map;
function initialize() {
  var mapOptions = {
    zoom: 12,
    center: new google.maps.LatLng(50.4501, 30.523400000000038),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  var infowindow = new google.maps.InfoWindow();

  marker = new google.maps.Marker({
    map:map,
    draggable:true,
    animation: google.maps.Animation.DROP,
    position: new google.maps.LatLng(50.4501, 30.523400000000038),
    icon: "/images/cur.png"
  });

  event = new Array();
<?php
	$i=0;
	foreach($events as $event){
		echo "event[$i] = new google.maps.Marker({map:map,draggable:false, position: new google.maps.LatLng(".$event['lat'].",".$event['lng']."),animation: google.maps.Animation.DROP});\n";
		echo "google.maps.event.addListener(event[$i], 'click', function(){showEvent(".$event['id'].")});";
		$i++;
	}
?>


//  geo = new google.maps.Geocoder();

  google.maps.event.addListener(marker, "drag", function(){
	infowindow.close();
  });

  google.maps.event.addListener(map, "drag", function(){
	infowindow.close();
	marker.setPosition(map.getCenter());
  });
  google.maps.event.addListener(map, "dragend", function(){
	marker.setPosition(map.getCenter());
	updateAddress();
  });
  google.maps.event.addListener(map, "zoom_changed", function(){
	marker.setPosition(map.getCenter());
	updateAddress();
  });
  google.maps.event.addListener(marker, "dragend", function(){
	updateAddress();
  });

	function updateAddress(){
//		geo.geocode({address: "",location:marker.position, region: "uk"},function(callback){
		$.post("/event/GetAddress",{"lat":marker.position['lat'](),"lng":marker.position['lng']()},function(data){
			var resp = JSON.parse(data);

			var cord = new google.maps.LatLng(marker.position['lat']()+0.003/Math.pow(2,(map.zoom-12)), marker.position['lng']());
			var info = resp['results'][0].address_components;
			var address=info[3]['long_name']+", "+info[2]['long_name']+", "+info[1]['long_name']+", "+info[0]['long_name'];
			community.paddress.value=address;
			infowindow.setContent(address);
			infowindow.maxWidth=200;
			infowindow.setPosition(cord);
			infowindow.open(map);

			var pos = marker.getPosition();
			community.lat.value=marker.position['lat']();
			community.lng.value=marker.position['lng']();
		});
	}
}

google.maps.event.addDomListener(window, 'load', initialize);

function showEvent(id){
	var params = "menubar=no,location=no,resizable=yes,scrollbars=yes,status=yes"
	window.open("/event/ViewEvent/"+id, "Недолiк", params)
}

function addAddress(){
	community.address.value=community.paddress.value;
}
</script>
	<div class="lCol main_section">
<?php if(!(Yii::app()->user->getId())){ ?>
<table>
<tr>
    <td><?= $form->labelEx($model,'EMAIL') ?></td><td><?= $form->textField($model,'EMAIL', array( "style"=>"width:250px")) ?></td>
    <?= $form->error($model,'EMAIL') ?>
</tr>
<tr>
    <td><?= $form->labelEx($model,'FIRST_NAME') ?></td><td><?= $form->textField($model,'FIRST_NAME',array( "style"=>"width:250px")) ?></td>
    <?= $form->error($model,'FIRST_NAME') ?>

    <td><?= $form->labelEx($model,'LAST_NAME') ?></td><td><?= $form->textField($model,'LAST_NAME',array( "style"=>"width:200px")) ?></td>
    <?= $form->error($model,'LAST_NAME') ?>
</tr>
</table>
<?php }else{ ?>
<?= $form->hiddenField($model,'EMAIL', array("value"=>"1")) ?>
<?= $form->error($model,'EMAIL') ?>

<?= $form->hiddenField($model,'FIRST_NAME',array("value"=>"1")) ?>
<?= $form->error($model,'FIRST_NAME') ?>

<?= $form->hiddenField($model,'LAST_NAME',array("value"=>"1")) ?>
<?= $form->error($model,'LAST_NAME') ?>
<?php } ?>
		<div class="form_top_bg clear">
			<div id="map-canvas"></div>
		</div>
<?php $this->endWidget(); ?>
