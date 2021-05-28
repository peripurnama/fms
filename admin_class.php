<?php



session_start();
Class Action {
	private $db;

	public function __construct() {
		ob_start();
   	include 'db_connect.php';
	//include 'send_notification.php';
    
    $this->db = $conn;
	}
	function __destruct() {
	    $this->db->close();
	    ob_end_flush();
	}

	function login(){
		extract($_POST);
		$qry = $this->db->query("SELECT * FROM users where username = '".$username."' ");
		$row = $qry -> fetch_array(MYSQLI_ASSOC);
		// printf ("%s", $qry);
		if (is_array($row))
		{
			// printf ("%s", "cek");
			if (password_verify($password, $row['password']))
			{
				foreach ($row as $key => $value) {
				
					if($key != 'fcm_token' && $key != 'passwors' && !is_numeric($key)) {
						//printf ("%s (%s)", $key, $value);
						$_SESSION['login_'.$key] = $value;
					}
						
				}
				return 1;
			} else {
				return 2;
			}
		}
		
		/*
		if($qry->num_rows > 0){
			foreach ($qry->fetch_array() as $key => $value) {
				
				if($key != 'passwors' && !is_numeric($key))
					printf ("%s = (%s)", $key, $value);
					$_SESSION['login_'.$key] = $value;
			}
			return 1;
		}else{
			return 2;
		}
		*/
		
	}
	function logout(){
		session_destroy();
		foreach ($_SESSION as $key => $value) {
			unset($_SESSION[$key]);
		}
		header("location:login.php");
	}

	function save_folder(){
		extract($_POST);
		$data = " name ='".$name."' ";
		$data .= ", parent_id ='".$parent_id."' ";
		if(empty($id)){
			$data .= ", user_id ='".$_SESSION['login_id']."' ";
			
			$check = $this->db->query("SELECT * FROM folders where user_id ='".$_SESSION['login_id']."' and name  ='".$name."'")->num_rows;
			if($check > 0){
				return json_encode(array('status'=>2,'msg'=> 'Folder name already exist'));
			}else{
				$save = $this->db->query("INSERT INTO folders set ".$data);
				if($save)
				return json_encode(array('status'=>1));
			}
		}else{
			$check = $this->db->query("SELECT * FROM folders where user_id ='".$_SESSION['login_id']."' and name  ='".$name."' and id !=".$id)->num_rows;
			if($check > 0){
				return json_encode(array('status'=>2,'msg'=> 'Folder name already exist'));
			}else{
				$save = $this->db->query("UPDATE folders set ".$data." where id =".$id);
				if($save)
				return json_encode(array('status'=>1));
			}

		}
	}

	function delete_folder(){
		extract($_POST);
		$delete = $this->db->query("DELETE FROM folders where id =".$id);
		if($delete)
			echo 1;
	}
	function delete_file(){
		extract($_POST);
		$path = $this->db->query("SELECT file_path from files where id=".$id)->fetch_array()['file_path'];
		$delete = $this->db->query("DELETE FROM files where id =".$id);
		if($delete){
					unlink('assets/uploads/'.$path);
					return 1;
				}
	}

	function save_files(){
		extract($_POST);
		if(empty($id)){
		if($_FILES['upload']['tmp_name'] != ''){
					$fname = strtotime(date('y-m-d H:i')).'_'.$_FILES['upload']['name'];
					$move = move_uploaded_file($_FILES['upload']['tmp_name'],'assets/uploads/'. $fname);
		
					if($move){
						$file = $_FILES['upload']['name'];
						$file = explode('.',$file);
						$chk = $this->db->query("SELECT * FROM files where SUBSTRING_INDEX(name,' ||',1) = '".$file[0]."' and folder_id = '".$folder_id."' and file_type='".$file[1]."' ");
						if($chk->num_rows > 0){
							$file[0] = $file[0] .' ||'.($chk->num_rows);
						}
						$data = " name = '".$file[0]."' ";
						$data .= ", folder_id = '".$folder_id."' ";
						$data .= ", description = '".$description."' ";
						$data .= ", user_id = '".$_SESSION['login_id']."' ";
						$data .= ", file_type = '".$file[1]."' ";
						$data .= ", file_path = '".$fname."' ";
						$data .= ", status = 'PENDING' ";
						if(isset($is_public) && $is_public == 'on')
						$data .= ", is_public = 1 ";
						else
						$data .= ", is_public = 0 ";

						$save = $this->db->query("INSERT INTO files set ".$data);
						$last_id = $this->db->insert_id;
						// printf ("%s -> (%s)", $save, $last_id);
						$task_colums = " (status, file_id, note, user_id, status_tracking)";
						$task_values = " ('PENDING', '".$last_id."', 'Menunggu Persetujuan', '".$_SESSION['login_id']."', 'Menunggu Approve Admin')";
						$save_task = $this->db->query("INSERT INTO tasks ".$task_colums." VALUES " .$task_values);

						$task_last_id = $this->db->insert_id;
						// printf ("%s -> (%s)", $save_task, $task_last_id);
						$sub_task_colums = " (status, task_id, note, user_type)";
						$sub_task_values = " ('PENDING', '".$task_last_id."', 'Menunggu Persetujuan', 1)";
						$save_sub_task = $this->db->query("INSERT INTO sub_tasks ".$sub_task_colums." VALUES " .$sub_task_values);

						$url = "https://fcm.googleapis.com/fcm/send";
						/** 
						* Firebase Console -> Select Projects From Top Naviagation 
						*      -> Left Side bar -> Project Overview -> Project Settings
						*      -> General -> Scroll Down and you will be able to see KEYS
						*/
						$subscription_key  = "key=AAAAsvg8HuM:APA91bG1Emb-L3CuBbG4CAjRcvq0fsGX0qhZ-gZOaHhvIBT7PPUA16X0Eyr4xsGs8bBBQhSAGThSkdi_vhPqz3d5WhY3qR8RMl9atohHMNDkYtvynaIwz6eW41Nkzr3btdPUgZeAjgkJ";

						/** We will need to set the following header to make request work */
						$request_headers = array(
							"Authorization:" . $subscription_key,
							"Content-Type: application/json"
						);

						/** Data that will be shown when push notifications get triggered */
						$postRequest = [
							"notification" => [
								"title" =>  "New Article",
								"body" =>  "Firebase Cloud Messaging for Web using JavaScript",
								"icon" =>  "https://c.disquscdn.com/uploads/users/34896/2802/avatar92.jpg",
								"click_action" =>  "http://localhost/filesystem"
							],
							/** Customer Token, As of now I got from console. You might need to pull from database */
							"to" =>  "c15Z4N9-5DJKYKoJ39OSuz:APA91bGbV0dq_T6n__r1R06edfChsiyoFdKZ4JGxGtWYXt5ihgc1WdZBnvaCFjALzUZCjEm-CxrYIZhG5UIuok6zVpZzgJ0fmnAst_JSbPFXRC61ehsBorstVUxtdQ5iHDnq0M2KgxbY"
						];

						/** CURL POST code */
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_URL, $url);
						curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postRequest));
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

						$season_data = curl_exec($ch);

						if (curl_errno($ch)) {
							print "Error: " . curl_error($ch);
							exit();
						}
						
						
						if($save) 
							return json_encode(array('status'=>1));
						
		
					}
		
				}
			}else{
						$data = " description = '".$description."' ";
						if(isset($is_public) && $is_public == 'on')
						$data .= ", is_public = 1 ";
						else
						$data .= ", is_public = 0 ";
						$save = $this->db->query("UPDATE files set ".$data. " where id=".$id);
						if($save)
						return json_encode(array('status'=>1));
			}

	}
	function file_rename(){
		extract($_POST);
		$file[0] = $name;
		$file[1] = $type;
		$chk = $this->db->query("SELECT * FROM files where SUBSTRING_INDEX(name,' ||',1) = '".$file[0]."' and folder_id = '".$folder_id."' and file_type='".$file[1]."' and id != ".$id);
		if($chk->num_rows > 0){
			$file[0] = $file[0] .' ||'.($chk->num_rows);
			}
		$save = $this->db->query("UPDATE files set name = '".$name."' where id=".$id);
		if($save){
				return json_encode(array('status'=>1,'new_name'=>$file[0].'.'.$file[1]));
		}
	}
	function save_user(){
		extract($_POST);
		$hashPassword = password_hash($password, PASSWORD_DEFAULT);
		$data = " name = '$name' ";
		$data .= ", username = '$username' ";
		$data .= ", password = '$hashPassword' ";
		$data .= ", type = '$type' ";
		if(empty($id)){
			$save = $this->db->query("INSERT INTO users set ".$data);
		}else{
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$id);
		}
		if($save){
			return 1;
		}
	}

	function approve_task(){
		extract($_POST);
		// printf ("%s -> ", $_POST);
		$data = " note = '".$note."' ";
		$data .= ", status = 'APPROVED' ";
		$data .= ", approved_by = '".$_SESSION['login_id']."' ";

		// printf ("%s -> (%s)", $save, $data);

		$tasks = $this->db->query("SELECT * FROM sub_tasks where id = ".$id);
		$row = $tasks -> fetch_array(MYSQLI_ASSOC);
		if ($row) {

			if($row['status'] == 'APPROVED') {
				return 3;
			} else if($row['status'] == 'REJECT') {
				return 4;
			}

			if($row['user_type'] == 1) {

				$cek_tasks = $this->db->query("SELECT * FROM sub_tasks where task_id = ".$row['task_id']." and user_type = 3");
				$row_cek_tasks = $cek_tasks -> fetch_array(MYSQLI_ASSOC);

				if (!$row_cek_tasks) {
					$sub_task_colums = " (status, task_id, note, user_type)";
					$sub_task_values = " ('PENDING', '".$row['task_id']."', 'Menunggu Persetujuan', 3)";
					$save_sub_task = $this->db->query("INSERT INTO sub_tasks ".$sub_task_colums." VALUES " .$sub_task_values);
				}

				$data_task = "status_tracking = 'Menunggu persetujuan procurment' ";

				$save_data_task = $this->db->query("UPDATE tasks set ".$data_task." where id = ".$row['task_id']);

			} else if($row['user_type'] == 3) {
				$cek_tasks = $this->db->query("SELECT * FROM sub_tasks where task_id = ".$row['task_id']." and user_type = 4");
				$row_cek_tasks = $cek_tasks -> fetch_array(MYSQLI_ASSOC);

				if (!$row_cek_tasks) {
					$sub_task_colums = " (status, task_id, note, user_type)";
					$sub_task_values = " ('PENDING', '".$row['task_id']."', 'Menunggu Persetujuan', 4)";
					$save_sub_task = $this->db->query("INSERT INTO sub_tasks ".$sub_task_colums." VALUES " .$sub_task_values);
				}

				$data_task = "status_tracking = 'Menunggu persetujuan project budget' ";

				$save_data_task = $this->db->query("UPDATE tasks set ".$data_task." where id = ".$row['task_id']);

			} else if($row['user_type'] == 4) {

				$data_task = " note = 'Berhasil disetujui' ";
				$data_task .= ", status = 'APPROVED' ";
				$data_task .= ", status_tracking = 'Data berhasil disetujui' ";

				$save_data_task = $this->db->query("UPDATE tasks set ".$data_task." where id = ".$row['task_id']);

			}
			
		}
		$save = $this->db->query("UPDATE sub_tasks set ".$data." where id = ".$id);
		if($save){
			return 1;
		} else {
			return 0;
		}
	}

	function reject_task(){
		extract($_POST);
		// printf ("%s -> ", $_POST);
		$data = " note = '".$note."' ";
		$data .= ", status = 'REJECT' ";
		$data .= ", approved_by = '".$_SESSION['login_id']."' ";

		// printf ("%s -> (%s)", $save, $data);

		$tasks = $this->db->query("SELECT * FROM sub_tasks where id = ".$id);
		$row = $tasks -> fetch_array(MYSQLI_ASSOC);
		if ($row) {

			if($row['status'] == 'APPROVED') {
				return 3;
			} else if($row['status'] == 'REJECT') {
				return 4;
			}

			if($row['user_type'] == 1) {
				
				$data_task = " note = '".$note."' ";
				$data_task .= ", status = 'REJECT' ";
				$data_task .= ", status_tracking = 'Maaf! Data tidak berhasil disetujui oleh Admin' ";

				$save_data_task = $this->db->query("UPDATE tasks set ".$data_task." where id = ".$row['task_id']);

			} else if($row['user_type'] == 3) {
				

				$data_task = " note = '".$note."' ";
				$data_task .= ", status = 'REJECT' ";
				$data_task .= ", status_tracking = 'Maaf! Data tidak berhasil disetujui oleh Procurment' ";

				$save_data_task = $this->db->query("UPDATE tasks set ".$data_task." where id = ".$row['task_id']);

			} else if($row['user_type'] == 4) {

				$data_task = " note = '".$note."' ";
				$data_task .= ", status = 'REJECT' ";
				$data_task .= ", status_tracking = 'Maaf! Data tidak berhasil disetujui oleh Product Budget' ";

				$save_data_task = $this->db->query("UPDATE tasks set ".$data_task." where id = ".$row['task_id']);

			}
			
		}
		$save = $this->db->query("UPDATE sub_tasks set ".$data." where id = ".$id);
		if($save){
			return 1;
		} else {
			return 0;
		}
	}

	function fcm(){
		extract($_POST);
		
		if($_SESSION['login_id']) {
			$data = " fcm_token = '$token' ";
			$save = $this->db->query("UPDATE users set ".$data." where id = ".$_SESSION['login_id']);
			if($save){
				return 1;
			}
		}
		
	}
}