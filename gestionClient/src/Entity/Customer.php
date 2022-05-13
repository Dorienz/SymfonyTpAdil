<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CustomerRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['customer:read','customer:all']],
        'denormalization_context' => ['groups' => ['customer:write']],
        ["pagination_client_items_per_page" => true]
    ],
    collectionOperations: [
        'get'    => [
            "security"  => "is_granted('ROLE_ADMIN')",
            "order"  => ["lastName"=>"asc"]
        ],
       
        ],
    itemOperations:[
        'get'    => ["security"  => "is_granted('ROLE_ADMIN') or object.user == user"]
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['firstName' => 'partial','lastName'=>'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['id','firstName','lastName'], arguments: ['orderParameterName' => 'order'])]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['invoice:read', 'customer:read'])]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['invoice:read', 'customer:read'])]
    private $lastName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['invoice:read', 'customer:read'])]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['invoice:read', 'customer:read'])]
    private $company;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'customers')]
    public $user;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: Invoice::class)]
    #[Groups(['invoice:read', 'customer:read','user:read'])]
    private $invoices;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(string $company): self
    {
        $this->company = $company;

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

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setCustomer($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getCustomer() === $this) {
                $invoice->setCustomer(null);
            }
        }

        return $this;
    }
}
