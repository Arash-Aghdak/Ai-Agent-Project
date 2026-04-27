<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var Collection<int, TaskRun>
     */
    #[ORM\OneToMany(targetEntity: TaskRun::class, mappedBy: 'createdBy')]
    private Collection $taskRuns;

    public function __construct()
    {
        $this->taskRuns = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;    
        $roles[] = 'ROLE_USER';


        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function eraseCredentials(): void
    {
        // اگر بعداً داده حساس موقت داشتی، اینجا پاک می‌کنی
    }

    /**
     * @return Collection<int, TaskRun>
     */
    public function getTaskRuns(): Collection
    {
        return $this->taskRuns;
    }

    public function addTaskRun(TaskRun $taskRun): static
    {
        if (!$this->taskRuns->contains($taskRun)) {
            $this->taskRuns->add($taskRun);
            $taskRun->setCreatedBy($this);
        }

        return $this;
    }

    public function removeTaskRun(TaskRun $taskRun): static
    {
        if ($this->taskRuns->removeElement($taskRun)) {
            // set the owning side to null (unless already changed)
            if ($taskRun->getCreatedBy() === $this) {
                $taskRun->setCreatedBy(null);
            }
        }

        return $this;
    }

    
}
