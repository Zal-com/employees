<?php

namespace App\Entity;

use App\Repository\SalaryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name:"salaries")]
#[ORM\UniqueConstraint(name: "emp_no_from_date_unique",
    columns: ["emp_no", "from_date"])]
#[ORM\Entity(repositoryClass: SalaryRepository::class)]
class Salary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(name: '`emp_no`')]
    private ?int $empNo = null;

    #[ORM\Column]
    private ?int $salary = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fromDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $toDate = null;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'salaries')]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no')]
    private ?Employee $employee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmpNo(): ?int
    {
        return $this->empNo;
    }

    public function getSalary(): ?int
    {
        return $this->salary;
    }

    public function setEmpNo(int $empNo): self
    {
        $this->empNo = $empNo;

        return $this;
    }

    public function setSalary(int $salary): self
    {
        $this->salary = $salary;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

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
}