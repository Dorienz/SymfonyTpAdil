<?php

namespace App\Entity;

use App\Entity\Customer;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\InvoiceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;


#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
    attributes: [
        'normalization_context' => ['groups' => ['user:read', 'customer:read','invoice:read','invoice:write','customer:all']],
        'denormalization_context' => ['groups' => ['user:read', 'customer:read','invoice:read','invoice:write']],
        ["pagination_client_items_per_page" => true]
    ],
    collectionOperations: [
        "get"=> [
            "security" => "is_granted('ROLE_ADMIN')",
            "order"=>["amount"=>"desc"]
        ],
        "post" => ["security" => "is_granted('ROLE_ADMIN')"],
       
        ],
        itemOperations:[
            'get'    => ["security"  => "is_granted('ROLE_ADMIN') or object.customer == user.customers"],
        ]
)]
#[ApiFilter(SearchFilter::class, properties: ['chrono' => 'exact'])]
#[ApiFilter(OrderFilter::class, properties: ['id','amount'], arguments: ['orderParameterName' => 'order'])]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    #[Groups(['invoice:read', 'customer:read'])]
    private $amount;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['invoice:read', 'customer:read'])]
    private $sentAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['invoice:read', 'customer:read'])]
    private $status;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'invoices')]
    #[Groups(['customer:all'])]
    public $customer;

    #[ORM\Column(type: 'integer')]
    private $chrono;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getChrono(): ?int
    {
        return $this->chrono;
    }

    public function setChrono(int $chrono): self
    {
        $this->chrono = $chrono;

        return $this;
    }
}
