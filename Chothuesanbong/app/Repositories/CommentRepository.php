<?php

namespace App\Repositories;

use App\Models\Comment;

class CommentRepository
{
    public function findByFieldId($fieldId,$perPage = 10)
    {
        return Comment::with(['user','children.user'])
        ->where('field_id', $fieldId)
        ->whereNull('parent_id')
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);
    }

    public function findByIdAndIsDeleted($commentId)
    {
        return Comment::where('id', $commentId)
            ->whereNull('deleted_at')
            ->first();
    }

    public function create(array $data)
    {
        $comment = Comment::create($data);
        $comment->load('user'); //Load quan hệ user sau khi tạo

        return $comment;
    }

    public function update($id, array $data)
    {
        $comment = $this->findByIdAndIsDeleted($id);

        $comment->update([
            'content' => $data['content'],
            'image_url' => $data['image_url'] ?? $comment->image_url
        ]);

        return $comment->fresh()->load(['user', 'children.user']);
    }

    public function delete($id)
    {
        $isDeleted = Comment::findOrFail($id)->delete();
        if ($isDeleted) {
            return $id;
        }
        return false;
    }
}
