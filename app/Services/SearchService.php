<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Product;
use App\Models\Tank;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vessel;
use Illuminate\Support\Facades\Route;

class SearchService
{
    public function search($user, $query, $category = 'all', $perPage = 10, $paginate = false)
    {
        $results = [];
        $query = trim($query);

        if (empty($query)) {
            return $paginate ? \Illuminate\Pagination\LengthAwarePaginator::make([], 0, $perPage) : [];
        }

        $isSuperAdmin = $user->isSuperAdmin();
        $isClient = $user->role_id == 3; // Client if role_id is 3, else admin

        // Define searchable categories and their configurations
        $categories = [
            'tanks' => [
                'model' => Tank::class,
                'fields' => ['id', 'number'],
                'route' => 'tanks.edit',
                'text' => fn($item) => $item->number,
                'url' => fn($item) => route('tanks.edit', $item->id),
                'condition' => fn($queryBuilder) => $isSuperAdmin ? $queryBuilder : $queryBuilder->where('company_id', $user->company_id),
            ],
            'transactions' => [
                'model' => Transaction::class,
                'fields' => ['id', 'work_order_number', 'bill_of_lading_number', 'customs_release_number', 'charge_permit_number', 'discharge_permit_number'],
                'route' => 'transactions.show',
                'text' => fn($item) => 'Transaction #' . $item->id . ' - ' . ($item->work_order_number ?? 'No work order'),
                'url' => fn($item) => route('transactions.show', $item->id),
                'condition' => fn($queryBuilder) => $isSuperAdmin ? $queryBuilder : $queryBuilder->where('company_id', $user->company_id),
            ],
            'clients' => [
                'model' => User::class,
                'fields' => ['id', 'first_name', 'last_name', 'phone', 'email', 'company.name'], // Include company.name
                'route' => 'clients.show',
                'text' => fn($item) => $item->full_name . ' - ' . ($item->email ?? 'No email') . ' - ' . ($item->company->name ?? 'No company'),
                'url' => fn($item) => route('clients.show', $item->id),
                'condition' => fn($queryBuilder) => $isSuperAdmin
                    ? $queryBuilder->with('company')
                    : $queryBuilder->with('company')->where('id', $user->id)->where('role_id', 3), // Filter for the current client
            ],
            'users' => [
                'model' => User::class,
                'fields' => ['id', 'first_name', 'last_name', 'phone', 'email'],
                'route' => 'users.show',
                'text' => fn($item) => $item->full_name . ' - ' . ($item->email ?? 'No email'),
                'url' => fn($item) => route('users.show', $item->id),
                'condition' => fn($queryBuilder) => $isSuperAdmin ? $queryBuilder : $queryBuilder->where('company_id', $user->company_id)->where('role_id', '!=', 3), // Exclude clients
            ],
            'products' => [
                'model' => Product::class,
                'fields' => ['name'],
                'route' => 'products.edit',
                'text' => fn($item) => $item->name,
                'url' => fn($item) => route('products.edit', $item->id),
                'condition' => fn($queryBuilder) => $queryBuilder,
            ],
            'vessels' => [
                'model' => Vessel::class,
                'fields' => ['name'],
                'route' => 'vessels.edit',
                'text' => fn($item) => $item->name,
                'url' => fn($item) => route('vessels.edit', $item->id),
                'condition' => fn($queryBuilder) => $queryBuilder,
            ],
        ];

        // Filter categories based on user role and category selection
        $searchCategories = ($category === 'all') ? array_keys($categories) : [$category];

        foreach ($searchCategories as $cat) {
            if (!isset($categories[$cat]) || !Route::has($categories[$cat]['route'])) {
                continue;
            }

            $queryBuilder = $categories[$cat]['model']::query();
            if (isset($categories[$cat]['condition'])) {
                $queryBuilder = $categories[$cat]['condition']($queryBuilder);
            }

            $queryBuilder->where(function ($q) use ($categories, $cat, $query) {
                foreach ($categories[$cat]['fields'] as $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $relatedField] = explode('.', $field);
                        $q->orWhereHas($relation, function ($subQuery) use ($relatedField, $query) {
                            $subQuery->where($relatedField, 'LIKE', "%{$query}%");
                        });
                    } elseif (str_contains($field, '->')) {
                        [$jsonField, $jsonKey] = explode('->', $field);
                        $q->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(`{$jsonField}`, '$.{$jsonKey}')) LIKE ?", ["%{$query}%"]);
                    } else {
                        $q->orWhere($field, 'LIKE', "%{$query}%");
                    }
                }
            });

            $items = $queryBuilder->take($perPage)->get();

            foreach ($items as $item) {
                $results[] = [
                    'type' => $cat,
                    'text' => $categories[$cat]['text']($item),
                    'url' => $categories[$cat]['url']($item),
                ];
            }
        }

        if ($paginate) {
            $total = count($results);
            $page = request()->input('page', 1);
            $offset = ($page - 1) * $perPage;
            $paginatedResults = array_slice($results, $offset, $perPage);
            return new \Illuminate\Pagination\LengthAwarePaginator($paginatedResults, $total, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);
        }

        return array_slice($results, 0, $perPage);
    }
}