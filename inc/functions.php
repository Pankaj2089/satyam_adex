<?php

function checkPermissions($pageName = NULL){
	$permission = false;
	if(isset($_SESSION['UserId'])){
		if($_SESSION['Type'] == 'Admin'){
			$permission = true;
		}else{
			if(!empty($_SESSION['AccountPermissions'])){
				$myPermissions = json_decode($_SESSION['AccountPermissions']);
				if(in_array($pageName,$myPermissions)){
					$permission = true;
				}
			}
		}
	}
	return $permission;
}

?>