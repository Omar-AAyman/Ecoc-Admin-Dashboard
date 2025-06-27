<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function getProducts()
    {
        return Product::all();
    }

    public function getProduct($id)
    {
        return Product::findOrFail($id);
    }

    public function createProduct(array $data, User $user)
    {
        return DB::transaction(function () use ($data, $user) {
            $product = Product::create($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'product.created',
                'description' => "Created product {$product->name}",
                'model_type' => Product::class,
                'model_id' => $product->id,
            ]);
            return $product;
        });
    }

    public function updateProduct($id, array $data, User $user)
    {
        return DB::transaction(function () use ($id, $data, $user) {
            $product = Product::findOrFail($id);
            $product->update($data);
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'product.updated',
                'description' => "Updated product {$product->name}",
                'model_type' => Product::class,
                'model_id' => $product->id,
            ]);
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
            $product->delete();
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'product.deleted',
                'description' => "Deleted product {$product->name}",
                'model_type' => Product::class,
                'model_id' => $id,
            ]);
            return true;
        });
    }
}
