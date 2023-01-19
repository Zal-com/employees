<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use App\Repository\DeptEmpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ORM\Table('departments')]
class Department
{
    #[ORM\Id]
    #[ORM\Column(name: '`dept_no`', type: 'string')]
    private ?string $id = null;

    #[ORM\Column(length: 40)]
    private ?string $dept_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roi_url = null;

    #[ORM\JoinTable(name: 'dept_manager')]
    #[ORM\JoinColumn(name: 'dept_no', referencedColumnName: 'dept_no')]
    #[ORM\InverseJoinColumn(name: 'emp_no', referencedColumnName: 'emp_no')]
    #[ORM\ManyToMany(targetEntity: Employee::class, inversedBy: 'departments')]
    private Collection $managers;


    #[ORM\OneToMany(mappedBy: 'department', targetEntity: DeptEmp::class)]
    private Collection $deptEmp;


    public function __construct()
    {
        $this->managers = new ArrayCollection();
        $this->deptEmp = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDeptName(): ?string
    {
        return $this->dept_name;
    }

    public function setDeptName(string $dept_name): self
    {
        $this->dept_name = $dept_name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getRoiUrl(): ?string
    {
        return $this->roi_url;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
    public function setRoiUrl(string $roi_url): self
    {
        $this->roi_url = $roi_url;

        return $this;
    }

    /**
     * @return Collection<int, Employee>
     */
    public function getManagers(): Collection
    {
        return $this->managers;
    }

    public function addManager(Employee $manager): self
    {
        if (!$this->managers->contains($manager)) {
            $this->managers->add($manager);
        }

        return $this;
    }

    public function removeManager(Employee $manager): self
    {
        $this->managers->removeElement($manager);

        return $this;
    }

    /**
     * @return Collection<int, DeptEmp>
     */
    public function getDeptEmp(): Collection
    {
        return $this->deptEmp;
    }

    public function addDeptEmp(DeptEmp $deptEmp): self
    {
        if (!$this->deptEmp->contains($deptEmp)) {
            $this->deptEmp->add($deptEmp);
            $deptEmp->setDepartment($this);
        }

        return $this;
    }

    public function removeDeptEmp(DeptEmp $deptEmp): self
    {
        if ($this->deptEmps->removeElement($deptEmp)) {
            // set the owning side to null (unless already changed)
            if ($deptEmp->getDepartment() === $this) {
                $deptEmp->setDepartment(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->id;
    }
}