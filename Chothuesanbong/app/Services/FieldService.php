<?php

namespace App\Services;

use App\Enums\ErrorCode;
use App\Exceptions\AppException;
use App\Repositories\FieldRepository;
use App\Services\FactoryService\Notification\NotificationFactory;
use App\Services\FactoryService\Notification\TelegramNotificationFactory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use GuzzleHttp\Client;

class FieldService
{
    protected $repository;
    protected $imageService;
    protected NotificationFactory $notificationFactory;

    public function __construct(FieldRepository $repository, ImageService $imageService)
    {
        $this->repository = $repository;
        $this->imageService = $imageService;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }

    public function paginate($perPage = 12)
    {
        return $this->repository->paginate($perPage);
    }

    public function findById($id)
    {
        if (!$this->repository->find($id)) {
            throw new AppException(ErrorCode::FIELD_NOT_FOUND);
        }
        $field = $this->repository->find($id);

        return $field;
    }

    public function searchByName(string $keyword)
    {
        return $this->repository->searchByName($keyword);
    }

    public function create(array $data, $imageRequest = null)
    {
        $field = $this->repository->create($data);

        if ($imageRequest && $imageRequest->hasFile('image')) {
            $this->imageService->uploadImage($imageRequest, $field->id);
        }

        $field->load(['category', 'state', 'images']);

        return $field;
    }

    public function update($id, array $data, $imageRequest = null)
    {
        $field = $this->repository->update($id, $data);

        if ($imageRequest && $imageRequest->hasFile('image')) {
            $this->imageService->deleteByFieldId($id);
            $this->imageService->uploadImage($imageRequest, $field->id);
        }
        $fieldUpdate = $field->load(['category', 'state', 'images']);

        //notify khi update thong tin san
        $this->notificationFactory = new TelegramNotificationFactory();
        $teleNotify = $this->notificationFactory->createNotification();
        $teleNotify->send($fieldUpdate, "cập nhật sân bóng !");

        return $fieldUpdate;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function getFilteredFields(Request $request, $perPage = 12)
    {
        $categoryId = $request->get('category_id');
        $userLat = $request->input('latitude');
        $userLng = $request->input('longitude');
        $perPage = $request->input('per_page', 12);

        $fields = $this->repository->getAvailableFields($categoryId);

//        $fields = $this->repository->getAvailableFields();

        $destinations = [];
        foreach ($fields as $field) {
            $destinations[] = [$field->longitude, $field->latitude];
        }

        $body = [
            'locations' => array_merge([[$userLng, $userLat]], $destinations),
            'metrics' => ['distance', 'duration'],
            'units' => 'km',
        ];

        $client = new Client();
        $response = $client->post('https://api.openrouteservice.org/v2/matrix/driving-car', [
            'headers' => [
                'Authorization' => env('OPENROUTESERVICE_API_KEY'),
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ],
            'json' => $body,
        ]);

        $data = json_decode($response->getBody(), true);
        $distances = $data['distances'][0]; // distance from user to each field
        $durations = $data['durations'][0];

        // Gắn khoảng cách vào field
        foreach ($fields as $index => $field) {
            $field->distance = round($distances[$index + 1], 2);
            $field->duration = round($durations[$index + 1] / 60, 2);
        }

        $sortedFields = $fields->sortBy('distance')->values(); // sort và reset index

        $page = request()->get('page', 1);

        $paginated = new LengthAwarePaginator(
            $sortedFields->forPage($page, $perPage)->values(),
            $sortedFields->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return $paginated;
    }
}
