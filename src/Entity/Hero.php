<?php

namespace App\Entity;

use App\Repository\HeroRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use DateTimeImmutable;

/**
 * Entité représentant la section Hero (bannière) du site.
 * Utilise VichUploader pour la gestion de l'image de fond.
 */
#[ORM\Entity(repositoryClass: HeroRepository::class)]
#[Vich\Uploadable]
class Hero
{
    /**
     * @var int|null Identifiant unique de l'entité
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Titre principal affiché dans la bannière
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le titre doit contenir au moins {{ limit }} caractères", maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères")]
    private ?string $title = null;

    /**
     * @var string|null Sous-titre ou texte secondaire de la bannière
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le sous-titre ne peut pas être vide")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le sous-titre doit contenir au moins {{ limit }} caractères", maxMessage: "Le sous-titre ne peut pas dépasser {{ limit }} caractères")]
    private ?string $subTitle = null;

    /**
     * @var string|null Nom du fichier de l'image de fond stocké en base de données
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $background = null;
    
    /**
     * @var File|null Fichier physique uploadé (non stocké en base, géré par VichUploader)
     */
    #[Vich\UploadableField(mapping: "hero_images", fileNameProperty: "background")]
    #[Assert\Image(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png", "image/webp"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPEG, PNG, WEBP)"
    )]
    private ?File $backgroundFile = null;
    
    /**
     * @var DateTimeImmutable|null Date de dernière mise à jour, requise par VichUploader pour forcer la mise à jour de l'entité lors d'un changement de fichier
     */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSubTitle(): ?string
    {
        return $this->subTitle;
    }

    public function setSubTitle(string $subTitle): static
    {
        $this->subTitle = $subTitle;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): static
    {
        $this->background = $background;

        return $this;
    }
    
    /**
     * @param File|UploadedFile|null $backgroundFile
     */
    public function setBackgroundFile(?File $backgroundFile = null): void
    {
        $this->backgroundFile = $backgroundFile;
        
        if (null !== $backgroundFile) {
            // Il est nécessaire qu'au moins un champ change si vous utilisez Doctrine
            // sinon les écouteurs d'événements ne seront pas appelés et le fichier ne sera pas sauvegardé
            $this->updatedAt = new DateTimeImmutable();
        }
    }
    
    public function getBackgroundFile(): ?File
    {
        return $this->backgroundFile;
    }
    
    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(?DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }
}
