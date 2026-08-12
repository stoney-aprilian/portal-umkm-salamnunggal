<?php

namespace App\Http\Requests\Owner;

/**
 * Product revision validation reuses the product creation rules:
 * the same documented fields with the same constraints.
 */
class UpdateProductRequest extends StoreProductRequest
{
}
