<?php

namespace App\Entity;

use App\Repository\OperationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=OperationRepository::class)
 */
class Operation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=25)
     */
    private $libelle;

    /**
     * @ORM\Column(type="text", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=StatutOperation::class, inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statutOperation;

    /**
     * @ORM\ManyToOne(targetEntity=TypeOperation::class, inversedBy="operations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeOperation;

    /**
     * @ORM\ManyToMany(targetEntity=Client::class, inversedBy="operations")
     */
    private $clients;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="operations")
     */
    private $utilisateur;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getStatutOperation(): ?StatutOperation
    {
        return $this->statutOperation;
    }

    public function setStatutOperation(?StatutOperation $statutOperation): self
    {
        $this->statutOperation = $statutOperation;

        return $this;
    }

    public function getTypeOperation(): ?TypeOperation
    {
        return $this->typeOperation;
    }

    public function setTypeOperation(?TypeOperation $typeOperation): self
    {
        $this->typeOperation = $typeOperation;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients[] = $client;
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        $this->clients->removeElement($client);

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function __toString() {
        return $this->libelle;
    }
}
