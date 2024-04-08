<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    private ?string $oldPassword = null;

    #[Assert\Length(
        min: 8,
        minMessage: "Votre nouveau mot de passe doit comporter au minimum {{ limit }} caractères",
    )]
    private ?string $newPassword = null;

    #[Assert\EqualTo(
        propertyPath: "newPassword",
        message: "Vous n'avez pas correctement confirmé votre nouveau mot de passe"
    )]
    private ?string $newPasswordConfirmation = null;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): static
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getNewPasswordConfirmation(): ?string
    {
        return $this->newPasswordConfirmation;
    }

    public function setNewPasswordConfirmation(string $newPasswordConfirmation): static
    {
        $this->newPasswordConfirmation = $newPasswordConfirmation;

        return $this;
    }
}
