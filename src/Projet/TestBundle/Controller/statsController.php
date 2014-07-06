<?php

namespace Projet\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ddeboer\DataImport\Reader\CsvReader;
use Projet\TestBundle\Entity\Data;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Projet\TestBundle\Entity\Document;

class statsController extends Controller
{
    public function hf_nbrAction(){
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$sDateBirth = "1985-10-29";
    	
    	$oDateNow = new \DateTime();
    	$oDateBirth = new \DateTime($sDateBirth);
    	$oDateIntervall = $oDateNow->diff($oDateBirth);
    	//echo $oDateIntervall->y;
    	
    	
    	
    	//DATA STATS POUR PYRAMIDE DES AGES
    	$ages = array(
	    			'0 and 4','5 and 9','10 and 14','15 and 19','20 and 24','25 and 29','30 and 34',
	    			'35 and 39','40 and 44','45 and 49','50 and 54','55 and 59','60 and 64','64 and 69',
	    			'70 and 74','75 and 79','80 and 84','85 and 89','90 and 94','95 and 200'
    	);
    	
    	
    	$data_age_jason = "[";
    	$data_age_jason .= "['Age', 'Femme', 'Homme' ],";
    	
    	foreach ($ages as $age){
    		$rsm = new ResultSetMappingBuilder($em);
    		$rsm->addScalarResult('nbr', 'count');
    		

    		$data_age_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE age between ".$age
    				." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
    				, $rsm)->getResult();
    		$data_age_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE age between ".$age
    				." AND d.libelleSexe = 'Masculin'"
    				, $rsm)->getResult();
    		    		
    		//echo var_dump($data_age_h);
    		
    		$data_age_jason .= "['".str_replace(' and ', "-", $age)." ans', ".$data_age_f[0]['count'].", -".$data_age_h[0]['count']."],";
    		
    	}

    	$data_age_jason .= "]";
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	

    	//DATA POUR NOMBRE DE FEMMES ET HOMMES
    	$background_colors = array('#CD00CD', '#8B1C62', '#EE4000', '#FFEFD5');
    	 
    	$rsm = new ResultSetMappingBuilder($em);
    	$rsm->addScalarResult('nbr', 'count');
    	$rsm->addScalarResult('libelle', 'd.libelleSexe');
    	 
    	$data_st_jason = "";
    	 
    	$data_st = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle FROM data d"
    			." GROUP BY d.libelleSexe"
    			, $rsm)->getResult();
    	 
    	$data_st_jason .= "[";
    	$o = 0;
    	$col ="";
    	foreach ($data_st as $data){
    		$data_st_jason .= "{value: ".$data['count'].", label: '".$data['d.libelleSexe']."'},";
    		$col .= "'".$background_colors[$o]."',";
    		$o++;
    	}
    	$data_st_jason .= "]";
    	 
    	 
    	 


    	//DATA POUR NOMBRE DE CDI ET CDD
    	$background_colors1 = array('#8B7500','#7FFF00', '#FFEFD5', '#EE4000', '#495E67');
    	
    	$rsm = new ResultSetMappingBuilder($em);
    	$rsm->addScalarResult('nbr', 'count');
    	$rsm->addScalarResult('typec', 'd.typeContrat');
    	
    	$data_sty_jason = "";
    	
    	$data_sty = $em->createNativeQuery("SELECT count(d.id) as nbr, d.typeContrat as typec FROM data d"
    			." GROUP BY d.typeContrat"
    			, $rsm)->getResult();
    	
    	$data_sty_jason .= "[";
    	$o1 = 0;
    	$col1 ="";
    	foreach ($data_sty as $data){
    		$data_sty_jason .= "{value: ".$data['count'].", label: '".$data['d.typeContrat']."'},";
    		$col1 .= "'".$background_colors1[$o1]."',";
    		$o1++;
    	}
    	$data_sty_jason .= "]";
    	

    	
    	
    	
    	
    	

    	//DATA POUR NOMBRE PAR CATEGORIE
    	$background_colors2 = array('#495E67','#8B7500', '#EE4000', '#7FFF00', '#FFEFD5');
    	
    	$rsm = new ResultSetMappingBuilder($em);
    	$rsm->addScalarResult('nbr', 'count');
    	$rsm->addScalarResult('cat', 'd.categorie');
    	
    	$data_cat_jason = "";
    	
    	$data_cat = $em->createNativeQuery("SELECT count(d.id) as nbr, d.categorie as cat FROM data d"
    			." GROUP BY d.categorie"
    			, $rsm)->getResult();
    	
    	$data_cat_jason .= "[";
    	$o2 = 0;
    	$col2 ="";
    	foreach ($data_cat as $data){
    		$data_cat_jason .= "{value: ".$data['count'].", label: '".$data['d.categorie']."'},";
    		$col2 .= "'".$background_colors2[$o2]."',";
    		$o2++;
    	}
    	$data_cat_jason .= "]";
    	
    	
    	 
    	 
    	 
    	
        return $this->render('PBundle:Stats:hf_nbr.html.twig',
			array(
        		"stats" => $data_st_jason,
				"couleur" => $col,
        		"stats_ctype" => $data_sty_jason,
				"couleur1" => $col1,
        		"stats_cat" => $data_cat_jason,
				"couleur2" => $col2,
				"stats_age" => $data_age_jason
        	)
		);
    }
}