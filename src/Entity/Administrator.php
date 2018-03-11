<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Administrator - Represents a person who can administrate HAS.
 *
 * @author Christopher Anciaux <christopher.anciaux@gmail.com>
 */
class Administrator implements UserInterface, EquatableInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var bool
     */
    private $disabled = false;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param string $role
     *
     * @return bool True if the administrator has the given role, false otherwise
     */
    public function hasRole($role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * @param array $roles
     *
     * @return Administrator
     */
    public function setRoles(?array $roles): Administrator
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @param string $role
     *
     * @return Administrator
     */
    public function addRole(string $role): Administrator
    {
        $this->roles[] = $role;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return Administrator
     */
    public function setPassword(string $password): Administrator
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return Administrator
     */
    public function setUsername(?string $username): Administrator
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled(): ?bool
    {
        return $this->disabled;
    }

    /**
     * @param boolean $disabled
     *
     * @return Administrator
     */
    public function setDisabled(?bool $disabled): Administrator
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function eraseCredentials()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Administrator) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
