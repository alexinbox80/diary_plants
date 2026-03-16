<?php //пользователь приложения

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Domain\ValueObject\User\Name;
use App\Domain\ValueObject\User\Email;
use App\Domain\ValueObject\User\Phone;
use App\Domain\Entity\Traits\CreatedAtTrait;
use App\Domain\Entity\Traits\DeletedAtTrait;
use App\Domain\Entity\Traits\UpdatedAtTrait;
use Webmozart\Assert\Assert as WebmozartAssert;
use App\Domain\Entity\Interfaces\EntityInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Domain\Entity\Interfaces\SoftDeletableInterface;
use App\Domain\Entity\Interfaces\HasMetaTimestampsInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Table(name: '`user`')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'user__email__ind', columns: ['email'])]
#[ORM\Index(name: 'user__phone__ind', columns: ['phone'])]
#[ORM\Index(name: 'user__refresh_token__ind', columns: ['refresh_token'])]
#[ORM\UniqueConstraint(name: 'user__email__uniq', columns: ['email'], options: ['where' => '(deleted_at IS NULL)'])]
#[ORM\UniqueConstraint(name: 'user__phone__uniq', columns: ['phone'], options: ['where' => '(deleted_at IS NULL)'])]
#[ORM\UniqueConstraint(name: 'user__refresh_token__uniq', columns: ['refresh_token'], options: ['where' => '(deleted_at IS NULL)'])]
class User implements EntityInterface, HasMetaTimestampsInterface, SoftDeletableInterface, UserInterface, PasswordAuthenticatedUserInterface
{
    use CreatedAtTrait, UpdatedAtTrait, DeletedAtTrait;

    //идентификатор
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    //электронная почта
    #[ORM\Column(type:'email', length: 255, nullable: false)]
    private Email $email;

    //пароль пользователя
    #[ORM\Column(name: 'password', type: 'string', length: 255, nullable: false)]
    private string $password;

    //роль пользователя
    #[ORM\Column(type: 'json', length: 1024, nullable: false)]
    private array $roles = [];

    //рефреш токен
    #[ORM\Column(name: 'refresh_token', type: 'string', length: 32, nullable: true)]
    private ?string $refreshToken = null;

    //флаг блокировки пользователя
    #[ORM\Column(name: 'is_active', type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    //фамилия имя отчество
    #[ORM\Embedded(class: Name::class, columnPrefix: false)]
    private Name $name;

    //телефон пользователя
    #[ORM\Column(type:'phone', length: 16, nullable: true)]
    private ?Phone $phone = null;

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
    #[ORM\ManyToOne(targetEntity: Group::class, cascade: ['all'], inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'id', nullable: false)]
    private Group $group;

    public function __construct(
        Group $group,
        Email $email,
        string $password,
        Name $name,
        array $roles = [],
    )
    {
        $this->group = $group;
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->roles = $roles;
    }

    public function changeName(Name $name): self
    {
        // Здесь можно добавить проверку, если новое имя совпадает со старым, ничего не делать
        $this->name = $name;

        return $this;
    }

    public function upgradePassword(string $hashedPassword): self
    {
        if (empty($hashedPassword)) {
            throw new \InvalidArgumentException('The password hash cannot be empty.');
        }
        $this->password = $hashedPassword;

        return $this;
    }

    public function moveToGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function suspend(): void
    {
        $this->isActive = false;
    }

    public function confirmEmail(): void
    {
        $this->emailConfirmed = true;
        $this->emailCode = null;
    }

    public function confirmPhone(): void
    {
        $this->phoneConfirmed = true;
        $this->phoneCode = null;
    }

    public function getAvatarLink(): ?string
    {
        return $this->avatarLink;
    }

    public function setAvatarLink(?string $avatarLink): self
    {
        $this->avatarLink = $avatarLink;

        return $this;
    }

    public function getTimeZone(): string
    {
        return $this->timeZone ?? 'Europe/Moscow';
    }

    public function setTimeZone(string $timeZone): self
    {
        $this->timeZone = $timeZone;

        return $this;
    }

    public function updateRefreshToken(?string $token): self
    {
        $this->refreshToken = $token;

        return $this;
    }

    public function generateEmailCode(string $code): void
    {
        $this->emailCode = $code;
        $this->emailConfirmed = false;
    }

    public function generatePhoneCode(string $code): void
    {
        $this->phoneCode = $code;
        $this->phoneConfirmed = false;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
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

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getName(): Name
    {
        return $this->name;
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
//        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function changeRole(string $role): self
    {
        $role = strtoupper($role);

        $this->roles = [];
        $this->roles[] = $role;

        return $this;
    }

    public function addRole(string $role): void
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
    }

    public function removeRole(string $role): void
    {
        if (($key = array_search(strtoupper($role), $this->roles, true)) !== false) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function setPhone(Phone $phone): self
    {
        $this->phone = $phone;

        return $this;
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
}
