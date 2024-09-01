<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Services\GuestService;
use App\Services\ResponseService;
use App\Http\Requests\Guest\StoreUpdateRequest;
use App\Http\Resources\GuestResource;
use App\Models\Guest;

class GuestController extends Controller
{
    public function __construct(private GuestService $GuestService)
    { 

    }

    // Метод для получения списка гостей
    public function index(): JsonResponse
    {
        $guests = $this->GuestService->getGuests();

        return ResponseService::success(
            GuestResource::collection($guests)
        );
    }

    // Метод для получения записи гостя
    public function show(string $guestId): JsonResponse
    {
        $guest = Guest::find($guestId);

        if (!$guest) {
            return ResponseService::badRequest(message: 'Гость не найден в базе.');
        }

        return ResponseService::success(
            GuestResource::make($guest)
        );
    }

    // Метод для создания нового гостя
    public function store(StoreUpdateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $guest = $this->GuestService->createGuest($validated);

        return ResponseService::created(
            GuestResource::make($guest)
        );
    }

    // Метод обновления записи гостя
    public function update(string $sampleId, StoreUpdateRequest $request): JsonResponse
    {
        $guest = Guest::find($sampleId);

        if (!$guest) {
            return ResponseService::badRequest(message: 'Гость не найден в базе.');
        }

        $validated = $request->validated();
        $guest = $this->GuestService->updateGuest($guest, $validated);

        return ResponseService::success(
            GuestResource::make($guest)
        );
    }

    // Удаление записи гостя
    public function destroy(string|int $guestId): JsonResponse
    {
        $guest = Guest::find($guestId);

        if (!$guest) {
            return ResponseService::badRequest(message: 'Гость не найден в базе.');
        }

        $this->GuestService->deleteGuest($guest);

        return ResponseService::success(message: "Гость удален.");
    }

}
