<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Attribute;

class AttributeRepository
{
    public function getAll()
    {
        // جلب السمات مع قيمها
        return Attribute::with('values')->orderBy('id', 'desc')->get();
    }

    public function findById($id)
    {
        return Attribute::findOrFail($id);
    }

    public function create(array $data)
    {
        return Attribute::create($data);
    }

    public function update(Attribute $attribute, array $data)
    {
        $attribute->update($data);
        return $attribute;
    }

    public function delete(Attribute $attribute)
    {
        return $attribute->delete();
    }
}