<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
#[Index(name: 'idx_id', columns: ['id'])]
#[Index(name: 'idx_user_id', columns: ['user_id'])]
#[Index(name: 'idx_group_id', columns: ['group_id'])]
#[Index(name: 'idx_name', columns: ['name'])]
#[Index(name: 'idx_category', columns: ['category'])]
class Recipe
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id = '';

    #[ORM\Column(length: 36, name: 'user_id')]
    private string $userId;

    #[ORM\Column(length: 36, name: 'group_id', nullable: true)]
    private ?string $groupId = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $category = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $preparationTime = null;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $ingredients;

    /**
     * @var string[]
     */
    #[ORM\Column(type: 'json')]
    private array $steps = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(nullable: true)]
    private ?int $rating = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdOn;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'recipes')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private ?User $user;

    /**
     * @param string[] $ingredients
     * @param string[] $steps
     */
    public function __construct(
        string $id,
        User $user,
        ?string $groupId,
        string $name,
        ?string $category,
        ?string $description,
        \DateTimeInterface $preparationTime,
        array $ingredients,
        array $steps,
        ?string $image,
        ?int $rating,
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->userId = $user->getId();
        $this->groupId = $groupId;
        $this->name = $name;
        $this->category = $category;
        $this->description = $description;
        $this->preparationTime = $preparationTime;
        $this->ingredients = $ingredients;
        $this->steps = $steps;
        $this->image = $image;
        $this->rating = $rating;
        $this->createdOn = new \DateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUser(): User
    {
        if (null === $this->user) {
            throw new \LogicException('User not set, and Recipe always has a User.');
        }

        return $this->user;
    }

    public function setUserId(string $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function setGroupId(?string $groupId): static
    {
        $this->groupId = $groupId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getIngredients(): array
    {
        return $this->ingredients;
    }

    /**
     * @param string[] $products
     */
    public function setIngredients(array $products): static
    {
        $this->ingredients = $products;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param string[] $steps
     */
    public function setSteps(array $steps): static
    {
        $this->steps = $steps;

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

    public function getCreatedOn(): \DateTimeInterface
    {
        return $this->createdOn;
    }

    public function setCreatedOn(\DateTimeInterface $createdOn): static
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    public function getPreparationTime(): ?\DateTimeInterface
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(?\DateTimeInterface $preparationTime): static
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;

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
}
