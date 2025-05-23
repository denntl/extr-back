<?php

namespace App\Policies;

use App\Models\ApplicationComment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicationCommentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function manage(User $user, ApplicationComment $applicationComment): bool
    {
        if ($applicationComment->created_by === $user->id) {
            return true;
        }

        $application = $applicationComment->application;
        if ($application && $applicationComment->application->company_id === $user->company_id) {
            return true;
        }

        abort(403);
    }
}
