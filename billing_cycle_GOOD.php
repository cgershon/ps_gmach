<?php



$dsn = 'mysql:host=localhost;dbname=admin_gmahexpress';
$username = 'gmahexp';
$password = 'zx262626';
$options = array(  PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',); 

//exit('Stop');
$dbh = new PDO($dsn, $username, $password, $options);
   if( isset( $_POST['Nb']) )
	{   
	  $_SESSION['billing_cycle']   =  $_POST['Nb'];
	 $dbh -> exec( 'UPDATE `ko_bestkit_psubscription_period` SET  `billing_cycles` = '.$_SESSION['billing_cycle']    .', `allowed_start_days`="monday"  WHERE  `id_bestkit_psubscription_period` = "2" ' );
	//print_r( $dbh->errorInfo(), true );		
	//echo     'billing_cycle: '.$_SESSION['billing_cycle'];
	}
    else
    {
    	
   	 $dbh -> exec( 'UPDATE `ko_bestkit_psubscription_period` SET  `billing_cycles` = "1" `allowed_start_days`="monday"  WHERE  `id_bestkit_psubscription_period` = "2" ' );
	//print_r( $dbh->errorInfo(), true );	
	 //echo     'billing_cycle is Not  defined...';
     
    }
 
?>