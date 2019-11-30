<!DOCTYPE html>
<html>
<head>
	<title>Datos tema 1</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
</head>
<body>
<?php
$file=(!empty($_FILES["archivo"])) ? $_FILES["archivo"] : array();
$csv="";
$json="";
if(!empty($file)){
	$data=file_get_contents($file["tmp_name"]);
	$data=preg_replace('/ +/', "\t", $data);
	$data=explode("\n",$data);
	$header=array_shift($data);
	$header=array_values(array_filter(explode("\t",$header),function($v){ return $v!=""; }));
	array_push($header, "Media Calculada");
	array_push($header, "Desviacion");
	$headerArr=$header;
	$header=implode(",", $header);

	$data=str_replace(",", ".", $data);
	$data=str_replace("º", "", $data);
	$json=array();
	foreach ($data as $i => $v) {
		$temps=$data[$i]=explode("\t",$v);
		$temps=array_filter($temps,function($k){
			return ($k > 1 and $k < 14);
		},ARRAY_FILTER_USE_KEY);
		$mediaOld=$data[$i][14];
		$media=number_format(array_sum($temps)/count($temps),1);
		$dev=number_format(($mediaOld-$media),1);
		array_push($data[$i],$media); # No. de cifras significativas = 1
		array_push($data[$i],$dev); # No. de cifras significativas = 1
		array_walk($data[$i],function(&$v,$i){
			if($i<14 and $i > 2){
				$v=$v."°";
			}
		});
		foreach ($headerArr as $colId => $col) {
			$json[$i][$col]=$data[$i][$colId];
		}
		$data[$i]=implode(",", $data[$i]);
	}
	$json=json_encode($json,JSON_PRETTY_PRINT);
	file_put_contents("tema1.csv", $header."\n".implode("\n",$data)."\n");
	file_put_contents("tema1.json", $json);
	$csv='<a href="tema1.csv" target="_blank">Descargar CSV</a>';
	$json='<a href="tema1.json" target="_blank">Descargar JSON</a>';
}
?>
<section class="container">
	<div class="row">
		<h1>Métodos de Captura y Almacenamiento de los Datos(MEXAVDM) - Noviembre2019 | tema 1 - actividad</h1>
		<form method="post" enctype="multipart/form-data">
			<div class="form-group">
				<label>Archivo</label>
				<input class="" type="file" name="archivo">
			</div>
			<div align="right">
				<input class="btn btn-success" type="submit" value="Procesar">
			</div>
		</form>
	</div>
	<div class="row">
		<h2 class="col-12">Resultado:</h2>
		<div id="resultado">
			<?php echo $csv; ?>
			<?php echo $json; ?>
		</div>
	</div>
</section>
</body>
</html>