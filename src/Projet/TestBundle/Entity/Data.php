<?php

namespace Projet\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Data
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Projet\TestBundle\Entity\DataRepository")
 */
class Data
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleEtab", type="string", length=255)
     */
    private $libelleEtab;

    /**
     * @var string
     *
     * @ORM\Column(name="Classification", type="string", length=255)
     */
    private $classification;

    /**
     * @var string
     *
     * @ORM\Column(name="libelleSexe", type="string", length=255)
     */
    private $libelleSexe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEntree", type="date", nullable=true)
     */
    private $dateEntree;

    /**
     * @var string
     *
     * @ORM\Column(name="libSituation", type="string", length=255)
     */
    private $libSituation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEntreeSituation", type="date", nullable=true)
     */
    private $dateEntreeSituation;

    /**
     * @var string
     *
     * @ORM\Column(name="typeContrat", type="string", length=255)
     */
    private $typeContrat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="date", nullable=true)
     */
    private $dateNaissance;

    /**
     * @var integer
     *
     * @ORM\Column(name="age", type="integer", nullable=true)
     */
    private $age;
    
    /**
     * @var string
     *
     * @ORM\Column(name="recrutement", type="string", length=255)
     */
    private $recrutement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="categorie", type="string", length=255)
     */
    private $categorie;

    /**
     * @var string
     *
     * @ORM\Column(name="typeContrat2", type="string", length=255)
     */
    private $typeContrat2;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set libelleEtab
     *
     * @param string $libelleEtab
     * @return Data
     */
    public function setLibelleEtab($libelleEtab)
    {
        $this->libelleEtab = $libelleEtab;

        return $this;
    }

    /**
     * Get libelleEtab
     *
     * @return string 
     */
    public function getLibelleEtab()
    {
        return $this->libelleEtab;
    }

    /**
     * Set classification
     *
     * @param string $classification
     * @return Data
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;

        return $this;
    }

    /**
     * Get classification
     *
     * @return string 
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * Set libelleSexe
     *
     * @param string $libelleSexe
     * @return Data
     */
    public function setLibelleSexe($libelleSexe)
    {
        $this->libelleSexe = $libelleSexe;

        return $this;
    }

    /**
     * Get libelleSexe
     *
     * @return string 
     */
    public function getLibelleSexe()
    {
        return $this->libelleSexe;
    }

    /**
     * Set dateEntree
     *
     * @param \DateTime $dateEntree
     * @return Data
     */
    public function setDateEntree($dateEntree)
    {
        $this->dateEntree = $dateEntree;

        return $this;
    }

    /**
     * Get dateEntree
     *
     * @return \DateTime 
     */
    public function getDateEntree()
    {
        return $this->dateEntree;
    }

    /**
     * Set libSituation
     *
     * @param string $libSituation
     * @return Data
     */
    public function setLibSituation($libSituation)
    {
        $this->libSituation = $libSituation;

        return $this;
    }

    /**
     * Get libSituation
     *
     * @return string 
     */
    public function getLibSituation()
    {
        return $this->libSituation;
    }

    /**
     * Set dateEntreeSituation
     *
     * @param \DateTime $dateEntreeSituation
     * @return Data
     */
    public function setDateEntreeSituation($dateEntreeSituation)
    {
        $this->dateEntreeSituation = $dateEntreeSituation;

        return $this;
    }

    /**
     * Get dateEntreeSituation
     *
     * @return \DateTime 
     */
    public function getDateEntreeSituation()
    {
        return $this->dateEntreeSituation;
    }

    /**
     * Set typeContrat
     *
     * @param string $typeContrat
     * @return Data
     */
    public function setTypeContrat($typeContrat)
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    /**
     * Get typeContrat
     *
     * @return string 
     */
    public function getTypeContrat()
    {
        return $this->typeContrat;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     * @return Data
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime 
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set recrutement
     *
     * @param string $recrutement
     * @return Data
     */
    public function setRecrutement($recrutement)
    {
        $this->recrutement = $recrutement;

        return $this;
    }

    /**
     * Get recrutement
     *
     * @return string 
     */
    public function getRecrutement()
    {
        return $this->recrutement;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Data
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     * @return Data
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set typeContrat2
     *
     * @param string $typeContrat2
     * @return Data
     */
    public function setTypeContrat2($typeContrat2)
    {
        $this->typeContrat2 = $typeContrat2;

        return $this;
    }

    /**
     * Get typeContrat2
     *
     * @return string 
     */
    public function getTypeContrat2()
    {
        return $this->typeContrat2;
    }

    /**
     * Set age
     *
     * @param integer $age
     * @return Data
     */
    public function setAge($age)
    {
        $this->age = $age;

        return $this;
    }

    /**
     * Get age
     *
     * @return integer 
     */
    public function getAge()
    {
        return $this->age;
    }
}
