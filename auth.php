<?php

// Restriction de l'accéssibilité des cookies
$sessionPath = parse_url($CONF["scoobydoo_url"], PHP_URL_PATH);
session_set_cookie_params(0, $sessionPath);
session_start();

if(isset($_GET["logout"]))
{
	session_destroy();
	header("Location: ".$AADMIN->getCasUrl()."/logout?url=".$CONF['scoobydoo_url']);
  exit();
}

if(isset($_SESSION["loged"]) && $_SESSION["loged"] == 1) {
	// tout vas bien on est loged ;)
	// Si on a un cookie on récupére la session soap.
	if(isset($_SESSION['cookies'])) { 
		$AADMIN->_cookies = $_SESSION['cookies'];
		// Verification que la session soap n'a pas expiré. 
		try {
				$AADMIN->getFirstname();
		} catch (Exception $e) {
				session_destroy();
				// On envoie sur le cas
				header("Location: ".$AADMIN->getCasUrl()."/login?service=".$CONF['scoobydoo_url']);
				exit();
		}
		
	} else {
		// On délogue par sécurité
		session_destroy();
		// On envoie sur le cas
		header("Location: ".$AADMIN->getCasUrl()."/login?service=".$CONF['scoobydoo_url']);
		exit();
	}
} else {
	// User not loged
	//1. Regardons si on a un retour de CAS.
	if(isset($_GET["ticket"])) {
		// Connexion soap
		$ticket = $_GET["ticket"];
		try {
			$code = $AADMIN->loginCas($ticket, $CONF['scoobydoo_url']);
		} catch (Exception $e) {
				echo "<pre>".$e."</pre>";
		}
		if($code == 1)
		{
			$_SESSION['cookies'] = $AADMIN->_cookies;
			$_SESSION['loged'] = 1;
			// Pas obligatoire mais c'est mieux pour virer le ticket de la barre d'adresse
			header("Location: ".$CONF['scoobydoo_url']);
		  	exit();
		} else {
			echo $AADMIN->getErrorDetail($code);
			exit();
		}
	} else {
		//2. On renvoie sur le cas
		session_destroy();
		header("Location: ".$AADMIN->getCasUrl()."/login?service=".$CONF['scoobydoo_url']);
		exit();
	}
}
