<?php

declare(strict_types=1);

namespace App\Modules\Hospitalization\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Hospitalization\Actions\CreateNursingNoteAction;
use App\Modules\Hospitalization\DTOs\CreateNursingNoteDTO;
use App\Modules\Hospitalization\Repositories\NursingNoteRepositoryInterface;
use App\Modules\Hospitalization\Requests\StoreNursingNoteRequest;
use App\Modules\Hospitalization\Resources\NursingNoteResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NursingNoteController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected NursingNoteRepositoryInterface $repository
    ) {}

    public function index(string $hospitalizationUuid)
    {
        $this->authorize('viewAny', \App\Modules\Hospitalization\Models\NursingNote::class);
        return NursingNoteResource::collection(
            $this->repository->getByHospitalization($hospitalizationUuid)
        );
    }

    public function store(StoreNursingNoteRequest $request, CreateNursingNoteAction $action)
    {
        $this->authorize('create', \App\Modules\Hospitalization\Models\NursingNote::class);
        $note = $action->execute(CreateNursingNoteDTO::fromRequest($request));
        return (new NursingNoteResource($note))->response()->setStatusCode(201);
    }

    public function show(string $uuid)
    {
        $note = $this->repository->findByUuid($uuid);
        if (! $note) return response()->json(['message' => 'Note not found'], 404);
        $this->authorize('view', $note);
        return new NursingNoteResource($note);
    }

    public function update(StoreNursingNoteRequest $request, string $uuid)
    {
        $note = $this->repository->findByUuid($uuid);
        if (! $note) return response()->json(['message' => 'Note not found'], 404);
        $this->authorize('update', $note);
        $this->repository->update($uuid, $request->validated());
        return new NursingNoteResource($note->fresh());
    }

    public function destroy(string $uuid)
    {
        $note = $this->repository->findByUuid($uuid);
        if (! $note) return response()->json(['message' => 'Note not found'], 404);
        $this->authorize('delete', $note);
        $this->repository->delete($uuid);
        return response()->json(['message' => 'Note deleted'], 200);
    }
}
