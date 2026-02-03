<?php //пользователь приложения

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;

#[ORM\Table(name: '`user`')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'plant__email__ind', columns: ['email'])]
#[ORM\Index(name: 'plant__refresh_token__ind', columns: ['refresh_token'])]
#[ORM\UniqueConstraint(name: 'user__email__uniq', fields: ['email'], options: ['where' => '(deleted_at IS NULL)'])]
class User implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    //электронная почта - логин в систему
    #[ORM\Column(name: 'email', type: 'string', length: 64, unique: true, nullable: false)]
    private string $email;

    //пароль пользователя
    #[ORM\Column(name: 'password', type: 'string', length: 64, nullable: false)]
    private string $password;

    //роль пользователя
    #[ORM\Column(type: 'json', length: 1024, nullable: false)]
    private array $roles = [];

    //рефреш токен
    #[ORM\Column(type: 'string', length: 32, unique: true, nullable: true)]
    private ?string $refreshToken = null;

    //флаг блокировки пользователя
    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    //фамилия
    #[ORM\Column(name: 'last_name', type: 'string', length: 64, nullable: false)]
    private string $lastName;

    //имя
    #[ORM\Column(name: 'first_name', type: 'string', length: 64, nullable: false)]
    private string $firstName;

    //отчество
    #[ORM\Column(name: 'middle_name', type: 'string', length: 64, nullable: true)]
    private ?string $middleName = null;

    //телефон пользователя
    #[ORM\Column(name: 'phone', type: 'string', length: 16, nullable: true)]
    private ?string $phone = null;

    //аватар пользователя
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $avatarLink = null;

    //код подтверждения электронной почты
    #[ORM\Column(name: 'email_code', type: 'string', length: 6, unique: false, nullable: true, options: ['default' => null])]
    private ?string $emailCode = null;

    //электронная почта подтверждена
    #[ORM\Column(name: 'email_confirmed', type: 'boolean', options: ['default' => false])]
    private bool $emailConfirmed = false;

    //код подтверждения телефона
    #[ORM\Column(name: 'phone_code', type: 'string', length: 6, unique: false, nullable: true, options: ['default' => null])]
    private ?string $phoneCode = null;

    //телефон подтвержден
    #[ORM\Column(name: 'phone_confirmed', type: 'boolean', options: ['default' => false])]
    private bool $phoneConfirmed = false;

    //часовой пояс пользователя
    #[ORM\Column(name: 'time_zone', type: 'string', length: 20, unique: false, nullable: false, options: ['default' => 'Europe/Moscow'])]
    private string $timeZone = 'Europe/Moscow';

    //идентификатор связанной сущности group
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], fetch: 'EAGER', inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id')]
    private Group $group;

    public function __construct(
        Group $group,
        string $email,
        string $password,
        string $lastName,
        string $firstName,
        ?string $middleName = null,
        array $roles = [],
        bool $isActive = true,
        ?string $refreshToken = null,
        ?string $phone = null,
        ?string $avatarLink = null,
        ?string $emailCode = null,
        ?bool $emailConfirmed = false,
        ?string $phoneCode = null,
        ?bool $phoneConfirmed = false,
        ?string $timeZone = 'Europe/Moscow'
    )
    {
        $this->setCommonFields(
            $group,
            $email,
            $password,
            $lastName,
            $firstName,
            $middleName,
            $roles,
            $isActive,
            $refreshToken,
            $phone,
            $avatarLink,
            $emailCode,
            $emailConfirmed,
            $phoneCode,
            $phoneConfirmed,
            $timeZone
        );
    }

    public function changeFields(
        $group,
        $email,
        $password,
        $lastName,
        $firstName,
        $middleName,
        $roles,
        $isActive,
        $refreshToken,
        $phone,
        $avatarLink,
        $emailCode,
        $emailConfirmed,
        $phoneCode,
        $phoneConfirmed,
        $timeZone
    ): void {
        $this->setCommonFields(
            $group,
            $email,
            $password,
            $lastName,
            $firstName,
            $middleName,
            $roles,
            $isActive,
            $refreshToken,
            $phone,
            $avatarLink,
            $emailCode,
            $emailConfirmed,
            $phoneCode,
            $phoneConfirmed,
            $timeZone
        );
    }

    private function setCommonFields(
        Group $group,
        string $email,
        string $password,
        string $lastName,
        string $firstName,
        ?string $middleName = null,
        array $roles = [],
        bool $isActive = true,
        ?string $refreshToken = null,
        ?string $phone = null,
        ?string $avatarLink = null,
        ?string $emailCode = null,
        ?bool $emailConfirmed = false,
        ?string $phoneCode = null,
        ?bool $phoneConfirmed = false,
        ?string $timeZone = 'Europe/Moscow'
    ): void {
        $this->group = $group;

        $this->emailValidate($email);
        $this->email = $email;
        $this->password = $password;

        $this->lastNameValidate($lastName);
        $this->lastName = $lastName;

        $this->firstNameValidate($firstName);
        $this->firstName = $firstName;

        $this->middleNameValidate($middleName);
        $this->middleName = $middleName;

        $this->roles = $roles;
        $this->isActive = $isActive;
        $this->refreshToken = $refreshToken;

        $this->phoneValidate($phone);
        $this->phone = $phone;

        $this->avatarLink = $avatarLink;
        $this->emailCode = $emailCode;
        $this->emailConfirmed = $emailConfirmed;
        $this->phoneCode = $phoneCode;
        $this->phoneConfirmed = $phoneConfirmed;
        $this->timeZone = $timeZone;
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

    private function emailValidate(?string $email = null): void
    {
        if (!is_null($email)) {
            WebmozartAssert::maxLength($email, 255, 'The email must be a 255 chars length. Got: %s');
            WebmozartAssert::email($email, 'The email must be a valid email address. Got: %s');
        }
    }

    private function phoneValidate(?string $phone = null): void
    {
        if (!is_null($phone)) {
            WebmozartAssert::maxLength($phone, 16, 'The phone must be a 16 chars length. Got: %s');
            $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
            WebmozartAssert::notEmpty($digitsOnly, 'The phone must contain digits. Got: %s');
            WebmozartAssert::regex($digitsOnly, '/^[0-9]{10,11}$/', 'The phone must contain 10-11 digits. Got: %s');
        }
    }

    public function getId(): int
    {
        WebmozartAssert::notNull($this->id, sprintf('Id of Entity %s is null.', get_class($this)));
        return $this->id;
    }

    public function getGroup(): Group
    {
        return $this->group;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = RoleEnum::ROLE_USER->value;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getAvatarLink(): ?string
    {
        return $this->avatarLink;
    }

    public function getEmailCode(): ?string
    {
        return $this->emailCode;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed;
    }

    public function getPhoneCode(): ?string
    {
        return $this->phoneCode;
    }

    public function isPhoneConfirmed(): bool
    {
        return $this->phoneConfirmed;
    }

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }
}
