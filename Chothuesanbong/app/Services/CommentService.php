<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Http\Resources\CommentResource;
use App\Repositories\BookingRepository;
use App\Repositories\CommentRepository;
use App\Repositories\FieldRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommentService
{
    protected UserRepository $userRepository;
    protected CommentRepository $commentRepository;
    protected FieldRepository $fieldRepository;
    protected BookingRepository $bookingRepository;
    protected ImageService $imageService;


    public function __construct(CommentRepository $commentRepository, UserRepository $userRepository,
                                FieldRepository $fieldRepository, BookingRepository $bookingRepository, ImageService $imageService)
    {
        $this->userRepository = $userRepository;
        $this->commentRepository = $commentRepository;
        $this->fieldRepository = $fieldRepository;
        $this->bookingRepository = $bookingRepository;
        $this->imageService = $imageService;
    }

    public function findById($id)
    {
        $existComment = $this->commentRepository->findByIdAndIsDeleted($id);
        if ($existComment == null) {
            throw new AppException(ErrorCode::COMMENT_NON_EXISTED);
        }

        return new CommentResource($existComment);
    }

    public function findByFieldId($fieldId,$perPage)
    {
        $field = $this->fieldRepository->findByIdAndIsDeleted($fieldId, null);
        if ($field == null) {
            throw new AppException(ErrorCode::FIELD_NOT_FOUND);
        }

        $comments = $this->commentRepository->findByFieldId($fieldId,$perPage);
        return CommentResource::collection($comments);
    }


    public function createComment(array $data)
    {
        $user = $this->userRepository->findById($data['user_id']);
        $field = $this->fieldRepository->findByIdAndIsDeleted($data['field_id'], null);
        if(isset($data["parent_id"]) && $data["parent_id"] != null) { // neu co key comment cha thi
            $parentComment = $this->commentRepository->findByIdAndIsDeleted($data['parent_id']);
            if ($parentComment == null) { //check xem comment ton tai k
                throw new AppException(ErrorCode::COMMENT_NON_EXISTED);
            }

            if ($parentComment->parent_id != null) { // neu ton tai check xem co la comment con cua comment nao k -> comment 1 cap
                throw new AppException(ErrorCode::COMMENT_NOT_REPLY);
            }
        }

        if ($user == null) {
            throw new AppException(ErrorCode::USER_NON_EXISTED);
        }

        if ($field == null) {
            throw new AppException(ErrorCode::FIELD_NOT_FOUND);
        }

//        chi user da dat san do thi moi comment duoc
        $userBookedField = $this->bookingRepository->findByUserAndField($user->id, $field->id);
        if (!($userBookedField > 0) && $user->is_admin == 0) {
            throw new AppException(ErrorCode::UNAUTHORIZED_ACTION);
        }

        $data["status"] = 0;
        if(isset($data["image"]) && $data["image"] != null) { //neu co anh thi luu anh
            $data["image_url"] = $this->imageService->saveImageInDisk($data["image"],"comment");
        }

        $comment = $this->commentRepository->create($data);
        return new CommentResource($comment);
    }

    public function update($id, array $data)
    {
        $existComment = $this->commentRepository->findByIdAndIsDeleted($id);

        if ($existComment == null) {
            throw new AppException(ErrorCode::COMMENT_NON_EXISTED);
        }

        $user = $existComment->user_id;

        if ($user != $data["user_id"]) {
            throw new AppException(ErrorCode::UNAUTHORIZED);
        }

        // neu sua file anh cu -> anh moi se co key image va thuc hien xoa anh cu -> them anh moi
        if(isset($data["image"]) && $data["image"] != null) {
            $this->imageService->deleteImageInDisk($existComment->image_url);
            $data["image_url"] = $this->imageService->saveImageInDisk($data["image"],"comment");
        }
        else{
            // neu khong key(file) up len thi anh co the la k sua anh hoac xoa (khong co file ma chi co url anh) -> key image se la null
            // xoa thi status = 1 => thuc hien xoa anh
            if($data["image_status"] == 1) {
                $this->imageService->deleteImageInDisk($existComment->image_url);
                $data["image_url"] = "";
            }
            // neu k roi vao if == 1 thi $data["image_url"] = null
            // truong hop k sua anh ma giu nguyen thi da xu li o repo
        }

        $commentUpdate = $this->commentRepository->update($id, $data);

        return new CommentResource($commentUpdate);
    }

    public function delete($id, $currentUser, $role)
    {
        $existComment = $this->commentRepository->findByIdAndIsDeleted($id);

        if ($existComment == null) {
            throw new AppException(ErrorCode::COMMENT_NON_EXISTED);
        }

        if (!($currentUser == $existComment->user_id || $role == 1)) {
            throw new AppException(ErrorCode::UNAUTHORIZED);
        }

        return $this->commentRepository->delete($id);
    }

}
