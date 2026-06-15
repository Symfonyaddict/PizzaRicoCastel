<?php

namespace App\Entity;

use App\Repository\PizzaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Entité représentant une Pizza dans le système.
 * Utilise des callbacks de cycle de vie Doctrine pour générer automatiquement le slug.
 * Utilise VichUploader pour la gestion de l'image de la pizza.
 */
#[ORM\Entity(repositoryClass: PizzaRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Pizza
{
    /**
     * @var int|null Identifiant unique de la pizza
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom de la pizza
     */
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var string|null Liste des ingrédients de la pizza
     */
    #[ORM\Column(type: Types::TEXT)]
    private ?string $ingredient = null;

    /**
     * @var string|null Version formatée pour l'URL (slug) du nom de la pizza
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var string|null Nom du fichier image stocké en base de données
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    
    /**
     * @var File|null Fichier physique de l'image (géré par VichUploader)
     */
    #[Vich\UploadableField(mapping: 'pizza_images', fileNameProperty: 'image')]
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        mimeTypesMessage: 'Veuillez télécharger une image valide (JPEG, PNG, WEBP)',
    )]
    private ?File $imageFile = null;
    
    /**
     * @var \DateTimeInterface|null Date de dernière modification (nécessaire pour VichUploader)
     */
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @var float|null Prix de la pizza en taille moyenne
     */
    #[ORM\Column]
    private ?float $priceMedium = null;

    /**
     * @var float|null Prix de la pizza en taille grande
     */
    #[ORM\Column]
    private ?float $priceLarge = null;

    /**
     * @var bool|null Indique si c'est une pizza spéciale (ex: mise en avant)
     */
    #[ORM\Column]
    private ?bool $isSpecial = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        
        return $this;
    }

    public function getIngredient(): ?string
    {
        return $this->ingredient;
    }

    public function setIngredient(string $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
    
    /**
     * Génère automatiquement le slug à partir du nom
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function generateSlug(): void
    {
        if (empty($this->slug) || $this->slug === null) {
            $this->slug = $this->createSlug($this->getName());
        }
    }
    
    /**
     * Crée un slug à partir d'une chaîne de caractères
     */
    private function createSlug(string $string): string
    {
        // Convertir en minuscules et remplacer les espaces par des tirets
        $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $string), '-'));
        // Supprimer les caractères accentués
        $slug = str_replace(
            ['à','á','â','ã','ä', 'ç', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý','ÿ'],
            ['a','a','a','a','a', 'c', 'e','e','e','e', 'i','i','i','i', 'n', 'o','o','o','o','o', 'u','u','u','u', 'y','y'],
            $slug
        );
        return $slug;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }
    
    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    
    /**
     * @param File|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        
        if (null !== $imageFile) {
            // Il est nécessaire qu'au moins un champ change si vous utilisez Doctrine
            // sinon les écouteurs d'événements ne seront pas appelés et le fichier sera perdu
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }
    
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        
        return $this;
    }

    public function getPriceMedium(): ?float
    {
        return $this->priceMedium;
    }

    public function setPriceMedium(float $priceMedium): static
    {
        $this->priceMedium = $priceMedium;

        return $this;
    }

    public function getPriceLarge(): ?float
    {
        return $this->priceLarge;
    }

    public function setPriceLarge(float $priceLarge): static
    {
        $this->priceLarge = $priceLarge;

        return $this;
    }

    public function isSpecial(): ?bool
    {
        return $this->isSpecial;
    }

    public function setIsSpecial(bool $isSpecial): static
    {
        $this->isSpecial = $isSpecial;

        return $this;
    }
}
