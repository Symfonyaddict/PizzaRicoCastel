<?php

namespace App\Entity;

use App\Repository\BoissonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant une Boisson dans le système.
 * Utilise des callbacks de cycle de vie Doctrine pour générer automatiquement le slug.
 * Utilise VichUploader pour la gestion de l'image de la boisson.
 */
#[ORM\Entity(repositoryClass: BoissonRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]
class Boisson
{
    /**
     * @var int|null Identifiant unique de la boisson
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string|null Nom de la boisson
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    private ?string $name = null;

    /**
     * @var string|null Version formatée pour l'URL (slug) du nom de la boisson
     */
    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    /**
     * @var float|null Prix de la boisson
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    #[Assert\Range(min: 0.5, max: 100, notInRangeMessage: "Le prix doit être compris entre {{ min }}€ et {{ max }}€")]
    private ?float $price = null;

    /**
     * @var string|null Catégorie de la boisson (ex: Soda, Vin, Bière)
     */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $category = null;

    /**
     * @var string|null Contenance de la boisson (ex: 33cl, 75cl, 1.5L)
     */
    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(max: 20)]
    private ?string $capacity = null;

    /**
     * @var string|null Nom du fichier image stocké en base de données
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;
    
    /**
     * @var File|null Fichier physique de l'image (géré par VichUploader)
     */
    #[Vich\UploadableField(mapping: 'boisson_images', fileNameProperty: 'image')]
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getCapacity(): ?string
    {
        return $this->capacity;
    }

    public function setCapacity(?string $capacity): static
    {
        $this->capacity = $capacity;

        return $this;
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

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
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
}
