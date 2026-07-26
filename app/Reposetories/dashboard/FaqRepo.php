<?php

namespace App\Reposetories\dashboard;

use App\Models\Dashboard\Faq;
use Illuminate\Database\Eloquent\Collection;

class FaqRepo
{
    public function getAll(): Collection
    {
        return Faq::query()->latest()->get();
    }

    public function store(array $data): Faq
    {
        return Faq::create($data);
    }

    public function findOrFail(int $id): Faq
    {
        return Faq::query()->findOrFail($id);
    }

    public function update(int $id, array $data): Faq
    {
        $faq = $this->findOrFail($id);
        $faq->update($data);

        return $faq;
    }

    public function destroy(int $id): Faq
    {
        $faq = $this->findOrFail($id);
        $faq->delete();

        return $faq;
    }
}
