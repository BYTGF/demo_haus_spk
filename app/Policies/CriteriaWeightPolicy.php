<?php

namespace App\Policies;

use App\Models\CriteriaWeight;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CriteriaWeightPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CriteriaWeight $criteriaWeight): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CriteriaWeight $criteriaWeight): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CriteriaWeight $criteriaWeight): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CriteriaWeight $criteriaWeight): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CriteriaWeight $criteriaWeight): bool
    {
        //
    }
}
