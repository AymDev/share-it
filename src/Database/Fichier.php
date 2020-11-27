<?php

namespace App\Database;

/**
 * Les objets de la classe Fichier représentent les données de la table "fichier"
 * 1 instance = 1 ligne
 */
class Fichier
{
    /*
     * PHP 7.4 et +
     *      private ?int $id = null;
     * PHP < 7.4:
     *      private $id;
     */
    private ?int $id = null;
    private ?string $nom = null;
    private ?string $nom_original = null;
    private ?string $type = null;
    private int $telechargements = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * self désigne la classe actuelle
     * @return self retourne l'objet actuel
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNomOriginal(): ?string
    {
        return $this->nom_original;
    }

    public function setNomOriginal(string $nom_original): self
    {
        $this->nom_original = $nom_original;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getTelechargements(): int
    {
        return $this->telechargements;
    }

    public function setTelechargements(int $telechargements): self
    {
        $this->telechargements = $telechargements;
        return $this;
    }

    public function incrementTelechargements(): self
    {
        $this->telechargements++;
        return $this;
    }
}