<?php 
	$path = ossn_route()->www . 'sfavicons/';
	if(!file_exists($path . 'generated')){
?>
<div>
	<label><?php echo ossn_print('favicon:generator:logo:info');?></label>
    <p><?php echo ossn_print('favicon:generator:logo:info:2');?></p>
    <input class="form-control" type="file" name="logo" />
</div>
<div class="mt-3">
	<label><?php echo ossn_print('favicon:generator:sw');?></label>
    <p><?php echo ossn_print('favicon:generator:sw:info');?> </p>
    <?php 
		echo ossn_plugin_view('input/dropdown', array(
					'name' => 'service_worker',
					'placeholder' => ossn_print('favicons:generator:select'),
					'options' => array(
					  'enabled' => ossn_print('favicons:generator:enabled'),
					  'disabled' => ossn_print('favicons:generator:disabled'),
					 ),
		)); 
		?>
</div>
<div class="mt-3">
	<input type="submit" value="<?php echo ossn_print('save');?>" class="btn btn-sm btn-success" />
</div>
<?php } else { ?>

<div class="alert alert-info text-center">
	<div><?php echo ossn_print('favicon:generator:reset:info');?></div>
    <div class="mt-2"> 
    	<a href="<?php echo ossn_site_url("action/favicon_generator/reset", true);?>" class="btn btn-info btn-sm text-white"><?php echo ossn_print('favicon:generator:reset');?></a>
    </div>
</div>
<div class="icons-list-files offset-md-2 mt-3">
<?php
	$files = array_diff(scandir($path), array('.', '..', 'head.html', 'generated'));
	foreach($files  as $item){
		if($item == 'sw.js'){
			$item = 'Service Worker';	
		}
		echo "<li><i class='fa fa-check'></i> <span>{$item}</span></li>"	;
	}
?>
</div>
<style>
.icons-list-files li i {
	color: green;
    font-weight: bold;
    float: left;
    margin-top: 4px;
}
.icons-list-files li {
	display:flex;	
}
.icons-list-files {
    column-count: 3;
</style>
<?php } ?>