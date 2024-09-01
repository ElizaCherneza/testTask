<?php

namespace App\Services;
use App\Models\Guest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
// use Giggsey\PhoneNumber\PhoneNumberUtil;
// use Giggsey\PhoneNumber\NumberParseException;

class GuestService
{
    // Получаем список гостей
    public function getGuests(): Collection
    {
        $guests = Guest::get();
        return $guests;
    }

    // Метод для создания нового гостя
    public function createGuest(array $guestData): Guest
    {
        $validator = Validator::make($guestData, [
            'email' => 'required|email',
        ]);
    
        if ($validator->fails()) {
            // Если валидация не прошла, выбрасываем исключение с ошибками
            throw new ValidationException($validator);
        }

        // Проверяем, если country пусто
        if (empty($guestData["country"]) && !empty($guestData["phone"])) {

            $guestData["phone"] = $this->validatePhone($guestData["phone"]);
            $guestData["country"] = $this->getCountry($guestData["phone"]); 
        }

        $guest = Guest::create([
            "name" => $guestData["name"],
            "surname" => $guestData["surname"],
            "phone" => $guestData["phone"],
            "email" => $guestData["email"],
            "country" => $guestData["country"]
        ]);

        return $guest;
    }

    // Метод обновления записи гостя
    public function updateGuest(Guest $guest, array $guestData): Guest
    {
        $validator = Validator::make($guestData, [
            'email' => 'required|email',
        ]);
    
        if ($validator->fails()) {
            // Если валидация не прошла, выбрасываем исключение с ошибками
            throw new ValidationException($validator);
        }

        // Проверяем, если country пусто
        if (empty($guestData["country"]) && !empty($guestData["phone"])) {

            $guestData["phone"] = $this->validatePhone($guestData["phone"]);
            $guestData["country"] = $this->getCountry($guestData["phone"]); 
        }

        $guest->fill($guestData)->save();

        return $guest;
    }

    // Удаление записи гостя
    public function deleteGuest(Guest $guest): void
    {
        $guest->delete();
    }

    public function validatePhone($phone): string
    {
        // Удаляем все символы, кроме цифр и '+' в начале
        if (strpos($phone, '+') === 0) {
            // Если номер начинается с '+', сохраняем его и удаляем все остальные символы
            $phoneNumber = '+' . preg_replace('/[^\d]/', '', substr($phone, 1));
        } else {
            // Если '+' нет в начале, просто удаляем все нецифровые символы
            $phoneNumber = preg_replace('/\D/', '', $phone);
        }

        // Заменяем 8 на +7, если номер начинается с 8
        if (strpos($phoneNumber, '8') === 0) {
            $phoneNumber = '+7' . substr($phoneNumber, 1);
        }

        return $phoneNumber;
    }

    public function getCountry ($phone): string
    {
        $countryNames = [
            'RU' => 'Россия',
            'US' => 'Соединенные Штаты',
            'AB' => 'Абхазия',
            'AU' => 'Австралия',
            'AZ' => 'Азербайджан',
            'AI' => 'Ангилья',
            'AM' => 'Армения',
            'AF' => 'Афганистан',
            'BY' => 'Беларусь',
            'HU' => 'Венгрия',
            'DE' => 'Германия',
            'HK' => 'Гонконг',
            'GR' => 'Греция',
            'GE' => 'Грузия',
            'EG' => 'Египет',
            'IL' => 'Израиль',
            'IN' => 'Индия',
            'ES' => 'Испания',
            'IT' => 'Италия',
            'KZ' => 'Казахстан',
            'CA' => 'Канада',
            'CN' => 'Китай',
            'KP' => 'Корейская Народно-Демократическая Республика',
            'KR' => 'Республика Корея',
            'PK' => 'Пакистан',
            'RS' => 'Сербия',
            'UZ' => 'Узбекистан',
            'FR' => 'Франция',
            'JP' => 'Япония',

        ];
         
        $phoneUtil = PhoneNumberUtil::getInstance();
         
        try {
            // Парсим номер телефона
            $parsedNumber = $phoneUtil->parse($phone, null);

            // Получаем код страны
            $countryCode = $phoneUtil->getRegionCodeForNumber($parsedNumber);

            $countryName = $countryNames[$countryCode] ?? 'Unknown';

            $country = $countryName;
        } catch (NumberParseException $e) {
            
            // Если страна так и не опредилилась по номеру
            $country = 'Unknown';
        }

        return $country;
    }

}