<?php
$f3=require('lib/base.php');
$f3->route('GET /',function($f3){
	
});
$f3->route('POST /',function($f3){
	$id=uniqid();
	move_uploaded_file($_FILES['webcam']['tmp_name'], 'upload/'.$id.'.jpg');
	echo $id;
	die();
});
$f3->route('POST /generate',function($f3){
	$f=$f3->get('POST.id');
	$s=$f3->get('POST.style');
	$url='http://localhost:1338?f='.$f.'&s='.$s;
	echo $url;
	$curl = curl_init();
	curl_setopt_array($curl, array(
    	CURLOPT_RETURNTRANSFER => 0,
    	CURLOPT_URL => $url
	));
	curl_exec($curl);
	die();
});
$f3->run();
?>
<!DOCTYPE html>
<html>
	<head>
		<link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="bower_components/pure/pure-min.css">
		<link rel="stylesheet" href="bower_components/pure/grids-min.css">
		<style>
			.main{
				width:1200px;
				margin-left:auto;
				margin-right:auto;
			}
			.wp{
				position:relative;
				width:600px;
				margin-left:auto;
				margin-right:auto;
			}
			#my_camera{
				width:600px;
				height:600px;
				background-color:#ccc;
				position:relative;
				z-index:1;
			}
			#overlay{
				position:absolute;
				width:600px;
				height:600px;
				z-index:2;
				left:0px;
				top:0px;
				text-align:center;
				line-height:600px;
				font-size:6rem;
				color:#c00000;
			}
			#snaps{
				font-size:3rem;
			}
			#snaps #dismiss, #snaps #save{
				display:none;
			}
			#snaps.snap #dismiss, #snaps.snap #save{
				display:block;
			}
			#snaps.snap #capture{
				display:none;
			}
			ul{
				list-style:none;
				padding:0;
				margin:0;
			}
			.pure-button.green{
				background-color:#0a0;
				color:#fff;
			}
			.pure-button.blue{
				background-color:#00a;
				color:#fff;
			}
			.pure-button.red{
				background-color:#a00;
				color:#fff;
			}
			ul img:hover{
				border:15px solid #a00;
				box-sizing: border-box;
				cursor:pointer;
			}
			body ul.styles{
				display:none;
			}
			body.visible ul.styles{
				display:block;
			}
		</style>
		<script src="bower_components/mootools/dist/mootools-core.min.js"></script>
		<script src="bower_components/webcamjs/webcam.min.js"></script>
		<script>
			var App={
				init:function(){
					this.overlay=$('overlay');
					this.dismiss=$('dismiss');
					this.save=$('save');
					this.snaps=$('snaps');
					this.overlay.setStyles({'display':'none'});
					Webcam.attach( '#my_camera' );
					Webcam.set({crop_width:600, crop_height:600, width: 800, height: 600});
					$('capture').addEvent('click',this.countdown_start.bind(this));
					this.dismiss.addEvent('click',this.reset.bind(this));
					this.save.addEvent('click',this.upload.bind(this));
					var imgs=$$('ul img');
					console.log(imgs);
					for(var i=0;i<imgs.length;i++){
						imgs[i].addEvent('click', this.select_style.bind(this,imgs[i]));
					}
					console.log('init');
				},
				countdown_start:function(){
					this.reset();
					this.countdown();
				},
				countdown:function(){
					this.counter--;
					if(this.counter>=0){
						this.overlay.setStyles({'display':'block'});
						if(this.counter==0)
						{
							this.overlay.set('html','<i class="fa fa-smile-o" aria-hidden="true"></i>');
						}
						else{
							this.overlay.set('html',this.counter);
						}
						this.countdown.delay(1000,this);
					}
					else{
						this.overlay.set('html','');
						this.overlay.setStyles({'display':'none'});
						Webcam.freeze();
						this.snaps.addClass('snap');
					}
				},
				reset:function(){
						document.body.removeClass('visible');
						this.snaps.removeClass('snap');
						this.counter=6;
						this.overlay.set('html','');
						this.overlay.setStyles({'display':'none'});
						Webcam.unfreeze();
				},
				upload:function(data_uri){
					console.log('upload');
					Webcam.snap(this.do_upload.bind(this));
				},
				do_upload:function(data_uri){
						Webcam.upload( data_uri, '/',this.uploaded.bind(this));
				},
				uploaded:function(code,text){
					this.imgid=text;
					this.show_styles();
				},
				show_styles:function(){
					document.body.addClass('visible');
				},
				select_style:function(el){
					console.log(el);
					var src=el.get('src');
					console.log(src);
					this.reset();
					var req=new Request.JSON({url:'generate'});
					req.post({id:this.imgid,style:src});
					
				}
			}
			window.addEvent('domready',App.init.bind(App));
		</script>
	</head>
	<body>
		<div class="pure-g main">
		<ul class="pure-u-1-6 styles">
			<li><img src="assets/styles/01.jpg" class="pure-img"></li>
			<li><img src="assets/styles/02.jpg" class="pure-img"></li>
			<li><img src="assets/styles/03.jpg" class="pure-img"></li>
			<li><img src="assets/styles/12.jpg" class="pure-img"></li>
		</ul>
		<div class="wp pure-u-2-3">
			<div id="my_camera"></div>
			<div id="overlay"></div>
			<div class="pure-g" id="snaps">
				<button id="dismiss" class="pure-button pure-u-1-2 red"><i class="fa fa-times" aria-hidden="true"></i></button>
				<button id="capture" class="pure-button pure-u-1-1 blue"><i class="fa fa-camera" aria-hidden="true"></i></button>
				<button id="save" class="pure-button pure-u-1-2 green"><i class="fa fa-check" aria-hidden="true"></i></button>
			</div>
		</div>
		<ul class="pure-u-1-6 styles">
			<li><img src="assets/styles/09.jpg" class="pure-img"></li>
			<li><img src="assets/styles/13.jpg" class="pure-img"></li>
			<li><img src="assets/styles/07.jpg" class="pure-img"></li>
			<li><img src="assets/styles/08.jpg" class="pure-img"></li>
		</ul>
		</div>
	</body>
</html>