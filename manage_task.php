<?php 
include('db_connect.php');
if(isset($_GET['id'])){
	$task = $conn->query("SELECT * FROM tasks where id =".$_GET['id']);
	foreach($task->fetch_array() as $k =>$v){
		$meta[$k] = $v;
	}
}
?>
<div class="container-fluid">
	
	<form action="" id="manage-task">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="" class="control-label">Catatan</label>
			<textarea name="description" id="" cols="30" rows="10" class="form-control"><?php echo isset($meta['note']) ? $meta['note'] :'' ?></textarea>
		</div>
	</form>
</div>
<script>
	$('#manage-task').submit(function(e){
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=save_user',
			method:'POST',
			data:$(this).serialize(),
			success:function(resp){
				if(resp ==1){
					alert_toast("Data successfully saved",'success')
					setTimeout(function(){
						location.reload()
					},1500)
				}
			}
		})
	})
</script>