<?php

namespace Projet\TestBundle\classe;

use Ddeboer\DataImport\Reader\CsvReader;
use Projet\TestBundle\Entity\Data;

class MyData{
	public static $etat;

	public function __construct(){
		self::$etat = 0;
	}
	
	
	public function insert($em){
	    
	    for($i = 0; $i < 1780 ; $i++){
	    	self::$etat = $i;
	    }
	}
	
	public function getnb(){
		return self::$etat;
	}
	
}