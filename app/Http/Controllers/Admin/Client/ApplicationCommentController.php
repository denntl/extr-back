<?php

namespace App\Http\Controllers\Admin\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Client\ApplicationComment\CloneRequest;
use App\Http\Requests\Admin\Client\ApplicationComment\SearchRequest;
use App\Http\Requests\Admin\Client\ApplicationComment\StoreRequest;
use App\Http\Requests\Admin\Client\ApplicationComment\UpdateRequest;
use App\Models\ApplicationComment;
use App\Services\Client\Application\ApplicationService;
use App\Services\Client\Application\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ApplicationCommentController extends Controller
{
    public function search(SearchRequest $request, CommentService $commentService): JsonResponse
    {
        return response()->json([
            'comments' => $commentService->getCommonComments($request->validated('query', ''))
                ->map(function (ApplicationComment $item) {
                    return [
                        'value' => $item->id,
                        'label' => $item->lang . ' | ' . $item->author_name . ' | ' . mb_substr($item->text, 0, 100) . ' ...',
                    ];
                }),
        ]);
    }

    public function clone(CloneRequest $request, CommentService $commentService, ApplicationService $applicationService): JsonResponse
    {
        Gate::check('manage', $applicationService->getByPublicId($request->validated('public_id'), false));

        $comment = $commentService->clone($request->validated('id'), $request->validated('public_id'));

        return response()->json([
            'comment' => $comment,
        ]);
    }

    public function destroy(int $id, CommentService $commentService): JsonResponse
    {
        $comment = $commentService->getById($id, false);
        Gate::check('manage', $comment);

        $commentService->delete($id);

        return response()->json([
            'message' => 'Successfully deleted',
        ]);
    }

    public function edit(int $id, CommentService $commentService): JsonResponse
    {
        Gate::check('manage', $commentService->getById($id, false));

        $comment = $commentService->getById($id);

        return response()->json([
            'comment' => $comment,
        ]);
    }

    public function update(int $id, UpdateRequest $request, CommentService $commentService): JsonResponse
    {
        Gate::check('manage', $commentService->getById($id, false));
        $commentService->update($id, $request->validated());

        return response()->json([
            'comment' => $commentService->getById($id),
        ]);
    }

    public function store(int $id, StoreRequest $request, CommentService $commentService, ApplicationService $applicationService): JsonResponse
    {
        Gate::check('manage', $applicationService->getByPublicId($id, false));

        $params = array_merge($request->validated(), ['created_by' => auth()->id()]);
        $createdComment = $commentService->create($id, $params);

        return response()->json([
            'comment' => $commentService->getById($createdComment->id),
        ]);
    }
}
