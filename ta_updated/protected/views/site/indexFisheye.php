<?php /* @var $this SiteController */
	$this->pageTitle=Yii::app()->name;
?>

<!-- Script ini digunakan untuk menentukan nilai-nilai default yang akan digunakan sesuai dengan sesinya -->
<script type="text/javascript">
	var userID, defaultX, defaultY, defaultEdge, defaultParameter;
	var dataString = "";
	var help;
	var SelectedId;
	var defaultZoom;
	var defaultPan;

	<?php
		if (!Yii::app()->user->isGuest)
			echo ('userID='.Yii::app()->user->id.';');
		else
			echo 'userID="";';

		if(isset(Yii::app()->session['help'])) {
			echo('help='.Yii::app()->session['help'].';');
		} else {
			echo 'help="";';
		}

		if(isset(Yii::app()->session['IdPaper'])) {
			echo ('SelectedId="'.Yii::app()->session['IdPaper'].'";');
		} else {
			echo ('SelectedId="8,10,11,12,13,14,15,16,17,18,19";');
		}

		if(isset(Yii::app()->session['Edge'])) {
			echo ('defaultEdge="'.Yii::app()->session['Edge'].'";');
		} else {
			echo ('defaultEdge="Citation";');
		}

		if(isset(Yii::app()->session['sbX'])) {
			echo ('defaultX="'.Yii::app()->session['sbX'].'";');
		} else {
			echo ('defaultX="Domain Data";');
		}

		if(isset(Yii::app()->session['sbY'])) {
			echo ('defaultY="'.Yii::app()->session['sbY'].'";');
		} else {
			echo ('defaultY="Tahun Publikasi";');
		}

		if(isset(Yii::app()->session['zooming'])) {
			echo ('defaultZoom="'.Yii::app()->session['zooming'].'";');
		} else {
			echo ('defaultZoom="Fisheye";');
		}
		if(isset(Yii::app()->session['pan'])) {
			echo ('defaultPan="'.Yii::app()->session['pan'].'";');
		} else {
			echo ('defaultPan="Linier";');
		}
	?>
</script>

<!-- Script ini digunakan untuk mendapatkan nilai-nilai default -->
<script>
	var a = "<?php echo Yii::app()->request->getParam('r');?>".split("/");

	if(typeof a[2] === "undefined" || a.length==2) {
		defaultParameter = SelectedId;
	} else {
		defaultX = a[2];
		defaultY = a[3];
		defaultParameter = a[4];
		defaultEdge = a[5];
		defaultZoom = a[6];
		defaultPan = a[7];
	}   
</script>

<body id="body" onLoad="getDataInit(defaultX, defaultY, defaultParameter, defaultEdge, defaultZoom, defaultPan)">
	<!-- Import file -->
	<?php  
		$baseUrl = Yii::app()->baseUrl; 
		$cs = Yii::app()->getClientScript();
		$cs->registerScriptFile($baseUrl.'/js/d3.min.js');
		$cs->registerScriptFile($baseUrl.'/js/fisheye.js');
		$cs->registerCssFile($baseUrl.'/css/fisheye.css');
		$cs->registerScriptFile($baseUrl.'/js/json2.js');
		$cs->registerScriptFile($baseUrl.'/js/context-menu.js');
		$cs->registerScriptFile($baseUrl.'/js/bootstrap.js');
		$cs->registerCssFile($baseUrl.'/css/bootstrap.css');
		$cs->registerCssFile($baseUrl.'/css/jquery.dataTables.css');
		$cs->registerCssFile($baseUrl.'/css/dataTables.scroller.css');
		$cs->registerScriptFile($baseUrl.'/js/jquery.dataTables.js');
		$cs->registerScriptFile($baseUrl.'/js/dataTables.scroller.js');
		$cs->registerScriptFile($baseUrl.'/js/d3.layout.cloud.js');  
		$cs->registerScriptFile($baseUrl.'/js/jquery.tipsy.js');
		$cs->registerScriptFile($baseUrl.'/js/intro.js');
		$cs->registerCssFile($baseUrl.'/css/TA.css');
		$cs->registerCssFile($baseUrl.'/css/tipsy.css');
		$cs->registerCssFile($baseUrl.'/css/introjs.css');
		$cs->registerCssFile($baseUrl.'/css/bootstrap-responsive.min.css');
		$cs->registerScriptFile($baseUrl.'/js/fisheyePan.js');
	?>

	<!-- Tampilan di sebelah kanan peta penelitian, yaitu: jumlah paper, pemilihan parameter untuk: sumbu x, sumbu y, dan relasi -->
	<div class="right-content">
		<!-- Header -->

		<!-- Header untuk menampilkan jumlah paper -->
		<label style="width:100px">Jumlah Paper</label>
		<label style="width:13px">:</label>
		<div id="jumlahPaper" style="float:right; font-weight:bold;"></div>
 
		<br/>
		<br/>

		<!-- Fitur memilih paper yang akan ditampilkan -->
		<a href="#AddPaper" id="login_pop" class="button" data-intro="Tombol ini digunakan untuk menambahkan dan mengurangi paper yang akan divisualisasikan" data-step="2">
			Pilih Paper
		</a>
		
		<div>
			<a href="#SavePaper" id="save_paper" class="button" style="margin-top:15px; margin-bottom:30px; float:right">
				<!--<span class="glyphicon glyphicon-plus"></span>-->
				Simpan Peta
			</a>
		</div>
 
		<!-- Menampilkan help untuk pengguna -->
		<script>
			function startIntro(data) {
				var intro = introJs();

				// Mendefinisikan konten help
				intro.setOptions({
					steps: [
						{
							element:'#logo',
							intro:"Visualisasi yang membantu peneliti untuk <b>mengelompokkan paper</b> berdasarkan parameter tertentu. Digunakan untuk <b>memetakan penelitian</b> yang sudah ada, sehingga memudahkan peneliti untuk <b>menemukan topik penelitian</b> yang akan dikerjakannya"
						},
						// {
						//  element:'#login',
						//  intro:"<b style=\"font-size:20px\">Masuk</b><br/><br/>Pengguna yang telah masuk dapat menyimpan dan melihat peta penelitian yang telah ia simpan"
						// },
						{
							element: '#login_pop',
							intro: "<b style=\"font-size:20px\">Pilih Paper</b><br/><br/>Tombol ini digunakan untuk <b>menambahkan</b> dan <b>mengurangi</b> paper yang akan <b>divisualisasikan</b>"
						},
						{
							element: '.sub-right-content',
							intro: '<b style=\"font-size:20px\">Ubah Parameter</b><br/><br/>Ubah <b>sumbu X</b> dan <b>sumbu Y</b> visualisasi',
							position: 'left'
						},
						{
							element: '#relation',
							intro: "<b style=\"font-size:20px\">Ubah Relasi</b><br/><br/>Ubah relasi visualisasi (panah)<br/> Relasinya sebagai berikut : " + data,
							position: 'top'
						},
						{
							intro: "<b style=\"font-size:20px\">Relasi</b><br/><br/>Relasi digambarkan dengan <b>panah</b> yang memiliki kepala dan ekor panah. Paper pada <b>kepala panah</b> menunjukkan penelitian yang <b>lebih baik</b> atau <b>mengutip (citasi)</b> dari paper yang terdapat pada ekor (sumber), sementara <b>ekor panah</b> menunjukkan penelitian yang <b>lebih buruk</b> atau merupakan <b>sumber citasi</b>. Misal pada relasi citasi, paper pada ekor panah menunjukkan paper tersebut merujuk ke paper yang memiliki kepala panah"
						},
						{
							intro: "<b style=\"font-size:20px\">Zoom Data</b><br/><br/>Angka pada lingkaran menunjukkan <b>jumlah paper</b> pada suatu kordinat. Untuk data yang <b>lebih dari satu</b>, pengguna dapat melakukan <b>zoom</b> data dengan melakukan <b>klik pada lingkaran</b>. Zoom bertujuan untuk melihat informasi yang terkadung dengan lebih rinci."         
						},
						{
							intro: "<b style=\"font-size:20px\">Pengelompokan Data</b><br/><br/>Jika dilakukan zoom data, akan ditampilkan lingkaran berupa pengelompokan dari data yang sebelumnya dipilih. Warna <b>pink muda</b> menunjukkan <b>data tunggal</b>, warna <b>pink tua</b> menunjukkan <b>pengelompokan data</b> yang jika dipilih, pengguna dapat melihat <b>isi</b> dari pengelompokan data tersebut"
						},
						{
							intro: "<b style=\"font-size:20px\">Navigasi Level</b><br/><br/><img id=\"home\" src=\"<?php echo Yii::app()->request->baseUrl; ?>/images/breadcrumb.png\" height=\"30\" style=\"float:left;margin-right:10px;margin-bottom:10px\"></img>Untuk kembali ke data sebelumnya pengguna dapat melakukan klik pada <b><i>breadcrumb</i></b>. Untuk kembali ke peta penelitian, pengguna dapat melakukan klik pada <b>icon rumah (home)</b>"
						}
					]
				});
				
				// Memulai help
				// intro.start();
				/*
				$(".introjs-button introjs-nextbutton").click(function(){
					intro.setOption('doneLabel', 'Lihat zooming').start().oncomplete(function() {
						$( "#3" ).trigger( "click" );
					});
				});*/
			}
			
			if(userID == "") {
				$('#save_paper').css("display","none");
			} else {
				$('#save_paper').css("display","inline");
			}
		</script>
 
		<!-- Fitur untuk mengubah parameter pada sumbu x dan sumbu y -->
		<div class="sub-right-content" data-step="3" data-intro="Ubah sumbu X dan sumbu Y visualisasi">
			<!-- Header untuk menampilkan parameter yang dapat diubah -->
			<div class="sub-heading">Ubah Parameter</div>
			 
			<!-- Sumbu X -->
			<div>Sumbu X</div>
			<div class="dropdown">
				<?php
					echo CHtml::dropDownList('sumbuX', '', Chtml::listData(MetadataPenelitian::model()->findAllByAttributes(array('flag'=>array('1')), 'deskripsi <> \'Tahun Publikasi\''), 'deskripsi', 'deskripsi'), array(
					'ajax' => array(
						'type'=>'POST', //request type
						'url'=>CController::createUrl('metadataPenelitian/changeDropDown'), //url to call.
						//Style: CController::createUrl('currentController/methodToCall')
						'update'=>'#sumbuY', //selector to update
						//'data'=>'js:javascript statement' 
						//leave out the data key to pass all form values through
						'data'=>array('sumbuX' => 'js:this.value','sumbuYselected' => 'js:$(\'#sumbuY\').val()')
						/*
						'success' => "js:function(data)
						{
						alert(data);
					}",*/
					), 'class'=>'dropdown-style'));
				?>
			</div>
 
			<!-- Sumbu Y -->
			Sumbu Y
			<div class="dropdown">
					<?php
						echo CHtml::dropDownList('sumbuY', '', Chtml::listData(MetadataPenelitian::model()->findAllByAttributes(array('flag'=>array('1')), 'deskripsi <> \'Domain Data\''), 'deskripsi', 'deskripsi'), array(
						'ajax' => array(
							'type'=>'POST', //request type
							'url'=>CController::createUrl('metadataPenelitian/changeDropDown'), //url to call.
							//Style: CController::createUrl('currentController/methodToCall')
							'update'=>'#sumbuX', //selector to update
							//'data'=>'js:javascript statement' 
							//leave out the data key to pass all form values through
							'data'=>array('sumbuY' => 'js:this.value','sumbuXselected' => 'js:$(\'#sumbuX\').val()')
							/*
							'success' => "js:function(data)
							{
							alert(data);
						}",*/
						), 'class'=>'dropdown-style'));
					?>
			</div>
		</div>
 
		<!-- Fitur untuk mengubah parameter relasi -->
		<div id="relation" class="sub-right-content" data-step="4">
			<div class="sub-heading">Ubah Relasi</div>
			<div class="dropdown">
				<?php
					echo CHtml::dropDownList('edge', '', Chtml::listData(MetadataRelasi::model()->findAll(),'deskripsi', 'deskripsi'), array(
					'ajax' => array(
						'type'=>'POST', //request type
						'url'=>CController::createUrl('metadataPenelitian/changeDropDown'), //url to call.
						//Style: CController::createUrl('currentController/methodToCall')
						'update'=>'#edge', //selector to update
						//'data'=>'js:javascript statement' 
						//leave out the data key to pass all form values through
						'data'=>array('edge' => 'js:this.value','edgeSelected' => 'js:$(\'#edge\').val()')
						/*
						'success' => "js:function(data)
						{
						alert(data);
					}",*/
					), 'class'=>'dropdown-style'));
				?>
			</div>
		</div>

		<!-- Fitur untuk mengubah parameter zooming -->
		<div id="zooming" class="sub-right-content">
			<div class="sub-heading">Mode Zoom</div>
			<div class="dropdown">
				<?php
					echo CHtml::dropDownList('mode_zoom', '', array('Fisheye' => 'Fisheye', 'Breadcrumbs' => 'Breadcrumbs'), array(
					'ajax' => array(
						'type'=>'POST', //request type
						'url'=>CController::createUrl('metadataPenelitian/changeDropDown'),
						'update' => '#mode_zoom',
						'data'=>array('mode_zoom' => 'js:this.value', 'zoomSelected'=>'js:$(\'#mode_zoom\').val()')
					), 
					'class'=>'dropdown-style'));
				?> 
			</div>
		</div>

		<div id="pan" class="sub-right-content">
			<div class="sub-heading">Mode Pan</div>
			<div class="dropdown">
				<?php
					echo CHtml::dropDownList('mode_pan','',array('Distorsi' => 'Distorsi','Linier' => 'Linier'),array(
					'ajax' => array(
						'type'=>'POST', //request type
						'url'=>CController::createUrl('metadataPenelitian/changeDropDown'),
						'update' => '#mode_pan',
						'data'=>array('mode_pan' => 'js:this.value','panSelected'=>'js:$(\'#mode_pan\').val()')
					), 
					'class'=>'dropdown-style'));
				?> 
			</div>
		  </div>
	</div>
 
	<!-- Tampilan di sebelah kiri, yaitu peta penelitian -->
	<div class="left-content" style="width:80%">
		<img id="home" src="<?php echo Yii::app()->request->baseUrl; ?>/images/home.png" height="40" style="display:none; float:left; margin-right:10px"></img>
		<div id="sequence" style="display:none;"></div>
		<button id="reset" style="margin-left: 150px;" class="btn btn-info">Reset</button>
		<!-- Container untuk zoom menggunakan breadcrumb pada level 0 -->
		<!-- <p id="chart"> -->
			<svg class="chart" id="chart"></svg>
		<!-- </p> -->
	</div>
			
	<!-- Container untuk zoom menggunakan breadcrumb pada level 1 dan level 2 -->
	<svg class="svg" id="svg" widht="0" height="500" style="display:none;">
		<!-- <g transform="translate(0,10)scale(1,1)" style="stroke-width: 1px;"> -->
			<div id="circle_packing" style="position:absolute; z-index:1; margin-left:-700px; margin-top:25px; top:100px; display:none;">
				<svg class="circle_packing" height="423" width="423"></svg>
			</div>
		<!-- </g> -->
	</svg>
 
	<!-- Inisialisasi peta penelitian -->
	<script id="scriptInit" type="text/javascript">
		$('select[name^="sumbuX"] option[value="' + defaultX + '"]').attr("selected", "selected");
		$('select[name^="sumbuY"] option[value="' + defaultY + '"]').attr("selected", "selected");
		$('select[name^="edge"] option[value="' + defaultEdge + '"]').attr("selected", "selected");
		$('select[name^="mode_zoom"] option[value="' + defaultZoom + '"]').attr("selected", "selected");
 		$('select[name^="mode_pan"] option[value="'+defaultPan+'"]').attr("selected","selected");

		var nodes = {};
		 
		var margin = { top: 10, right: 30, bottom: 40, left: 150 },
		width = 950 - margin.left - margin.right,
		height = 510 - margin.top - margin.bottom;
		
		if ($("#mode_pan option:selected").text()=='Linier'){
			var x = d3.scale.ordinal()
			.rangeRoundBands([0, width], .1);
	 
			var y = d3.scale.ordinal()
			.rangeRoundBands([height, 0], .1);
		}
		else{
			var x = d3.fisheye.ordinal()
			.rangeRoundBands([0, width], .1);
	 
			var y = d3.fisheye.ordinal()
			.rangeRoundBands([height, 0], .1);
		}
 
		var xAxis = d3.svg.axis()
		.scale(x)
		.outerTickSize(0)
		.orient("bottom");
 
		var yAxis = d3.svg.axis()
		.scale(y)
		.outerTickSize(0)
		.orient("left");
 
		var chart = d3.select(".chart")
		.attr("width", width + margin.left + margin.right)
		.attr("height", 515)
		.append("g")
		.attr('class','wrapper map')
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
		 
		var parameter;   
		parameter="8,10,11,12,13,14,15,16,17,18,19";
				 
		var force = d3.layout.force();
		var sumbuX;
		var sumbuY;
		var pack = d3.layout.pack().padding(2).size([200,200]).value(function(d) {
			// console.log("d.name: " + d.name);
			return d.name.length;
		});
		var data;
		var b = {
			w: 75, h: 30, s: 3, t: 10
		};  
 
		chart.append("g")
		.attr("class", "x axis")
		.attr("transform", "translate(0," + height + ")")
		.call(xAxis);
 
		chart.append("g")
		.attr("class", "y axis")
		.attr("transform", "translate(0,0)")
		.call(yAxis);
 
		// Hitung x asal
		function hitungX(sourcex, sourcey, targetx, targety, r) {
			var miring = Math.sqrt(Math.pow((targetx - sourcex), 2) + Math.pow((targety - sourcey), 2));
			return ((targetx * r - sourcex * r + miring * sourcex) / miring);
		}
		 
		// Hitung x tujuan
		function hitungX2(sourcex, sourcey, targetx, targety, r) {
			var miring = Math.sqrt(Math.pow((sourcex - targetx), 2) + Math.pow((sourcey - targety), 2));
			return ((targetx * miring - targetx * r - sourcex * miring + sourcex * r) / miring) + sourcex;
		}
 
		// Fungsi untuk menggambar kembali tampilan sesuai dengan parameter yang dipilih
		function redraw(dataString) {
			//////////////////////
			// Variabel William //
			//////////////////////
			
			var zoomLevel0 = true;
			var zoomLevel1 = false;
			var zoomLevel2 = false;
			
			var margin = { top: 10, right: 30, bottom: 40, left: 150 };
 
			var width = 950 - margin.left - margin.right;
			var height = 510 - margin.top - margin.bottom;
 
			// Untuk mempersiapkan layout
			var force = d3.layout.force()
			.charge(-240)
			.linkDistance(40)
			.size([width, height]);
 
			// Ambil kelas .chart lalu buat tag g dengan atribut width dan height di dalamnya
			var svgFisheye = d3.select(".chart")
			.append("g")
			.attr("width", width + margin.left + margin.right)
			.attr("height", 515)
			.attr('class','wrapper map')
			.attr("transform", "translate(" + margin.left + "," + margin.top + ")");
 
			var color = d3.scale.category20();
 
			var fisheye = d3.fisheye.circular()
			.radius(100);

			//////////////////////
			// Variabel Riska //
			//////////////////////

			svgFisheye.append('rect')
			    .attr('class', 'background')
			    .attr('pointer-events', 'all')
			    .style('cursor','move')
			    .attr('fill', 'none')
			    .attr('height', height);

			var wrapperInner = svgFisheye.append('g')
			.attr('class','wrapper inner')
			.attr('clip-path','url(#wrapperClipPath)')
			.attr("transform", "translate(" + 0 + "," + 0 + ")"); 

			wrapperInner.append("rect")
		        .attr("class", "background")
		        .attr("width", width)
		        .attr('pointer-events', 'all')
		        .style('fill','none')
		        .style('cursor','move')
		        .attr("height", height);

		    var panCanvas = wrapperInner.append("g")
		        .attr("class", "panCanvas")
		        .attr("width", width)
		        .attr("height", height)
		        .attr("transform", "translate(0,0)");

		     panCanvas.append("rect")
		        .attr("class", "background")
		        .attr("width", width+385)
		        .style('fill','none')
		        .attr("height", height+230);

			//menampung elemen yang bisa di-pan
			var svg1 = panCanvas.append('svg')
				    .attr('height', height+230)
			    .attr('width', width+385);
				    
			svg1.append('g').attr('class', 'draggable');

			var canvasChart = d3.select('.chart');

			var defs = canvasChart.append('defs');
		
			defs.append("clipPath")
		        .attr("id", "wrapperClipPath")
		        .attr("class", "wrapper clipPath")
		        .append("rect")
		        .attr("class", "background")
		        .attr("width", width)
		        .attr("height", height);
		            
		    defs.append("clipPath")
		        .attr("id", "overviewClipPath")
		        .attr("class", "overview clipPath")
		        .attr("width", width)
		        .attr("height", height)
		 		.append("rect")
		        .attr("class", "background")
		        .attr("width", width)
		        .attr("height", height);
		            
		    var filter = defs.append("svg:filter")
		        .attr("id", "overviewDropShadow")
		        .attr("x", "-20%")
		        .attr("y", "-20%")
		        .attr("width", "150%")
		        .attr("height", "150%");

		    filter.append("svg:feOffset")
		        .attr("result", "offOut")
		        .attr("in", "SourceGraphic")
		        .attr("dx", "1")
		        .attr("dy", "1");

		    filter.append("svg:feColorMatrix")
		        .attr("result", "matrixOut")
		        .attr("in", "offOut")
		        .attr("type", "matrix")
		        .attr("values", "0.1 0 0 0 0 0 0.1 0 0 0 0 0 0.1 0 0 0 0 0 0.5 0");

		    filter.append("svg:feGaussianBlur")
		       .attr("result", "blurOut")
		       .attr("in", "matrixOut")
		       .attr("stdDeviation", "10");

		    filter.append("svg:feBlend")
		       .attr("in", "SourceGraphic")
		       .attr("in2", "blurOut")
		       .attr("mode", "normal");
		    var overviewRadialFill = defs.append("radialGradient")
		        .attr({
		            id:"overviewGradient",
		            gradientUnits:"userSpaceOnUse",
		            cx:"500",
		            cy:"500",
		            r:"400",
		            fx:"500",
		            fy:"500"
		        });
		    overviewRadialFill.append("stop")
		        .attr("offset", "0%")
		        .attr("stop-color", "#FFFFFF");
		    overviewRadialFill.append("stop")
		        .attr("offset", "40%")
		        .attr("stop-color", "#EEEEEE");
		    overviewRadialFill.append("stop")
		        .attr("offset", "100%")
		        .attr("stop-color", "#E0E0E0");

			///////////////////////////////////////////
			// Persiapan membuat garis sumbu x dan y //
			///////////////////////////////////////////

			if ($("#mode_pan option:selected").text()=='Linier'){
				var x = d3.scale.ordinal()
				.rangeRoundBands([0, width], .1);
		 
				var y = d3.scale.ordinal()
				.rangeRoundBands([height, 0], .1);
			}
			else{
				var x = d3.fisheye.ordinal()
				.rangeRoundBands([0, width], .1);
		 
				var y = d3.fisheye.ordinal()
				.rangeRoundBands([height, 0], .1);
			}

			var xAxis = d3.svg.axis()
			.scale(x)
			.outerTickSize(0)
			.orient("bottom");
	 
			var yAxis = d3.svg.axis()
			.scale(y)
			.outerTickSize(0)
			.orient("left");

			// svgFisheye.append("g")
			// .attr("class", "x axis")
			// .attr("transform", "translate(0, " + height + ")")
			// .call(xAxis);
	 
			// svgFisheye.append("g")
			// .attr("class", "y axis")
			// .attr("transform", "translate(0, 0)")
			// .call(yAxis);

			///////////////////////////////////////////////////
			// Persiapan membuat garis sumbu x dan y selesai //
			///////////////////////////////////////////////////
			
			d3.selectAll(".paperParent").remove();
			d3.selectAll(".circle").remove();
			d3.selectAll(".labelParent").remove();

			d3.selectAll(".paperChild").remove();
			d3.selectAll(".labelChild").remove();

			d3.selectAll(".paperGrandChild").remove();
			d3.selectAll(".labelGrandChild").remove();

			d3.selectAll(".link").remove();
			d3.selectAll(".line").remove();

			// d3.selectAll(".lingkaran").remove();
			// d3.selectAll(".tes0.link").remove();
			// d3.selectAll("circle").remove();
			// d3.selectAll("line").remove();
			// d3.selectAll(".label2").remove();

			data2 = JSON.parse(dataString); // Parse data dari basis data ke dalam bentuk JSON
			 
			data = data2.data3; // Ambil data dengan tag "data3" (berupa array of nodes)
			
			var n = data.nodes.length;
			force.nodes(data.nodes).links(data.links);
 
			// console.log(data);
			// console.log(data['links'][0]);
			// console.log(data['nodes']);
			 
			data.nodes = getChildren(data.nodes); // Ambil anak-anak (tag "children") dari data sebelumnya (array of nodes)

			// var margin = {top: 10, right: 30, bottom: 30, left: 50};
			// var width = 850 - margin.left - margin.right;
			// var height = 500 - margin.top - margin.bottom;
			var posisiX, posisiY;
			var minimum;
			 
			// Sorting sumbu X dan Y
			// Sorting angka
			
			// Fungsi apabila dipilih parameter pada sumbu x dengan nilai "Tahun Publikasi"
			if ($("#sumbuX option:selected").text() == 'Tahun Publikasi') {
				// data.nodes = data.nodes;
				// var i;
				for(var i = 0; i < data.nodes.length; i++) {
					data.nodes[i].sumbu_x = parseInt(data.nodes[i].sumbu_x);
				}
 				if ($("#mode_pan option:selected").text()=='Linier'){
					posisiX = d3.scale.ordinal()          
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_x, b.sumbu_x)}).map(function(d) { return d.sumbu_x; }))
					.rangeRoundBands([0, width+385], .1);
				}
				else{
					posisiX = d3.fisheye.ordinal()          
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_x, b.sumbu_x)}).map(function(d) { return d.sumbu_x; }))
					.rangeRoundBands([0, width], .1);
				}
			}
			 
			// Sorting huruf
			else {
				for(i = 0; i < data.nodes.length; i++) {
					data.nodes[i].sumbu_x = data.nodes[i].sumbu_x.charAt(0).toUpperCase() + data.nodes[i].sumbu_x.slice(1);
				}
				if ($("#mode_pan option:selected").text()=='Linier'){
					posisiX = d3.scale.ordinal()
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_x, b.sumbu_x)}).map(function(d) { return d.sumbu_x; }))
					.rangeRoundBands([0, width+385], .1);
				}
				else{
					posisiX = d3.fisheye.ordinal()
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_x, b.sumbu_x)}).map(function(d) { return d.sumbu_x; }))
					.rangeRoundBands([0, width], .1);
				}
			}
			
			// Fungsi apabila dipilih parameter pada sumbu y dengan nilai "Tahun Publikasi"
			if ($("#sumbuY option:selected").text() == 'Tahun Publikasi') {
				// y = d3.scale.linear()
				// .domain([d3.min(data.nodes.map(function(d) {return d.sumbu_y; }))-5, d3.max(data.nodes.map(function(d) {return d.sumbu_y; }))])
				// .range([0, height]);
				
				// Ubah angka string menjadi angka numeric
				for(var i = 0; i < data.nodes.length; i++) {
					data.nodes[i].sumbu_y = parseInt(data.nodes[i].sumbu_y);
				}
 				if ($("#mode_pan option:selected").text()=='Linier'){
					posisiY = d3.scale.ordinal()
					.rangeRoundBands([height+230, 0], .1)
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_y, b.sumbu_y)}).map(function(d) { return d.sumbu_y; }));
				}
				else{
					posisiY = d3.fisheye.ordinal()
					.rangeRoundBands([height, 0], .1)
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_y, b.sumbu_y)}).map(function(d) { return d.sumbu_y; }));
				}
			} else {
				for(i = 0; i < data.nodes.length; i++) {
					data.nodes[i].sumbu_y = data.nodes[i].sumbu_y.charAt(0).toUpperCase() + data.nodes[i].sumbu_y.slice(1);
				}
				if ($("#mode_pan option:selected").text()=='Linier'){
					posisiY = d3.scale.ordinal()
					.rangeRoundBands([height+230, 0], .1)
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_y, b.sumbu_y)}).map(function(d) { return d.sumbu_y; }));
				}
				else{
					posisiY = d3.fisheye.ordinal()
					.rangeRoundBands([height, 0], .1)
					.domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_y, b.sumbu_y)}).map(function(d) { return d.sumbu_y; }));
				}
			}
			 
			if(posisiY.rangeBand() > posisiX.rangeBand()) {
				minimum = posisiX.rangeBand();
			} else {
				minimum = posisiY.rangeBand();
			}
			 
			var start;
			if((minimum / 2) < 15) {
				alert("Data yang dimasukkan terlalu banyak! Kurangi data");
				if(document.URL.indexOf("#") >= 0) {
					var location = document.URL.split("#");
					document.location.href = location[0] + '#AddPaper';
				} else {
					document.location.href = document.URL + '#AddPaper';
				}
				 
				// start = minimum / 2 - 1;
			} else {
				if(d3.min(data.nodes.map(function(d) {return d.id.length; })) != d3.max(data.nodes.map(function(d) { return d.id.length; }))) {
					start = 15;
				} else {
					start = minimum / 2;
				}
			}
			 
			var posisiR = d3.scale.linear()
			.domain([d3.min(data.nodes.map(function(d) {return d.id.length; })), d3.max(data.nodes.map(function(d) { return d.id.length; }))])
			.range([start, minimum / 2]);
			
			// Run the layout a fixed number of times.
			// The ideal number of times scales with graph complexity.
			// Of course, don't run too long—you'll hang the page!
			force.start();
			for (var i = n; i > 0; --i) force.tick();
			force.stop();
			
			//////////////////////////////////////
			// Membuat garis pada sumbu x dan y //
			//////////////////////////////////////
			 
			xAxis = d3.svg.axis().scale(posisiX).outerTickSize(0).orient("bottom").tickFormat(function(d) {
				if(d.length > minimum / 10) {
					chart.selectAll(".x.axis").selectAll(".tick").each(function( index ) {
						$(this).tipsy({ 
							gravity: 'n', 
							html: true,
							delayIn: 1000,
							title: function() {
								return "<span style=\"font-size:12px\">" + index + "</span>";
							}
						});
					});
					d = d.substr(0, minimum / 10); return d + "..."
				} else {
					return d;
				}
			});
 
			yAxis = d3.svg.axis().scale(posisiY).outerTickSize(0).orient("left").tickFormat(function(d) {
				if(d.length > 10) {
					chart.selectAll(".y.axis").selectAll(".tick").each(function( index ) {
						$(this).tipsy({ 
							gravity: 'e', 
							html: true,
							delayIn: 1000,
							title: function() {
								return "<span style=\"font-size:12px\">" + index + "</span>";
							}
						});
					});
					 
					d = d.substr(0, 10); return d + "..."
				} else {
					return d;
				}
			});
			
			chart.selectAll("g.y.axis")
			.call(yAxis);

			chart.selectAll("g.x.axis")
			.call(xAxis);
			
			var keyword = new Array(data.nodes.length);
			for(i = 0; i < data.nodes.length; i++) {
				keyword[i] = new Array();
				keyword[i] = data.nodes[i].keyword[0].replace(/ /g,"\n");;
				data.nodes[i].keyword = [];
				$.merge(data.nodes[i].keyword, keyword[i]);
			}

			// Ubah label pada sumbu X dan Y
			if($("#sumbuY option:selected").text().indexOf(' ') >= 0) {
				svgFisheye.append("text")
				.attr("class", "sumbuYlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + -115 + "," + ((height / 2) - 15) + ")")  // text is drawn off the screen top left, move down and out and rotate
				.text($("#sumbuY option:selected").text().split(' ')[0]);

				svgFisheye.append("text")
				.attr("class", "sumbuYlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + -115 + "," + (height / 2) + ")")  // text is drawn off the screen top left, move down and out and rotate
				.text($("#sumbuY option:selected").text().split(' ')[1]);

				svgFisheye.append("text")
				.attr("class", "sumbuXlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + (width / 2) + "," + (height + 45) + ")")  // centre below axis
				.text($("#sumbuX option:selected").text());
			} else {
				svgFisheye.append("text")
				.attr("class", "sumbuYlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + -115 + "," + (height / 2) + ")")  // text is drawn off the screen top left, move down and out and rotate
				.text($("#sumbuY option:selected").text());
 
				svgFisheye.append("text")
				.attr("class", "sumbuXlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + (width / 2) + ","+(height + 45) + ")")  // centre below axis
				.text($("#sumbuX option:selected").text());
			}
			
			//////////////////////////////////////////////
			// Membuat garis pada sumbu x dan y selesai //
			//////////////////////////////////////////////
			
			////////////////////////////////
			// Membuat representasi paper //
			////////////////////////////////
 
			var elemParent = svgFisheye.select(".draggable").selectAll("g.circle")
			.data(data.nodes);

			// Buat tag g dengan kelas lingkaran
			var elemParentEnter = elemParent.enter()
			.append("g")
			.attr("class", "paperParent");
			
			data.nodes.forEach(function(d, i) {
				d.x = posisiX(d.sumbu_x)
				+ (posisiX.rangeBand() / 2);
				d.y = posisiY(d.sumbu_y)
				+ (posisiY.rangeBand() / 2);
			});

			// Buat tag circle di dalam tag lingkaran dengan class nodeParent
			var node = elemParentEnter.append("circle")
			.attr("class", "nodeParent")
			.attr("id", function(d, i) {
				return "circleParent-" + i;  // id tiap circle
			})
			.attr("cx", function(d, i) { return d.x; }) // Koordinat lingkaran pada sumbu x
			.attr("cy", function(d, i) { return d.y; }) // Koordinat lingkaran pada sumbu y
			.attr("r", function(d, i) {
				// Mengatur jari-jari lingkaran
				if(d.size.length == 1) {
					if(d.size[0] == 1) {
						return 15;
					}
				} else {
					var realSize = 0;

					for(var iterator = 0; iterator < d.size.length; iterator++) {
						realSize += d.size[iterator];
					}

					if (realSize == 2) {
						return 20;
					} else if (realSize == 3) {
						return 25;
					} else if (realSize == 4) {
						return 30;
					}
				}
			})
			.style("fill", "#FFC2AD");

			// .style("fill", function(d, i) {
			// 	if(d.size.length == 1) {
			// 		return "#6C9ECA";
			// 	} else {
			// 		if(d.children.length == 2) {
			// 			return "#447DB1";
			// 		} else if(d.children.length == 3) {
			// 			return "#2868A2";
			// 		} else if(d.children.length == 4) {
			// 			return "#0F528E";
			// 		} 
			// 	}
			// });
			// .call(force.drag);

			// Buat tag text di dalam tag lingkaran dengan class label
			var label = elemParentEnter.append("text")
			.attr("class", "labelParent")
			.attr("font-family", "sans-serif") // Jenis font
			.attr("font-size", "14px") // Ukuran font
			.attr("text-anchor", "middle")
			.attr("x", function(d, i) { return d.x; }) // Koordinat label pada sumbu x
			.attr("y", function(d, i) { return d.y + 5; }) // Koordinat label pada sumbu y
			.text(function(d) {
				// Isi label
				var realSize = 0;

				for(var iterator = 0; iterator < d.size.length; iterator++) {
					realSize += d.size[iterator];
				}

				return realSize;
			});

			// Hover untuk node dengan jumlah data 1
			var g1 = svgFisheye.select(".draggable").selectAll("g.paperParent").data(data.nodes);

			$("svg circle").each(function(d, i) {
				if(g1[0][d].__data__.children.length == 1) {
					$(g1[0][d]).tipsy({ 
						gravity: 'w', 
						html: true,
						delayIn: 1000,
						title: function() {				
							return "<span style=\"font-size:12px\">" + this.__data__.children[0].judul + "</span><br>Peneliti : " + this.__data__.children[0].peneliti;
						}
					});
				} else {
					$(g1[0][d]).tipsy({ 
						gravity: 'w', 
						html: true,
						delayIn: 1000,
						title: function() {
							var realKeyword = "";

							for(var iterator = 0; iterator < g1[0][d].__data__.keyword.length; iterator ++) {
								realKeyword += g1[0][d].__data__.keyword[iterator];
							}

							return "<span style=\"font-size:12px\">" + realKeyword + "</span>";
						}
					});
				}
			});

			elemParentEnter.on("mouseover", function(d, i) {
				fisheye.focus(d3.mouse(this));

				// Fisheye untuk setiap node
				node.each(function(d) { d.fisheye = fisheye(d); })
				.attr("r", function(d) { return d.fisheye.z * 15 });
				
				// Fisheye untuk setiap label
				label.each(function(d) { d.fisheye = fisheye(d); })
				.attr("r", function(d) { return d.fisheye.z * 15});

				// Fisheye untuk setiap garis
				// link.attr("x1", function(d) { return d.source.fisheye.x; })
				// .attr("y1", function(d) { return d.source.fisheye.y; })
				// .attr("x2", function(d) { return d.target.fisheye.x; })
				// .attr("y2", function(d) { return d.target.fisheye.y; });
			});

			elemParentEnter.on("click", function(d, i) {
				if(d.children.length == 1) {
					if(document.URL.indexOf("#") >= 0) {
						var location = document.URL.split("#");
						document.location.href = location[0] + '#ShowDetailPaper';
					} else {
						document.location.href = document.URL + '#ShowDetailPaper';
					}

					var maxKey = 0;
					var maxValue = 0;

					$.each(d.children[0], function(key, value) {
						if(maxKey < key.length) { maxKey = key.length; }
						if(maxValue < value.length) { maxValue = value.length; }
					});

					$.each(d.children[0], function(key, value) {
						if(key == "id" || key == "creater") {}
						else {
							$( "#popup-content" ).append( "<li><label style=\"width:" + maxKey * 8 + "px\">" + key + "</label><label style=\"width:10px\"> : </label></li>" );
							if(value == "") {}
							else {
								$( "#popup-content" ).append('<span class="detail-content">' + value + '</span>');
							}
						}
					});

					$('a[href="#close"]').click(function() {
						$( "#popup-content" ).empty();
						$( "#map_name" ).val('');
					});

					$('a[href="#x"]').click(function() {
						$( "#popup-content" ).empty();
						$( "#map_name" ).val('');
					});                 
				} else { // d.children.length > 1
					// Periksa apakah boolean bernilai true / false untuk menentukan zoom level mana yang dipakai
					// Menonaktifkan zoom pada level 0 dan mengaktifkan zoom pada level 1
					if (zoomLevel0 == true) {
						elemParentEnter.on("mouseover", function(d, i) {});

						var clickedParent = "circleParent-" + i;

						var circleParent = document.getElementById(clickedParent);
						circleParent.classList.add("circleStroke");

						zoomLevel0 = false;
						zoomLevel1 = true;
						zoomLevel2 = false;

						var dataChild = getChildrenWithGrouping(d);

						if(dataChild.length == 2) {
							dataChild.forEach(function(p, i) {
								if(i == 0) {
									p.x = d.x;
									p.y = d.y - 75;
								} else if(i == 1) {
									p.x = d.x + 75;
									p.y = d.y;
								}
							});
						}

						if(dataChild.length == 3) {
							dataChild.forEach(function(p, i) {
								if(i == 0) {
									p.x = d.x - 75;
									p.y = d.y;
								} else if(i == 1) {
									p.x = d.x;
									p.y = d.y - 75;
								} else if(i == 2) {
									p.x = d.x + 75;
									p.y = d.y;
								}
							});
						}

						if(dataChild.length == 4) {
							dataChild.forEach(function(p, i) {
								if(i == 0) {
									p.x = d.x;
									p.y = d.y - 75;
								} else if(i == 1) {
									p.x = d.x + 75;
									p.y = d.y;
								} else if(i == 2) {
									p.x = d.x;
									p.y = d.y + 75;
								} else if(i == 3) {
									p.x = d.x - 75;
									p.y = d.y;
								}
							});
						}
						
						// Ubah warna paperParent
						node.style("fill", "#DDDDDD");

						var elemChild = svgFisheye.select(".draggable").selectAll("g.circle")
						.data(dataChild);

						var elemChildEnter = elemChild.enter()
						.append("g")
						.attr("class", "paperChild");

						var nodeChild = elemChildEnter.append("circle")
						.attr("class", "nodeChild")
						.attr("id", function(p, i) {
							return "circleChild-" + i;  // id tiap circle
						})
						.attr("cx", function(p) { return p.x; })
						.attr("cy", function(p) { return p.y; })
						.attr("r", function(p, ii) { return 15; })
						.style("fill", "#FFC2AD")
						// .style("fill", function(p, ii) {
						// 	var lastIndex = p.length - 1;

						// 	if(p[lastIndex] == null) {
						// 		return "#6C9ECA";
						// 	} else if(p.length == 2) {
						// 		return "#447DB1";
						// 	}
						// })
						.style("stroke-width", "0px");
						// .call(force.drag);

						var labelChild = elemChildEnter.append("text")
						.attr("class", "labelChild")
						.attr("font-family", "sans-serif")
						.attr("font-size", "14px")
						.attr("text-anchor", "middle")
						.attr("x", function(p) {
							return p.x;
						})
						.attr("y", function(p) {
							return p.y + 5;
						})
						.text(function(p, i) { return d.size[i] });

						// Hover untuk node dengan jumlah data 1
						var g2 = svgFisheye.select(".draggable").selectAll("g.paperChild").data(dataChild);

						$("svg circle").each(function(p) {
							$(g2[0][p]).tipsy({ 
								gravity: 'w',
								html: true,
								delayIn: 1000,
								title: function() {
									var size = g2[0][p].__data__[0].length;

									if(size == null) {
										return "<span style=\"font-size:12px\">" + g2[0][p].__data__[0].judul + "</span><br>Peneliti : " + g2[0][p].__data__[0].peneliti;
									} else {
										return "<span style=\"font-size:12px\">" + g2[0][p].__data__[0].children[0].keyword + "</span>";
									}
								}
							});
						});

						elemChildEnter.on("mouseover", function(p) {
							fisheye.focus(d3.mouse(this));

							// Fisheye untuk setiap node
							nodeChild.each(function(p) { p.fisheye = fisheye(p); })
							.attr("r", function(p) { return p.fisheye.z * 15; });

							// Fisheye untuk setiap label
							labelChild.each(function(p) { p.fisheye = fisheye(p); })
							.attr("r", function(p) { return p.fisheye.z * 15; });
						});

						elemChildEnter.on("click", function(p, i) {
							if(d.size[i] == 1) {
								if(document.URL.indexOf("#") >= 0) {
									var location = document.URL.split("#");
									document.location.href = location[0] + '#ShowDetailPaper';
								} else {
									document.location.href = document.URL + '#ShowDetailPaper';
								}

								var maxKeyChildren = 0;
								var maxValueChildren = 0;

								$.each(p[0], function(key, value) {
									if(maxKeyChildren < key.length) { maxKeyChildren = key.length; }
									if(maxValueChildren < value.length) { maxValueChildren = value.length; }
								});

								$.each(p[0], function(keyChildren, valueChildren) {
									if(valueChildren == "" || keyChildren == "x" || keyChildren == "y" || keyChildren == "px" || keyChildren == "py" || keyChildren == "fisheye" || keyChildren == "id" || keyChildren == "name") {}
									else {
										$( "#popup-content" ).append( "<li><label style=\"width:" + maxKeyChildren * 8 + "px\">" + keyChildren + "</label><label style=\"width:10px\"> : </label></li>" );
										$( "#popup-content" ).append('<span class="detail-content">' + valueChildren + '</span>');
									}
								});

								$('a[href="#close"]').click(function() {
									$( "#popup-content" ).empty();
									$( "#map_name" ).val('');
								});

								$('a[href="#x"]').click(function() {
									$( "#popup-content" ).empty();
									$( "#map_name" ).val('');
								});
							} else {
								if(zoomLevel1 == true) {
									zoomLevel0 = false;
									zoomLevel1 = false;
									zoomLevel2 = true;

									elemParentEnter.on("mouseover", function(d, i) {});
									elemChildEnter.on("mouseover", function(p, i) {});

									var clickedChild = "circleChild-" + i;

									var circleChild = document.getElementById(clickedChild);
									circleChild.classList.add("circleStroke2");

									// Ubah warna paperParent
									node.style("fill", "#DDDDDD");

									// Ubah warna paperChild
									nodeChild.style("fill", "#DDDDDD");

									console.log("p[0].children");
									console.log(p[0].children);

									var dataGrandChild = p[0].children;

									dataGrandChild.forEach(function(q, i) {
										if(i == 0) {
											q.x = p.x;
											q.y = p.y - 75;
										}
										else if (i == 1) {
											q.x = p.x + 75;
											q.y = p.y;
										}
									});

									var elemGrandChild = svgFisheye.select(".draggable").selectAll("g.circle")
									.data(dataGrandChild);

									var elemGrandChildEnter = elemGrandChild.enter()
									.append("g")
									.attr("class", "paperGrandChild");

									var nodeGrandChild = elemGrandChildEnter.append("circle")
									.attr("class", "nodeGrandChild")
									.attr("id", function(q, i) {
										return "circleGrandChild-" + i;  // id tiap circle
									})
									.attr("cx", function(q, i) { return q.x; })
									.attr("cy", function(q, i) { return q.y; })
									.attr("r", function(q, i) { return 15; })
									.style("fill", "#FFC2AD")
									.style("stroke-width", "0px");
									// .call(force.drag);

									var labelGrandChild = elemGrandChildEnter.append("text")
									.attr("class", "labelGrandChild")
									.attr("font-family", "sans-serif")
									.attr("font-size", "14px")
									.attr("text-anchor", "middle")
									.attr("x", function(q, i) {
										return q.x;
									})
									.attr("y", function(q, i) {
										return q.y + 5;
									})
									.text("1");

									// Hover untuk node dengan jumlah data 1
									var g3 = svgFisheye.select(".draggable").selectAll("g.paperGrandChild").data(dataGrandChild);

									$("svg circle").each(function(q, i) {
										$(g3[0][q]).tipsy({ 
											gravity: 'w', 
											html: true,
											delayIn: 1000,
											title: function() {
												return "<span style=\"font-size:12px\">" + g3[0][q].__data__.judul + "</span><br>Peneliti : " + g3[0][q].__data__.peneliti;
											}
										});
									});

									elemGrandChildEnter.on("mouseover", function(q, i) {
										fisheye.focus(d3.mouse(this));

										// Fisheye untuk setiap node
										nodeGrandChild.each(function(q) { q.fisheye = fisheye(q); })
										.attr("r", function(q) { return q.fisheye.z * 15; });

										// Fisheye untuk setiap label
										labelGrandChild.each(function(q) { q.fisheye = fisheye(q); })
										.attr("r", function(q) { return q.fisheye.z * 15; });
									});

									elemGrandChildEnter.on("click", function(q, i) {
										// if(q.length == 1) {
											if(document.URL.indexOf("#") >= 0) {
												var location = document.URL.split("#");
												document.location.href = location[0] + '#ShowDetailPaper';
											} else {
												document.location.href = document.URL + '#ShowDetailPaper';
											}

											var maxKeyGrandChild = 0;
											var maxValueGrandChild = 0;

											$.each(q, function(keyGrandChild, valueGrandChild) {
												if(maxKeyGrandChild < keyGrandChild.length) { maxKeyGrandChild = keyGrandChild.length; }
												if(maxValueGrandChild < valueGrandChild.length) { maxValueGrandChild = valueGrandChild.length; }
											});

											$.each(q, function(keyGrandChild, valueGrandChild) {
												if(keyGrandChild == "id" || keyGrandChild == "creater" || keyGrandChild == "x" || keyGrandChild == "y" || keyGrandChild == "name" || keyGrandChild == "fisheye") {}
												else {
													$( "#popup-content" ).append( "<li><label style=\"width:" + maxKeyGrandChild * 8 + "px\">" + keyGrandChild + "</label><label style=\"width:10px\"> : </label></li>" );
													if(valueGrandChild == "") {}
													else {
														$( "#popup-content" ).append('<span class="detail-content">' + valueGrandChild + '</span>');
													}
												}
											});

											$('a[href="#close"]').click(function() {
												$( "#popup-content" ).empty();
												$( "#map_name" ).val('');
											});

											$('a[href="#x"]').click(function() {
												$( "#popup-content" ).empty();
												$( "#map_name" ).val('');
											});
										// }
									});
								}

								// Menonaktifkan zoom pada level 2 dan mengaktifkan zoom pada level 1
								else if(zoomLevel1 == false) {

									circleChild = document.getElementsByClassName("circleStroke2");
									circleChild[0].classList.remove("circleStroke2");

									elemChildEnter.on("mouseover", function(p, i) {
										$(".paperGrandChild").remove();

										node.style("fill", "#DDDDDD");

										nodeChild.style("fill", "#FFC2AD");

										// nodeChild.style("fill", function(p, i) {
										// 	var lastIndex = p.length - 1;

										// 	if(p[lastIndex] == null) {
										// 		return "#6C9ECA";
										// 	} else if(p.length == 2) {
										// 		return "#447DB1";
										// 	}
										// });

										fisheye.focus(d3.mouse(this));

										// Fisheye untuk setiap node
										nodeChild.each(function(p) { p.fisheye = fisheye(p); })
										.attr("r", function(p) { return p.fisheye.z * 15});

										// Fisheye untuk setiap label
										labelChild.each(function(p) { p.fisheye = fisheye(p); })
										.attr("r", function(p) { return p.fisheye.z * 15});
									});

									zoomLevel0 = false;
									zoomLevel1 = true;
									zoomLevel2 = false;
								}
							}
						});
					}

					// Menonaktifkan zoom pada level 1 dan mengaktifkan zoom pada level 0
					else if (zoomLevel0 == false) {

						circleParent = document.getElementsByClassName("circleStroke");
						circleParent[0].classList.remove("circleStroke");

						elemParentEnter.on("mouseover", function(d, i) {
							$(".paperChild").remove();
							$(".paperGrandChild").remove();

							node.style("fill", "#FFC2AD");

							// node.style("fill", function(d, i) {
							// 	if(d.size.length == 1) {
							// 		return "#6C9ECA";
							// 	} else {
							// 		if(d.children.length == 2) {
							// 			return "#447DB1";
							// 		} else if(d.children.length == 3) {
							// 			return "#2868A2";
							// 		} else if(d.children.length == 4) {
							// 			return "#0F528E";
							// 		} 
							// 	}
							// });

							fisheye.focus(d3.mouse(this));

							// Fisheye untuk setiap node
							node.each(function(d) { d.fisheye = fisheye(d); })
							.attr("r", function(d) { return d.fisheye.z * 15});

							// Fisheye untuk setiap label
							label.each(function(d) { d.fisheye = fisheye(d); })
							.attr("r", function(d) { return d.fisheye.z * 15});

							// Fisheye untuk setiap garis
							// link.attr("x1", function(d) { return d.source.fisheye.x; })
							// .attr("y1", function(d) { return d.source.fisheye.y; })
							// .attr("x2", function(d) { return d.target.fisheye.x; })
							// .attr("y2", function(d) { return d.target.fisheye.y; });
						});

						zoomLevel0 = true;
						zoomLevel1 = false;
						zoomLevel2 = false;
					}
				}
			});
			
			////////////////////////////////////////
			// Membuat representasi paper selesai //
			////////////////////////////////////////
 
			//////////////////
			// Membuat link //
			//////////////////

			// Hitung x asal
			function hitungXAsal(sourcex, sourcey, targetx, targety, r) {
				var miring = Math.sqrt(Math.pow((targetx - sourcex), 2) + Math.pow((targety - sourcey), 2));
				return ((targetx * r - sourcex * r + miring * sourcex) / miring);
			}
			 
			// Hitung x tujuan
			function hitungXTujuan(sourcex, sourcey, targetx, targety, r) {
				var miring = Math.sqrt(Math.pow((sourcex - targetx), 2) + Math.pow((sourcey - targety), 2));
				return ((targetx * miring - targetx * r - sourcex * miring + sourcex * r) / miring) + sourcex;
			}

			// Setting data untuk link, source dan targetnya ud data node
			var rlink = new Array();
			if(data.links.length != 0) {
				var counter_rlink;
				counter_rlink = 0;
				var rlinks = new Array(data.links.length);
				for(var i = 0; i < data.links.length; i++) {
					var j, k, l, m;
					j = 0; k = 0;
					 
					var sudah_ketemu; sudah_ketemu = 0;
					while(data.nodes.length > j && !sudah_ketemu) {
						if(data.nodes[j].id.length == 1 && data.links[i].source != data.nodes[j].id[0]) {
							j++;                
						}
						else if(data.nodes[j].id.length == 1 && data.links[i].source == data.nodes[j].id[0]) {
							sudah_ketemu = 1;
						}
						else {
							l = 0;
							 
							while(data.nodes[j].id.length > l && data.links[i].source != data.nodes[j].id[l]) { // 3>0 && 1!=1
								l++;
							}
 
							if(data.nodes[j].id.length < l || data.links[i].source != data.nodes[j].id[l]) {
								j++;
							}
							else if (data.nodes[j].id.length > l && data.links[i].source == data.nodes[j].id[l]) {
								sudah_ketemu = 1;
							}
						}
					}
					 
					sudah_ketemu = 0;
					 
					while(data.nodes.length > k && !sudah_ketemu) {
						// console.log(data.nodes[k]);
						if(data.nodes[k].id.length == 1 && data.links[i].target != data.nodes[k].id[0]) {
							k++;
						}
						else if (data.nodes[k].id.length == 1 && data.links[i].target == data.nodes[k].id[0]) {
							sudah_ketemu = 1;
						}
						 
						else {
							m = 0;
							 
							while(data.nodes[k].id.length > m && data.links[i].target != data.nodes[k].id[m]) {
								m++;
							}
							 
							if(data.nodes[k].id.length < m || data.links[i].target != data.nodes[k].id[m]) {
								k++;
							}
							else if(data.nodes[k].id.length > m && data.links[i].target == data.nodes[k].id[m]) {
								sudah_ketemu = 1;
							}
						}
					}
					 
					// Untuk melist semua kemungkinan apakah source dan target berada dalam 1 level atau tidak
					if(j < data.nodes.length && k < data.nodes.length && ((data.nodes[j].id.length == 1 && data.nodes[k].id.length == 1 && data.links[i].target == data.nodes[k].id && data.links[i].source == data.nodes[j].id) || (data.nodes[j].id.length > 1 && data.nodes[k].id.length > 1 && data.links[i].target == data.nodes[k].id[m] && data.links[i].source == data.nodes[j].id[l]) || (data.nodes[j].id.length == 1 && data.nodes[k].id.length > 1 && data.links[i].target == data.nodes[k].id[m] && data.links[i].source == data.nodes[j].id) ||(data.nodes[j].id.length > 1 && data.nodes[k].id.length == 1 && data.links[i].target == data.nodes[k].id && data.links[i].source ==data.nodes[j].id[l]))) {
						 
						rlink[counter_rlink] = new Array();
						rlink[counter_rlink].source = data.nodes[j];
						 
						rlink[counter_rlink].target = data.nodes[k];
						counter_rlink++;
					} else {}
				}
			}

			// Panah dan garis hanya akan dibuat jika linknya ada
			if(rlink.length != 0) {
				// Untuk membuat panah
				var marker = svgFisheye.select('.draggable').selectAll("g.marker").data(data.links)
					.enter().append("marker")
					.attr("id", function(d, i) { return i; })
					.attr("viewBox", "0 -5 10 10")
					.attr("refX", function(d) {
						if((posisiY(d.target.value) == posisiY(d.source.value)) && (posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x))) {}
						 
						if(posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x)) {
							return 10;
						} else {
							return 10;
						}           
					})
					.attr("refY", 0)
					.attr("markerWidth", 6)
					.attr("markerHeight", 6)
					.attr("orient", "auto")
					.append("svg:path")
					.attr("d", "M0,-5L10,0L0,5")
					.attr("fill", "none")
					.attr("stroke", "black");
				 
				// (X1, Y1) koordinat asal
				// (X2, Y2) koordinat tujuan						 
				var link = svgFisheye.select('.draggable').selectAll("g.link").data(rlink)
				.enter().append("line")
				.attr("class", "link")
				.attr("x1", function(d) {
					if((posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) && (posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x))) {
						return posisiX(d.source.sumbu_x)+ (x.rangeBand() / 180) + posisiR(d.source.id.length); 
					}
					 
					// Garis horizontal jika lingkaran asal ada di kiri target
					else if ((posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) && (posisiX(d.target.sumbu_x) < posisiX(d.source.sumbu_x))) {
						return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180) - posisiR(d.source.id.length);
					}
					 
					// Garis vertical
					else if(posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) {
						return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180);
					}
					 
					// Garis miring
					else {
						return hitungXAsal((posisiX(d.source.sumbu_x) + (x.rangeBand() / 180)),(posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)), (posisiX(d.target.sumbu_x) + (x.rangeBand() / 180)), (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180)), posisiR(d.source.id.length));
					}
				})
				.attr("y1", function(d) { 
					//garis horizontal
					if(posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) {
						return posisiY(d.source.sumbu_y) + (y.rangeBand() / 180);
					}
					 
					//garis vertical dengan lingkaran asal ada di atas target
					else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) > posisiY(d.source.sumbu_y))) {
						return (posisiY(d.source.sumbu_y)+ (y.rangeBand() / 180) + posisiR(d.source.id.length));
					}
					 
					//garis vertical dengan lingkaran asal ada di bawah target
					else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) < posisiY(d.source.sumbu_y))) {
						return (posisiY(d.source.sumbu_y) + (y.rangeBand() / 180) - posisiR(d.source.id.length));
					}

					else {
						var miring = Math.sqrt(Math.pow(((posisiX(d.source.sumbu_x) + x.rangeBand() / 180)-(posisiX(d.target.sumbu_x) + x.rangeBand() / 180)), 2) + Math.pow(((posisiY(d.source.sumbu_y)+y.rangeBand() / 180)-(posisiY(d.target.sumbu_y) + y.rangeBand() / 180)), 2));
						return (posisiY(d.source.sumbu_y) + y.rangeBand() / 180)-(((posisiY(d.source.sumbu_y) + y.rangeBand() / 180)-(posisiY(d.target.sumbu_y) + y.rangeBand() / 180)) * posisiR(d.source.id.length) / miring);
					}
				})
				// Sama seperti diatas, hanya untuk lingkaran target
				.attr("x2", function(d) {
					if((posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y))) {
						return posisiX(d.target.sumbu_x) + (x.rangeBand() / 180) - posisiR(d.target.id.length); 
					}
					else if ((posisiX(d.target.sumbu_x) < posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y))) {
						return posisiX(d.target.sumbu_x) + (x.rangeBand() / 180) + posisiR(d.target.id.length); 
					}
					else if(posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) {
						return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180);
					} else {
						return hitungXTujuan((posisiX(d.source.sumbu_x) + (x.rangeBand() / 180)), (posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)),(posisiX(d.target.sumbu_x) + (x.rangeBand() / 180)),(posisiY(d.target.sumbu_y) + (y.rangeBand() / 180)), posisiR(d.target.id.length));
					}   
				})
				.attr("y2", function(d) {
					if(posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) {
						return posisiY(d.target.sumbu_y) + (y.rangeBand() / 180);
					}
					else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) > posisiY(d.source.sumbu_y))) {
						return (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180) - posisiR(d.target.id.length));
					}
					else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) < posisiY(d.source.sumbu_y))) {
						return (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180) + posisiR(d.target.id.length));
					} else {
						var miring = Math.sqrt(Math.pow(((posisiX(d.source.sumbu_x) + x.rangeBand() / 180) - (posisiX(d.target.sumbu_x) + x.rangeBand() / 180)), 2) + Math.pow(((posisiY(d.source.sumbu_y) + y.rangeBand() / 180) - (posisiY(d.target.sumbu_y) + y.rangeBand() / 180)), 2));
						return posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)-(((miring - posisiR(d.target.id.length)) * ((posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)) - (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180))) / miring));
					}
				})
				.attr("marker-end", function(d, i) { return "url(#" + i + ")"; });
			}

			//////////////////////////
			// Membuat link selesai //
			//////////////////////////
		
			
			
			/* PANNING WITH DIRECT REPOSITIONING TECHNIQUE (GRAB AND DRAG) */
			if ($("#mode_pan option:selected").text()=='Linier'){
				svgFisheye.select('.background').on('mousemove',null);
				d3.select('#reset').style('visibility','visible');
				svgFisheye.append('rect')
				    .attr('class', 'block')
				    .attr('fill', 'white')
				    .attr('height', 200)
				    .attr('width', 201)
				    .attr("transform", "translate(-200,460)");
				svgFisheye.append('line')
					.style('stroke','#000')
					.style('shape-rendering','crispEdges')
					.attr('x1',0).attr('y1',0)
					.attr('x2',0).attr('y2',460);
				svgFisheye.append('line')
					.style('stroke','#000')
					.style('shape-rendering','crispEdges')
					.attr('x1',0).attr('y1',460)
					.attr('x2',800).attr('y2',460);
				
				var drag = d3.behavior.drag()
					.on("drag", dragmove);

				function dragmove(d) {
					var translate = d3.transform(d3.select(".draggable").attr("transform")).translate;

			                x = d3.event.dx + translate[0],
			                y = d3.event.dy + translate[1];

					  d3.select(".draggable").attr('transform', 'translate(' + (x) + ',' + (y) + ')');
					  d3.select(".x").attr('transform', 'translate(' + (x) + ',' + height + ')');	
					  d3.select(".y").attr('transform', 'translate(' + 0 + ',' + (y) + ')');
					  // var transformTarget = getXYTranslate(panCanvas.attr("transform"));
				      d3.select(".frame").attr("transform", "translate(" + (-x) + "," + (-y) + ")");
				}
				wrapperInner.select('.background').call(drag);

				canvasChart.call(overviewmap); //call overview map

				d3.select("#reset").on('click', function(){
					svgFisheye.select('.draggable').transition()
						.attr("transform", function(d,i){
							return "translate(" + 0 + ", "+ 0 +")";
						})
					d3.select(".x").transition().attr('transform', 'translate(' + 0 + ',' + height + ')');	
					d3.select(".y").transition().attr('transform', 'translate(' + 0 + ',' + 0 + ')');
					d3.select(".frame").transition().attr("transform", "translate(" + 0 + "," + 0 + ")");
					canvasChart.select(".panCanvas").transition().attr("transform", "translate(" + 0 + "," + 0 + ")");
				})
			}
			else{
				svgFisheye.select('.background').on('mousedown.drag',null);
				canvasChart.select('.overviewmap').remove();
				d3.select('#reset').style('visibility','hidden');
				//respond to the mouse and distort where necessary
				wrapperInner.select(".background").on("mousemove", function(){
					if(!d3.event.ctrlKey){	//if the ctrl key is not pressed
					  var mouse = d3.mouse(this);
				      posisiX.distortion(2).focus(mouse[0]);
				      posisiY.distortion(2).focus(mouse[1]);

				      //redraw
				      // Buat tag circle di dalam tag lingkaran dengan class nodeParent

						elemParentEnter.append("circle")
						.attr("class", "nodeParent")
						.attr("id", function(d, i) {
							return "circleParent-" + i;  // id tiap circle
							// return "circle-" + d.id;
						})
						.attr("cx", function(d, i) { return d.x; }) // Koordinat lingkaran pada sumbu x
						.attr("cy", function(d, i) { return d.y;}) // Koordinat lingkaran pada sumbu y
						.attr("r", function(d, i) {
							// Mengatur jari-jari lingkaran
							if(d.size.length == 1) {
								if(d.size[0] == 1) {
									return 15;
								}
							} else {
								var realSize = 0;
								var totalSize = 0;

								for(var iterator = 0; iterator < d.size.length; iterator++) {
									realSize += d.size[iterator];
									totalSize += realSize;
								}

								if (realSize == 2) {
									return 20;
								} else if (realSize == 3) {
									return 25;
								} else if (realSize == 4) {
									return 30;
								}
							}
						 })
						.style("fill", "FFC2AD");

						elemParentEnter.append("text")
						.attr("class", "labelParent")
						.attr("font-family", "sans-serif") // Jenis font
						.attr("font-size", "14px") // Ukuran font
						.attr("text-anchor", "middle")
						.attr("x", function(d, i) { return d.x; }) // Koordinat label pada sumbu x
						.attr("y", function(d, i) { return d.y + 5; }) // Koordinat label pada sumbu y
						.text(function(d) {
							// Isi label
							var realSize = 0;

							for(var iterator = 0; iterator < d.size.length; iterator++) {
								realSize += d.size[iterator];
							}

							return realSize;
						});

						link.attr("x1", function(d) {
							if((posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) && (posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x))) {
								return posisiX(d.source.sumbu_x)+ (x.rangeBand() / 180) + posisiR(d.source.id.length); 
							}
							 
							// Garis horizontal jika lingkaran asal ada di kiri target
							else if ((posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) && (posisiX(d.target.sumbu_x) < posisiX(d.source.sumbu_x))) {
								return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180) - posisiR(d.source.id.length);
							}
							 
							// Garis vertical
							else if(posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) {
								return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180);
							}
							 
							// Garis miring
							else {
								return hitungXAsal((posisiX(d.source.sumbu_x) + (x.rangeBand() / 180)),(posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)), (posisiX(d.target.sumbu_x) + (x.rangeBand() / 180)), (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180)), posisiR(d.source.id.length));
							}
						})
						.attr("y1", function(d) { 
							//garis horizontal
							if(posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) {
								return posisiY(d.source.sumbu_y) + (y.rangeBand() / 180);
							}
							 
							//garis vertical dengan lingkaran asal ada di atas target
							else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) > posisiY(d.source.sumbu_y))) {
								return (posisiY(d.source.sumbu_y)+ (y.rangeBand() / 180) + posisiR(d.source.id.length));
							}
							 
							//garis vertical dengan lingkaran asal ada di bawah target
							else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) < posisiY(d.source.sumbu_y))) {
								return (posisiY(d.source.sumbu_y) + (y.rangeBand() / 180) - posisiR(d.source.id.length));
							}

							else {
								var miring = Math.sqrt(Math.pow(((posisiX(d.source.sumbu_x) + x.rangeBand() / 180)-(posisiX(d.target.sumbu_x) + x.rangeBand() / 180)), 2) + Math.pow(((posisiY(d.source.sumbu_y)+y.rangeBand() / 180)-(posisiY(d.target.sumbu_y) + y.rangeBand() / 180)), 2));
								return (posisiY(d.source.sumbu_y) + y.rangeBand() / 180)-(((posisiY(d.source.sumbu_y) + y.rangeBand() / 180)-(posisiY(d.target.sumbu_y) + y.rangeBand() / 180)) * posisiR(d.source.id.length) / miring);
							}
						})
						// Sama seperti diatas, hanya untuk lingkaran target
						.attr("x2", function(d) {
							if((posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y))) {
								return posisiX(d.target.sumbu_x) + (x.rangeBand() / 180) - posisiR(d.target.id.length); 
							}
							else if ((posisiX(d.target.sumbu_x) < posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y))) {
								return posisiX(d.target.sumbu_x) + (x.rangeBand() / 180) + posisiR(d.target.id.length); 
							}
							else if(posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) {
								return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180);
							} else {
								return hitungXTujuan((posisiX(d.source.sumbu_x) + (x.rangeBand() / 180)), (posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)),(posisiX(d.target.sumbu_x) + (x.rangeBand() / 180)),(posisiY(d.target.sumbu_y) + (y.rangeBand() / 180)), posisiR(d.target.id.length));
							}   
						})
						.attr("y2", function(d) {
							if(posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) {
								return posisiY(d.target.sumbu_y) + (y.rangeBand() / 180);
							}
							else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) > posisiY(d.source.sumbu_y))) {
								return (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180) - posisiR(d.target.id.length));
							}
							else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) < posisiY(d.source.sumbu_y))) {
								return (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180) + posisiR(d.target.id.length));
							} else {
								var miring = Math.sqrt(Math.pow(((posisiX(d.source.sumbu_x) + x.rangeBand() / 180) - (posisiX(d.target.sumbu_x) + x.rangeBand() / 180)), 2) + Math.pow(((posisiY(d.source.sumbu_y) + y.rangeBand() / 180) - (posisiY(d.target.sumbu_y) + y.rangeBand() / 180)), 2));
								return posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)-(((miring - posisiR(d.target.id.length)) * ((posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)) - (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180))) / miring));
							}
						})
						.attr("marker-end", function(d, i) { return "url(#" + i + ")"; });

					chart.select(".x.axis").call(xAxis);
				    chart.select(".y.axis").call(yAxis);
					}
				});
			}
			/* PANNING WITH NAVIGATION WINDOW TECHNIQUE (OVERVIEW MAP) */	
			function overviewmap(selection){
				var target = panCanvas,
					overviewScale = 0.1,
					scale = 1,
					frameX,
					frameY;
				var base = selection;
			    var container = selection.append("g")
			        .attr("class", "overviewmap");
			       
			    overviewmap.node = container.node();

			 	var frame = container.append("g")
			        .attr("class", "frame")
			        .attr('transform','translate(0,0)');

			    frame.append("rect")
			        .attr("class", "background")
			        .attr("width", width)
			        .attr("height", height)
			        .attr("filter", "url(#overviewDropShadow)");
			    	
			    var drag = d3.behavior.drag()
			        .on("dragstart.overviewmap", function() {
			            var frameTranslate = getXYTranslate(frame.attr("transform"));
			                frameX = frameTranslate[0];
			                frameY = frameTranslate[1];
			        })
			        .on("drag.overviewmap", function() {
			            d3.event.sourceEvent.stopImmediatePropagation();
			                frameX += d3.event.dx;
			                frameY += d3.event.dy;
			                frame.attr("transform", "translate(" + frameX + "," + frameY + ")");
			                var translate =  [(-frameX*scale),(-frameY*scale)];
			                target.attr("transform", "translate(" + translate + ")scale(" + scale + ")");
			                d3.select('.x').attr('transform', 'translate(' + (-frameX*scale) + ',' + height + ')scale('+ scale +')');	
					  		d3.select('.y').attr('transform', 'translate(' + 0 + ',' + (-frameY*scale) + ')scale('+ scale +')');
			        });

			    frame.call(drag);
			    var render = function(){
			    	// scale = 1.75;
			        container.attr("transform", "scale(" + overviewScale + ")");
				    var node = target.node().cloneNode(true);
				    node.removeAttribute("id");
				    base.selectAll(".overviewmap .panCanvas").remove();
				    overviewmap.node.appendChild(node);
				    var transformTarget = getXYTranslate(target.attr("transform"));
				    frame.attr("transform", "translate(" + (-transformTarget[0]/scale) + "," + (-transformTarget[1]/scale) + ")")
				        .select(".background")
				        .attr("width", width/scale)
				        .attr("height", height/scale);
				    frame.node().parentNode.appendChild(frame.node());
				    d3.select(node).attr("transform", "translate(0,0)");
			    };
			    selection.call(render);
			}
		}

		function getXYTranslate(translateString){
			var split = translateString.split(",");
		    var x = split[0] ? ~~split[0].split("(")[1] : 0;
		    var y = split[1] ? ~~split[1].split(")")[0] : 0;
		    return [x, y];
		}
 		
		var view;
		var active = d3.select(null);
		var counter = 0;
			 
		function transition(d, chart, x, y) {
			var dx = 40.5 * 2, dy = 40.5 * 2;
			scale = .9 / Math.max(dx / width, dy / height),
			translate = [width / 2 - scale * x, height / 2 - scale * y + 50];
			 
			chart.transition()
			.duration(750)
			.style("stroke-width", 1.5 / scale + "px")
			.attr("transform", "translate(" + translate + ")scale(" + scale + ")")
			.each("end", function() {
				counter += 1;
				d3.select(".chart")
				.style("display", "none");
				d3.select("#sequence")
				.style("display", "inline");
				d3.select("#home")
				.style("display", "inline");
				d3.select(".svg")
				.style("display", "inline");
				d3.select(".circle_packing")
				.style("display", "inline");
				d3.select("#circle_packing")
				.style("display", "inline");
				if(counter == 1){
					return intialZoomed(d, chart, x, y);
				} else {
						//return intialZoomed(d, chart, x, y);
					}
			});
		}
			 
		function returnTransition() {
			var dx = 40.5 * 2, dy = 40.5 * 2;
			scale = .9 / Math.max(dx / width, dy / height),
			//scale = 5,
			translate = [150, 10];
			 
			chart.transition()
			.duration(750)
			.style("stroke-width", 1 + "px")
			.attr("transform", "translate(150,10)");
		}
 
		var circle2;
		var text2;
		var node2;
		var diameter = 110;
		var nodes2;
			 
		function intialZoomed(data2, chart, x, y) {
			initializeBreadcrumbTrail();
			var color = d3.scale.linear()
			.domain([-1, 5])
			.range(["hsl(152,80%,80%)", "hsl(228,30%,40%)"])
			.interpolate(d3.interpolateHcl);
 
			var deepest;
			 
			var opacity = d3.scale.linear()
			.domain([0, 3])
			.range([0, 1]);
			 
			var focus = data2,view;
			nodes2 = pack.nodes(data2);
			mouseoverInit();
			 
			var svg = d3.select(".svg")
			.attr("width", 950)
			.attr("height", 515)
			.append("g")
			.attr("class","graph")
			.attr("transform", "translate(" + 950 / 2 + "," + height / 2 + ")");
 
			// var svg = d3.select("body").append("svg")
			// .attr("width", 950)
			// .attr("height", 515)
			// .append("g")
			// .attr("transform", "translate(" + 950 / 2 + "," + height / 2 + ")");
 
			var circle_packing = d3.select(".circle_packing")
			.attr("width", 443)
			.attr("height", 443)
			.append("g")
			.attr("class","g_circle")
			.attr("transform", "translate(" + 220 + "," + 220 + ")");
 
			circle2 = circle_packing.selectAll("circle").data(nodes2)
			.enter().append("circle")
			.attr("class", function(d) { return d.parent ? d.children ? "node" : "node node--leaf" : "node node--root"; })
			.style("fill", function(d) { if(d.children && d.parent) { return "#F2A9A2"; } else if(!d.parent) { return null; } else {return "#FFC2AD"; }})
			.style("stroke", function(d) { if(!d.parent) { return "white"; }})
			.style("display", function(d) { if (d.parent === focus) { return "inline"; } else { return d.parent === data2 ? null : "none"; }})
			// .style("fill-opacity", function(d){if(!d.parent){return "white";} else if(d.children){ return opacity(d.depth);}})
			// .style("display", function(d) { return d.parent== null || d.parent === data2 ? null : "none"; })
			.on("click", function(d) {
				if (d.children) {
					mouseover(d);
					zoom(d), d3.event.stopPropagation();
				} else {
					if(document.URL.indexOf("#") >= 0) {
						var location = document.URL.split("#");
						document.location.href = location[0] + '#ShowDetailPaper';
					} else {
						document.location.href = document.URL + '#ShowDetailPaper';
					}
 
					var maxKey, maxValue;
					maxKey = 0;
					maxValue = 0;
					$.each(d, function(key, value) {
						if(maxKey < key.length) {
							maxKey = key.length;
						}
						if(maxValue < value.length) {
							maxValue = value.length;
						}
					});
 
					$.each(d, function(key, value) {
						if(key == "id" || key == "name" || key == "depth" || key == "value" || key == "parent" || key == "r" || key == "x" || key == "y" || key == "creater") {}
						else {
							$( "#popup-content" ).append( "<li><label style=\"width:" + maxKey * 8 + "px\">" + key + "</label><label style=\"width:10px\"> : </label></li>" );
							if(value == "") {}
							else {
								$( "#popup-content" ).append('<span class="detail-content">'+value+'</span>');
							}
						}
					}); 
					$('a[href="#close"]').click(function(){
						$( "#popup-content" ).empty();
						$( "#map_name" ).val('');
					});
					$('a[href="#x"]').click(function(){
						$( "#popup-content" ).empty();
						$( "#map_name" ).val('');
					});
				}
			})
			 
			text2 = circle_packing.selectAll("text")
			.data(nodes2)
			.enter().append("text")
			.attr("class", "label2")
			.style("fill-opacity", function(d) { return d.parent === data2 ? 1 : 0; })
			.style("display", function(d) {if (d.parent === focus) { return "inline" } else {return d.parent === data2 ? null : "none"; }})
			.text(function(d) { return d.name; });
			node2 = circle_packing.selectAll("circle,text");
			zoomTo([data2.x, data2.y, 52.28]);
			 
			var arrayX = [];
			arrayX[0]=  { sumbu_x:data2.sumbu_x };
			var arrayY = [];
			arrayY[0]= { sumbu_y:data2.sumbu_y };
 
			var x2 = d3.scale.ordinal()
			.rangeRoundBands([0, 600], .1)
			.domain(arrayX.map(function(d) {return d.sumbu_x; }));

			var y2 = d3.scale.ordinal()
			.rangeRoundBands([height, 0], .1)
			.domain(arrayY.map(function(d) {return d.sumbu_y; }));
			 
			var xAxis2 = d3.svg.axis()
			.scale(x2)
			.orient("bottom");
 
			var yAxis2 = d3.svg.axis()
			.scale(y2)
			.orient("left");
 
			svg.append("g")
			.attr("class", "x axis")
			.attr("transform", "translate(-300," + 230 + ")")
			.call(xAxis2);
 
			svg.append("g")
			.attr("class", "y axis")
			.attr("transform", "translate(-300,-230)")
			.call(yAxis2);
 
			if($("#sumbuY option:selected").text().indexOf(' ') >= 0) {
				svg.append("text")
				.attr("class", "sumbuYlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + -400 + "," + (-5) + ")")  // text is drawn off the screen top left, move down and out and rotate
				.text($("#sumbuY option:selected").text().split(' ')[0]);
 
				svg.append("text")
				.attr("class", "sumbuYlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + -400 + "," + (10) + ")")  // text is drawn off the screen top left, move down and out and rotate
				.text($("#sumbuY option:selected").text().split(' ')[1]);
 
				svg.append("text")
				.attr("class", "sumbuXlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + (0) + "," + (270) + ")")  // centre below axis
				.text($("#sumbuX option:selected").text());
			} else {
				svg.append("text")
				.attr("class", "sumbuYlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + -400 + "," + (0) + ")")  // text is drawn off the screen top left, move down and out and rotate
				.text($("#sumbuY option:selected").text());
 
				svg.append("text")
				.attr("class", "sumbuXlabel")
				.attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
				.attr("transform", "translate(" + (0) + "," + (270) + ")")  // centre below axis
				.text($("#sumbuX option:selected").text());
			}
		}
 
		function zoom(data) {
			var focus0 = focus; focus = data;
			var transition = d3.transition()
			.duration(d3.event.altKey ? 7500 : 750)
			.tween("zoom", function(d) {
				if(data.parent) {
					var i = d3.interpolateZoom(view, [focus.x, focus.y, focus.r / 2]);
				} else {
					var i = d3.interpolateZoom(view, [focus.x, focus.y, diameter / 2]);
				}
 
				return function(t) { zoomTo(i(t)); };
			});
 
			transition.selectAll("text")
			.filter(function(d) { if (typeof d !== "undefined"){ return d.parent === focus || this.style.display === "inline"; }})
			.style("fill-opacity", function(d) { return d.parent === focus ? 1 : 0; })
			.each("start", function(d) { if (d.parent === focus) this.style.display = "inline"; })
			.each("end", function(d) { if (d.parent !== focus) this.style.display = "none"; });
 
			transition.selectAll("circle")
			.filter(function(d) { if (typeof d !== "undefined"){ return d.parent === focus || this.style.display === "inline"; }})
			.style("fill-opacity", function(d) { return d.parent === focus ? 1 : 0; })
			.each("start", function(d) { if (d.parent === focus) this.style.display = "inline"; })
			.each("end", function(d) { if (d.parent !== focus) this.style.display = "none"; });
		}
 
		function zoomTo(v) {
			var k = diameter / v[2];
			view = v;
			node2.attr("transform", function(d) { return "translate(" + (d.x - v[0]) * k + "," + (d.y - v[1]) * k + ")"; });
			circle2.attr("r", function(d) { return d.r * k; });
		}
 
		$("#home").click(function(){
			counter = 0;
			d3.select(".graph").remove();
			d3.select("#trail").remove();
			d3.select(".g_circle").remove();
			d3.select(".svg")
			.style("display", "none");
			d3.select("#sequence")
			.style("display", "none");
			d3.select("#home")
			.style("display", "none");
			d3.select(".circle_packing")
			.style("display", "none");
			d3.select(".chart")
			.style("display", "inline");
			returnTransition();
		});
 
		function getXmlHttpRequest() {
			var xmlHttpObj;
 
			if(window.XMLHttpRequest)
				xmlHttpObj = new XMLHttpRequest();
			else {
				try {
					xmlHttpObj = new ActiveXObject("Msxm12.XMLHTTP");
				}
				catch(e) {
					try {
						xmlHttpObj = new ActiveXObject("Microsoft.XMLHTTP");
					}
					catch(e) {
						xmlHttpObj = false;
					}
				}
			}
			return xmlHttpObj;
		}
 
		// Fungsi untuk mendapatkan data sesuai dengan parameter pada sumbu x, sumbu y, dan jenis relasi
		function getData(sbX, sbY, parameter, edge, zooming, pan){
			window.xmlhttp = getXmlHttpRequest();
			if(!window.xmlhttp)
				return;
			window.xmlhttp.open('POST', 'index.php?r=metadataPenelitian/getData ', true);
			var query = 'sumbuX=' + sbX + '&sumbuY=' + sbY + '&parameter=' + parameter + '&edge=' + edge + '&zooming=' + zooming +'&mode_pan='+pan;
 
			window.xmlhttp.onreadystatechange = function() {
				if(window.xmlhttp.readyState == 4 && window.xmlhttp.status == 200) {
					var response = window.xmlhttp.responseText;
					counter = 0;
					d3.select(".graph").remove();
					d3.select("#trail").remove();
					d3.select(".g_circle").remove();
 
					redraw(response);
					//drawTablePaper(response);
				}
			};
			window.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			window.xmlhttp.send(query);     
		}
		 
		// Fungsi untuk mendapatkan data sesuai dengan parameter pada sumbu x, sumbu y, dan jenis relasi pada inisialisasi
		function getDataInit(sbX, sbY, parameter, edge, zooming, pan){
			window.xmlhttp = getXmlHttpRequest();
			if(!window.xmlhttp)
				return;
			window.xmlhttp.open('POST', 'index.php?r=metadataPenelitian/getData ', true);
			var query = 'sumbuX=' + sbX + '&sumbuY=' + sbY + '&parameter=' + parameter + '&edge=' + edge + '&zooming=' + zooming +'&mode_pan='+pan;
			
			window.xmlhttp.onreadystatechange = function() {
				if(window.xmlhttp.readyState == 4 && window.xmlhttp.status == 200) {
					var response = window.xmlhttp.responseText;
					//console.log(response);
					//data = JSON.parse(response);
					//console.log(data);
					 
					redraw(response);
					initTable(response);
					drawTablePaper(response);
					data = JSON.parse(response);
						 
					for(var i = 0; i < data.relation.length; i++) {
						dataString = dataString.concat("<li><b>" + data.relation[i].deskripsi + "</b> : " + data.relation[i].keterangan + "</li>");
					}
					if(userID == "" || help != "") {
						startIntro(dataString);
							<?php unset(Yii::app()->session['help']);?>
					}
				}
			};
			window.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			window.xmlhttp.send(query);
		}
		 
		// Fungsi untuk mengubah tampilan apabila dilakukan perubahan pada sumbu x
		$("#sumbuX").change(function() {
			$('.sumbuXlabel').remove();
			$('.sumbuYlabel').remove();
			sumbuX = $("#sumbuX option:selected").text();
			sumbuY = $("#sumbuY option:selected").text();
			defaultX = sumbuX;      
			edge = $("#edge option:selected").text();
			zooming = $("#mode_zoom option:selected").text();
			pan = $("#mode_pan option:selected").text();
			if(typeof(sumbuX) != 'undefined' && typeof(sumbuY) != 'undefined') {
				if(typeof(parameter) == 'undefined') {
					getData(sumbuX, sumbuY, 'all', edge, zooming, pan);
				}
				else {
					getData(sumbuX, sumbuY, parameter, edge, zooming, pan);
				}
			}
		});
		 
		// Fungsi untuk mengubah tampilan apabila dilakukan perubahan pada sumbu y
		$( "#sumbuY" ).change(function() {
			$('.sumbuXlabel').remove();
			$('.sumbuYlabel').remove();
			sumbuX = $("#sumbuX option:selected").text();
			sumbuY = $("#sumbuY option:selected").text();
			defaultY = sumbuY;
			edge = $("#edge option:selected").text();
			zooming = $("#mode_zoom option:selected").text();
			pan = $("#mode_pan option:selected").text();
			if(typeof(sumbuX) != 'undefined' && typeof(sumbuY) != 'undefined') {
				if(typeof(parameter) == 'undefined') {
					getData(sumbuX, sumbuY, 'all', edge, zooming, pan);
				} else {
					getData(sumbuX, sumbuY, parameter, edge, zooming, pan);
				}
			}
		});
		 
		// Fungsi untuk mengubah tampilan apabila dilakukan perubahan pada jenis relasi
		$("#edge").change(function() {
			sumbuX = $("#sumbuX option:selected").text();
			sumbuY = $("#sumbuY option:selected").text();
			edge = $("#edge option:selected").text();
			defaultEdge = edge;
			zooming = $("#mode_zoom option:selected").text();
			pan = $("#mode_pan option:selected").text();
			if(typeof(edge) != 'undefined') {
				if(typeof(parameter) == 'undefined') {
					getData(sumbuX, sumbuY, 'all', edge, zooming, pan);
				}
				else {
					getData(sumbuX, sumbuY, parameter, edge, zooming, pan);
				}
			}
		});

		$("#mode_zoom").change(function() {
			sumbuX = $("#sumbuX option:selected").text();
			sumbuY = $("#sumbuY option:selected").text();
			edge = $("#edge option:selected").text();
			zooming = $("#mode_zoom option:selected").text();
			pan = $("#mode_pan option:selected").text();

			if(zooming == "Fisheye") {
				window.location.assign("http://localhost/citationnetwork/ta_updated/index.php?r=site/indexFisheye")
			} else if(zooming == "Breadcrumbs") {
				window.location.assign("http://localhost/citationnetwork/ta_updated/index.php?r=site/index")
			}
		});
		
		$("#mode_pan").change(function() {
			sumbuX = $("#sumbuX option:selected").text();
			sumbuY = $("#sumbuY option:selected").text();
			edge = $("#edge option:selected").text();
			pan = $("#mode_pan option:selected").text();
			zooming = $("#mode_zoom option:selected").text();
			defaultPan=pan;
			if(typeof(pan) != 'undefined')
			{
				if(typeof(parameter)=='undefined')
				{
					getData(sumbuX, sumbuY,'all',edge,zooming,pan);
				}
				else
				{
					getData(sumbuX, sumbuY,parameter,edge,zooming,pan);
				}
			
			}
		});

		function DropDown(el) {
			this.dd = el;
			this.placeholder = this.dd.children('span');
			this.opts = this.dd.find('ul.dropdown > li');
			this.val = '';
			this.index = -1;
			this.initEvents();
		}
		 
		DropDown.prototype = {
			initEvents : function() {
				var obj = this;
 
				obj.dd.on('click', function(event) {
					$(this).toggleClass('active');
					return false;
				});
 
				obj.opts.on('click',function() {
					var opt = $(this);
					obj.val = opt.text();
					obj.index = opt.index();
					obj.placeholder.text(obj.val);
				});
			},
			 
			getValue : function() {
				return this.val;
			},
			 
			getIndex : function() {
				return this.index;
			}
		}
 
		$(function() {
			var dd = new DropDown( $('#dd') );
 
			$(document).click(function() {
				// all dropdowns
				$('.wrapper-dropdown-3').removeClass('active');
			});
		});
 
		var table;
		var t;
 
		function initTable(data) {
			var data_selected = JSON.parse(data).all_data[0];
			var data_not_selected = JSON.parse(data).all_data[1];
			// Create new table unselected paper (id:TableAddPaper)
			table = $('#TableAddPaper').dataTable({
				"aaData": data_not_selected,
				"columns": [
					{ "data": "judul" },
					{ "data": "peneliti" }
				],
				"sScrollX" : "100%",
				"scrollY" : "200px",
				"scrollX": true,
				"scrollCollapse": true,
				"paging": false,
				"aaSorting": [],
 
				"fnCreatedRow": function( nRow, aData, iDataIndex ) {
					$(nRow).attr('id', aData['id']);
				}
			});
 
			// Create new table selected paper (id:AddedPaper)
			t = $('#AddedPaper').dataTable({
				"aaData": data_selected,
				"columns": [
					{ "data": "judul" },
					{ "data": "peneliti" }
				],
				"sScrollX": "100%",
				"scrollY" : "200px",
				"scrollX": true,
				"scrollCollapse": true,
				"paging": false,
				"aaSorting": [],
				"fnCreatedRow": function( nRow, aData, iDataIndex ) {
					$(nRow).attr('id', aData['id']);
				}
			});

			var jumlahPaper;
			 
			jumlahPaper = t.fnSettings().fnRecordsTotal();
			$("#jumlahPaper").text(jumlahPaper);
			if (jumlahPaper > 21) {
				//$('#SaveButton').attr('disabled','disabled');
			}
		}
 
		function drawTablePaper(data) {
			// Array of added paper
			var rowNode1 = new Array(100);
			var rowNode2 = new Array(100);
			 
			// Array 2 dimension of selected paper from unselected paper
			var dataPindah1 = new Array(100);
			var dataPindah2 = new Array(100);
			for (var i = 0; i < 100; i++) {
				dataPindah1[i] = new Array(3);
				dataPindah2[i] = new Array(3);
			}
 
			var time;
			// Counter : hitung 
			var counter, counter1, counter2, counter3;
			counter = 0; counter1 = 0; counter2 = 0; counter3 = 0;  
 
			// Set id pada data table scroll body selected paper (supaya waktu add paper ada efek scroll)
			$('.dataTables_scrollBody').eq(0).attr('id', 'TableAddPaperScrollBody');
			$('.dataTables_scrollBody').eq(1).attr('id', 'AddedPaperScrollBody');
 
			$('.dataTables_filter').find('input').attr("class", "searchInput");
			// Membuat setting pointer awal untuk body table
			// Jika table kosong maka pointer jadi default
			// Jika table terisi maka pointer diubah menjadi pointer
			changeCursor(); 
 
			// Fungsi untuk menangani klik pada row pada TableAddPaper(unselected paper)
			// Menyimpan data klik pada table 
			$('#TableAddPaper tbody').on('click', 'tr', function () {
				if($(this).attr("class") == 'selected1') {
					$(this).css("background-color", ""); 
					$(this).removeClass();
					counter--;
					delete dataPindah1[counter][0];
					delete dataPindah1[counter][1];
					delete dataPindah1[counter][2];
				} else {
					$('#TableAddPaper tbody tr').each(function (i, row) {
						if($(row).css('background-color') == 'rgb(255, 255, 0)') {
							$(row).css('background-color',"");
						}
					});
					 
					$('#AddedPaper tbody tr').each(function (i, row) {
						if($(row).css('background-color') == 'rgb(255, 255, 0)') {
							$(row).css('background-color',"");
						}
					});
					 
					dataPindah1[counter][0] = $('td', this).eq(0).text();
					dataPindah1[counter][1] = $('td', this).eq(1).text();
					dataPindah1[counter][2] = $(this).attr('id');
					counter++;
					$(this).css({'background-color' : 'Gainsboro '});
					$(this).removeClass();
					$(this).addClass("selected1");
				}
			});
		 
			$('.searchInput').focus(function(){
				$('#AddedPaper tbody tr').each(function (i, row) {
					if($(row).css('background-color') == 'rgb(255, 255, 0)') {
						$(row).css('background-color', "");
					}
				});
				$('#TableAddPaper tbody tr').each(function (i, row) {
					if($(row).css('background-color') == 'rgb(255, 255, 0)') {
						$(row).css('background-color',"");
					}
				}); 
			});

			$('#AddedPaper tbody').on('click', 'tr', function () {
				if($(this).attr("class") == 'selected2') {
					$(this).css("background-color","");
					$(this).removeClass();
					counter1--;
					 
					delete dataPindah2[counter1][0];
					delete dataPindah2[counter1][1];
					delete dataPindah2[counter1][2];
				} else {
					$('#AddedPaper tbody tr').each(function (i, row) {
						if($(row).css('background-color') == 'rgb(255, 255, 0)') {
							$(row).css('background-color',"");
						}
					});
					$('#TableAddPaper tbody tr').each(function (i, row) {
						if($(row).css('background-color') == 'rgb(255, 255, 0)') {
							$(row).css('background-color',"");
						}
					});
				 
					// ClearTimeout(time);
					dataPindah2[counter1][0] = $('td', this).eq(0).text();
					dataPindah2[counter1][1] = $('td', this).eq(1).text();
					dataPindah2[counter1][2] = $(this).attr('id');
				 
					counter1++;
					$(this).css({'background-color' : 'Gainsboro '});
					$(this).removeClass();
					$(this).addClass("selected2");
				}
			});

			$("#rightButton").click(function() {
				//clearTimeout(time);
				if(typeof dataPindah1[0][0] !== 'undefined') {
					if(typeof rowNode1[0] !== 'undefined') {
						for(var i = 0; i < counter2; i++) {
							$(rowNode1[i]).removeAttr("style");
						}
						counter2 = 0;
					}
				 
					var json;
					json = "[";
					for(var i = 0; i < counter; i++) {
						if(i == counter - 1) {
							json = json + "{\"judul\":\"" + dataPindah1[i][0] + "\",\"peneliti\":\"" + dataPindah1[i][1] + "\"}";
						} else {
							json = json + "{\"judul\":\"" + dataPindah1[i][0] + "\",\"peneliti\":\"" + dataPindah1[i][1] + "\"},";
						}
					}
				 
					json = json + "]";
					json = JSON.parse(json);
				 
					var rownode = t.fnAddData(json);
					for(var i = 0; i < counter; i++) {
						var theNode = $('#AddedPaper').dataTable().fnSettings().aoData[rownode[i]].nTr;
						theNode.setAttribute('id',dataPindah1[i][2]);
						$('#AddedPaper > tbody > tr').eq(rownode[i]).css('background-color', 'Yellow');
					}
				 
					table.api().row('.selected1').remove().draw();
					changeCursor(); 
					var rowpos = $('#AddedPaper tr:last').position();
				 
					$('#AddedPaperScrollBody').animate({scrollTop: rowpos.top}, "slow", function() {
						jumlahPaper = t.fnSettings().fnRecordsTotal();
						if(jumlahPaper <= 21) {}
						else {}
					});
			 
					counter2 = counter;
					counter = 0;
				}
			});

			$("#leftButton").click(function() {
				// clearTimeout(time);
				if(typeof dataPindah2[0][0] !== 'undefined') {
					if(typeof rowNode2[0] !== 'undefined') {
						for(var i = 0; i < counter3; i++) {
							$(rowNode2[i]).removeAttr("style");
						}
						counter3 = 0;
					}
				 
					var json2;
					json2 = "[";
				 
					for(var i = 0; i < counter1; i++) {
						if(i == counter1 - 1) {
							json2 = json2 + "{\"judul\":\"" + dataPindah2[i][0] + "\",\"peneliti\":\"" + dataPindah2[i][1] + "\"}";
						} else {
							json2 = json2 + "{\"judul\":\"" + dataPindah2[i][0] + "\",\"peneliti\":\"" + dataPindah2[i][1] + "\"},";
						}
					}
				 
					json2 = json2 + "]";
					t.api().row('.selected2').remove().draw();
					json2 = JSON.parse(json2);
					var rownode = table.fnAddData(json2);
				 
					for(var i = 0; i < counter1; i++) {
						var theNode = $('#TableAddPaper').dataTable().fnSettings().aoData[rownode[i]].nTr;
						theNode.setAttribute('id', dataPindah2[i][2]);
						$('#TableAddPaper > tbody > tr').eq(rownode[i]).css('background-color', 'Yellow');
					}
				 
					changeCursor();

					var rowpos = $('#TableAddPaper tr:last').position();
				 
					$('#TableAddPaperScrollBody').animate({scrollTop: rowpos.top}, "slow",function() {
						jumlahPaper = t.fnSettings().fnRecordsTotal();
						if(jumlahPaper <= 21) {}
						else { }
					});
			 
					counter3 = counter1;
					counter1 = 0;
				}
			});

			$("#SaveButton").click(function() {
				jumlahPaper = t.fnSettings().fnRecordsTotal();
				if(jumlahPaper <= 21) {
					$('.sumbuXlabel').remove();
					$('.sumbuYlabel').remove();
					var total = $('#AddedPaper tbody tr').length;
					SelectedId=$('#AddedPaper tbody tr').attr('id')+',';
					$('#AddedPaper tbody tr').each(function(index) {
						if(index==0) {}
						else {
							if(index == total - 1) {
								SelectedId = SelectedId+$(this).attr('id');
							} else {
								SelectedId = SelectedId+$(this).attr('id')+","; 
							}
						}
						$(this).css('background-color', '');
					});
			  
					$('#TableAddPaper tbody tr').each(function(index) {
						$(this).css('background-color', '');
					});
					parameter = SelectedId;
					 
					if(document.URL.indexOf("#") >= 0) {
						var location = document.URL.split("#");
						document.location.href = location[0] + '#close';
					} else {
						document.location.href = document.URL + '#close';
					}
					 
					edge = $("#edge option:selected").text();
					zooming = $("#mode_zoom option:selected").text();
					pan = $("#mode_pan option:selected").text();
					getData(defaultX, defaultY, SelectedId, edge, zooming, pan);
					jumlahPaper = t.fnSettings().fnRecordsTotal();
					$("#jumlahPaper").text(jumlahPaper);
					$("#Close").attr("href", "#close");
				} else {
					//$('#SaveButton').attr('disabled','disabled');
					alert("Jumlah paper melebihi 21. Kurangi paper");
				}
				d3.select('.frame').remove();
			});
		};
		 
		function changeCursor() {
			if($('#AddedPaper .dataTables_empty').length) {
				$('#AddedPaper tbody tr').css({'cursor' : 'default'});
			} else {
				$('#AddedPaper tbody tr').css({'cursor' : 'pointer'});
			}
 
			if($('#TableAddPaper .dataTables_empty').length) {
				$('#TableAddPaper tbody tr').css({'cursor' : 'default'});
			} else {
				$('#TableAddPaper tbody tr').css({'cursor' : 'pointer'});
			}
		}
			 
		function initializeBreadcrumbTrail() {
			// Add the svg area.
			var trail = d3.select("#sequence").append("svg:svg")
				.attr("width", width)
				.attr("height", 50)
				.attr("id", "trail");
			//.translate(10,10);
			// Add the label at the end, for the percentage.
			trail.append("svg:text")
			.attr("id", "endlabel")
			.style("fill", "#000");
		}

		function mouseover(d) {
			var sequenceArray = getAncestors(d);
			updateBreadcrumbs(sequenceArray);
		}

		function mouseoverInit() {
			var sequenceArray = [{name:"Level 1",depth:0}];
			updateBreadcrumbs(sequenceArray);
		}

		function getAncestors(node) {
			var path = [];
			var home={name:"Level 1",depth:0};
			var current = node;
			while (current.parent) {
				path.unshift(current);
				current = current.parent;
			}
			path.unshift(home);
			return path;
		}

		function breadcrumbPoints(d, i) {
			var points = [];
			points.push("0,0");
			points.push(b.w + ",0");
			points.push(b.w + b.t + "," + (b.h / 2));
			points.push(b.w + "," + b.h);
			points.push("0," + b.h);
		 
			if (i > 0) { // Leftmost breadcrumb; don't include 6th vertex.
				points.push(b.t + "," + (b.h / 2));
			}
			return points.join(" ");
		}

		// Update the breadcrumb trail to show the current sequence and percentage.
		function updateBreadcrumbs(nodeArray) {

			// Data join; key function combines name and depth (= position in sequence).
			var g = d3.select("#trail")
				.selectAll("g")
				.data(nodeArray, function(d) { return d.name + d.depth; });

			// Add breadcrumb and label for entering nodes.
			var entering = g.enter().append("svg:g");
		 
			entering.append("svg:polygon")
			.attr("points", breadcrumbPoints);

			entering.append("svg:text")
			.attr("x", (b.w + b.t) / 2)
			.attr("y", b.h / 2)
			.attr("dy", "0.35em")
			.attr("text-anchor", "middle")
			.text(function(d) { return "Level "+(d.depth+1); });

			// Set position for entering and updating nodes.
			g.attr("transform", function(d, i) {
				return "translate(" + i * (b.w + b.s) + ", 10)";
			})
			.attr("class",function(d,i){if(nodeArray.length-1==i){return "not_click_breadcrumb"}else{return "click_breadcrumb"}})
			.on("click", function(d, i) {if(nodeArray.length-1==i){}else{ zoom(nodes2[i]);updateBreadcrumbs(getAncestors(d))}});
			 
			// Remove exiting nodes.
			g.exit().remove();

			// Now move and update the percentage at the end.
			d3.select("#trail").select("#endlabel")
			.attr("x", (nodeArray.length + 0.5) * (b.w + b.s))
			.attr("y", b.h / 2)
			.attr("dy", "0.35em")
			.attr("text-anchor", "middle");

			// Make the breadcrumb trail visible, if it's hidden.
			d3.select("#trail")
			.style("visibility", "");
		}

		function group(listOfPapers, jumlahPengelompokan) {
			var paperGrouping = new Array(jumlahPengelompokan);
			if(listOfPapers.length == 3) {	
				paperGrouping[0] = new Array(2);
				paperGrouping[0] = listOfPapers[0];
				paperGrouping[0].name = listOfPapers[0].keyword;
				paperGrouping[1] = new Array(2);
				paperGrouping[1].name = "Text Categorization(2)";
				paperGrouping[1]['children'] = new Array(2);
				paperGrouping[1]['children'][0] = new Array(2);
				paperGrouping[1]['children'][0] = listOfPapers[1];
				paperGrouping[1]['children'][0].name = listOfPapers[1].keyword;
				paperGrouping[1]['children'][1] = new Array(2);
				paperGrouping[1]['children'][1] = listOfPapers[2];
				paperGrouping[1]['children'][1].name = listOfPapers[2].keyword;
			} else {                    
				for(var i = 0; i < listOfPapers.length; i++) {
					paperGrouping[i] = new Array(2);
					paperGrouping[i] = listOfPapers[i];
					paperGrouping[i].name = listOfPapers[i].keyword;
				}
			}
			return paperGrouping;
		}

		function grouping(papers, listOfSizes) {
			var paperGrouping = new Array(listOfSizes.length);
			var urutan_di_papers = 0;

			for(var i = 0; i < listOfSizes.length; i++) {
				paperGrouping[i] = new Array(listOfSizes.length);
				for(var j = 0; j < listOfSizes[i]; j++) {
					paperGrouping[i][j] = new Array(listOfSizes[i]);
					paperGrouping[i][j] = papers.children[urutan_di_papers + j];
				}
				urutan_di_papers += listOfSizes[i];
			}
			return paperGrouping;
		}

		function getChildren(papers) {
			var newPapers = new Array(papers.length);
			for(var i = 0; i < papers.length; i++) {
				if(papers[i]['id'].length == 3) {
					newPapers[i] = papers[i];
					var children = new Object();
					children = group(papers[i].children, 2);
					newPapers[i].children = children;
				}
				else if(papers[i]['id'].length > 1) {
					newPapers[i] = papers[i];
					var children = new Object();
					children = group(papers[i].children, papers[i].children.length);
					newPapers[i].children = children;
				}
				else {
					newPapers[i] = papers[i];
				}
			}
			return newPapers;
		}

		function getChildrenWithGrouping(paper) {
			return grouping(paper, paper.size);
		}
 
		$("#save_paper").click(function() {
			$("#save_map_name").click(function(){
				map_name = $('#map_name').val();
				if(map_name == '') {
					alert("Nama map harus diisi");
				}
				else {
					sumbuX = $("#sumbuX option:selected").text();
					sumbuY = $("#sumbuY option:selected").text();
					edge = $("#edge option:selected").text();
					pan = $("#mode_pan option:selected").text();
					saveData(userID,SelectedId,sumbuX,sumbuY,edge, map_name, pan);
					$( "#map_name" ).val('');
				}
			});
			$('a[href="#close"]').click(function(){
				$( "#popup-content" ).empty();
					$( "#map_name" ).val('');
			});
			$('a[href="#x"]').click(function(){
				$( "#popup-content" ).empty();
				$( "#map_name" ).val('');
			});
		});
					 
		function saveData(userID, paperID, sumbuX, sumbuY, relation, map_name, pan) {
			window.xmlhttp = getXmlHttpRequest();
			if(!window.xmlhttp)
				return;
			window.xmlhttp.open('POST', 'index.php?r=metadataPenelitian/saveData ', true);
			var query =  'userID=' + userID + '&paperID=' + paperID + '&sumbuX=' + sumbuX + '&sumbuY=' + sumbuY + '&relation=' + relation + '&map_name=' + map_name + '&mode_pan=' + pan;
			 
			window.xmlhttp.onreadystatechange = function() {
				if(window.xmlhttp.readyState == 4 && window.xmlhttp.status == 200) {
					var response = window.xmlhttp.responseText;
					if(response == '1') {
						alert("Penyimpanan berhasil");
						if(document.URL.indexOf("#") >= 0) {
							var location=document.URL.split("#");
							document.location.href = location[0] + '#close';
						}
						else {
							document.location.href=document.URL+'#close';
						}
					}
					else {
						alert("Penyimpanan gagal. Coba lagi");
					}
					//drawTablePaper(response);
				}
			};
			window.xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			window.xmlhttp.send(query);
		}
	</script>

	<script type="text/javascript">
		// if ($("#mode_zoom option:selected").text() == 'Fisheye') {
			// var zoomLevel0 = true;
			// var zoomLevel1 = false;
			// var zoomLevel2 = false;
			
			// var margin = { top: 10, right: 30, bottom: 40, left: 150 };
 
			// var width = 950 - margin.left - margin.right;
			// var height = 510 - margin.top - margin.bottom;
 
			// // Untuk mempersiapkan layout
			// var force = d3.layout.force()
			// .charge(-240)
			// .linkDistance(40)
			// .size([width, height]);
 
			// // Ambil kelas .chart lalu buat tag g dengan atribut width dan height di dalamnya
			// var svgFisheye = d3.select(".chart")
			// .append("g")
			// .attr("width", width + margin.left + margin.right)
			// .attr("height", 515)
			// .attr("transform", "translate(" + margin.left + "," + margin.top + ")");
 
			// var color = d3.scale.category20();
 
			// var fisheye = d3.fisheye.circular()
			// .radius(100);

			// ///////////////////////////////////////////
			// // Persiapan membuat garis sumbu x dan y //
			// ///////////////////////////////////////////

			// var x = d3.scale.ordinal()
			// .rangeRoundBands([0, width], .1);
	 
			// var y = d3.scale.ordinal()
			// .rangeRoundBands([height, 0], .1);

			// var xAxis = d3.svg.axis()
			// .scale(x)
			// .orient("bottom");
	 
			// var yAxis = d3.svg.axis()
			// .scale(y)
			// .orient("left");

			// svgFisheye.append("g")
			// .attr("class", "x axis")
			// .attr("transform", "translate(0, " + height + ")")
			// .call(xAxis);
	 
			// svgFisheye.append("g")
			// .attr("class", "y axis")
			// .attr("transform", "translate(0, 0)")
			// .call(yAxis);

			// ///////////////////////////////////////////////////
			// // Persiapan membuat garis sumbu x dan y selesai //
			// ///////////////////////////////////////////////////
 
			// d3.json("dummySize.json", function(data) {
				// var n = data.nodes.length;
				// force.nodes(data.nodes).links(data.links);

				// /////////////////////////////////
				// // Menentukan posisi lingkaran //
				// /////////////////////////////////
 
				// var minimum;

				// // Initialize the positions deterministically, for better results.
				// // Atur koordinat lingkaran pada sumbu x dan sumbu y
				// var posisiX, posisiY;
				
				// // Fungsi apabila dipilih parameter pada sumbu x dengan nilai "Tahun Publikasi"
				// if ($("#sumbuX option:selected").text() == 'Tahun Publikasi') {
					// for(var i = 0; i < data.nodes.length; i++) {
						// data.nodes[i].sumbu_x = parseInt(d[i].sumbu_x);
					// }
	 
					// posisiX = d3.scale.ordinal()          
					// .domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_x, b.sumbu_x)}).map(function(d) { return d.sumbu_x; }))
					// .rangeRoundBands([0, width], .1);
				// }
				 
				// // Sorting huruf
				// else {
					// for(i = 0; i < data.nodes.length; i++) {
						// data.nodes[i].sumbu_x = data.nodes[i].sumbu_x.charAt(0).toUpperCase() + data.nodes[i].sumbu_x.slice(1);
					// }
					// posisiX = d3.scale.ordinal()
					// .domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_x, b.sumbu_x)}).map(function(d) { return d.sumbu_x; }))
					// .rangeRoundBands([0, width], .1);
				// }

				// // Fungsi apabila dipilih parameter pada sumbu y dengan nilai "Tahun Publikasi"
				// if ($("#sumbuY option:selected").text() == 'Tahun Publikasi') {
					
					// // Ubah angka string menjadi angka numeric 
					// for(var i = 0; i < data.nodes.length; i++) {
						// data.nodes[i].sumbu_y = parseInt(data.nodes[i].sumbu_y);
					// }
	 
					// posisiY = d3.scale.ordinal()
					// .rangeRoundBands([height, 0], .1)
					// .domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_y, b.sumbu_y)}).map(function(d) { return d.sumbu_y; }));
				// } else {
					// for(i = 0; i < data.nodes.length; i++) {
						// data.nodes[i].sumbu_y = data.nodes[i].sumbu_y.charAt(0).toUpperCase() + data.nodes[i].sumbu_y.slice(1);
					// }
					 
					// posisiY = d3.scale.ordinal()
					// .rangeRoundBands([height, 0], .1)
					// .domain(data.nodes.sort(function(a, b) { return d3.ascending(a.sumbu_y, b.sumbu_y)}).map(function(d) { return d.sumbu_y; }));
				// }

				// // Run the layout a fixed number of times.
				// // The ideal number of times scales with graph complexity.
				// // Of course, don't run too long—you'll hang the page!
				// force.start();
				// for (var i = n; i > 0; --i) force.tick();
				// force.stop();

				// /////////////////////////////////////////
				// // Menentukan posisi lingkaran selesai //
				// /////////////////////////////////////////

				// if(posisiY.rangeBand() > posisiX.rangeBand()) {
					// minimum = posisiX.rangeBand();
				// } else {
					// minimum = posisiY.rangeBand();
				// }

				// var start;
				// if((minimum / 2) < 15) {
					// alert("Data yang dimasukkan terlalu banyak! Kurangi data");
					// if(document.URL.indexOf("#") >= 0) {
						// var location = document.URL.split("#");
						// document.location.href = location[0] + '#AddPaper';
					// } else {
						// document.location.href = document.URL + '#AddPaper';
					// }
					 
					// // start = minimum / 2 - 1;
				// } else {
					// if(d3.min(data.nodes.map(function(d) {return d.id.length; })) != d3.max(data.nodes.map(function(d) { return d.id.length; }))) {
						// start = 15;
					// } else {
						// start = minimum / 2;
					// }
				// }

				// var posisiR = d3.scale.linear()
				// .domain([d3.min(data.nodes.map(function(d) {return d.id.length; })), d3.max(data.nodes.map(function(d) { return d.id.length; }))])
				// .range([start, minimum / 2]);

				// //////////////////////////////////////
				// // Membuat garis pada sumbu x dan y //
				// //////////////////////////////////////

				// // xAxis = d3.svg.axis().scale(posisiX).orient("bottom").tickFormat(function(d) {
					// // if(d.length > minimum / 10) {
						// // svgFisheye.selectAll(".x.axis").selectAll(".tick").each(function(index) {
							// // $(this).tipsy({ 
								// // gravity: 'n', 
								// // html: true,
								// // delayIn: 1000,
								// // title: function() {
									// // return "<span style=\"font-size:12px\">" + index + "</span>";
								// // }
							// // });
						// // });
						// // d = d.substr(0, minimum / 10); return d + "..."
					// // } else {
						// // return d;
					// // }
				// // });
	 
				// // yAxis = d3.svg.axis().scale(posisiY).orient("left").tickFormat(function(d) {
					// // if(d.length > 10) {
						// // svgFisheye.selectAll(".y.axis").selectAll(".tick").each(function( index ) {
							// // $(this).tipsy({ 
								// // gravity: 'e', 
								// // html: true,
								// // delayIn: 1000,
								// // title: function() {
									// // return "<span style=\"font-size:12px\">" + index + "</span>";
								// // }
							// // });
						// // });
						 
						// // d = d.substr(0, 10); return d + "..."
					// // } else {
						// // return d;
					// // }
				// // });

				// // svgFisheye.selectAll("g.y.axis")
				// // .call(yAxis);

				// // svgFisheye.selectAll("g.x.axis")
				// // .call(xAxis);

				// // var keyword = new Array(data.nodes.length);
				// // for(i = 0; i < data.nodes.length; i++) {
					// // keyword[i] = new Array();
					// // keyword[i] = data.nodes[i].keyword[0].replace(/ /g,"\n");;
					// // data.nodes[i].keyword = [];
					// // $.merge(data.nodes[i].keyword, keyword[i]);
				// // }

				// // // Ubah label pada sumbu X dan Y
				// // if($("#sumbuY option:selected").text().indexOf(' ') >= 0) {
					// // svgFisheye.append("text")
					// // .attr("class", "sumbuYlabel")
					// // .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
					// // .attr("transform", "translate(" + -115 + "," + ((height / 2) - 15) + ")")  // text is drawn off the screen top left, move down and out and rotate
					// // .text($("#sumbuY option:selected").text().split(' ')[0]);

					// // svgFisheye.append("text")
					// // .attr("class", "sumbuYlabel")
					// // .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
					// // .attr("transform", "translate(" + -115 + "," + (height / 2) + ")")  // text is drawn off the screen top left, move down and out and rotate
					// // .text($("#sumbuY option:selected").text().split(' ')[1]);

					// // svgFisheye.append("text")
					// // .attr("class", "sumbuXlabel")
					// // .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
					// // .attr("transform", "translate(" + (width / 2) + "," + (height + 45) + ")")  // centre below axis
					// // .text($("#sumbuX option:selected").text());
				// // } else {
					// // svgFisheye.append("text")
					// // .attr("class", "sumbuYlabel")
					// // .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
					// // .attr("transform", "translate(" + -115 + "," + (height / 2) + ")")  // text is drawn off the screen top left, move down and out and rotate
					// // .text($("#sumbuY option:selected").text());
	 
					// // svgFisheye.append("text")
					// // .attr("class", "sumbuXlabel")
					// // .attr("text-anchor", "middle")  // this makes it easy to centre the text as the transform is applied to the anchor
					// // .attr("transform", "translate(" + (width / 2) + ","+(height + 45) + ")")  // centre below axis
					// // .text($("#sumbuX option:selected").text());
				// // }

				// //////////////////////////////////////////////
				// // Membuat garis pada sumbu x dan y selesai //
				// //////////////////////////////////////////////

				// //////////////////
				// // Membuat link //
				// //////////////////

				// // Hitung x asal
				// function hitungXAsal(sourcex, sourcey, targetx, targety, r) {
					// var miring = Math.sqrt(Math.pow((targetx - sourcex), 2) + Math.pow((targety - sourcey), 2));
					// return ((targetx * r - sourcex * r + miring * sourcex) / miring);
				// }
				 
				// // Hitung x tujuan
				// function hitungXTujuan(sourcex, sourcey, targetx, targety, r) {
					// var miring = Math.sqrt(Math.pow((sourcex - targetx), 2) + Math.pow((sourcey - targety), 2));
					// return ((targetx * miring - targetx * r - sourcex * miring + sourcex * r) / miring) + sourcex;
				// }

				// // Setting data untuk link, source dan targetnya ud data node
				// var rlink = new Array();
				// if(data.links.length != 0) {
					// var counter_rlink;
					// counter_rlink = 0;
					// var rlinks = new Array(data.links.length);
					// for(var i = 0; i < data.links.length; i++) {
						// var j, k, l, m;
						// j = 0; k = 0;
						 
						// var sudah_ketemu; sudah_ketemu = 0;
						// while(data.nodes.length > j && !sudah_ketemu) {
							// if(data.nodes[j].id.length == 1 && data.links[i].source != data.nodes[j].id[0]) {
								// j++;                
							// }
							// else if(data.nodes[j].id.length == 1 && data.links[i].source == data.nodes[j].id[0]) {
								// sudah_ketemu = 1;
							// }
							// else {
								// l = 0;
								 
								// while(data.nodes[j].id.length > l && data.links[i].source != data.nodes[j].id[l]) { // 3>0 && 1!=1
									// l++;
								// }
	 
								// if(data.nodes[j].id.length < l || data.links[i].source != data.nodes[j].id[l]) {
									// j++;
								// }
								// else if (data.nodes[j].id.length > l && data.links[i].source == data.nodes[j].id[l]) {
									// sudah_ketemu = 1;
								// }
							// }
						// }
						 
						// sudah_ketemu = 0;
						 
						// while(data.nodes.length > k && !sudah_ketemu) {
							// // console.log(data.nodes[k]);
							// if(data.nodes[k].id.length == 1 && data.links[i].target != data.nodes[k].id[0]) {
								// k++;
							// }
							// else if (data.nodes[k].id.length == 1 && data.links[i].target == data.nodes[k].id[0]) {
								// sudah_ketemu = 1;
							// }
							 
							// else {
								// m = 0;
								 
								// while(data.nodes[k].id.length > m && data.links[i].target != data.nodes[k].id[m]) {
									// m++;
								// }
								 
								// if(data.nodes[k].id.length < m || data.links[i].target != data.nodes[k].id[m]) {
									// k++;
								// }
								// else if(data.nodes[k].id.length > m && data.links[i].target == data.nodes[k].id[m]) {
									// sudah_ketemu = 1;
								// }
							// }
						// }
						 
						// // Untuk melist semua kemungkinan apakah source dan target berada dalam 1 level atau tidak
						// if(j < data.nodes.length && k < data.nodes.length && ((data.nodes[j].id.length == 1 && data.nodes[k].id.length == 1 && data.links[i].target == data.nodes[k].id && data.links[i].source == data.nodes[j].id) || (data.nodes[j].id.length > 1 && data.nodes[k].id.length > 1 && data.links[i].target == data.nodes[k].id[m] && data.links[i].source == data.nodes[j].id[l]) || (data.nodes[j].id.length == 1 && data.nodes[k].id.length > 1 && data.links[i].target == data.nodes[k].id[m] && data.links[i].source == data.nodes[j].id) ||(data.nodes[j].id.length > 1 && data.nodes[k].id.length == 1 && data.links[i].target == data.nodes[k].id && data.links[i].source ==data.nodes[j].id[l]))) {
							 
							// rlink[counter_rlink] = new Array();
							// rlink[counter_rlink].source = data.nodes[j];
							 
							// rlink[counter_rlink].target = data.nodes[k];
							// counter_rlink++;
						// } else {}
					// }
				// }

				// // Panah dan garis hanya akan dibuat jika linknya ada
				// if(rlink.length != 0) {
					// // Untuk membuat panah
					// var marker = svgFisheye.selectAll("g.marker").data(data.links)
						// .enter().append("marker")
						// .attr("id", function(d, i) { return i; })
						// .attr("viewBox", "0 -5 10 10")
						// .attr("refX", function(d) {
							// if((posisiY(d.target.value) == posisiY(d.source.value)) && (posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x))) {}
							 
							// if(posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x)) {
								// return 10;
							// } else {
								// return 10;
							// }           
						// })
						// .attr("refY", 0)
						// .attr("markerWidth", 6)
						// .attr("markerHeight", 6)
						// .attr("orient", "auto")
						// .append("svg:path")
						// .attr("d", "M0,-5L10,0L0,5")
						// .attr("fill", "none")
						// .attr("stroke", "black");
					 
					// // (X1, Y1) koordinat asal
					// // (X2, Y2) koordinat tujuan						 
					// var link = svgFisheye.selectAll("g.link").data(rlink)
					// .enter().append("line")
					// .attr("class", "link")
					// .attr("x1", function(d) {
						// if((posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) && (posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x))) {
							// return posisiX(d.source.sumbu_x)+ (x.rangeBand() / 180) + posisiR(d.source.id.length); 
						// }
						 
						// // Garis horizontal jika lingkaran asal ada di kiri target
						// else if ((posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) && (posisiX(d.target.sumbu_x) < posisiX(d.source.sumbu_x))) {
							// return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180) - posisiR(d.source.id.length);
						// }
						 
						// // Garis vertical
						// else if(posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) {
							// return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180);
						// }
						 
						// // Garis miring
						// else {
							// return hitungXAsal((posisiX(d.source.sumbu_x) + (x.rangeBand() / 180)),(posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)), (posisiX(d.target.sumbu_x) + (x.rangeBand() / 180)), (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180)), posisiR(d.source.id.length));
						// }
					// })
					// .attr("y1", function(d) { 
						// //garis horizontal
						// if(posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) {
							// return posisiY(d.source.sumbu_y) + (y.rangeBand() / 180);
						// }
						 
						// //garis vertical dengan lingkaran asal ada di atas target
						// else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) > posisiY(d.source.sumbu_y))) {
							// return (posisiY(d.source.sumbu_y)+ (y.rangeBand() / 180) + posisiR(d.source.id.length));
						// }
						 
						// //garis vertical dengan lingkaran asal ada di bawah target
						// else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) < posisiY(d.source.sumbu_y))) {
							// return (posisiY(d.source.sumbu_y) + (y.rangeBand() / 180) - posisiR(d.source.id.length));
						// }

						// else {
							// var miring = Math.sqrt(Math.pow(((posisiX(d.source.sumbu_x) + x.rangeBand() / 180)-(posisiX(d.target.sumbu_x) + x.rangeBand() / 180)), 2) + Math.pow(((posisiY(d.source.sumbu_y)+y.rangeBand() / 180)-(posisiY(d.target.sumbu_y) + y.rangeBand() / 180)), 2));
							// return (posisiY(d.source.sumbu_y) + y.rangeBand() / 180)-(((posisiY(d.source.sumbu_y) + y.rangeBand() / 180)-(posisiY(d.target.sumbu_y) + y.rangeBand() / 180)) * posisiR(d.source.id.length) / miring);
						// }
					// })
					// // Sama seperti diatas, hanya untuk lingkaran target
					// .attr("x2", function(d) {
						// if((posisiX(d.target.sumbu_x) > posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y))) {
							// return posisiX(d.target.sumbu_x) + (x.rangeBand() / 180) - posisiR(d.target.id.length); 
						// }
						// else if ((posisiX(d.target.sumbu_x) < posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y))) {
							// return posisiX(d.target.sumbu_x) + (x.rangeBand() / 180) + posisiR(d.target.id.length); 
						// }
						// else if(posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) {
							// return posisiX(d.source.sumbu_x) + (x.rangeBand() / 180);
						// } else {
							// return hitungXTujuan((posisiX(d.source.sumbu_x) + (x.rangeBand() / 180)), (posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)),(posisiX(d.target.sumbu_x) + (x.rangeBand() / 180)),(posisiY(d.target.sumbu_y) + (y.rangeBand() / 180)), posisiR(d.target.id.length));
						// }   
					// })
					// .attr("y2", function(d) {
						// if(posisiY(d.target.sumbu_y) == posisiY(d.source.sumbu_y)) {
							// return posisiY(d.target.sumbu_y) + (y.rangeBand() / 180);
						// }
						// else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) > posisiY(d.source.sumbu_y))) {
							// return (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180) - posisiR(d.target.id.length));
						// }
						// else if((posisiX(d.target.sumbu_x) == posisiX(d.source.sumbu_x)) && (posisiY(d.target.sumbu_y) < posisiY(d.source.sumbu_y))) {
							// return (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180) + posisiR(d.target.id.length));
						// } else {
							// var miring = Math.sqrt(Math.pow(((posisiX(d.source.sumbu_x) + x.rangeBand() / 180) - (posisiX(d.target.sumbu_x) + x.rangeBand() / 180)), 2) + Math.pow(((posisiY(d.source.sumbu_y) + y.rangeBand() / 180) - (posisiY(d.target.sumbu_y) + y.rangeBand() / 180)), 2));
							// return posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)-(((miring - posisiR(d.target.id.length)) * ((posisiY(d.source.sumbu_y) + (y.rangeBand() / 180)) - (posisiY(d.target.sumbu_y) + (y.rangeBand() / 180))) / miring));
						// }
					// })
					// .attr("marker-end", function(d, i) { return "url(#" + i + ")"; });
				// }

				// //////////////////////////
				// // Membuat link selesai //
				// //////////////////////////

				

				// ////////////////////////////////
				// // Membuat representasi paper //
				// ////////////////////////////////

				// var elemParent = svgFisheye.selectAll("g.circle")
				// .data(data.nodes);

				// // Buat tag g dengan kelas lingkaran
				// var elemParentEnter = elemParent.enter()
				// .append("g")
				// .attr("class", "paperParent");
				
				// data.nodes.forEach(function(d, i) {
					// d.x = posisiX(d.sumbu_x)
					// + (posisiX.rangeBand() / 2);
					// d.y = posisiY(d.sumbu_y)
					// + (posisiY.rangeBand() / 2);
				// });

				// // Buat tag circle di dalam tag lingkaran dengan class nodeParent
				// var node = elemParentEnter.append("circle")
				// .attr("class", "nodeParent")
				// .attr("id", function(d, i) {
					// return "circleParent-" + i;  // id tiap circle
				// })
				// .attr("cx", function(d, i) { return d.x; }) // Koordinat lingkaran pada sumbu x
				// .attr("cy", function(d, i) { return d.y;}) // Koordinat lingkaran pada sumbu y
				// .attr("r", function(d, i) {
					// // Mengatur jari-jari lingkaran
					// if(d.size.length == 1) {
						// if(d.size[0] == 1) {
							// return 15;
						// }
					// } else {
						// var realSize = 0;
						// var totalSize = 0;

						// for(var iterator = 0; iterator < d.size.length; iterator++) {
							// realSize += d.size[iterator];
							// totalSize += realSize;
						// }

						// if (realSize == 2) {
							// return 20;
						// } else if (realSize == 3) {
							// return 25;
						// } else if (realSize == 4) {
							// return 30;
						// }
					// }
				// })
				// .style("fill", "#FFC2AD");

				// // .style("fill", function(d, i) {
				// // 	if(d.size.length == 1) {
				// // 		return "#6C9ECA";
				// // 	} else {
				// // 		if(d.children.length == 2) {
				// // 			return "#447DB1";
				// // 		} else if(d.children.length == 3) {
				// // 			return "#2868A2";
				// // 		} else if(d.children.length == 4) {
				// // 			return "#0F528E";
				// // 		} 
				// // 	}
				// // });
				// // .call(force.drag);

				// // Buat tag text di dalam tag lingkaran dengan class label
				// var label = elemParentEnter.append("text")
				// .attr("class", "labelParent")
				// .attr("font-family", "sans-serif") // Jenis font
				// .attr("font-size", "14px") // Ukuran font
				// .attr("text-anchor", "middle")
				// .attr("x", function(d, i) { return d.x; }) // Koordinat label pada sumbu x
				// .attr("y", function(d, i) { return d.y + 5; }) // Koordinat label pada sumbu y
				// .text(function(d) {
					// // Isi label
					// return d.children.length;
				// });

				// // Hover untuk node dengan jumlah data 1
				// var g1 = svgFisheye.selectAll("g.paperParent").data(data.nodes);

				// $("svg circle").each(function(d, i) {
					// if(g1[0][d].__data__.children.length == 1) {
						// $(g1[0][d]).tipsy({ 
							// gravity: 'w', 
							// html: true,
							// delayIn: 1000,
							// title: function() {				
								// return "<span style=\"font-size:12px\">" + this.__data__.children[0].judul + "</span><br>Peneliti : " + this.__data__.children[0].peneliti;
							// }
						// });
					// } else {
						// $(g1[0][d]).tipsy({ 
							// gravity: 'w', 
							// html: true,
							// delayIn: 1000,
							// title: function() {
								// var realKeyword = "";

								// for(var iterator = 0; iterator < g1[0][d].__data__.keyword.length; iterator ++) {
									// realKeyword += g1[0][d].__data__.keyword[iterator];
								// }

								// return "<span style=\"font-size:12px\">" + realKeyword + "</span>";
							// }
						// });
					// }
				// });

				// elemParentEnter.on("mouseover", function(d, i) {
					// fisheye.focus(d3.mouse(this));
 
					// // Fisheye untuk setiap node
					// node.each(function(d) { d.fisheye = fisheye(d); })
					// .attr("r", function(d) { return d.fisheye.z * 15 });
					
					// // Fisheye untuk setiap label
					// label.each(function(d) { d.fisheye = fisheye(d); })
					// .attr("r", function(d) { return d.fisheye.z * 15});

					// // Fisheye untuk setiap garis
					// link.attr("x1", function(d) { return d.source.fisheye.x - 10; })
					// .attr("y1", function(d) { return d.source.fisheye.y + 10; })
					// .attr("x2", function(d) { return d.target.fisheye.x + 10; })
					// .attr("y2", function(d) { return d.target.fisheye.y - 10; });
				// });

				// elemParentEnter.on("click", function(d, i) {
					// if(d.children.length == 1) {
						// if(document.URL.indexOf("#") >= 0) {
							// var location = document.URL.split("#");
							// document.location.href = location[0] + '#ShowDetailPaper';
						// } else {
							// document.location.href = document.URL + '#ShowDetailPaper';
						// }

						// var maxKey = 0;
						// var maxValue = 0;

						// $.each(d.children[0], function(key, value) {
							// if(maxKey < key.length) { maxKey = key.length; }
							// if(maxValue < value.length) { maxValue = value.length; }
						// });

						// $.each(d.children[0], function(key, value) {
							// if(key == "id" || key == "creater") {}
							// else {
								// $( "#popup-content" ).append( "<li><label style=\"width:" + maxKey * 8 + "px\">" + key + "</label><label style=\"width:10px\"> : </label></li>" );
								// if(value == "") {}
								// else {
									// $( "#popup-content" ).append('<span class="detail-content">' + value + '</span>');
								// }
							// }
						// });

						// $('a[href="#close"]').click(function() {
							// $( "#popup-content" ).empty();
							// $( "#map_name" ).val('');
						// });

						// $('a[href="#x"]').click(function() {
							// $( "#popup-content" ).empty();
							// $( "#map_name" ).val('');
						// });                 
					// } else { // d.children.length > 1
						// // Periksa apakah boolean bernilai true / false untuk menentukan zoom level mana yang dipakai
						// // Menonaktifkan zoom pada level 0 dan mengaktifkan zoom pada level 1
						// if (zoomLevel0 == true) {
							// elemParentEnter.on("mouseover", function(d, i) {});

							// var clickedParent = "circleParent-" + i;

							// var circleParent = document.getElementById(clickedParent);
							// circleParent.classList.add("circleStroke");

							// zoomLevel0 = false;
							// zoomLevel1 = true;
							// zoomLevel2 = false;

							// var dataChild = getChildrenWithGrouping(d);

							// if(dataChild.length == 2) {
								// dataChild.forEach(function(p, ii) {
									// if(ii == 0) {
										// p.x = d.x;
										// p.y = d.y - 75;
									// } else if (ii == 1) {
										// p.x = d.x + 75;
										// p.y = d.y;
									// }
								// });
							// }

							// if(dataChild.length == 3) {
								// dataChild.forEach(function(p, ii) {
									// if(ii == 0) {
										// p.x = d.x - 75;
										// p.y = d.y;
									// } else if (ii == 1) {
										// p.x = d.x;
										// p.y = d.y - 75;
									// } else if (ii == 2) {
										// p.x = d.x + 75;
										// p.y = d.y;
									// }
								// });
							// }

							// if(dataChild.length == 4) {
								// dataChild.forEach(function(p, ii) {
									// if(ii == 0) {
										// p.x = d.x;
										// p.y = d.y - 75;
									// } else if (ii == 1) {
										// p.x = d.x + 75;
										// p.y = d.y;
									// } else if (ii == 2) {
										// p.x = d.x;
										// p.y = d.y + 75;
									// } else if (ii == 3) {
										// p.x = d.x - 75;
										// p.y = d.y;
									// }
								// });
							// }
							
							// // Ubah warna paperParent
							// node.style("fill", "#DDDDDD");

							// var elemChild = svgFisheye.selectAll("g.circle")
							// .data(dataChild);

							// var elemChildEnter = elemChild.enter()
							// .append("g")
							// .attr("class", "paperChild");

							// var nodeChild = elemChildEnter.append("circle")
							// .attr("class", "nodeChild")
							// .attr("id", function(p, ii) {
								// return "circleChild-" + ii;  // id tiap circle
							// })
							// .attr("cx", function(p, ii) { return p.x; })
							// .attr("cy", function(p, ii) { return p.y; })
							// .attr("r", function(p, ii) { return 15; })
							// .style("fill", "#FFC2AD")
							// // .style("fill", function(p, ii) {
							// // 	var lastIndex = p.length - 1;

							// // 	if(p[lastIndex] == null) {
							// // 		return "#6C9ECA";
							// // 	} else if(p.length == 2) {
							// // 		return "#447DB1";
							// // 	}
							// // })
							// .style("stroke-width", "0px");
							// // .call(force.drag);

							// var labelChild = elemChildEnter.append("text")
							// .attr("class", "label")
							// .attr("font-family", "sans-serif")
							// .attr("font-size", "14px")
							// .attr("text-anchor", "middle")
							// .attr("x", function(p, ii) {
								// return p.x;
							// })
							// .attr("y", function(p, ii) {
								// return p.y + 5;
							// })
							// .text(function(p, ii){ return d.size[ii] });

							// // Hover untuk node dengan jumlah data 1
							// var g2 = svgFisheye.selectAll("g.paperChild").data(dataChild);

							// $("svg circle").each(function(p, ii) {
								// $(g2[0][p]).tipsy({ 
									// gravity: 'w',
									// html: true,
									// delayIn: 1000,
									// title: function() {
										// var lastIndex = g2[0][p].__data__.length - 1;

										// if(g2[0][p].__data__[lastIndex] == null) {
											// return "<span style=\"font-size:12px\">" + g2[0][p].__data__[0].judul + "</span><br>Peneliti : " + g2[0][p].__data__[0].peneliti;
										// } else {
											// return "<span style=\"font-size:12px\">" + g2[0][p].__data__[0].keyword + "</span>";
										// }
									// }
								// });
							// });

							// elemChildEnter.on("mouseover", function(p, ii) {
								// fisheye.focus(d3.mouse(this));

								// // Fisheye untuk setiap node
								// nodeChild.each(function(p) { p.fisheye = fisheye(p); })
								// .attr("r", function(p) { return p.fisheye.z * 15; });

								// // Fisheye untuk setiap label
								// labelChild.each(function(p) { p.fisheye = fisheye(p); })
								// .attr("r", function(p) { return p.fisheye.z * 15; });
							// });

							// elemChildEnter.on("click", function(p, i) {
								// if(d.size[i] == 1) {
									// if(document.URL.indexOf("#") >= 0) {
										// var location = document.URL.split("#");
										// document.location.href = location[0] + '#ShowDetailPaper';
									// } else {
										// document.location.href = document.URL + '#ShowDetailPaper';
									// }

									// var maxKeyChildren = 0;
									// var maxValueChildren = 0;

									// $.each(p[0], function(key, value) {
										// if(maxKeyChildren < key.length) { maxKeyChildren = key.length; }
										// if(maxValueChildren < value.length) { maxValueChildren = value.length; }
									// });

									// $.each(p[0], function(keyChildren, valueChildren) {
										// if(valueChildren == "" || keyChildren == "x" || keyChildren == "y" || keyChildren == "px" || keyChildren == "py" || keyChildren == "fisheye" || keyChildren == "id") {}
										// else {
											// $( "#popup-content" ).append( "<li><label style=\"width:" + maxKeyChildren * 8 + "px\">" + keyChildren + "</label><label style=\"width:10px\"> : </label></li>" );
											// $( "#popup-content" ).append('<span class="detail-content">' + valueChildren + '</span>');
										// }
									// });

									// $('a[href="#close"]').click(function() {
										// $( "#popup-content" ).empty();
										// $( "#map_name" ).val('');
									// });

									// $('a[href="#x"]').click(function() {
										// $( "#popup-content" ).empty();
										// $( "#map_name" ).val('');
									// });
								// } else {
									// if(zoomLevel1 == true) {
										// zoomLevel0 = false;
										// zoomLevel1 = false;
										// zoomLevel2 = true;

										// elemParentEnter.on("mouseover", function(d, i) {});
										// elemChildEnter.on("mouseover", function(p, i) {});

										// var clickedChild = "circleChild-" + i;

										// var circleChild = document.getElementById(clickedChild);
										// circleChild.classList.add("circleStroke2");

										// // Ubah warna paperParent
										// node.style("fill", "#DDDDDD");

										// // Ubah warna paperChild
										// nodeChild.style("fill", "#DDDDDD");

										// var dataGrandChild = p;

										// dataGrandChild.forEach(function(q, i) {
											// if(i == 0) {
												// q.x = p.x;
												// q.y = p.y - 75;
											// } else if (i == 1) {
												// q.x = p.x + 75;
												// q.y = p.y;
											// }
										// });

										// var elemGrandChild = svgFisheye.selectAll("g.circle")
										// .data(dataGrandChild);

										// var elemGrandChildEnter = elemGrandChild.enter()
										// .append("g")
										// .attr("class", "paperGrandChild");

										// var nodeGrandChild = elemGrandChildEnter.append("circle")
										// .attr("class", "nodeGrandChild")
										// .attr("id", function(q, i) {
											// return "circleGrandChild-" + i;  // id tiap circle
										// })
										// .attr("cx", function(q, i) { return q.x; })
										// .attr("cy", function(q, i) { return q.y; })
										// .attr("r", function(q, i) { return 15; })
										// .style("fill", "#FFC2AD")
										// .style("stroke-width", "0px");
										// // .call(force.drag);

										// var labelGrandChild = elemGrandChildEnter.append("text")
										// .attr("class", "label")
										// .attr("font-family", "sans-serif")
										// .attr("font-size", "14px")
										// .attr("text-anchor", "middle")
										// .attr("x", function(q, i) {
											// return q.x;
										// })
										// .attr("y", function(q, i) {
											// return q.y + 5;
										// })
										// .text("1");

										// // Hover untuk node dengan jumlah data 1
										// var g3 = svgFisheye.selectAll("g.paperGrandChild").data(dataGrandChild);

										// $("svg circle").each(function(q, i) {
											// $(g3[0][q]).tipsy({ 
												// gravity: 'w', 
												// html: true,
												// delayIn: 1000,
												// title: function() {
													// return "<span style=\"font-size:12px\">" + g3[0][q].__data__.judul + "</span><br>Peneliti : " + g3[0][q].__data__.peneliti;
												// }
											// });
										// });

										// elemGrandChildEnter.on("mouseover", function(q, i) {
											// fisheye.focus(d3.mouse(this));

											// // Fisheye untuk setiap node
											// nodeGrandChild.each(function(q) { q.fisheye = fisheye(q); })
											// .attr("r", function(q) { return q.fisheye.z * 15; });

											// // Fisheye untuk setiap label
											// labelGrandChild.each(function(q) { q.fisheye = fisheye(q); })
											// .attr("r", function(q) { return q.fisheye.z * 15; });
										// });

										// elemGrandChildEnter.on("click", function(q, i) {
											// // if(q.length == 1) {
												// if(document.URL.indexOf("#") >= 0) {
													// var location = document.URL.split("#");
													// document.location.href = location[0] + '#ShowDetailPaper';
												// } else {
													// document.location.href = document.URL + '#ShowDetailPaper';
												// }

												// var maxKey = 0;
												// var maxValue = 0;

												// $.each(q, function(key, value) {
												 // if(maxKey < key.length) { maxKey = key.length; }
												 // if(maxValue < value.length) { maxValue = value.length; }
												// });

												// $.each(q, function(key, value) {
													// if(key == "id" || key == "creater") {}
													// else {
														// $( "#popup-content" ).append( "<li><label style=\"width:" + maxKey * 8 + "px\">" + key + "</label><label style=\"width:10px\"> : </label></li>" );
														// if(value == "") {}
														// else {
															// $( "#popup-content" ).append('<span class="detail-content">' + value + '</span>');
														// }
													// }
												// });

												// $('a[href="#close"]').click(function() {
													// $( "#popup-content" ).empty();
													// $( "#map_name" ).val('');
												// });

												// $('a[href="#x"]').click(function() {
													// $( "#popup-content" ).empty();
													// $( "#map_name" ).val('');
												// });
											// // }
										// });
									// }

									// // Menonaktifkan zoom pada level 2 dan mengaktifkan zoom pada level 1
									// else if(zoomLevel1 == false) {

										// circleChild = document.getElementsByClassName("circleStroke2");
										// circleChild[0].classList.remove("circleStroke2");

										// elemChildEnter.on("mouseover", function(p, i) {
											// $(".paperGrandChild").remove();

											// node.style("fill", "#DDDDDD");

											// nodeChild.style("fill", "#FFC2AD");

											// // nodeChild.style("fill", function(p, i) {
											// // 	var lastIndex = p.length - 1;

											// // 	if(p[lastIndex] == null) {
											// // 		return "#6C9ECA";
											// // 	} else if(p.length == 2) {
											// // 		return "#447DB1";
											// // 	}
											// // });

											// fisheye.focus(d3.mouse(this));

											// // Fisheye untuk setiap node
											// nodeChild.each(function(p) { p.fisheye = fisheye(p); })
											// .attr("r", function(p) { return p.fisheye.z * 15});

											// // Fisheye untuk setiap label
											// labelChild.each(function(p) { p.fisheye = fisheye(p); })
											// .attr("r", function(p) { return p.fisheye.z * 15});
										// });

										// zoomLevel0 = false;
										// zoomLevel1 = true;
										// zoomLevel2 = false;
									// }
								// }
							// });
						// }

						// // Menonaktifkan zoom pada level 1 dan mengaktifkan zoom pada level 0
						// else if (zoomLevel0 == false) {

							// circleParent = document.getElementsByClassName("circleStroke");
							// circleParent[0].classList.remove("circleStroke");

							// elemParentEnter.on("mouseover", function(d, i) {
								// $(".paperChild").remove();
								// $(".paperGrandChild").remove();

								// node.style("fill", "#FFC2AD");

								// // node.style("fill", function(d, i) {
								// // 	if(d.size.length == 1) {
								// // 		return "#6C9ECA";
								// // 	} else {
								// // 		if(d.children.length == 2) {
								// // 			return "#447DB1";
								// // 		} else if(d.children.length == 3) {
								// // 			return "#2868A2";
								// // 		} else if(d.children.length == 4) {
								// // 			return "#0F528E";
								// // 		} 
								// // 	}
								// // });

								// fisheye.focus(d3.mouse(this));

								// // Fisheye untuk setiap node
								// node.each(function(d) { d.fisheye = fisheye(d); })
								// // .attr("cx", function(d) { return d.fisheye.x; })
								// // .attr("cy", function(d) { return d.fisheye.y; })
								// .attr("r", function(d) { return d.fisheye.z * 15});

								// // Fisheye untuk setiap label
								// label.each(function(d) { d.fisheye = fisheye(d); })
								// // .attr("x", function(d) { return d.fisheye.x; })
								// // .attr("y", function(d) { return d.fisheye.y; })
								// .attr("r", function(d) { return d.fisheye.z * 15});

								// // Fisheye untuk setiap garis
								// link.attr("x1", function(d) { return d.source.fisheye.x - 10; })
								// .attr("y1", function(d) { return d.source.fisheye.y + 10; })
								// .attr("x2", function(d) { return d.target.fisheye.x + 10; })
								// .attr("y2", function(d) { return d.target.fisheye.y - 10; });
							// });

							// zoomLevel0 = true;
							// zoomLevel1 = false;
							// zoomLevel2 = false;
						// }
					// }
				// });
				// ////////////////////////////////////////
				// // Membuat representasi paper selesai //
				// ////////////////////////////////////////
			// });
		// }
	</script>

	<!-- popup form #1 -->
	<a href="#x" class="overlay" id="AddPaper"></a>
	<div class="popup">
		<div class="content_popup">
			<h5>Catatan : Jumlah paper yang dapat divisualisasikan maksimal <strong>21</strong> paper</h5>
			<div class="LeftPopUp" style="float:left">
				<h2>Unselected Paper</h2>
				<!--<p>Please enter your login and password here</p>-->
				<table cellpadding="0" cellspacing="0" border="0" id="TableAddPaper">
					<thead>
						<tr>
							<th>Judul</th>
							<th>Peneliti</th>
						</tr>
					</thead>
				</table>
			</div>

			<div style="top:50%; position:absolute;left:48%">
				<button type="button" id="rightButton" class="btn btn-default btn-sm">
					<span class="glyphicon glyphicon-chevron-right"></span>
				</button>
				<br/>
				<button type="button" id="leftButton" class="btn btn-default btn-sm" style="margin-top:10px">
					<span class="glyphicon glyphicon-chevron-left"></span>
				</button>
			</div>

			<div class="RightPopUp" style="float:right">
				<h2 id="SelectedPaper">Selected Paper</h2>
				<table cellpadding="0" cellspacing="0" border="0" id="AddedPaper">
					<thead>
						<tr>
							<th>Judul</th>
							<th>Peneliti</th>
						</tr>
					</thead>

					<tbody id="SelectedRow"></tbody>
				</table>
			</div>
		</div>

		<button id="SaveButton" class="button"style="float:right;margin-right:10px;width:auto">Simpan</button>
		<a class="close" href="#close" id="Close"></a>
	</div>

	<!-- popup form #2 -->
	<a href="#x" class="overlay" id="ShowDetailPaper"></a>
	<div class="popup" style ="width:800px;">
		<h2>Detail Paper</h2>
		<div id="popup-content"></div>
		<a class="close" href="#close" id="closeDetail"></a>
	</div>

	<a href="#x" class="overlay" id="SavePaper"></a>
	<div class="popup" style ="width:300px;">
		<h2>Simpan Peta</h2>
		<div style="margin-top:20px">
			<label>Nama Peta : </label>
			<input type="text" class="saveInput" id="map_name"></input>
			<div>
				<button class="button" id="save_map_name" style="width:70px; float:right; margin-top:20px; margin-right:10px">Simpan</button>
			</div>
		</div>
		<a class="close" href="#close" id="closeDetail"></a>
	</div>
</body>