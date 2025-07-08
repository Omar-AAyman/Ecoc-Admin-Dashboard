<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductService
{
    protected $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function getProduct($id)
    {
        return Product::findOrFail($id);
    }

    public function getPaginatedProducts($search = null, $perPage = 10)
    {
        $query = Product::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('density', 'like', "%$search%");
            });
        }

        return $query->orderBy('id', 'asc')->paginate($perPage);
    }

    public function createProduct(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $product = Product::create($data);
            $this->activityLogService->logActivity(
                $user,
                'product.created',
                "Created product {$product->name}",
                $product,
                [],
                $product->getAttributes()
            );
            return $product;
        });
    }

    public function updateProduct($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $product = Product::findOrFail($id);
            $oldData = $product->getAttributes();
            $product->update($data);
            $this->activityLogService->logActivity(
                $user,
                'product.updated',
                "Updated product {$product->name}",
                $product,
                $oldData,
                $product->getAttributes()
            );
            return $product;
        });
    }

    public function deleteProduct($id, User $user)
    {
        return DB::transaction(function () use ($id, $user) {
            $product = Product::findOrFail($id);
            if ($product->tanks()->exists() || $product->shipments()->exists() || $product->deliveries()->exists()) {
                throw new \Exception('Cannot delete product with associated tanks, shipments, or deliveries');
            }
            $oldData = $product->getAttributes();
            $productName = $product->name;
            $product->delete();
            $this->activityLogService->logActivity(
                $user,
                'product.deleted',
                "Deleted product {$productName}",
                $product,
                $oldData,
                []
            );
            return true;
        });
    }
}
