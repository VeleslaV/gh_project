<?php

namespace VelJo\GHProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Band
 * @package VelJo\GHProjectBundle\Entity
 *
 * @ORM\Entity(repositoryClass="VelJo\GHProjectBundle\Entity\BandRepository")
 */
class Band
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $albums;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $photos;

    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $songs;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="integer")
     */
    private $rating;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     *
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    public function __construct()
    {
        $this->albums = array();
        $this->photos = array();
        $this->songs = array();
        $this->rating = 0;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setName($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDescription($value)
    {
        $this->description = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setAlbums($value)
    {
        $this->albums = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setPhotos($value)
    {
        $this->photos = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function setSongs($value)
    {
        $this->songs = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getSongs()
    {
        return $this->songs;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setRating($value)
    {
        $this->rating = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param \DateTime $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        $this->updatedAt = $value;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
