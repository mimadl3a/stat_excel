<?php

namespace Projet\TestBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 *
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="Projet\TestBundle\Entity\DocumentRepository")
 */
class Document
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
     * @ORM\Column(name="chemin", type="string", length=255)
     */
    private $chemin;

    /**
     * @Assert\File(maxSize="6000000")
     */
    public $file;

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
     * Set chemin
     *
     * @param string $chemin
     * @return Document
     */
    public function setChemin($chemin)
    {
        $this->chemin = $chemin;

        return $this;
    }

    /**
     * Get chemin
     *
     * @return string 
     */
    public function getChemin()
    {
        return $this->chemin;
    }
    
    public function getAbsolutePath()
    {
    	return null === $this->chemin ? null : $this->getUploadRootDir().'/'.$this->chemin;
    }
    
    public function getWebPath()
    {
    	return null === $this->chemin ? null : $this->getUploadDir().'/'.$this->chemin;
    }
    
    protected function getUploadRootDir()
    {
    	// le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
    
    protected function getUploadDir()
    {
    	// on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
    	// le document/image dans la vue.
    	return 'upload';
    }
    
    
    
    
    
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
    	if (null !== $this->file) {
    		// faites ce que vous voulez pour générer un nom unique
    		$this->chemin = $this->file->getClientOriginalName();
    	}
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
	    public function upload()
	{
	    // la propriété « file » peut être vide si le champ n'est pas requis
	    if (null === $this->file) {
	        return;
	    }
	
	    // utilisez le nom de fichier original ici mais
	    // vous devriez « l'assainir » pour au moins éviter
	    // quelconques problèmes de sécurité
	
	    // la méthode « move » prend comme arguments le répertoire cible et
	    // le nom de fichier cible où le fichier doit être déplacé
	    $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
	
	    // définit la propriété « path » comme étant le nom de fichier où vous
	    // avez stocké le fichier
	    $this->path = $this->file->getClientOriginalName();
	
	    // « nettoie » la propriété « file » comme vous n'en aurez plus besoin
	    $this->file = null;
	}

    
    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
    	if ($file = $this->getAbsolutePath()) {
    		unlink($file);
    	}
    }
}
