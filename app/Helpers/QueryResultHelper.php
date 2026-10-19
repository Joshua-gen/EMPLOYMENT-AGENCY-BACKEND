<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class QueryResultHelper
{
    public static function onSuccessPost($data): array
    {
        return [
            'status'  => true,
            'message' => 'New record added successfully',
            'data'    => $data,
        ];
    }

    public static function onSuccessGet($data): array
    {
        return [
            'status'  => true,
            'message' => 'Records fetched successfully',
            'data'    => $data,
        ];
    }

    public static function onSuccessPaginated(LengthAwarePaginator $paginator): array
    {
        return [
            'status'  => true,
            'message' => 'Records fetched successfully',
            'data'    => $paginator->items(),
            'meta'    => PaginationHelper::meta($paginator),
        ];
    }

    public static function onSuccessPut($data): array
    {
        return [
            'status'  => true,
            'message' => 'Record updated successfully',
            'data'    => $data,
        ];
    }

    public static function onSuccessDelete(): array
    {
        return [
            'status'  => true,
            'message' => 'Record deleted successfully',
        ];
    }

    public static function onError($e = null): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
        ];
    }

    public static function onCustomError(string $message): array
    {
        return [
            'status'  => false,
            'message' => $message,
        ];
    }

    public static function onCustomSuccess(string $message): array
    {
        return [
            'status'  => true,
            'message' => $message,
        ];
    }
}
