<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $date = null;

    #[ORM\Column(length: 255)]
    private ?string $heure = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_carte = null;

    #[ORM\Column(length: 255)]
    private ?string $type_carte = null;

    #[ORM\Column(length: 255)]
    private ?string $code_auth = null;

    #[ORM\Column(length: 255)]
    private ?string $montant_transaction = null;

    #[ORM\Column(length: 255)]
    private ?string $type_transaction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getNumeroCarte(): ?string
    {
        return $this->numero_carte;
    }

    public function setNumeroCarte(string $numero_carte): self
    {
        $this->numero_carte = $numero_carte;

        return $this;
    }

    public function getTypeCarte(): ?string
    {
        return $this->type_carte;
    }

    public function setTypeCarte(string $type_carte): self
    {
        $this->type_carte = $type_carte;

        return $this;
    }

    public function getCodeAuth(): ?string
    {
        return $this->code_auth;
    }

    public function setCodeAuth(string $code_auth): self
    {
        $this->code_auth = $code_auth;

        return $this;
    }

    public function getMontantTransaction(): ?string
    {
        return $this->montant_transaction;
    }

    public function setMontantTransaction(string $montant_transaction): self
    {
        $this->montant_transaction = $montant_transaction;

        return $this;
    }

    public function getTypeTransaction(): ?string
    {
        return $this->type_transaction;
    }

    public function setTypeTransaction(string $type_transaction): self
    {
        $this->type_transaction = $type_transaction;

        return $this;
    }
}
