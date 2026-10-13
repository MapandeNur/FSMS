<?php

namespace App\Policies;

use App\Models\StudentDocument;
use App\Models\User;

class StudentDocumentPolicy
{
    public function view(User $user, StudentDocument $document): bool
    {
        return $this->canAccessDocument($user, $document);
    }

    public function delete(User $user, StudentDocument $document): bool
    {
        return $this->canAccessDocument($user, $document);
    }

    public function download(User $user, StudentDocument $document): bool
    {
        return $this->canAccessDocument($user, $document);
    }

    private function canAccessDocument(
        User $user,
        StudentDocument $document
    ): bool {
        // Admin ana access yote.
        if ($user->hasRole('admin')) {
            return true;
        }

        // HR Officer ana access yote.
        if ($user->hasRole('hr_officer')) {
            return true;
        }

        // Student anaweza kufikia documents zake mwenyewe.
        if (
            $user->hasRole('student') &&
            $document->student?->user_id === $user->id
        ) {
            return true;
        }

        // Supervisor anaweza kufikia document ya student
        // ambaye ana active placement kwake.
        if ($user->hasRole('supervisor')) {
            return $document->student
                ?->placements()
                ->where('supervisor_id', $user->id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }
}