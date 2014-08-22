<?php

namespace Projet\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ddeboer\DataImport\Reader\CsvReader;
use Projet\TestBundle\Entity\Data;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query\ResultSetMapping;
use Projet\TestBundle\Entity\Document;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    public function indexAction(Request $req){   	
    	$em = $this->getDoctrine()->getManager();
    	$document = new Document();
    	

    	$session = new Session();
    	CalendarController::notification($em, $session);
    	
    	$form = $this->createFormBuilder($document)
		    	->add('file','file',array(
	                'required' => true
			    	)
		    	)
		    	->getForm();
    	
    	$form->handleRequest($req);
		if($form->isValid()){
			$em->persist($document);
			$em->flush();
			$this->get("session")->getFlashBag()->add("erreur", "Fichier transfer&eacute;, en attente de traitement !");
			return $this->redirect($this->generateUrl("p_homepage", array('valide' => '1')));
		}
    	
    	
        return $this->render('PBundle:Default:index.html.twig',
			array(
        		"form" => $form->createView()
        	)
		);
    }
    public function ajaxDataAction(Request $request){
    	if ($request->isXmlHttpRequest()) {
    	$em = $this->getDoctrine()->getManager();
    	$repo_doc = $em->getRepository("PBundle:Document");
    	$doc = $repo_doc->findOneBy(array(),array("id" => "DESC"), 1,0);
    	
    	$chemin = $doc->getChemin();
    	

    	//SUPPRIMER LE CONTENU DE LA BASE
    	$query_spr ="TRUNCATE TABLE DATA";
    	$stmt1 = $em->getConnection()->prepare($query_spr);
    	$stmt1->execute();

    	
    	
    	//INSERTION DES DONNEES
    	$handle = fopen("..\\web\\upload\\".$chemin, "r");

    	$file = new \SplFileObject("..\\web\\upload\\".$chemin);
    	$reader = new CsvReader($file);
    	$i = 0;
    	if ($handle) {
    		while (($line = fgets($handle)) !== false and substr($line,0,2)!=";;") {
    			// process the line read.
    			$tab =explode(";",$line);
    			$date1 ="";
    			if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tab[11])){
    				$d1 = new \DateTime($tab[11]);
    				$date1 = date_format(new \DateTime($d1->format("Y-m-d")),"Y-m-d");
    			}
    			
    			$date2 ="";
    			if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tab[13])){
    				$d2 = new \DateTime($tab[13]);
    				$date2 = date_format(new \DateTime($d2->format("Y-m-d")),"Y-m-d");
    			}
    			$date3 ="";
    			$age = 0;
    			if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tab[18])){
    				$d3 = new \DateTime($tab[18]);
    				$date3 = date_format(new \DateTime($d3->format("Y-m-d")),"Y-m-d");
    				
    				$sDateBirth = $date3;
    				$oDateNow = new \DateTime();
    				$oDateBirth = new \DateTime($sDateBirth);
    				$oDateIntervall = $oDateNow->diff($oDateBirth);
    				$age = $oDateIntervall->y.".".$oDateIntervall->m;
    				
    			}
    			$date4 ="";
    			if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tab[54])){
    				$d4 = new \DateTime($tab[54]);
    				$date4 = date_format(new \DateTime($d4->format("Y-m-d")),"Y-m-d");
    			}
    			$date5 ="";
    			if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tab[50])){
    				$d5 = new \DateTime($tab[50]);
    				$date5 = date_format(new \DateTime($d5->format("Y-m-d")),"Y-m-d");
    			}
    			$query ="INSERT INTO DATA(libelleEtab,Classification,"
    					."libelleSexe,dateEntree,libSituation,dateEntreeSituation,typeContrat,dateNaissance,age,recrutement,"
    					."date,categorie,typeContrat2,datePromo,nouvelleSituation) values(".
    					"'".$tab[2]."',"
    					."'".$tab[4]."',"
    					."'".utf8_encode($tab[9])."',"
    					."'".$date1."',"
    					."'".utf8_encode($tab[12])."',"
    					."'".$date2."',"
    					."'".$tab[14]."',"
    					."'".$date3."',"
    					.$age.","
    					."'".$tab[53]."',"
    					."'".$date4."',"
    					."'".$tab[55]."',"
    					."'".$tab[56]."',"
    					."'".$date5."',"
    					."'".$tab[52]."'"
    					.")";
    			
    			$stmt = $em->getConnection()->prepare($query);
    			$stmt->execute();
    			
    			ob_flush();
    			flush();

    			echo round(($i*100)/$reader->count())." -";
    			$i++;
    		}
    	} else {
    		echo "Erreur lors de la lecture du fichier.";
    	}
    	fclose($handle);
    	
    	}
    	return new Response("100 -");
    }
}













    	/*
    	$file = new \SplFileObject('..\web\upload\file.csv');
    	$reader = new CsvReader($file);
    	//$reader->count()
    	for($i = 0; $i < $reader->count() ; $i++){
    	
    		
    		$date1 ="";
    		if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[11])){
    			$d1 = new \DateTime($reader->getRow($i)[11]);
    			$date1 = date_format(new \DateTime($d1->format("Y-m-d")),"Y-m-d");
    		}
    		
    		$date2 ="";
    		if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[13])){
    			$d2 = new \DateTime($reader->getRow($i)[13]);
    			$date2 = date_format(new \DateTime($d2->format("Y-m-d")),"Y-m-d");
    		}
    		$date3 ="";
    		if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[18])){
    			$d3 = new \DateTime($reader->getRow($i)[18]);
    			$date3 = date_format(new \DateTime($d3->format("Y-m-d")),"Y-m-d");
    		}
    		$date4 ="";
    		if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[53])){
    			$d4 = new \DateTime($reader->getRow($i)[53]);
    			$date4 = date_format(new \DateTime($d4->format("Y-m-d")),"Y-m-d");
    		}
    		$query ="INSERT INTO DATA(libelleEtab,Classification,"
    				."libelleSexe,dateEntree,libSituation,dateEntreeSituation,typeContrat,dateNaissance,recrutement,"
    				."date,categorie,typeContrat2) values(".
    				"'".$reader->getRow($i)[2]."',"
    				."'".$reader->getRow($i)[4]."',"
    				."'".$reader->getRow($i)[9]."',"
    				."'".$date1."',"
    				."'".$reader->getRow($i)[12]."',"
    				."'".$date2."',"
    				."'".$reader->getRow($i)[15]."',"
    				."'".$date3."',"
    				."'".$reader->getRow($i)[52]."',"
    				."'".$date4."',"
    				."'".$reader->getRow($i)[55]."',"
    				."'".$reader->getRow($i)[56]."'"
    				.")";
    		$stmt = $em->getConnection()->prepare($query);
    		$stmt->execute();
    		
    		//echo $query;
    		
    		ob_flush();
    		flush();
    		echo round(($i*100)/($reader->count()))." -";
    		}*/

/*
 $data = new Data();
$data->setLibelleEtab($reader->getRow($i)[2]);
$data->setClassification($reader->getRow($i)[4]);
$data->setLibelleSexe($reader->getRow($i)[9]);
 
 
$data->setLibSituation($reader->getRow($i)[12]);
$data->setTypeContrat($reader->getRow($i)[15]);
$data->setRecrutement($reader->getRow($i)[53]);
$data->setCategorie($reader->getRow($i)[55]);
$data->setTypeContrat2($reader->getRow($i)[56]);
 
 
if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[11])){
$d1 = new \DateTime($reader->getRow($i)[11]);
$data->setDateEntree(new \DateTime($d1->format("Y-m-d")));
}

if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[54])){
$d2 = new \DateTime($reader->getRow($i)[54]);
$data->setDate(new \DateTime($d2->format("Y-m-d")));
}

if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[18])){
$d3 = new \DateTime($reader->getRow($i)[18]);
$data->setDateNaissance(new \DateTime($d3->format("Y-m-d")));
}
if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $reader->getRow($i)[14])){
$d4 = new \DateTime($reader->getRow($i)[14]);
$data->setDateEntreeSituation(new \DateTime($d4->format("Y-m-d")));
}
 
 
$em->persist($data);
$em->flush();
*/