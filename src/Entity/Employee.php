<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[ORM\Table('employees')]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: '`emp_no`', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 14)]
    private ?string $first_name = null;

    #[ORM\Column(length: 16)]
    private ?string $last_name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birth_date = null;

    #[ORM\Column(length: 1)]
    #[Assert\Choice(choices:['M', 'F', 'X'])]
    private ?string $gender = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $hire_date = null;

    #[ORM\JoinTable(name: 'dept_manager')]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no')]
    #[ORM\InverseJoinColumn(name: 'dept_no', referencedColumnName: 'dept_no')]
    #[ORM\ManyToMany(targetEntity: Department::class, mappedBy: 'managers')]
    private Collection $departments;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: DeptManager::class)]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no')]
    private Collection $managingStories;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Salary::class, orphanRemoval: false)]
    private Collection $salaries;

    #[ORM\OneToMany(mappedBy: 'employee', targetEntity: Demand::class)]
    private Collection $demands;

    #[ORM\JoinTable('dept_emp')]
    #[ORM\JoinColumn(name: 'emp_no', referencedColumnName: 'emp_no')]
    #[ORM\InverseJoinColumn(name:'dept_no', referencedColumnName: 'dept_no')]
    #[ORM\ManyToMany(mappedBy: 'employee', targetEntity: Department::class)]
    private Collection $deptEmps;
    //Storie departments

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->managingStories = new ArrayCollection();
        $this->salaries = new ArrayCollection();
        $this->demands = new ArrayCollection();
        $this->deptEmps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): self
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): self
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): self
    {
        $this->birth_date = $birth_date;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

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

    public function getHireDate(): ?\DateTimeInterface
    {
        return $this->hire_date;
    }

    public function setHireDate(\DateTimeInterface $hire_date): self
    {
        $this->hire_date = $hire_date;

        return $this;
    }

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->addManager($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            $department->removeManager($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DeptManager>
     */
    public function getManagingStories(): Collection
    {
        return $this->managingStories;
    }

    public function addManagingStory(DeptManager $managingStory): self
    {
        if (!$this->managingStories->contains($managingStory)) {
            $this->managingStories->add($managingStory);
            $managingStory->setEmployee($this);
        }

        return $this;
    }

    public function removeManagingStory(DeptManager $managingStory): self
    {
        if ($this->managingStories->removeElement($managingStory)) {
            // set the owning side to null (unless already changed)
            if ($managingStory->getEmployee() === $this) {
                $managingStory->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Salary>
     */
    public function getSalaries(): Collection
    {
        return $this->salaries;
    }

    public function addSalary(Salary $salary): self
    {
        if (!$this->salaries->contains($salary)) {
            $this->salaries->add($salary);
            $salary->setEmployee($this);
        }

        return $this;
    }

    public function removeSalary(Salary $salary): self
    {
        if ($this->salaries->removeElement($salary)) {
            // set the owning side to null (unless already changed)
            if ($salary->getEmployee() === $this) {
                $salary->setEmployee(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Demand>
     */
    public function getDemands(): Collection
    {
        return $this->demands;
    }

    public function addDemand(Demand $demand): self
    {
        if (!$this->demands->contains($demand)) {
            $this->demands->add($demand);
            $demand->setEmployee($this);
        }

        return $this;
    }

    public function removeDemand(Demand $demand): self
    {
        if ($this->demands->removeElement($demand)) {
            // set the owning side to null (unless already changed)
            if ($demand->getEmployee() === $this) {
                $demand->setEmployee(null);
            }
        }

        return $this;
    }

    public function __toString() :string {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * @return Collection<int, DeptEmp>
     */
    public function getDeptEmps(): Collection
    {
        return $this->deptEmps;
    }

    public function addDeptEmp(DeptEmp $deptEmp): self
    {
        if (!$this->deptEmps->contains($deptEmp)) {
            $this->deptEmps->add($deptEmp);
            $deptEmp->setEmployee($this);
        }

        return $this;
    }

    public function removeDeptEmp(DeptEmp $deptEmp): self
    {
        if ($this->deptEmps->removeElement($deptEmp)) {
            // set the owning side to null (unless already changed)
            if ($deptEmp->getEmployee() === $this) {
                $deptEmp->setEmployee(null);
            }
        }

        return $this;
    }
}