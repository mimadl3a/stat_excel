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
    public function hf_nbrAction(Request $req){
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	/*$sDateBirth = "1985-10-29";
    	
    	$oDateNow = new \DateTime();
    	$oDateBirth = new \DateTime($sDateBirth);
    	$oDateIntervall = $oDateNow->diff($oDateBirth);
    	echo $oDateIntervall->y;*/
    	
    	
    	$sites = array();
    	$rsm = new ResultSetMappingBuilder($em);
    	$rsm->addScalarResult('libelle', 'd.libelleEtab');
    	$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
    		." GROUP BY d.libelleEtab"
    		, $rsm)->getResult();
    	
    	
    	$dataform = $req->request->all();
    	$st = "";
    	if(isset($dataform['site']) and $dataform['site'] != "Tous"){
    		$st = " AND d.libelleEtab='".$dataform['site']."'";
    	}

    	//DATA STATS POUR PYRAMIDE DES AGES
    	$ages = array(
    			'0 and 4','5 and 9','10 and 14','15 and 19','20 and 24','25 and 29','30 and 34',
    			'35 and 39','40 and 44','45 and 49','50 and 54','55 and 59','60 and 64','64 and 69',
    			'70 and 74','75 and 79','80 and 84','85 and 99'
    	);
    	
    	 
    	$data_age_jason = "[";
    	$data_age_jason .= "['Age', 'Femme', 'Homme' ],";
    	 
    	foreach ($ages as $age){
    		$rsm = new ResultSetMappingBuilder($em);
    		$rsm->addScalarResult('nbr', 'count');
    	
    		$data_age_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE age between ".$age
    				." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    		$data_age_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE age between ".$age
    				." AND d.libelleSexe = 'Masculin'"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    	
    		
    		if($age == "85 and 99"){
    			$data_age_jason .= "['+85 ans', ".$data_age_f[0]['count'].", -".$data_age_h[0]['count']."],";
    		}else{
    			$data_age_jason .= "['".str_replace(' and ', "-", $age)." ans', ".$data_age_f[0]['count'].", -".$data_age_h[0]['count']."],";
    		}
    		
    	
    	}
    	
    	$data_age_jason .= "]";
    	 
    	 
    	 
    	 

    	//DATA STATS PAR ANCIENTE
    	$nbr_annees = array(
    			'0 and 2','2 and 5','5 and 12','12 and 20','20 and 25','25 and 200'
    	);
    	 
    	 
    	$data_an_jason = "[";
    	$data_an_jason .= "['Ancien', 'Femme', 'Homme' ],";
    	 
    	foreach ($nbr_annees as $nbr_annee){
    		$rsm = new ResultSetMappingBuilder($em);
    		$rsm->addScalarResult('nbr', 'count');
    	
    		$data_an_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE round(DATEDIFF(CURDATE(),dateEntree)/360) between ".$nbr_annee
    				." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    		$data_an_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE round(DATEDIFF(CURDATE(),dateEntree)/360) between ".$nbr_annee
    				." AND d.libelleSexe = 'Masculin'"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    	
    		if($nbr_annee == "25 and 200"){
    			$data_an_jason .= "['+25 ans', ".$data_an_f[0]['count'].", -"
    					.$data_an_h[0]['count']."],";
    		}else{
    			$data_an_jason .= "['".str_replace(' and ', "-", $nbr_annee)
    			." ans', ".$data_an_f[0]['count'].", -"
    					.$data_an_h[0]['count']."],";
    		}
    		
    	
    	}
    	
    	$data_an_jason .= "]";
    	 
    	 
    	 
    	 
    	 
    	
    	
    	
    	
    	
    	
    	

    	//DATA POUR NOMBRE DE FEMMES ET HOMMES
    	$background_colors = array('#CD00CD', '#8B1C62', '#EE4000', '#FFEFD5');
    	 
    	$rsm = new ResultSetMappingBuilder($em);
    	$rsm->addScalarResult('nbr', 'count');
    	$rsm->addScalarResult('libelle', 'd.libelleSexe');
    	 
    	$data_st_jason = "";
    	 
    	$data_st = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle FROM data d"
    			." WHERE d.libSituation = 'Actif'".$st
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
    			." WHERE d.libSituation = 'Actif'".$st
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
    	$rsm->addScalarResult('cat', 'd.classification');
    	
    	$data_cat_jason = "";
    	
    	$data_cat = $em->createNativeQuery("SELECT count(d.id) as nbr, d.classification as cat FROM data d"
    			." WHERE d.libSituation = 'Actif'".$st
    			." GROUP BY d.classification"
    			, $rsm)->getResult();
    	
    	$data_cat_jason .= "[";
    	$o2 = 0;
    	$col2 ="";
    	foreach ($data_cat as $data){
    		$data_cat_jason .= "{value: ".$data['count'].", label: '".$data['d.classification']."'},";
    		$col2 .= "'".$background_colors2[$o2]."',";
    		$o2++;
    	}
    	$data_cat_jason .= "]";
    	
    	
    	 
		//SELECT LAST_DAY(CURRENT_DATE)
    	 
    	
        return $this->render('PBundle:Stats:hf_nbr.html.twig',
			array(
        		"stats" => $data_st_jason,
				"couleur" => $col,
        		"stats_ctype" => $data_sty_jason,
				"couleur1" => $col1,
        		"stats_cat" => $data_cat_jason,
				"couleur2" => $col2,
				"stats_age" => $data_age_jason,
				"stats_an" => $data_an_jason,
				"sites" => $sites
        	)
		);
    }

	public function par_dateAction(Request $req){
		$em = $this->getDoctrine()->getManager();
		

		$sites = array();
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('libelle', 'd.libelleEtab');
		$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
				." GROUP BY d.libelleEtab"
				, $rsm)->getResult();
		 
		 
		$dataform = $req->request->all();
		$st = "";
		if(isset($dataform['site']) and $dataform['site'] != "Tous"){
			$st = " AND d.libelleEtab='".$dataform['site']."'";
		}
		
		$date = date("Y-m");
		if($req->get('startDate'))$date = $req->get('startDate');
		
		
		
		$d = explode("-", $date);
		$num = cal_days_in_month(CAL_GREGORIAN, $d[1], $d[0]);
		
		$tab = array();
		
		for ($i = 1 ;$i <= $num; $i++){
			if($i<10){
				$tab[$i] = array($date."-0".$i, 0, 0);
			}else{
				$tab[$i] = array($date."-".$i, 0, 0);
			}
			
		}
		
		//DATA POUR NOMBRE DE FEMMES ET HOMMES EMBOUCHES PAR MOIS DONNEE
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$rsm->addScalarResult('libelle', 'd.libelleSexe');
		$rsm->addScalarResult('dt', 'd.dateEntree');
		
		$data_all_jason = "";

		$data_f = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle, d.dateEntree as dt FROM data d"
				." WHERE (d.libelleSexe = 'Feminin' or d.libelleSexe = 'F')"
				." AND d.dateEntree like '".$date."-%'".$st
				." GROUP BY d.dateEntree"
				, $rsm)->getResult();
		
		$data_h = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle, d.dateEntree as dt FROM data d"
				." WHERE d.libelleSexe = 'Masculin'"
				." AND d.dateEntree like '".$date."-%'".$st
				." GROUP BY d.dateEntree"
				, $rsm)->getResult();
		
						
		$data_all_jason .= "[";
		foreach ($data_h as $data){
			for ($i = 1 ;$i <= $num; $i++){
				if($tab[$i][0] == $data['d.dateEntree']){
					$tab[$i][1] = $data['count'];
				}
			}
		}
		foreach ($data_f as $data){
			for ($i = 1 ;$i <= $num; $i++){
				if($tab[$i][0] == $data['d.dateEntree']){
					$tab[$i][2] = $data['count'];
				}
			}
		}
		for ($i = 1 ;$i <= $num; $i++){
			$data_all_jason .= "{y: '".$tab[$i][0]."', a: ".$tab[$i][1].", b:".$tab[$i][2]."},";
		}
		
		$data_all_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE EMBOUCHES PAR CAT PAR MOIS DONNEE
		$tab1 = array();
		$catstab = array("","");
		$cats_libelle = array();
		$o = 0;

		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('classification', 'd.classification');
		$cats = $em->createNativeQuery("SELECT d.classification"
				." FROM data d WHERE 1=1".$st
				." GROUP BY d.classification"
				." ORDER BY d.classification DESC"
				, $rsm)->getResult();
		$cats_json = "[";
		foreach ($cats as $c){
			$catstab[$o] = "";
			$cats_libelle[$o] = $c;
			$o++;
			$cats_json .= "'".$c['d.classification']."',";
		}
		$cats_json .= "]";
		
		for ($i = 1 ;$i <= $num; $i++){
			if($i<10){
				$t = range(0, count($catstab));
				$t[0] = $date."-0".$i;
				$tab1[$i] = $t;
			}else{
				$t = range(0, count($catstab));
				$t[0] = $date."-".$i;
				$tab1[$i] = $t;
			}				
		}
		
		$data_allt_jason = "[";
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		for ($i = 1 ;$i <= $num; $i++){
			$data_allt_jason .= "{y: '".$tab1[$i][0]."',";
			for($j = 0 ;$j < count($tab1[$i])-1; $j++){
				$datas = $em->createNativeQuery("SELECT count(*) as nbr, d.classification as classification"
					." FROM data d"
					." WHERE d.dateEntree = '".$tab1[$i][0]."'"
					." AND d.classification = '".$cats_libelle[$j]['d.classification']."'".$st
					, $rsm)->getResult();
				
				
				$tab1[$i][$j+1] = $datas[0]['count'];
				$data_allt_jason .= $cats_libelle[$j]['d.classification'].": ".$datas[0]['count'].",";
			}
			$data_allt_jason .= "},";
		}
		
		
		
		$data_allt_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		


		//DATA POUR NOMBRE EMBOUCHES PAR TYPE DE CONTRAT(CDI, CDD) PAR MOIS DONNEE
		$tab2 = array();
		$typetab = array("","");
		$types_libelle = array();
		$o = 0;
		
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('typeContrat', 'd.typeContrat');
		$types = $em->createNativeQuery("SELECT d.typeContrat"
				." FROM data d where 1=1".$st
				." GROUP BY d.typeContrat"
				." ORDER BY d.typeContrat DESC"
				, $rsm)->getResult();
		$types_json = "[";
		foreach ($types as $c){
			$typetab[$o] = "";
			$types_libelle[$o] = $c;
			$o++;
			$types_json .= "'".$c['d.typeContrat']."',";
		}
		$types_json .= "]";
		
		for ($i = 1 ;$i <= $num; $i++){
			if($i<10){
				$t = range(0, count($typetab));
				$t[0] = $date."-0".$i;
				$tab1[$i] = $t;
			}else{
				$t = range(0, count($typetab));
				$t[0] = $date."-".$i;
				$tab1[$i] = $t;
			}
		}
		
		$data_alltype_jason = "[";
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		for ($i = 1 ;$i <= $num; $i++){
			$data_alltype_jason .= "{y: '".$tab1[$i][0]."',";
			for($j = 0 ;$j < count($tab1[$i])-1; $j++){
				$datas = $em->createNativeQuery("SELECT count(*) as nbr, d.typeContrat as typeContrat"
						." FROM data d"
						." WHERE d.dateEntree = '".$tab1[$i][0]."'"
						." AND d.typeContrat = '".$types_libelle[$j]['d.typeContrat']."'".$st
						, $rsm)->getResult();
		
		
				$tab1[$i][$j+1] = $datas[0]['count'];
				$data_alltype_jason .= $types_libelle[$j]['d.typeContrat'].": ".$datas[0]['count'].",";
			}
			$data_alltype_jason .= "},";
		}
		
		
		
		$data_alltype_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		return $this->render('PBundle:Stats:par_date.html.twig',
			array(
				"stats" => $data_all_jason,
				"stats_cat" => $data_allt_jason,
				"cats" => $cats_json,
				"stats_types" => $data_alltype_jason,
				"types" => $types_json,
				"date" => $date,
				"sites" => $sites
			)
		);
	}

	public function indicateurAction(){
		$em = $this->getDoctrine()->getManager();
		$nbr = 0;
		
		
		$rsm = new ResultSetMappingBuilder($em);
		

		//TAUX DE FEMMES PAR RAPPORT AU HOMME
		$taux_femme = array();
		
		$rsm->addScalarResult('nb', 'count');
		$total = $em->createNativeQuery("select count(*) as nb from data"
				, $rsm)->getResult();
		
		for($i = 1; $i <= date("m"); $i++){
			$nbr++;
			$rsm->addScalarResult('nbr_f', 'count');
				
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_f = $em->createNativeQuery("SELECT count(*) as nbr_f"
					." FROM data d"
					." WHERE (d.libelleSexe = 'Feminin' or d.libelleSexe = 'F')"
					." AND d.dateEntree < '2014-".$a."-01'"
					, $rsm)->getResult();
				
			$calcul = round(($data_f[0]['count']*100)/$total[0]['count']);
			array_push($taux_femme, $calcul." %");
				
		}
		//----------------------------------
		
		


		//MOYENNE D'AGE
		$moyenne = array();
		
		for($i = 1; $i <= date("m"); $i++){
			$rsm->addScalarResult('age', 'd.age');
				
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_m = $em->createNativeQuery("SELECT SUM(d.age) as age"
					." FROM data d"
					." WHERE d.dateEntree < '2014-".$a."-01'"
					, $rsm)->getResult();
				
			$calcul = round($data_m[0]['d.age']/$total[0]['count']);
			array_push($moyenne, $calcul." ans");
				
		}
		//----------------------------------
		


		//TAUX ANCIEN
		$taux_anc = array();
		
		for($i = 1; $i <= date("m"); $i++){
			$rsm->addScalarResult('nbr', 'count');
				
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_m = $em->createNativeQuery("SELECT count(d.id) as nbr"
					." FROM data d"
					." WHERE d.dateEntree < '2014-".$a."-01'"
					." AND round(DATEDIFF(CURDATE(),dateEntree)/360)<5"
					, $rsm)->getResult();
				
			$calcul = $data_m[0]['count']/$total[0]['count'];
			array_push($taux_anc, $calcul." %");
				
		}
		//----------------------------------
		
		
		
		
		
		
		return $this->render("PBundle:Stats:indicateur.html.twig",
			array(
				'nbr_mois' => $nbr,
				'taux_femme' => $taux_femme,
				'moyenne' => $moyenne,
				'taux_anc' => $taux_anc
			)
		);
	}
}