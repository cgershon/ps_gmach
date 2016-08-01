<?php session_start();

// Inclure la librairie smarty
//require_once('tools/smarty/Smarty.class.php');
 
// Instancier notre objet smarty
//$smarty = new Smarty();
 
$dsn = 'mysql:host=localhost;dbname=admin_gmahexpress';
$username = 'gmahexp';
$password = 'zx262626';
$options = array(  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',); 

$dbh = new PDO($dsn, $username, $password, $options);
$id_customer= NULL ; 
if( isset( $_POST['step'])  )
	if ( $_POST['step'] ===  "RECORD"  )
	{
		  if(    isset( $_SESSION['billing_cycles'] )  )
			{   
				 //  var_dump( $_SESSION['billing_cycles'] );   exit;
			  // $_SESSION['billing_cycle']   =  $_POST['Nb'];
			//   if( isset( $_POST['Nb']) )
				  $_SESSION['token']   =  $_POST['token'];
			/*	if( ! $id_customer  )
				   	{
				   	//	return;
				   	}
			*/
		//	   $insert = "INSERT INTO `ko_bestkit_psubscription_period` (`id_costumer`, `id_bestkit_psubscription_period`, `billing_period`, `billing_freq`, `billing_cycles`, `allowed_start_days`) VALUES ('', NULL, '1', '1', '1', 'monday') ";
		//	   $read = " SELECT *  FROM  `ko_bestkit_psubscription_period` WHERE  `id_costumer` = ' ".  $id_customer." ' " ;
		//	   $read_ok = $dbh -> exec(  $read  );
		 //  var_dump(	  $read_ok );
			  	 if ( $read_ok  || 1  )
				    {
				 	    //    $dbh -> exec(  $insert  );	
				   		 $dbh -> exec( 'UPDATE `ko_bestkit_psubscription_period` SET  `billing_cycles` = '.$_SESSION['billing_cycles']    .', `token` = "'. $_SESSION['token']   .'" , `allowed_start_days`="monday"  WHERE  `id_bestkit_psubscription_period` = "2"  AND `id_customer` = "1"  ' );
					}
					
					//echo     'billing_cycle: '.$_SESSION['billing_cycles'];
			}
	   	 else
	   	 {
	    	
	   		 $dbh -> exec( 'UPDATE `ko_bestkit_psubscription_period` SET  `billing_cycles` = "1" `allowed_start_days`="monday"  WHERE  `id_bestkit_psubscription_period` = "2"  AND `id_customer` = "1" ' );
			//print_r( $dbh->errorInfo(), true );	
		 	//echo     'billing_cycle is Not  defined...';
		 	//	var_dump(  $id_customer );exit;
	     
	 	   }
	 		echo "RECORD" ;   
	}
	else    //  BEFORE the  account  where created 
		if ( $_POST['step'] ===  "BILLING_CYCLE"  )	
		{
			
			$_SESSION['billing_cycles']= $_POST['Nb'];
			$_SESSION['token']   =  $_POST['token'];
	   		$dbh -> exec( 'UPDATE `ko_bestkit_psubscription_period` SET  `billing_cycles` = '.$_SESSION['billing_cycles']    .', `token` = "'. 					$_SESSION['token']   .'" , `allowed_start_days`="monday"  WHERE  `id_bestkit_psubscription_period` = "2"  AND `id_customer` = "1"  ' );

 			echo "BILLING_CYCLE " ;
 		//	var_dump( $_SESSION['billing_cycles'] );  exit;
		}
	
		
?>