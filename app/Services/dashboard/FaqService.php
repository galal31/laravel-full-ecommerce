<?php

namespace App\Services\dashboard;

use App\Models\Dashboard\Faq;
use App\Reposetories\dashboard\FaqRepo;
use Illuminate\Database\Eloquent\Collection;

class FaqService
{
    public function __construct(private FaqRepo $faqRepo) {}

    public function getAll(): Collection
    {
        return $this->faqRepo->getAll();
    }

    public function store(array $data): Faq
    {
        return $this->faqRepo->store($data);
    }

    public function faqById(int $id): Faq
    {
        return $this->faqRepo->findOrFail($id);
    }

    public function update(int $id, array $data): Faq
    {
        return $this->faqRepo->update($id, $data);
    }

    public function destroy(int $id): Faq
    {
        return $this->faqRepo->destroy($id);
    }
}
