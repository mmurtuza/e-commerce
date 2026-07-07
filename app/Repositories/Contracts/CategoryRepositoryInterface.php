<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getTree(): Collection;

    public function getActive(): Collection;

    public function findBySlug(string $slug): ?Category;

    public function findById(int $id): ?Category;

    public function getWithProductCount(): Collection;
}
