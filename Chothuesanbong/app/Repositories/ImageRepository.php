<?php

namespace App\Repositories;

use App\Models\Image;

class ImageRepository
{
    protected $model;

    public function __construct(Image $image)
    {
        $this->model = $image;
    }

    // Lưu 1 ảnh
    public function store($fieldId, $path)
    {
        $image = new Image();
        $image->image_url = $path;
        $image->field_id = $fieldId;
        $image->save();

        return $image;
    }

    // Lưu nhiều ảnh cùng lúc
    public function storeMultiple($fieldId, $paths)
    {
        foreach ($paths as $path) {
            $this->store($fieldId, $path); // Lưu từng ảnh
        }
    }

    // Xóa ảnh theo FieldId
    public function deleteByFieldId($fieldId)
    {
        $this->model->where('field_id', $fieldId)->delete();
    }

    public function getByFieldId($fieldId)
    {
        return $this->model->where('field_id', $fieldId)->get();
    }
}
