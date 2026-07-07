<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

interface OrderRepositoryInterface
{
    public function getByUser(int $userId, int $perPage = 10): LengthAwarePaginator;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function getByStatus(OrderStatus $status): Collection;

    public function getSalesReport(Carbon $from, Carbon $to): array;

    public function getRevenueByDate(Carbon $from, Carbon $to): Collection;
}
