<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Source
 *
 * @ORM\Table(name="source")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SourceRepository")
 */
class Source
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Car", mappedBy="source")
     */
    private $cars;


    public function __construct()
    {
        $this->cars = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add car
     *
     * @param \AppBundle\Entity\Car $car
     *
     * @return $this
     */
    public function addCar(Car $car)
    {
        $this->cars[] = $car;

        return $this;
    }

    /**
     * Remove car
     *
     * @param \AppBundle\Entity\Car $car
     *
     * @return $this
     */
    public function removeCar(Car $car)
    {
        $this->cars->removeElement($car);

        return $this;
    }

    /**
     * Get cars
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCars()
    {
        return $this->cars;
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Source
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Source
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

