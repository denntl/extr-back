<?php

namespace App\Services\Manage\Application;

use App\Models\ApplicationComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class CommentService
{
    public function getCommonComments(string $searchQuery): Collection
    {
        $query = ApplicationComment::query()
            ->whereNull('application_id');

        $queryParts = array_slice(array_filter(explode(' ', $searchQuery)), 0, 5);

        if (count($queryParts)) {
            $query->where(function (Builder $builder) use ($queryParts, $searchQuery) {
                $builder->where('lang', mb_strtoupper($searchQuery))
                    ->orWhere('text', 'like', "%$searchQuery%")
                    ->orWhere('author_name', 'like', "%$searchQuery%");
                foreach ($queryParts as $queryPart) {
                    $builder->orWhere('lang', mb_strtoupper($queryPart))
                        ->orWhere('text', 'like', "%$queryPart%")
                        ->orWhere('author_name', 'like', "%$queryPart%");
                }
            });
        }

        return $query->limit(20)->get();
    }

    public function clone(int $commentId, int $appId): ApplicationComment
    {
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);
        $application = $applicationService->getById($appId);
        /** @var ApplicationComment $comment */
        $comment = ApplicationComment::query()->findOrFail($commentId);

        $newComment = $comment->replicate();
        $newComment->application_id = $application->id;
        $newComment->origin_id = $comment->id;
        $newComment->save();

        return $this->getById($newComment->id);
    }

    public function create(int $appId, array $data): ApplicationComment
    {
        /** @var ApplicationService $applicationService */
        $applicationService = app(ApplicationService::class);
        $application = $applicationService->getById($appId);

        $data['application_id'] = $application->id;
        $data['lang'] = mb_strtoupper($data['lang']);

        return ApplicationComment::query()->create($data);
    }

    public function getById(int $id, ?bool $public = true): ApplicationComment
    {
        $query = ApplicationComment::query();
        if ($public) {
            $query->select(['id', 'author_name', 'text', 'stars', 'lang', 'icon', 'answer', 'date', 'likes', 'answer_author']);
        }
        return $query->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        return ApplicationComment::query()
            ->where('id', $id)
            ->delete();
    }

    public function update(int $id, array $data): ApplicationComment
    {
        $comment = ApplicationComment::query()->findOrFail($id);
        $comment->update($data);

        return $comment;
    }
}
