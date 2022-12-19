<?php

namespace App\Entity;

use App\Repository\DeptEmpRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeptEmpRepository::class)]
#[ORM\Table('`dept_emp`')]
class DeptEmp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'emp_no', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $from_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $to_date = null;

    #[ORM\OneToMany(mappedBy: 'emp_no', targetEntity: Employee::class)]
    private ?Collection $employee = null;

    #[ORM\OneToMany(mappedBy: 'dept_no', targetEntity: Department::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collection $department = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->from_date;
    }

    public function setFromDate(\DateTimeInterface $from_date): self
    {
        $this->from_date = $from_date;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->to_date;
    }

    public function setToDate(\DateTimeInterface $to_date): self
    {
        $this->to_date = $to_date;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): self
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
