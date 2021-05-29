<style>
	.logo {
    margin: auto;
    font-size: 20px;
    background: white;
    padding: 5px 11px;
    border-radius: 50% 50%;
    color: #000000b3;
}

.notification {
  /* background-color: #555; */
  color: white;
  text-decoration: none;
  /* padding: 5px 16px; */
  position: relative;
  display: inline-block;
  border-radius: 2px;
}

a:hover {
  color: #F8F9FA;
}

.notification .badge {
  /* position: absolute; */
  /* top: -10px; */
  /* right: -10px; */
  /* padding: 5px 10px; */
  /* border-radius: 50%; */
  background: red;
  color: white;
}
</style>

<nav class="navbar navbar-dark bg-success fixed-top " style="padding:0;">
  <div class="container-fluid mt-2 mb-2">
  	<div class="col-lg-12">
  		<div class="col-md-1 float-left" style="display: flex;">
  			<img width="120" src="assets/itlogo2.png">
  		</div>
	  	<div class="col-md-2 mt-2 float-right">
        <a href="index.php?page=notification" class="notification">
          <span><i class="fa fa-bell"></i> Notification</span>
          <span class="badge notif-count">3</span>
        </a>
	  		<a class="text-light" href="ajax.php?action=logout"><?php echo $_SESSION['login_name'] ?> <i class="fa fa-power-off"></i></a>
	    </div>
    </div>
  </div>
  
</nav>

<script>
	
  $.ajax({
			url:'ajax.php?action=count_notif',
			method:'GET',
			success:function(resp){
				$('.notif-count').html(resp);
			}
		})

	
</script>