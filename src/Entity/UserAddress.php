<?php

namespace App\Entity;

use App\Repository\UserAddressRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAddressRepository::class)
 */
class UserAddress
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $address = '';

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="addresses",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __toString()
    {
        return $this->address;
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
