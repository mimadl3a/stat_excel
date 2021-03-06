<?php

namespace Projet\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\DBAL\DBALException;
use Symfony\Component\HttpFoundation\Request;
use Projet\TestBundle\Entity\Calendrier;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Session\Session;

use Doctrine\ORM\Query\ResultSetMappingBuilder;

class CalendarController extends Controller{
	
	public function indexAction(){
		$data = "";
		$em = $this->getDoctrine()->getManager();
		
		$session = new Session();
		self::notification($em, $session);
		
		$all = $em->createQuery("SELECT c.id, c.title, c.description, c.date"
							   ." FROM PBundle:Calendrier c"
		)->getResult();
		
		$serializer = new Serializer(array(new GetSetMethodNormalizer()), array('json' => new JsonEncoder()));
		$json = $serializer->serialize($all, 'json');
		
		
		return $this->render("PBundle:Calendar:index.html.twig",array(
			'data' => $data,
			'liste' => $json,
		));
	}
	
	
	public function ajouterAction(Request $req){
		if($req->get("titre") && $req->get("descr")){
			$em = $this->getDoctrine()->getManager();		
			$a = new Calendrier();
	
			$a->setTitle($req->get("titre"));
			$a->setDescription($req->get("descr"));
			$a->setDate($req->get("date"));
			
			
			
			$em->persist($a);
			$em->flush();
			return new Response("ok");
		}else{
			return;
		}
	}
	
	public function supprimerAction(Request $req){
		if($req->get("id")){
			$em = $this->getDoctrine()->getManager()->getRepository("PBundle:Calendrier");
			$a = $em->find($req->get("id"));
			
			$em = $this->getDoctrine()->getManager();
			$em->remove($a);
			$em->flush();
			return new Response("ok");
		}else{
			return;
		}
	}

	
	public function modifierAction(Request $req){
		if($req->get("id") && $req->get("titre") && $req->get("descr")){
			$em = $this->getDoctrine()->getManager();
			$repo = $em->getRepository("PBundle:Calendrier");
			$a = $repo->find($req->get("id"));
	
			$a->setTitle($req->get("titre"));
			$a->setDescription($req->get("descr"));
	
			$em->flush();
			return new Response("ok");
		}else{
			return;
		}
	}
	
	public static function notification($em, $session) {
		// NBR RENDEZ VOUS
		$rsm = new ResultSetMappingBuilder ( $em );
		$rsm->addScalarResult ( 'nbr', 'count' );
	
		$data = $em->createNativeQuery ( "SELECT count(rdv.id) as nbr FROM calendrier rdv"
				. " WHERE rdv.date BETWEEN DATE_FORMAT(CURRENT_DATE, '%Y-%m-%d') AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY) "
				. " ORDER BY rdv.date", $rsm )
				->getResult ();
		if ($data) {
			$session->set ( 'nbr_rdv', $data [0] ['count'] );
		} else {
			$session->set ( 'nbr_rdv', '0' );
		}
	
	}
	
}