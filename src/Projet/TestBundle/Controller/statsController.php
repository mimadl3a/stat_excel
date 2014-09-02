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
use Symfony\Component\HttpFoundation\Session\Session;

class statsController extends Controller
{
    public function hf_nbrAction(Request $req){
    	
    	$em = $this->getDoctrine()->getManager();
    	

    	$session = new Session();
    	CalendarController::notification($em, $session);
    	    	
    	
    	$sites = array();
    	$rsm = new ResultSetMappingBuilder($em);
    	$rsm->addScalarResult('libelle', 'd.libelleEtab');
    	$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
    		." GROUP BY d.libelleEtab"
    		, $rsm)->getResult();
    	
    	
    	$dataform = $req->request->all();
    	$st = "";
    	$site = "Tous";
    	if(isset($dataform['site']) and $dataform['site'] != "Tous"){
    		$st = " AND d.libelleEtab='".$dataform['site']."'";
    		$site = $dataform['site'];
    	}
    	$tab_data1 = array();
    	$tab_data1[0] = array("Homme");
    	$tab_data1[1] = array("Femme");
    	$tab_data1[2] = array("Total");
    	$t_h = 0;
    	$t_f = 0;

    	//DATA STATS POUR PYRAMIDE DES AGES
    	$ages = array(
    		'15 and 19.99','20 and 24.99','25 and 29.99','30 and 34.99','35 and 39.99'
    		,'40 and 44.99','45 and 49.99','50 and 54.99','55 and 59.99','60 and 64.99'
    	);
    	$ages0 = array(
    		'15 - 20','20 - 25','25 - 30','30 - 35','35 - 40',
    		'40 - 45','45 - 50','50 - 55','55 - 60','60 - 65',
    	);
    	$ages1 = array(
    		'15 <br>-<br> 20','20 <br>-<br> 25','25 <br>-<br> 30','30 <br>-<br> 35','35 <br>-<br> 40',
    		'40 <br>-<br> 45','45 <br>-<br> 50','50 <br>-<br> 55','55 <br>-<br> 60','60 <br>-<br> 65',
    		'Total'
    	);
    	
    	
    	$data_age_jason = "[";
    	$data_age_jason .= "['Age', 'Femme', 'Homme' ],";
    	
    	$i = 0;
    	foreach ($ages as $age){
    		$rsm = new ResultSetMappingBuilder($em);
    		$rsm->addScalarResult('nbr', 'count');
    	
    		$data_age_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE age between ".$age
    				." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    		array_push($tab_data1[1], $data_age_f[0]['count']);
    		$t_f += $data_age_f[0]['count'];
    		
    		$data_age_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE age between ".$age
    				." AND d.libelleSexe = 'Masculin'"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    		array_push($tab_data1[0], $data_age_h[0]['count']);
    		$t_h += $data_age_h[0]['count'];
    		
    		

    		array_push($tab_data1[2], $data_age_h[0]['count']+$data_age_f[0]['count']);
    	

    		$data_age_jason .= "['".str_replace(' and ', "-", $ages0[$i])." ans', ".$data_age_f[0]['count'].", -".$data_age_h[0]['count']."],";
    		$i++;
    		
    		
    	
    	}
    	//TOTAL
    	array_push($tab_data1[0], $t_h);
    	array_push($tab_data1[1], $t_f);
    	array_push($tab_data1[2], $t_h+$t_f);
    	
    	$data_age_jason .= "]";
    	 
    	
    	 
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	
    	 

    	//DATA STATS PAR ANCIENTE

    	$tab_data2 = array();
    	$tab_data2[0] = array("Homme");
    	$tab_data2[1] = array("Femme");
    	$tab_data2[2] = array("Total");
    	$t_h = 0;
    	$t_f = 0;

    	$nbr_annees = array(
    		'0 and 1.99','2 and 4.99','5 and 11.99','12 and 19.99','20 and 24.99','25 and 200'
    	);
    	$nbr_annees0 = array(
    		'0 - 2','2 - 5','5 - 12','12 - 20','20 - 25','25+'
    	);
    	$nbr_annees1 = array(
    		'0 <br>-<br> 2','2 <br>-<br> 5','5 <br>-<br> 12','12 <br>-<br> 20','20 <br>-<br> 25','25+', "Total"
    	);
    	 
    	 
    	$data_an_jason = "[";
    	$data_an_jason .= "['Ancien', 'Femme', 'Homme' ],";
    	 
    	$i=0;
    	foreach ($nbr_annees as $nbr_annee){
    		$rsm = new ResultSetMappingBuilder($em);
    		$rsm->addScalarResult('nbr', 'count');
    	
    		$data_an_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE DATEDIFF(CONCAT_WS('-',YEAR(CURDATE()),MONTH(CURDATE()),'01'),dateEntree)/365"
    				." between ".$nbr_annee
    				." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    		array_push($tab_data2[1], $data_an_f[0]['count']);
    		$t_f += $data_an_f[0]['count'];
    		
    		
    		$data_an_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
    				." WHERE DATEDIFF(CONCAT_WS('-',YEAR(CURDATE()),MONTH(CURDATE()),'01'),dateEntree)/365"
    				." between ".$nbr_annee
    				." AND d.libelleSexe = 'Masculin'"
    				." AND d.libSituation = 'Actif'".$st
    				, $rsm)->getResult();
    		array_push($tab_data2[0], $data_an_h[0]['count']);
    		$t_h += $data_an_h[0]['count'];
    		
    		

    		array_push($tab_data2[2], $data_an_h[0]['count']+$data_an_f[0]['count']);
    		
    		
    		/*
    		if($nbr_annee == "25 and 200"){
    			$data_an_jason .= "['+25 ans', ".$data_an_f[0]['count'].", -".$data_an_h[0]['count']."],";
    		}else{
    			$str = str_replace('.9', "",str_replace(' and ', "-", $nbr_annee));
    			$data_an_jason .= "['".$str." ans', ".$data_an_f[0]['count'].", -".$data_an_h[0]['count']."],";
    		}*/
    		$data_an_jason .= "['".$nbr_annees0[$i]." ans', ".$data_an_f[0]['count'].", -".$data_an_h[0]['count']."],";
    		$i++;
    	
    	}

    	array_push($tab_data2[0], $t_h);
    	array_push($tab_data2[1], $t_f);
    	array_push($tab_data2[2], $t_f+$t_h);
    	
    	$data_an_jason .= "]";
    	 
    	 
    	 
    	 
    	 
    	
    	
    	
    	
    	
    	
    	

    	//DATA POUR NOMBRE DE FEMMES ET HOMMES
    	$background_colors = array('#CD00CD', '#8B1C62', '#EE4000', '#FFEFD5');
    	$tab_data3 = "";
    	
    	
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
    		$tab_data3 .= "<b>".$data['d.libelleSexe']."</b>: ".$data['count']."<br>";
    		$data_st_jason .= "{value: ".$data['count'].", label: '".$data['d.libelleSexe']."'},";
    		$col .= "'".$background_colors[$o]."',";
    		$o++;
    	}
    	$data_st_jason .= "]";
    	 
    	 
    	 


    	//DATA POUR NOMBRE DE CDI ET CDD
    	$background_colors1 = array('#8B7500','#7FFF00', '#FFEFD5', '#EE4000', '#495E67');
    	$tab_data4 = "";
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
    		$tab_data4 .= "<b>".$data['d.typeContrat']."</b>: ".$data['count']."<br>";
    		$data_sty_jason .= "{value: ".$data['count'].", label: '".$data['d.typeContrat']."'},";
    		$col1 .= "'".$background_colors1[$o1]."',";
    		$o1++;
    	}
    	$data_sty_jason .= "]";
    	

    	
    	
    	
    	
    	

    	//DATA POUR NOMBRE PAR CATEGORIE
    	$background_colors2 = array('#495E67','#8B7500', '#EE4000', '#7FFF00', '#FFEFD5');
    	$tab_data5 = "";
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
    		$tab_data5 .= "<b>".$data['d.classification']."</b>: ".$data['count']."<br>";
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
				"sites" => $sites,
				"site" => $site,
				"tab_data1" => $tab_data1,
				"ages" => $ages1,
				"tab_data2" => $tab_data2,
				"nbr_annees" => $nbr_annees1,
				"tab_data3" => $tab_data3,
				"tab_data4" => $tab_data4,
				"tab_data5" => $tab_data5
        	)
		);
    }
	public function par_dateAction(Request $req){
		$em = $this->getDoctrine()->getManager();
		
		$session = new Session();
		CalendarController::notification($em, $session);

		$sites = array();
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('libelle', 'd.libelleEtab');
		$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
				." GROUP BY d.libelleEtab"
				, $rsm)->getResult();
		 
		 
		$dataform = $req->request->all();
		$st = "";
		$site = "Tous";
		if(isset($dataform['site']) and $dataform['site'] != "Tous"){
			$st = " AND d.libelleEtab='".$dataform['site']."'";
			$site = $dataform['site'];
		}
		
		$date = date("Y");
		if($req->get('startDate'))$date = $req->get('startDate');
				
		$tab = array();
		
		$tab_data1 = array();
		$tab_data1[0] = array("Homme", array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
		$tab_data1[1] = array("Femme", array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
		$tab_data1[2] = array("<b>Total</b>", array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
		
		
		for ($i = 1 ;$i <= 12; $i++){
			if($i<10){
				$tab[$i] = array($date."-0".$i, 0, 0);
			}else{
				$tab[$i] = array($date."-".$i, 0, 0);
			}			
		}
		$tab[13] = array("<b>Total</b>", 0, 0);;
		
		//DATA POUR NOMBRE DE FEMMES ET HOMMES EMBOUCHES PAR ANNEE DONNEE
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$rsm->addScalarResult('libelle', 'd.libelleSexe');
		$rsm->addScalarResult('dt', 'd.dateEntree');
		
		$data_all_jason = "";

		
		$data_f = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle,"
				." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt FROM data d"
				." WHERE (d.libelleSexe = 'Feminin' or d.libelleSexe = 'F')"
				." AND d.dateEntree like '".$date."-%'".$st
				." AND d.libSituation='Actif'"
				." GROUP BY dt"
				, $rsm)->getResult();
		$data_h = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle,"
				." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt FROM data d"
				." WHERE d.libelleSexe = 'Masculin'"
				." AND d.dateEntree like '".$date."-%'".$st
				." AND d.libSituation='Actif'"
				." GROUP BY dt"
				, $rsm)->getResult();

		$data_all_jason .= "[";
		foreach ($data_h as $data){
			for ($i = 1 ;$i <= 12; $i++){
				if($tab[$i][0] == $data['d.dateEntree']){
					$tab[$i][1] = $data['count'];
					$tab_data1[0][1][$i-1] = $data['count'];
					$tab_data1[2][1][$i-1] += $data['count'];
					$tab_data1[0][1][12] += $data['count'];
				}
			}
		}
		
		foreach ($data_f as $data){
			for ($i = 1 ;$i <= 12; $i++){
				if($tab[$i][0] == $data['d.dateEntree']){
					$tab[$i][2] = $data['count'];
					$tab_data1[1][1][$i-1] = $data['count'];
					$tab_data1[2][1][$i-1] += $data['count'];
					$tab_data1[1][1][12] += $data['count'];
				}
			}
		}
		$tab_data1[2][1][12] = $tab_data1[1][1][12]+$tab_data1[0][1][12];
		for ($i = 1 ;$i <= 12; $i++){
			$data_all_jason .= "{y: '".$tab[$i][0]."', a: ".$tab[$i][1].", b:".$tab[$i][2]."},";
		}
		
		$data_all_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE EMBOUCHES PAR CAT PAR ANNEE DONNEE
		$tab1 = array();
		$catstab = array("","");
		$cats_libelle = array();
		$o = 0;
		

		$tab_data2 = array();
		
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('classification', 'd.classification');
		$cats = $em->createNativeQuery("SELECT d.classification"
				." FROM data d WHERE 1=1".$st
				." GROUP BY d.classification"
				." ORDER BY d.classification DESC"
				, $rsm)->getResult();
		$cats_json = "[";
		foreach ($cats as $c){
			$tab_data2[$o] = array($c['d.classification'], array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
			$catstab[$o] = "";
			$cats_libelle[$o] = $c;
			$o++;
			$cats_json .= "'".$c['d.classification']."',";
		}
		$cats_json .= "]";
		$tab_data2[count($cats)+1] = array("<b>Total</b>", array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
		
		for ($i = 1 ;$i <= 12; $i++){
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
		
		$total = 0;
		$data_allt_jason = "[";
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		for ($i = 1 ;$i <= 12; $i++){
			$data_allt_jason .= "{y: '".$tab1[$i][0]."',";
			for($j = 0 ;$j < count($cats_libelle); $j++){
				$datas = $em->createNativeQuery("SELECT count(*) as nbr, d.classification as classification,"
						." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt "
						." FROM data d"
						." WHERE d.dateEntree like '".$tab1[$i][0]."-%'"
						." AND d.classification = '".$cats_libelle[$j]['d.classification']."'".$st
						." AND d.libSituation='Actif'"
						." GROUP BY dt"
						, $rsm)->getResult();
				
				
		
				if($datas){
					$tab1[$i][$j+1] = $datas[0]['count'];
					$data_allt_jason .= $cats_libelle[$j]['d.classification'].": ".$datas[0]['count'].",";

					$tab_data2[$j][1][$i-1] = $datas[0]['count'];
					$tab_data2[$j][1][12] += $datas[0]['count'];
					$tab_data2[count($cats)+1][1][$i-1] += $datas[0]['count'];
					$total +=  $datas[0]['count'];
				}else{
					$tab1[$i][$j+1] = "0";
					$data_allt_jason .= $cats_libelle[$j]['d.classification'].": 0,";

					$tab_data2[$j][1][$i-1] = "0";
				}
			}
			$data_allt_jason .= "},";
		}
		$tab_data2[count($cats)+1][1][12] = $total;
		
		
		
		$data_allt_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		//DATA POUR NOMBRE EMBOUCHES PAR TYPE DE CONTRAT(CDI, CDD) PAR MOIS DONNEE
		$tab2 = array();
		$typetab = array("","");
		$types_libelle = array();
		$o = 0;

		$tab_data3 = array();
		
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('typeContrat', 'd.typeContrat');
		$types = $em->createNativeQuery("SELECT d.typeContrat"
				." FROM data d where 1=1".$st
				." GROUP BY d.typeContrat"
				." ORDER BY d.typeContrat DESC"
				, $rsm)->getResult();
		$types_json = "[";
		foreach ($types as $c){
			$tab_data3[$o] = array($c['d.typeContrat'], array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
			$typetab[$o] = "";
			$types_libelle[$o] = $c;
			$o++;
			$types_json .= "'".$c['d.typeContrat']."',";
		}
		$types_json .= "]";
		$tab_data3[count($types)+1] = array("<b>Total</b>", array("0","0","0","0","0","0","0","0","0","0","0","0","0"));
		
		for ($i = 1 ;$i <= 12; $i++){
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
		
		$total = 0;
		$data_alltype_jason = "[";
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		for ($i = 1 ;$i <= 12; $i++){
			$data_alltype_jason .= "{y: '".$tab1[$i][0]."',";
			for($j = 0 ;$j < count($types_libelle); $j++){
				$datas = $em->createNativeQuery("SELECT count(*) as nbr, d.typeContrat as typeContrat,"
						." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt"
						." FROM data d"
						." WHERE d.dateEntree like '".$tab1[$i][0]."-%'"
						." AND d.typeContrat = '".$types_libelle[$j]['d.typeContrat']."'".$st
						." AND d.libSituation='Actif'"
						." GROUP BY dt"
						, $rsm)->getResult();
		
				if($datas){
					$tab1[$i][$j+1] = $datas[0]['count'];
					$data_alltype_jason .= $types_libelle[$j]['d.typeContrat'].": ".$datas[0]['count'].",";
					
					$tab_data3[$j][1][$i-1] = $datas[0]['count'];
					$tab_data3[$j][1][12] += $datas[0]['count'];
					$tab_data3[count($types)+1][1][$i-1] += $datas[0]['count'];
					$total += $datas[0]['count'];
					
				}else{
					$tab1[$i][$j+1] = "0";
					$data_alltype_jason .= $types_libelle[$j]['d.typeContrat'].": 0,";
					
					$tab_data3[$j][1][$i-1] = "0";
				}
				
			}
			$data_alltype_jason .= "},";
		}
		$tab_data3[count($types)+1][1][12] = $total;
		
		
		$data_alltype_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE DEPART PAR MOTIF PAR CAT PAR ANNEE DONNEE
		
		//RECUPERER LES MOTIFS
		$motifs_libelle = array();
		$oo = 0;
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('libSituation', 'd.libSituation');
		$motifs = $em->createNativeQuery("SELECT d.libSituation"
				." FROM data d WHERE d.libSituation<>'Actif'".$st
				." GROUP BY d.libSituation"
				." ORDER BY d.libSituation DESC"
				, $rsm)->getResult();
		
		foreach ($motifs as $c){
			$motifs_libelle[$oo][0] = $c;
				
			$cats_c = array();
				
			$rsm = new ResultSetMappingBuilder($em);
			foreach($cats as $cat){
				$rsm->addScalarResult('nbr', 'count');
				$count = $em->createNativeQuery("SELECT count(*) as nbr"
						." FROM data d WHERE d.libSituation='".$c['d.libSituation']."'"
						." AND d.classification = '".$cat['d.classification']."'"
						." AND d.dateEntreeSituation like '".$date."-%'".$st
						, $rsm)->getResult();
				array_push($cats_c, $count[0]['count']);
			}
			//TOTAL PAR LIGNE
			$t_l = 0;
			for($ia = 0; $ia<count($cats_c); $ia++){
				$t_l+=$cats_c[$ia];
			}
			array_push($cats_c, $t_l);
			//---------------
				
			$motifs_libelle[$oo][1] = $cats_c;
			$oo++;
		}
		$tab_total = array();
		for($uu = 0;$uu < count($cats)+1; $uu++){
			$total = 0;
			for($u = 1;$u <= count($motifs_libelle); $u++){
				$total += $motifs_libelle[$u-1][1][$uu];
			}
			array_push($tab_total, "<b><i>".$total."</i></b>");
		}
		$motifs_libelle[$oo][0] = array("d.libSituation"=>"<b><i>Total</i></b>");
		$motifs_libelle[$oo][1] = $tab_total;
		
		$cats_motif = $cats;
		array_push($cats_motif, array("d.classification" => "Total"));
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE DE PROMOS PAR ANNEE DONNEE
		
		//RECUPERER LES NOUVELLES SITUATIONS
		$npromo_libelle = array();
		$sexe = array("Masculin", "Feminin");
		$oo = 0;
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nouvelleSituation', 'd.nouvelleSituation');
		$libelles = $em->createNativeQuery("SELECT d.nouvelleSituation"
				." FROM data d WHERE d.nouvelleSituation<>''".$st
				." GROUP BY d.nouvelleSituation"
				." ORDER BY d.nouvelleSituation DESC"
				, $rsm)->getResult();
		
		foreach ($libelles as $c){
			$npromo_libelle[$oo][0] = $c;
				
			$promos_c = array();
				
			$rsm = new ResultSetMappingBuilder($em);
			foreach($sexe as $s){
				$rsm->addScalarResult('nbr', 'count');
				$count = $em->createNativeQuery("SELECT count(*) as nbr"
						." FROM data d WHERE d.nouvelleSituation='".$c['d.nouvelleSituation']."'"
						." AND d.libelleSexe = '".$s."'"
						." AND d.datePromo like '".$date."-%'".$st
						, $rsm)->getResult();
				array_push($promos_c, $count[0]['count']);
			}
			//TOTAL PAR LIGNE
			$t_l = 0;
			for($ia = 0; $ia<count($promos_c); $ia++){
				$t_l+=$promos_c[$ia];
			}
			array_push($promos_c, $t_l);
			//---------------
				
			$npromo_libelle[$oo][1] = $promos_c;
			$oo++;
		}
		$tab_total = array();
		for($uu = 0;$uu < count($sexe)+1; $uu++){
			$total = 0;
			for($u = 1;$u <= count($npromo_libelle); $u++){
				$total += $npromo_libelle[$u-1][1][$uu];
			}
			array_push($tab_total, "<b><i>".$total."</i></b>");
		}
		$npromo_libelle[$oo][0] = array("d.nouvelleSituation"=>"<b><i>Total</i></b>");
		$npromo_libelle[$oo][1] = $tab_total;
		
		array_push($sexe, "Total");
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		//TAUX DE SORTIE
		$tab_ratios = array();
		

		//NBR DEPART
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$nbr_depart = $em->createNativeQuery("SELECT count(*) as nbr"
				." FROM data d WHERE d.libSituation<>'Actif'"
				." AND d.dateEntreeSituation like '".$date."-%'".$st
				, $rsm)->getResult();
		$n_depart = array();
		array_push($n_depart, array("Nombre de départs",$nbr_depart[0]['count']));
		array_push($tab_ratios,$n_depart);//REMPLIR TAB FINAL

		


		//NBR TOTAL
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$nbr_total = $em->createNativeQuery("SELECT count(*) as nbr"
				." FROM data d WHERE d.libSituation = 'Actif'"
				." AND d.dateEntree < '".$date."-12-31'".$st
				, $rsm)->getResult();
		$t_total = array();
		array_push($t_total, array("Nombre total actif",$nbr_total[0]['count']));
		array_push($tab_ratios,$t_total);//REMPLIR TAB FINAL
		
		


		//TAUX 1
		$t1 = array();
		array_push($t1, array("<b>Taux de sortie</b>",substr((($n_depart[0][1]/$t_total[0][1])*100)/date("m"),0,8)." "));
		array_push($tab_ratios,$t1);//REMPLIR TAB FINAL 
		
		
		
		
		
		//-------------------------------------------------------------------------

		
		


		//NBR DEMISSION
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$nbr_dem = $em->createNativeQuery("SELECT count(*) as nbr"
				." FROM data d WHERE d.libSituation = 'Demissionaire'"
				." AND d.dateEntreeSituation like '".$date."-%'".$st
				, $rsm)->getResult();
		$tnbr_dem = array();
		array_push($tnbr_dem, array("Nombre de démissionaires",$nbr_dem[0]['count']));
		array_push($tab_ratios,$tnbr_dem);//REMPLIR TAB FINAL
		
		
		//NBR DEPART
		

		//TAUX 2
		$t2 = array();
		if($n_depart[0][1]>0){
			array_push($t2, array("<b>Taux de démission</b>",substr(($tnbr_dem[0][1]*100/$n_depart[0][1]),0,4)." %"));
		}else{
			array_push($t2, array("<b>Taux de démission</b>","0 %"));
		}

		array_push($tab_ratios,$n_depart);//REMPLIR TAB FINAL
		array_push($tab_ratios,$t2);//REMPLIR TAB FINAL
		
		
		





		//NBR TAUX REMPLACEMENT
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$nbr_remp = $em->createNativeQuery("SELECT count(*) as nbr"
				." FROM data d"
				." WHERE d.dateEntree like '".$date."-%'".$st
				, $rsm)->getResult();
		
		$tnbr_remp = array();
		array_push($tnbr_remp, array("Nombre des NR",$nbr_remp[0]['count']));
		array_push($tab_ratios,$tnbr_remp);//REMPLIR TAB FINAL
		
		
		//NBR DEPART
		
		
		//TAUX 3
		$t3 = array();
		if($n_depart[0][1]>0){
			array_push($t3, array("<b>Taux de remplacement</b>",substr(($tnbr_remp[0][1]*100/$n_depart[0][1]),0,8)." %"));
		}else{
			array_push($t3, array("<b>Taux de remplacement</b>","0 %"));
		}
		
		array_push($tab_ratios,$n_depart);//REMPLIR TAB FINAL
		array_push($tab_ratios,$t3);//REMPLIR TAB FINAL
		
		





		//NBR TAUX REMPLACEMENT HORS OUV.
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$nbr_remp_h = $em->createNativeQuery("SELECT count(*) as nbr"
				." FROM data d WHERE d.classification <> 'OUV'"
				." AND d.dateEntree like '".$date."-%'".$st
				, $rsm)->getResult();
		
		$tnbr_remp_h = array();
		array_push($tnbr_remp_h, array("Nombre des NR - <i>hors OUV</i>",$nbr_remp_h[0]['count']));
		array_push($tab_ratios,$tnbr_remp_h);//REMPLIR TAB FINAL
		
		
		//NBR DEPART
		
		
		//TAUX 4
		$t4 = array();
		if($n_depart[0][1]>0){
			array_push($t4, array("<b>Taux de remplacement</b>",substr(($tnbr_remp_h[0][1]*100/$n_depart[0][1]),0,8)." %"));
		}else{
			array_push($t4, array("<b>Taux de remplacement</b>","0 %"));
		}
		
		array_push($tab_ratios,$n_depart);//REMPLIR TAB FINAL
		array_push($tab_ratios,$t4);//REMPLIR TAB FINAL
		
		
		
		
		
		

		//NBR TURN OVER
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$total = $em->createNativeQuery("SELECT count(*) as nbr"
				." FROM data d WHERE"
				." d.dateEntree < '".$date."-12-31'".$st
				, $rsm)->getResult();
		
		$tnbr_trv = array();
		array_push($tnbr_trv, array("Total des effectifs",$total[0]['count']));
		
		
		//NBR DEPART
		
		
		//TAUX 5
		$t5 = array();
		if($n_depart[0][1]>0){
			array_push($t5, array("<b>Turn over</b>",substr(($nbr_remp[0]['count']+$nbr_depart[0]['count'])/$total[0]['count'],0,4)));
		}else{
			array_push($t5, array("<b>Turn over</b>","0 %"));
		}
		
		//array_push($tab_ratios,$n_depart);//REMPLIR TAB FINAL
		//array_push($tab_ratios,$tnbr_trv);//REMPLIR TAB FINAL
		array_push($tab_ratios,$t5);//REMPLIR TAB FINAL
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		return $this->render('PBundle:Stats:par_date.html.twig',
			array(
				"stats" => $data_all_jason,
				"stats_cat" => $data_allt_jason,
				"cats" => $cats_json,
				"stats_types" => $data_alltype_jason,
				"types" => $types_json,
				"date" => $date,
				"sites" => $sites,
				"motifs_data" => $motifs_libelle,
				"cts" => $cats_motif,
				"site" => $site,
				"npromo_libelle" => $npromo_libelle,
				"sexe" => $sexe,
				"tab_ratios" => $tab_ratios,
				"mois" => $tab,
				"tab_data1" => $tab_data1,
				"tab_data2" => $tab_data2,
				"tab_data3" => $tab_data3,
				
			)
		);
	}
	public function indicateurAction(Request $req){
		$em = $this->getDoctrine()->getManager();
		$nbr = 0;
		

		$dataform = $req->request->all();
		$st = "";
		$site = "Tous";
		if(isset($dataform['site']) and $dataform['site'] != "Tous"){
			$st = " AND d.libelleEtab='".$dataform['site']."'";
			$site = $dataform['site'];
		}
		
		$sites = array();
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('libelle', 'd.libelleEtab');
		$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
				." GROUP BY d.libelleEtab"
				, $rsm)->getResult();
			
		
		
		
		
		
		$session = new Session();
		CalendarController::notification($em, $session);
		
		$rsm = new ResultSetMappingBuilder($em);
		

		//TAUX DE FEMMES PAR RAPPORT AU HOMME
		$taux_femme = array();
		
		$rsm->addScalarResult('nb', 'count');
		$total = $em->createNativeQuery("select count(*) as nb from data d where d.libSituation='Actif'".$st
				, $rsm)->getResult();
		$tt = 0;
		for($i = 1; $i <= date("m"); $i++){
			$nbr++;
			$rsm->addScalarResult('nbr_f', 'count');
				
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_f = $em->createNativeQuery("SELECT count(*) as nbr_f"
					." FROM data d"
					." WHERE (d.libelleSexe = 'Feminin' or d.libelleSexe = 'F')"
					." AND d.dateEntree < '".date("Y")."-".$a."-".cal_days_in_month(CAL_GREGORIAN, $a, date("Y"))."'"
					." AND d.libSituation='Actif'".$st
					, $rsm)->getResult();
			
				
			$calcul = round(($data_f[0]['count']*100)/$total[0]['count'],2);
			array_push($taux_femme, $calcul." %");
			$tt += $calcul;
				
		}
		array_push($taux_femme, round($tt/date("m"),2)." %");
		//----------------------------------
		
		


		//MOYENNE D'AGE
		$moyenne = array();
		$tt = 0;
		for($i = 1; $i <= date("m"); $i++){
			$rsm->addScalarResult('age', 'd.age');
				
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_m = $em->createNativeQuery("SELECT SUM(d.age) as age"
					." FROM data d"
					." WHERE d.dateEntree < '".date("Y")."-".$a."-".cal_days_in_month(CAL_GREGORIAN, $a, date("Y"))."'"
					." AND d.libSituation='Actif'".$st
					, $rsm)->getResult();
				
			$calcul = round($data_m[0]['d.age']/$total[0]['count']);
			array_push($moyenne, $calcul." ans");
			$tt += $calcul;
		}
		array_push($moyenne, round($tt/date("m"))." ans");
		//----------------------------------
		


		//TAUX ANCIEN
		$taux_anc = array();
		$tt = 0;
		$ac = 0;
		for($i = 1; $i <= date("m"); $i++){
			$rsm->addScalarResult('nbr', 'count');
				
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_m = $em->createNativeQuery("SELECT count(d.id) as nbr"
					." FROM data d"
					." WHERE d.dateEntree <= '".date("Y")."-".$a."-".cal_days_in_month(CAL_GREGORIAN, $a, date("Y"))."'"
					." AND round(DATEDIFF('".date("Y")."-".$a."-01',dateEntree)/365)<5"
					." AND d.libSituation='Actif'".$st
					, $rsm)->getResult();
			
				
			$calcul = substr($data_m[0]['count']/$total[0]['count'],0,4);
			array_push($taux_anc, $calcul." ");
			$tt += $calcul;
				
		}
		array_push($taux_anc, substr($tt/date("m"),0,4)." ");
		//----------------------------------
		
		
		
		


		//TAUX DEPART
		$taux_dep = array();
		$tt = 0;
		
		for($i = 1; $i <= date("m"); $i++){
			$rsm->addScalarResult('nbr', 'count');
		
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_s = $em->createNativeQuery("SELECT count(d.id) as nbr"
					." FROM data d"
					." WHERE dateEntreeSituation like '".date("Y")."-".$a."-%'"
					." AND d.libSituation <> 'Actif'".$st
					, $rsm)->getResult();
		
			$calcul = substr(($data_s[0]['count']/$total[0]['count'])*100,0,8);
			array_push($taux_dep, $calcul." ");
			$tt += $calcul;
		
		}
		array_push($taux_dep, substr($tt/date("m"),0,8)." ");
		//----------------------------------
		
		
		
		
		//ROTATION PERSONNEL
		$taux_rot = array();
		$tt = 0;
		
		for($i = 1; $i <= date("m"); $i++){
			$rsm->addScalarResult('nbr', 'count');
		
			$a = $i;
			if($i<10)$a = "0".$i;
			$data_s = $em->createNativeQuery("SELECT count(d.id) as nbr"
					." FROM data d"
					." WHERE dateEntreeSituation like '".date("Y")."-".$a."-%'"
					." AND d.libSituation like 'Démissionaire'"
					, $rsm)->getResult();
			$data_e = $em->createNativeQuery("SELECT count(d.id) as nbr"
					." FROM data d"
					." WHERE d.dateEntree like '".date("Y")."-".$a."-%'".$st
					, $rsm)->getResult();
		
			$calcul = substr($data_s[0]['count'] + $data_e[0]['count']/$total[0]['count']*10,0,4);
			array_push($taux_rot, $calcul." ");
			$tt += $calcul;
		
		}
		array_push($taux_rot, round($tt/date("m"),2)." ");
		//----------------------------------
		
		
		
		
		
		
		return $this->render("PBundle:Stats:indicateur.html.twig",
			array(
				'nbr_mois' => $nbr,
				'taux_femme' => $taux_femme,
				'moyenne' => $moyenne,
				'taux_anc' => $taux_anc,
				'taux_dep' => $taux_dep,
				'taux_rot' => $taux_rot,
				'sites' => $sites,
				'site' => $site
			)
		);
	}

	
	
	
	
	
	
	
	
	
	
	
	
	public function exportAction(Request $req){

		$em = $this->getDoctrine()->getManager();
		$sites = array();
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('libelle', 'd.libelleEtab');
		$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
				." GROUP BY d.libelleEtab"
				, $rsm)->getResult();
		 
		 
		$dataform = $req->request->all();
		$st = "";
		$site = "Tous";
		if(isset($dataform['site']) and $dataform['site'] != "Tous"){
			$st = " AND d.libelleEtab='".$dataform['site']."'";
			$site = $dataform['site'];
		}
		$tab_data1 = array();
		$tab_data1[0] = array("Homme");
		$tab_data1[1] = array("Femme");
		$tab_data1[2] = array("Total");
		$t_h = 0;
		$t_f = 0;
		
		//DATA STATS POUR PYRAMIDE DES AGES
		$ages = array(
				'15 and 19.99','20 and 24.99','25 and 29.99','30 and 34.99','35 and 39.99'
				,'40 and 44.99','45 and 49.99','50 and 54.99','55 and 59.99','60 and 64.99'
		);
		$ages0 = array(
				'15 - 20','21 - 25','26 - 30','31 - 35','36 - 40',
				'41 - 45','46 - 50','51 - 55','56 - 60','61 - 65',
		);
		$ages1 = array(
				'15 <br>-<br> 20','21 <br>-<br> 25','26 <br>-<br> 30','31 <br>-<br> 35','36 <br>-<br> 40',
				'41 <br>-<br> 45','46 <br>-<br> 50','51 <br>-<br> 55','56 <br>-<br> 60','61 <br>-<br> 65',
				'Total'
		);
		 
		 
		$data_age_jason = "[";
		$data_age_jason .= "['Age', 'Femme', 'Homme' ],";
		 
		$i = 0;
		foreach ($ages as $age){
			$rsm = new ResultSetMappingBuilder($em);
			$rsm->addScalarResult('nbr', 'count');
			 
			$data_age_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
					." WHERE age between ".$age
					." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
					." AND d.libSituation = 'Actif'".$st
					, $rsm)->getResult();
			array_push($tab_data1[1], $data_age_f[0]['count']);
			$t_f += $data_age_f[0]['count'];
		
			$data_age_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
					." WHERE age between ".$age
					." AND d.libelleSexe = 'Masculin'"
					." AND d.libSituation = 'Actif'".$st
					, $rsm)->getResult();
			array_push($tab_data1[0], $data_age_h[0]['count']);
			$t_h += $data_age_h[0]['count'];
		
		
		
			array_push($tab_data1[2], $data_age_h[0]['count']+$data_age_f[0]['count']);
		
			$i++;
		
		
			 
		}
		//TOTAL
		array_push($tab_data1[0], $t_h);
		array_push($tab_data1[1], $t_f);
		array_push($tab_data1[2], $t_h+$t_f);
		 
		
		
		$html = "<h2>Statistiques par ages</h2>";
		$html .= "<table border='1' cellspacing='0' cellpadding='5' style='font-family:tahoma'>";
			//AGES
			$html .= "<tr>";
			$html .= "<td>Ages</td>";
				foreach ($ages1 as $age){
					$html .= "<td>".$age."</td>";
				}
			$html .= "<tr>";

			//DATA AGE
			foreach ($tab_data1 as $data){
				$html .= "<tr>";
				foreach ($data as $valeur){
					$html .= "<td>".$valeur."</td>";
				}
				$html .= "<tr>";
			}
		
		$html .= "</table>";
		
		
		
		
		
		
		
		
		

		//DATA STATS PAR ANCIENTE
		
		$tab_data2 = array();
		$tab_data2[0] = array("Homme");
		$tab_data2[1] = array("Femme");
		$tab_data2[2] = array("Total");
		$t_h = 0;
		$t_f = 0;
		
		$nbr_annees = array(
				'0 and 1.99','2 and 4.99','5 and 11.99','12 and 19.99','20 and 24.99','25 and 200'
		);
		$nbr_annees0 = array(
				'0 - 2','3 - 5','6 - 12','13 - 20','21 - 25','26+'
		);
		$nbr_annees1 = array(
				'0 <br>-<br> 2','3 <br>-<br> 5','6 <br>-<br> 12','13 <br>-<br> 20','21 <br>-<br> 25','26+', "Total"
		);
		
		
		$data_an_jason = "[";
		$data_an_jason .= "['Ancien', 'Femme', 'Homme' ],";
		
		$i=0;
		foreach ($nbr_annees as $nbr_annee){
			$rsm = new ResultSetMappingBuilder($em);
			$rsm->addScalarResult('nbr', 'count');
			 
			$data_an_f = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
					." WHERE DATEDIFF(CONCAT_WS('-',YEAR(CURDATE()),MONTH(CURDATE()),'01'),dateEntree)/365"
					." between ".$nbr_annee
					." AND (d.libelleSexe = 'Feminin' or d.libelleSexe = 'f')"
					." AND d.libSituation = 'Actif'".$st
					, $rsm)->getResult();
			array_push($tab_data2[1], $data_an_f[0]['count']);
			$t_f += $data_an_f[0]['count'];
		
		
			$data_an_h = $em->createNativeQuery("SELECT count(d.id) as nbr FROM data d"
					." WHERE DATEDIFF(CONCAT_WS('-',YEAR(CURDATE()),MONTH(CURDATE()),'01'),dateEntree)/365"
					." between ".$nbr_annee
					." AND d.libelleSexe = 'Masculin'"
					." AND d.libSituation = 'Actif'".$st
					, $rsm)->getResult();
			array_push($tab_data2[0], $data_an_h[0]['count']);
			$t_h += $data_an_h[0]['count'];
		
		
		
			array_push($tab_data2[2], $data_an_h[0]['count']+$data_an_f[0]['count']);
		
			$i++;
			 
		}
		
		array_push($tab_data2[0], $t_h);
		array_push($tab_data2[1], $t_f);
		array_push($tab_data2[2], $t_f+$t_h);
		
		
		
		
		
		
		
		$html .= "<h2>Statistiques par anciennet&eacute;</h2>";
		$html .= "<table border='1' cellspacing='0' cellpadding='5' style='font-family:tahoma'>";
			//AGES
			$html .= "<tr>";
			$html .= "<td>Annee</td>";
				foreach ($nbr_annees1 as $age){
					$html .= "<td>".$age."</td>";
				}
			$html .= "<tr>";

			//DATA ANCIENNETE
			foreach ($tab_data2 as $data){
				$html .= "<tr>";
				foreach ($data as $valeur){
					$html .= "<td>".$valeur."</td>";
				}
				$html .= "<tr>";
			}
		
		$html .= "</table>";
		
		
		
		
		
		
		
		
		
		
		


		//DATA POUR NOMBRE DE FEMMES ET HOMMES
		$background_colors = array('#CD00CD', '#8B1C62', '#EE4000', '#FFEFD5');
		$tab_data3 = "";
		 
		 
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
			$tab_data3 .= "<b>".$data['d.libelleSexe']."</b>: ".$data['count']."<br>";
			$col .= "'".$background_colors[$o]."',";
			$o++;
		}
		
		
		
		$html .= "<h2>Statistiques par sexe</h2>";
		$html .= "".$tab_data3;
		
		
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE DE CDI ET CDD
		$background_colors1 = array('#8B7500','#7FFF00', '#FFEFD5', '#EE4000', '#495E67');
		$tab_data4 = "";
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
			$tab_data4 .= "<b>".$data['d.typeContrat']."</b>: ".$data['count']."<br>";
			$col1 .= "'".$background_colors1[$o1]."',";
			$o1++;
		}
		
		
		$html .= "<h2>Statistiques par type de contrat</h2>";
		$html .= "".$tab_data4;
		
		
		
		
		
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE PAR CATEGORIE
		$background_colors2 = array('#495E67','#8B7500', '#EE4000', '#7FFF00', '#FFEFD5');
		$tab_data5 = "";
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
			$tab_data5 .= "<b>".$data['d.classification']."</b>: ".$data['count']."<br>";
			$col2 .= "'".$background_colors2[$o2]."',";
			$o2++;
		}
		$html .= "<h2>Statistiques par classification</h2>";
		$html .= "".$tab_data5;
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		

		$sites = array();
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('libelle', 'd.libelleEtab');
		$sites = $em->createNativeQuery("SELECT d.libelleEtab as libelle FROM data d"
				." GROUP BY d.libelleEtab"
				, $rsm)->getResult();
			
			
		$dataform = $req->request->all();
		$st = "";
		$site = "Tous";
		if(isset($dataform['site']) and $dataform['site'] != "Tous"){
			$st = " AND d.libelleEtab='".$dataform['site']."'";
			$site = $dataform['site'];
		}
		
		$date = date("Y");
		if($req->get('startDate'))$date = $req->get('startDate');
		
		$tab = array();
		
		$tab_data1 = array();
		$tab_data1[0] = array("Homme", array("0","0","0","0","0","0","0","0","0","0","0","0"));
		$tab_data1[1] = array("Femme", array("0","0","0","0","0","0","0","0","0","0","0","0"));
		
		
		for ($i = 1 ;$i <= 12; $i++){
			if($i<10){
				$tab[$i] = array($date."-0".$i, 0, 0);
			}else{
				$tab[$i] = array($date."-".$i, 0, 0);
			}
		}
		
		//DATA POUR NOMBRE DE FEMMES ET HOMMES EMBOUCHES PAR ANNEE DONNEE
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('nbr', 'count');
		$rsm->addScalarResult('libelle', 'd.libelleSexe');
		$rsm->addScalarResult('dt', 'd.dateEntree');
		
		$data_all_jason = "";
		
		
		$data_f = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle,"
				." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt FROM data d"
				." WHERE (d.libelleSexe = 'Feminin' or d.libelleSexe = 'F')"
				." AND d.dateEntree like '".$date."-%'".$st
				." AND d.libSituation='Actif'"
				." GROUP BY dt"
				, $rsm)->getResult();
		$data_h = $em->createNativeQuery("SELECT count(d.id) as nbr, d.libelleSexe as libelle,"
				." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt FROM data d"
				." WHERE d.libelleSexe = 'Masculin'"
				." AND d.dateEntree like '".$date."-%'".$st
				." AND d.libSituation='Actif'"
				." GROUP BY dt"
				, $rsm)->getResult();
		
		
		$data_all_jason .= "[";
		foreach ($data_h as $data){
			for ($i = 1 ;$i <= 12; $i++){
				if($tab[$i][0] == $data['d.dateEntree']){
					$tab[$i][1] = $data['count'];
					$tab_data1[0][1][$i-1] = $data['count'];
				}
			}
		}
		foreach ($data_f as $data){
			for ($i = 1 ;$i <= 12; $i++){
				if($tab[$i][0] == $data['d.dateEntree']){
					$tab[$i][2] = $data['count'];
					$tab_data1[1][1][$i-1] = $data['count'];
				}
			}
		}
		
		$data_all_jason .= "]";
		//END----------------------------------------------------------------------
		
		
		
		
		$html .= "<h2>Nombre d'embouch&eacute;s par sexe</h2>";
		$html .= "<table border='1' cellspacing='0' cellpadding='5' style='font-family:tahoma'>";
			$html .= "<tr><td>Date:</td>";
			for ($i = 1 ;$i <= 12; $i++){
				$html .= "<td>".$tab[$i][0]."</td>";
			}
			$html .= "</tr>";
			$html .= "<tr><td>Homme</td>";
			foreach ($tab_data1[0][1] as $data){
				$html .= "<td>".$data."</td>";
			}
			$html .= "</tr>";
			$html .= "<tr><td>Femme</td>";
			foreach ($tab_data1[1][1] as $data){
				$html .= "<td>".$data."</td>";
			}
			$html .= "</tr>";
		$html .= "</table>";
		
		
		
		
		
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE EMBOUCHES PAR CAT PAR ANNEE DONNEE
		$tab1 = array();
		$catstab = array("","");
		$cats_libelle = array();
		$o = 0;
		
		
		$tab_data2 = array();
		
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('classification', 'd.classification');
		$cats = $em->createNativeQuery("SELECT d.classification"
				." FROM data d WHERE 1=1".$st
				." GROUP BY d.classification"
				." ORDER BY d.classification DESC"
				, $rsm)->getResult();
		$cats_json = "[";
		foreach ($cats as $c){
			$tab_data2[$o] = array($c['d.classification'], array("0","0","0","0","0","0","0","0","0","0","0","0"));
			$catstab[$o] = "";
			$cats_libelle[$o] = $c;
			$o++;
			$cats_json .= "'".$c['d.classification']."',";
		}
		$cats_json .= "]";
		
		for ($i = 1 ;$i <= 12; $i++){
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
		for ($i = 1 ;$i <= 12; $i++){
			$data_allt_jason .= "{y: '".$tab1[$i][0]."',";
			for($j = 0 ;$j < count($cats_libelle); $j++){
				$datas = $em->createNativeQuery("SELECT count(*) as nbr, d.classification as classification,"
						." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt "
						." FROM data d"
						." WHERE d.dateEntree like '".$tab1[$i][0]."-%'"
						." AND d.classification = '".$cats_libelle[$j]['d.classification']."'".$st
						." AND d.libSituation='Actif'"
						." GROUP BY dt"
						, $rsm)->getResult();
		
		
		
				if($datas){
					$tab1[$i][$j+1] = $datas[0]['count'];						
					$tab_data2[$j][1][$i-1] = $datas[0]['count'];
				}else{
					$tab1[$i][$j+1] = "0";		
					$tab_data2[$j][1][$i-1] = "0";
				}
			}
		}
		
		
		
		//END----------------------------------------------------------------------
		
		
		
		
		
		$html .= "<h2>Nombre d'embouch&eacute;s par cat&eacute;gorie:</h2>";
		$html .= "<table border='1' cellspacing='0' cellpadding='5' style='font-family:tahoma'>";
		
		
		$html .= "<tr><td>Date:</td>";
		for ($i = 1 ;$i <= 12; $i++){
			$html .= "<td>".$tab[$i][0]."</td>";
		}
			$html .= "</tr>";
		
		for ($i = 0 ;$i <= count($tab_data2)-1; $i++){
			$html .= "<tr><td>".$tab_data2[$i][0]."</td>";
				for ($j = 1 ;$j <= 12; $j++){
					$html .= "<td>".$tab_data2[$i][1][$j-1]."</td>";
				}
			$html .= "</tr>";
		}
		
		$html .= "</table>";
		
		
		
		
		
		
		
		

		//DATA POUR NOMBRE EMBOUCHES PAR TYPE DE CONTRAT(CDI, CDD) PAR MOIS DONNEE
		$tab2 = array();
		$typetab = array("","");
		$types_libelle = array();
		$o = 0;
		
		$tab_data3 = array();
		
		$rsm = new ResultSetMappingBuilder($em);
		$rsm->addScalarResult('typeContrat', 'd.typeContrat');
		$types = $em->createNativeQuery("SELECT d.typeContrat"
				." FROM data d where 1=1".$st
				." GROUP BY d.typeContrat"
				." ORDER BY d.typeContrat DESC"
				, $rsm)->getResult();
		$types_json = "[";
		foreach ($types as $c){
			$tab_data3[$o] = array($c['d.typeContrat'], array("0","0","0","0","0","0","0","0","0","0","0","0"));
			$typetab[$o] = "";
			$types_libelle[$o] = $c;
			$o++;
			$types_json .= "'".$c['d.typeContrat']."',";
		}
		$types_json .= "]";
		
		for ($i = 1 ;$i <= 12; $i++){
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
		for ($i = 1 ;$i <= 12; $i++){
			$data_alltype_jason .= "{y: '".$tab1[$i][0]."',";
			for($j = 0 ;$j < count($types_libelle); $j++){
				$datas = $em->createNativeQuery("SELECT count(*) as nbr, d.typeContrat as typeContrat,"
						." DATE_FORMAT(d.dateEntree,'%Y-%m') as dt"
						." FROM data d"
						." WHERE d.dateEntree like '".$tab1[$i][0]."-%'"
						." AND d.typeContrat = '".$types_libelle[$j]['d.typeContrat']."'".$st
						." AND d.libSituation='Actif'"
						." GROUP BY dt"
						, $rsm)->getResult();
		
				if($datas){
					$tab1[$i][$j+1] = $datas[0]['count'];
					$tab_data3[$j][1][$i-1] = $datas[0]['count'];
				}else{
					$tab1[$i][$j+1] = "0";						
					$tab_data3[$j][1][$i-1] = "0";
				}
		
			}
		}
		
		//END----------------------------------------------------------------------
		
		
		
		
		
		
		
		
		
		$html .= "<h2>Nombre d'embouch&eacute;s par type de contrat:</h2>";
		$html .= "<table border='1' cellspacing='0' cellpadding='5' style='font-family:tahoma'>";
		
		
		$html .= "<tr><td>Date:</td>";
		for ($i = 1 ;$i <= 12; $i++){
			$html .= "<td>".$tab[$i][0]."</td>";
		}
			$html .= "</tr>";
		
		for ($i = 0 ;$i <= count($tab_data3)-1; $i++){
			$html .= "<tr><td>".$tab_data3[$i][0]."</td>";
				for ($j = 1 ;$j <= 12; $j++){
					$html .= "<td>".$tab_data3[$i][1][$j-1]."</td>";
				}
			$html .= "</tr>";
		}
		
		$html .= "</table>";
		
		
		
		
		
		
		
		
		$fichier = "Export__".date("Y-m-d")."__".time();
		return new Response(
				$this->get('knp_snappy.pdf')->getOutputFromHtml($html),
				200,
				array(
						'Content-Type'          => 'application/pdf',
						'Content-Disposition'   => 'attachment; filename="'.$fichier.'.pdf"'
				)
		);
		
		
		/*
		return new Response($html);*/
		
	}
}