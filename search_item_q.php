<?php
   try {
	   
       $con = new PDO("mysql:host=localhost;dbname=project;charset=utf8", 'root', '');
       $con -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       $floor = $_GET['floor'];
       $slot = $_GET['slot'];
       $faculty = $_GET['faculty'];
	   if($floor == 0 && $slot == 0 && $faculty == 0){
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor == 0 && $slot == 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor == 0 && $slot != 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.slot=$slot 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor == 0 && $slot != 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.slot=$slot AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor != 0 && $slot == 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor != 0 && $slot == 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor  AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor != 0 && $slot != 0 && $faculty == 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor AND storage.slot=$slot
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }else if($floor != 0 && $slot != 0 && $faculty != 0){ 
			$sql = "select * from storage join faculty ON storage.fac_id = faculty.fac_id 
					where storage.floor=$floor AND storage.slot=$slot AND faculty.fac_id = $faculty 
					order by `faculty`.`fac_name` , storage.floor , storage.slot ASC";
	   }
       $q = $con->query($sql);
       $result = $q->fetchAll(PDO::FETCH_ASSOC);
        echo  json_encode($result);
        $con = null;
    } catch(PDOException $e) {
	echo "Error :" . $e->getMessage();
    }
?>