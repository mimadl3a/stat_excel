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
    	// le chemin absolu du r�pertoire o� les documents upload�s doivent �tre sauvegard�s
    	return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }
    
    protected function getUploadDir()
    {
    	// on se d�barrasse de � __DIR__ � afin de ne pas avoir de probl�me lorsqu'on affiche
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
    		// faites ce que vous voulez pour g�n�rer un nom unique
    		$this->chemin = $this->file->getClientOriginalName();
    	}
    }
    
    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
	    public function upload()
	{
	    // la propri�t� � file � peut �tre vide si le champ n'est pas requis
	    if (null === $this->file) {
	        return;
	    }
	
	    // utilisez le nom de fichier original ici mais
	    // vous devriez � l'assainir � pour au moins �viter
	    // quelconques probl�mes de s�curit�
	
	    // la m�thode � move � prend comme arguments le r�pertoire cible et
	    // le nom de fichier cible o� le fichier doit �tre d�plac�
	    $this->file->move($this->getUploadRootDir(), $this->file->getClientOriginalName());
	
	    // d�finit la propri�t� � path � comme �tant le nom de fichier o� vous
	    // avez stock� le fichier
	    $this->path = $this->file->getClientOriginalName();
	
	    // � nettoie � la propri�t� � file � comme vous n'en aurez plus besoin
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
