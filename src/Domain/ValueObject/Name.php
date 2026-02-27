<?php

namespace App\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use Webmozart\Assert\Assert as WebmozartAssert;

#[ORM\Embeddable]
final readonly class Name
{
    //фамилия
    #[ORM\Column(name: 'first_name', type: 'string', length: 64)]
    private string $first;

    //имя
    #[ORM\Column(name: 'last_name', type: 'string', length: 64)]
    private string $last;

    //отчество
    #[ORM\Column(name: 'middle_name', type: 'string', length: 64, nullable: true, options: ['default' => null])]
    private ?string $middle;

    public function __construct(string $last, string $first, ?string $middle = null)
    {
        $this->lastNameValidate($last);
        $this->last = $last;

        $this->firstNameValidate($first);
        $this->first = $first;

        $this->middleNameValidate($middle);
        $this->middle = $middle;
    }

    private function lastNameValidate(string $lastName): void
    {
        WebmozartAssert::stringNotEmpty($lastName, 'Last name should not be empty. Got: %s');
        //WebmozartAssert::alpha($lastName, 'Last name should be in alphabet. Got: %s');
        WebmozartAssert::regex($lastName, '/^[a-zA-Zа-яА-ЯёЁ]+$/u', 'Last name should contain only letters (Latin or Cyrillic). Got: %s');
        WebmozartAssert::lengthBetween($lastName, 2, 64, 'The last name must be a string valid length of 2-64 letters. Got: %s');
    }

    private function firstNameValidate(string $firstName): void
    {
        WebmozartAssert::stringNotEmpty($firstName, 'First name should not be empty. Got: %s');
        //WebmozartAssert::alpha($firstName, 'First name should be in alphabet. Got: %s');
        WebmozartAssert::regex($firstName, '/^[a-zA-Zа-яА-ЯёЁ]+$/u', 'Last name should contain only letters (Latin or Cyrillic). Got: %s');
        WebmozartAssert::lengthBetween($firstName, 2, 64, 'The first name must be a string valid length of 2-64 letters. Got: %s');
    }

    private function middleNameValidate(?string $middleName = null): void
    {
        WebmozartAssert::nullOrString($middleName, 'The middle name must be a string valid length of 2-64 letters or null. Got: %s');
        if (!is_null($middleName))
        {
            //WebmozartAssert::alpha($middleName, 'Middle name should be in alphabet. Got: %s');
            WebmozartAssert::regex($middleName, '/^[a-zA-Zа-яА-ЯёЁ]+$/u', 'Last name should contain only letters (Latin or Cyrillic). Got: %s');
            WebmozartAssert::lengthBetween($middleName, 2, 64, 'The middle name must be a string valid length of 2-64 letters. Got: %s');
        }
    }

    public function getFirst(): string
    {
        return $this->first;
    }

    public function getLast(): string
    {
        return $this->last;
    }

    public function getMiddle(): ?string
    {
        return $this->middle;
    }

    public function getFull(): string
    {
        if ($this->middle)
            return $this->first . ' ' . $this->last . ' ' . $this->middle;
        else
            return $this->first . ' ' . $this->last;
    }

    public function isEqual(self $anotherName): bool
    {
        return ($this->first === $anotherName->getFirst())
            && ($this->last === $anotherName->getLast()
            && ($this->middle === $anotherName->getMiddle()));
    }

    public function __toString(): string
    {
        return $this->getFull();
    }
}
