<?php

namespace Projet\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller {
	public function loginAction() {
		// Si le visiteur est d�j� identifi�, on le redirige vers l'accueil
		if ($this->get ( 'security.context' )->isGranted ( 'IS_AUTHENTICATED_REMEMBERED' )) {
			return $this->redirect ( $this->generateUrl ( 'p_homepage' ) );
		}
		
		$request = $this->getRequest ();
		$session = $request->getSession ();
		
		// On v�rifie s'il y a des erreurs d'une pr�c�dente soumission du formulaire
		if ($request->attributes->has ( SecurityContext::AUTHENTICATION_ERROR )) {
			$error = $request->attributes->get ( SecurityContext::AUTHENTICATION_ERROR );
		} else {
			$error = $session->get ( SecurityContext::AUTHENTICATION_ERROR );
			$session->remove ( SecurityContext::AUTHENTICATION_ERROR );
		}
		
		return $this->render ( 'PBundle:Default:login.html.twig', array (
				// Valeur du pr�c�dent nom d'utilisateur entr� par l'internaute
				'last_username' => $session->get ( SecurityContext::LAST_USERNAME ),
				'error' => $error 
		) );
	}
}