<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trailer;
use Illuminate\Support\Facades\DB;

class TrailerService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getPaginatedTrailers($search = null, $perPage = 10)
    {
        $query = Trailer::query();

        if ($search) {
            $query->where('trailer_number', 'like', "%$search%");
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function getTrailer($id)
    {
        return Trailer::findOrFail($id);
    }

    public function createTrailer(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $trailer = Trailer::create($data);
            $this->activityLogService->logActivity(
                $user,
                'trailer.created',
                "Created trailer {$trailer->trailer_number}",
                $trailer,
                [],
                $trailer->getAttributes()
            );
            return $trailer;
        });
    }

    public function updateTrailer($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $trailer = Trailer::findOrFail($id);
            $oldData = $trailer->getAttributes();
            $trailer->update($data);
            $this->activityLogService->logActivity(
                $user,
                'trailer.updated',
                "Updated trailer {$trailer->trailer_number}",
                $trailer,
                $oldData,
                $trailer->getAttributes()
            );
            return $trailer;
        });
    }

    public function deleteTrailer($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $trailer = Trailer::findOrFail($id);
            $oldData = $trailer->getAttributes();
            $trailerNumber = $trailer->trailer_number;
            $trailer->delete();
            $this->activityLogService->logActivity(
                $user,
                'trailer.deleted',
                "Deleted trailer {$trailerNumber}",
                $trailer,
                $oldData,
                []
            );
            return true;
        });
    }
}
