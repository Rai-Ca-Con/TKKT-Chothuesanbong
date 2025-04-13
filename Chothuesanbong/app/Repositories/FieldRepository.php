<?php
namespace App\Repositories;

use App\Models\Field;

class FieldRepository
{
    protected $model;

    public function __construct(Field $field)
    {
        $this->model = $field;
    }

    public function getAll()
    {
        return $this->model->with(['category', 'state', 'images'])->get();
    }

//    public function getAvailableFields()
//    {
//        return $this->model
//            ->whereNull('deleted_at')
//            ->with(['category', 'state', 'images'])
//            ->get();
//    }

    public function getAvailableFields($categoryId = null)
    {
        $query = $this->model
            ->whereNull('deleted_at')
            ->with(['category', 'state', 'images']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->get();
    }

    public function searchByName(string $keyword)
    {
        return $this->model
            ->where('name', 'like', '%' . $keyword . '%')
            ->with(['category', 'state', 'images']) // nếu muốn load các mối quan hệ
            ->get();
    }

    public function paginate($perPage = 10)
    {
        return $this->model->with(['category', 'state', 'images'])->paginate($perPage);
    }

    public function find($id)
    {
        return $this->model->with(['category', 'state', 'images'])->find($id);
    }

    public function findById($id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        $field = $this->model->create($data);
        $field->load(['state', 'category', 'images']);
        return $field;
    }

    public function update($id, array $data)
    {
        $field = $this->model->findOrFail($id);
        $field->update($data);
        $field->load(['state', 'category', 'images']);
        return $field;
    }

    public function delete($id)
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function findByIdAndIsDeleted($fieldId,$isDeleted)
    {
        return Field::where('id', $fieldId)
            ->whereNull('deleted_at')
            ->first();
    }
}
