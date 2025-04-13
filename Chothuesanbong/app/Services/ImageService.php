<?php

namespace App\Services;

use App\Repositories\ImageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageService
{
    protected $repository;

    public function __construct(ImageRepository $repository)
    {
        $this->repository = $repository;
    }

    // Upload và lưu ảnh
    public function uploadImage(Request $request, $fieldId)
    {
        // Kiểm tra xem có phải là nhiều ảnh hay không
        $images = $request->file('image');

        $paths = [];

        // Nếu là mảng ảnh (nhiều ảnh)
        if (is_array($images)) {
            foreach ($images as $image) {
                $paths[] = $this->saveImage($image); // Lưu từng ảnh và lấy đường dẫn
            }
            // Lưu nhiều ảnh vào DB
            $this->repository->storeMultiple($fieldId, $paths);
        } elseif ($images) {
            // Nếu chỉ có 1 ảnh
            $paths[] = $this->saveImage($images);
            $this->repository->storeMultiple($fieldId, $paths);
        }
    }

    // Lưu một ảnh vào server và trả về đường dẫn
    private function saveImage($imageFile)
    {
        $path = $imageFile->store('images', 'public');
        return 'storage/' . $path; // Lưu đường dẫn ảnh
    }

    // Xóa ảnh theo fieldId
    public function deleteByFieldId($fieldId)
    {
        // Lấy danh sách ảnh trước khi xóa DB
        $images = $this->repository->getByFieldId($fieldId);

        foreach ($images as $image) {
            $path = str_replace('storage/', '', $image->image_url);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Sau khi xóa file trong storage, xóa dữ liệu trong DB
        $this->repository->deleteByFieldId($fieldId);
    }


    // Lấy ảnh theo fieldId
    public function getByFieldId($fieldId)
    {
        return $this->repository->getByFieldId($fieldId);
    }

    public function saveImageInDisk($imageFile,$folder)
    {
        $path = $imageFile->store('images/'.$folder, 'public');
        return 'storage/' . $path; // Lưu đường dẫn ảnh
    }

    public function deleteImageInDisk($imageUrl)
    {
        $path = str_replace('storage/', '', $imageUrl);
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
