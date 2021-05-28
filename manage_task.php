<?php 
include('db_connect.php');
if(isset($_GET['id'])){
	$task = $conn->query("SELECT * FROM sub_tasks where id =".$_GET['id']);
	foreach($task->fetch_array() as $k =>$v){
		$meta[$k] = $v;
	}
}
?>
<div class="container-fluid">
	
	<form action="" id="manage_task">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="" class="control-label">Catatan</label>
			<textarea name="note" id="" cols="30" rows="10" class="form-control"><?php echo isset($meta['note']) ? $meta['note'] :'' ?></textarea>
		</div>
	</form>
</div>
<script>
	$('#approved').click(function(e){
		
		e.preventDefault();
		start_load()
		$.ajax({
			url:'ajax.php?action=approve_task',
			method:'POST',
			data:$('#manage_task').serialize(),
			success:function(resp){
				console.log(resp);
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