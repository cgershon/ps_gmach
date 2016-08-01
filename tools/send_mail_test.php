<?php
/* ********************************************************************************************** */   
 public function send_mail( $to,$tpl_name,$options )
 	{
 	 	 	global $cookie;
	           
	             $passage_ligne = '\r';  
	          	$id_customer = (int)$this->context->customer->id ;
	          	$customer_name = $this->context->customer->firstname.  ' '.$this->context->customer->lastname  ;
	          	$Gmach_mail="gmach@ygpc.net";
	          
	          	$id_lang=  intval($cookie->id_lang);
	          	$template= $tpl_name;
	          	$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$to;
	          	$to_name = $customer_name;
	         	$from='gmach@ygpc.net';
	          	$from_name =  'gmachexpress';
	          	$file_attachment = NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;
			//=====Déclaration des messages au format texte et au format HTML.
	             $txt =' בקשתכם בטיפול    לאישור . שרות לקוחות של הגמ"ח';
			$message_html = '<html><head></head><body><p align="center"> <img src="'.$options['datas']["tz"].'"  alt="gmachexpress" width="100"height="100"  />'.$customer_name.'</p><div style="text-align:right; padding-right:10px;"></div></body></html>';
			//==========
			 
			//=====Création de la boundary
			$boundary = "-----=".md5(rand());
			//==========
			 
			//=====Définition du sujet.
			$preferences = ['input-charset' => 'UTF-8', 'output-charset' => 'UTF-8'];
			$encoded_subject = iconv_mime_encode('Subject', $subject, $preferences);
			$encoded_subject = substr($encoded_subject, strlen('Subject: '));
			//=========
			 
			//=====Création du header de l'e-mail.
			$headers = 'From: <'.$from. '>'. $passage_ligne.' Bcc:< info@ygpc.net>'.  $passage_ligne;
			$headers.= ' Reply-to: Gmach_Express<'.$from.'>'.$passage_ligne;
			$headers.= ' MIME-Version: 1.0'.$passage_ligne;
			$headers.= ' Content-Type: multipart/alternative'.$passage_ligne.' boundary="'.$boundary.'" '.$passage_ligne;
                	$headers .=' Content-Transfer-Encoding: quoted-printable' .  $passage_ligne;
                 // $headers .= "Content-Type: image/jpg  ;".  $passage_ligne;
		 
			//=====Création du message. 
			$message = $passage_ligne."--".$boundary.$passage_ligne;
			//=====Ajout du message au format texte.
			$message.= ' Content-Type: text/plain; charset="UTF-8" '.$passage_ligne;
			$message.= " Content-Transfer-Encoding: 8bit".$passage_ligne;
			$message.= $passage_ligne.$txt.$passage_ligne;
			//==========
			$message.= $passage_ligne."--".$boundary.$passage_ligne;
			//=====Ajout du message au format HTML
			$message.= ' Content-Type: text/html; charset="UTF-8" '.$passage_ligne;
			$message.= ' Content-Transfer-Encoding: 8bit'.$passage_ligne;
			$message.= $passage_ligne.$message_html.$passage_ligne;
			//==========
			$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
			$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
			//==========
			 
			//=====Envoi de l'e-mail.
	             $result=mail( $to, $encoded_subject,$message,$headers );     // ADDED YGPC
			//==========
 	
			// Inform the manager
	 		$id_lang=  intval($cookie->id_lang);
	          	$template= 'validate_bank_datas';
	          	//$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$Gmach_mail;
	          	$to_name = 'שרות לקוחות';
	         	$from="gmach@ygpc.net";
	          	$from_name =  'gmachexpress';
	          	$file_attachment =  NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;   
	             $txt ='בקשה התקבלה  לאשר אותה    : עבור שרות לקוחות של הגמ"ח ';
	           
			//=====Création du message. 
			$message = $passage_ligne."--".$boundary.$passage_ligne;
			//=====Ajout du message au format texte.
			$message.= ' Content-Type: text/plain; charset="UTF-8" '.$passage_ligne;
			$message.= " Content-Transfer-Encoding: 8bit".$passage_ligne;
			$message.= $passage_ligne.$txt.$passage_ligne;
			//==========
			$message.= $passage_ligne."--".$boundary.$passage_ligne;
			//=====Ajout du message au format HTML
			$message.= ' Content-Type: text/html; charset="UTF-8" '.$passage_ligne;
			$message.= ' Content-Transfer-Encoding: 8bit'.$passage_ligne;
			$message.= $passage_ligne.$message_html.$passage_ligne;
			//==========
			$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
			$message.= $passage_ligne."--".$boundary."--".$passage_ligne;
			//==========
			 
			//=====Envoi de l'e-mail.
	             $result=mail( $to, $encoded_subject,$message,$headers );     // ADDED YGPC
			//==========
 	
	              

	                $result=mail( $to, $encoded_subject,$txt,$headers );     // ADDED YGPC  
	                
	                
			
 	}
    /* ******************************************************************************************** */
/* http://emilienmalbranche.fr/prestashop-ecommerce-tutoriels/tutoriels/envoyer-des-mails-grace-a-la-fonction-mailsend-de-prestashop/
Dans le cas d’une boutique en ligne l’envoi d’ emails est très important pour communiquer avec vos clients.
Je vais vous présenter une fonction de prestashop très simple et utile pour envoyer des mails avec en complément un template que vous pourrez mettre en forme facilement !

La classe Mail et sa fonction Send()
Commençons par le code, je vous expliquerai ensuite le fonctionnement de ce dernier.

global $cookie;
 
$subject = 'Bonjour';
$donnees = array('{nom}'  => 'Jobs' ,  '{prenom}'  => 'Steve' );
$destinataire = 'mail@destinataire.com';
 
Mail::Send(intval($cookie->id_lang), 'montemplate', $sujet , $donnees, $destinataire, NULL, NULL, NULL, NULL, NULL, 'mails/');
Dans un premier temps, nous initialisons diverses variables qui contiennent le sujet du mail, les données que ce dernier comprendra (oui oui, on peut mettre des variables dans les mails :) ) ainsi que l’adresse mail du destinataire.

Ces variables seront utilisées dans la fonction ‘Send()’ de la classe ‘Mail’. J’utilise des variables pour mettre toutes les informations d’envoi du mail, cela permet une meilleure clarté.

Voyons maintenant la fonction en détails, elle comprends beaucoup de paramètres :

L’id de la langue [ ici intval($cookie->id_lang), variable cookie qui récupère l'id de la langue actuelle ]
Le nom du template [ ici 'montemplate' ]
Le sujet [ ici 'Bonjour' ]
Un tableau contenant les données à placer dans le template [ ici $donnees ]
Le destinataire [ ici 'mail@destinataire.com' ]
Le nom du destinataire [ ici NULL ]
L’adresse mail de l’émetteur [ ici NULL ]
Le nom de l’émetteur [ ici NULL ]
Une pièce jointe [ ici NULL ]
Le mode SMTP [ ici NULL ]
Le chemin vers le dossier contenant le template [ ici le dossier mails à la racine ]
Le template
Rendez-vous dans le dossier mails/fr , c’est là que notre fonction va aller chercher le fichier template que nous lui avons indiqué. (rappelez-vous : ‘mails/’)

Vous devez impérativement créer deux fichiers, un .txt et un .html portant tout les deux le nom de votre template ( ici  ‘montemplate’ ).
Créez donc les fichiers montemplate.txt et montemplate.html dans le dossier mails/fr/.

Le fichier html est celui utilisé pour le template du mail, avec donc du code html.
Le fichier txt est utilisé au cas où le destinataire n’arrive pas à lire le mail, il contient alors du texte brut.

Ajoutons maintenant du contenu dans notre fichier montemplate.html :

<h1>Bonjour {prenom} {nom}</h1>

Puis pour notre fichier montemplate.txt :

Bonjour {prenom} {nom}

  public static function Send(  $id_lang, $template, $subject, $template_vars, $to,
    							    $to_name = null, $from = null, $from_name = null, $file_attachment = null, $mode_smtp = null,
   							    $template_path = _PS_MAIL_DIR_, $die = false, $id_shop = null, $bcc = null, $reply_to = null)
*/    
// ADDED YGPC
public  function send_mail_swift($to,$tpl_name,$options) 
	 {
	 		global $cookie;
	          	$id_customer = (int)$this->context->customer->id ;
	          	$customer_name = $this->context->customer->firstname.  ' '.$this->context->customer->lastname  ;
	          	$Gmach_mail="gmach@ygpc.net";
	          
	          	$id_lang=  intval($cookie->id_lang);
	          	$template= $tpl_name;
	          	$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$to;
	          	$to_name = $customer_name;
	         	$from="gmach@ygpc.net";
	          	$from_name =  'gmachexpress';
	          	$file_attachment = NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;
	          	
	          	$result = 	Mail::Send(	$id_lang, $template, $subject, $template_vars, $to,
										$to_name , $from , $from_name , $file_attachment , $mode_smtp ,
										$template_path , $ddie, $id_shop , $bcc , $reply_to  );
	         	//	var_dump($result ,$to,$tpl_name,$options); exit;	
	 		// Inform the Director 
	 		$id_lang=  intval($cookie->id_lang);
	          	$template= 'validate_bank_datas';
	          	$subject=  $options['subject'];
	          	$template_vars= $options['datas'];
	          	$to=$Gmach_mail;
	          	$to_name = 'שרות לקוחות';
	         	$from="gmach@ygpc.net";
	          	$from_name =  'gmachexpress';
	          	$file_attachment =  NULL;
   			$mode_smtp = null;
        		$template_path = _PS_MAIL_DIR_;
        		$ddie = false;
        		$id_shop = null;
        		$bcc = null;
        		$reply_to = null;
        		
	 		$result = 	Mail::Send(	$id_lang, $template, $subject, $template_vars, $to,
										$to_name , $from , $from_name , $file_attachment , $mode_smtp ,
										$template_path , $ddie, $id_shop , $bcc , $reply_to  );
	        //	var_dump($result ,$to,$tpl_name,$options); exit;	
	              return $result;	
	         //		var_dump($result ,$to,$tpl_name,$options); exit;	
	        //  	var_dump(intval($cookie->id_lang),	$result ,$to,$tpl_name,$options); exit;		 
		
	 }   
  /* **************************************************************************************************** */  
  ?>