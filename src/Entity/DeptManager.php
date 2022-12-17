<?php

namespace App\Entity;

use App\Repository\DeptManagerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeptManagerRepository::class)]
class DeptManager
{
    #[ORM\Id]
    #[ORM\Column]
    private ?int $emp_no = null;

    #[ORM\Id]
    #[ORM\Column(length: 4)]
    private ?string $dept_no = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $from_date = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $to_date = null;

    #[ORM\ManyToOne(inversedBy: 'managingStories')]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no',nullable: false)]
    private ?Employee $employee = null;

    public function getEmpNo(): ?int
    {
        return $this->emp_no;
    }

    public function setEmpNo(int $emp_no): self
    {
        $this->emp_no = $emp_no;

        return $this;
    }

    public function getDeptNo(): ?string
    {
        return $this->dept_no;
    }

    public function setDeptNo(string $dept_no): self
    {
        $this->dept_no = $dept_no;

        return $this;
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
}