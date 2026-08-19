<?php

namespace App\Livewire\TransferPosting;

use App\Models\MaterialTransferPosting;
use Livewire\Component;

class Show extends Component
{
    public MaterialTransferPosting $materialTransferPosting;

    public function mount(MaterialTransferPosting $materialTransferPosting)
    {
        $this->materialTransferPosting = $materialTransferPosting->load('fromProject', 'toProject', 'createdBy', 'items.fromTool', 'histories.user', 'histories.items.tool', 'histories.items.createdBy', 'fromCt', 'toCt');
    }

    public function render()
    {
        return view('livewire.transfer-posting.show');
    }
}
