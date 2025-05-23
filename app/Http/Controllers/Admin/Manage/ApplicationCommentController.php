<?php

namespace App\Http\Controllers\Admin\Manage;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Manage\ApplicationComment\CloneRequest;
use App\Http\Requests\Admin\Manage\ApplicationComment\SearchRequest;
use App\Http\Requests\Admin\Manage\ApplicationComment\StoreRequest;
use App\Http\Requests\Admin\Manage\ApplicationComment\UpdateRequest;
use App\Models\ApplicationComment;
use App\Services\Manage\Application\ApplicationService;
use App\Services\Manage\Application\CommentService;
use Illuminate\Http\JsonResponse;

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
        $comment = $commentService->clone($request->validated('id'), $request->validated('application_id'));

        return response()->json([
            'comment' => $comment,
        ]);
    }

    public function destroy(int $id, CommentService $commentService): JsonResponse
    {
        $commentService->delete($id);

        return response()->json([
            'message' => 'Successfully deleted',
        ]);
    }

    public function edit(int $id, CommentService $commentService): JsonResponse
    {
        $comment = $commentService->getById($id);

        return response()->json([
            'comment' => $comment,
        ]);
    }

    public function update(int $id, UpdateRequest $request, CommentService $commentService): JsonResponse
    {
        $commentService->update($id, $request->validated());

        return response()->json([
            'comment' => $commentService->getById($id),
        ]);
    }

    public function store(int $id, StoreRequest $request, CommentService $commentService, ApplicationService $applicationService): JsonResponse
    {
        $params = array_merge($request->validated(), ['created_by' => auth()->id()]);
        $createdComment = $commentService->create($id, $params);

        return response()->json([
            'comment' => $commentService->getById($createdComment->id),
        ]);
    }
}
