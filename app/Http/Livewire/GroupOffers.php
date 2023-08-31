<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TempOffer;
use Livewire\WithPagination;
use RealRashid\SweetAlert\Facades\Alert;

class GroupOffers extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['updateNbrPendingOffers'];
   
    public $nbrPendingOffer ;

    public function mount(){
        // count the number of offers per authenticated user
        $this->nbrPendingOffer = TempOffer::where('user_id',auth()->user()->id)->count();
    }

    public function updateNbrPendingOffers(){
        // count the number of offers per authenticated user
        $this->nbrPendingOffer = TempOffer::where('user_id',auth()->user()->id)->count();
    }

    public function render()
    {
        // select the offers per authenticated user
        $pendingOffers = TempOffer::where('user_id',auth()->user()->id)->paginate(10);
        return view('livewire.group-offers',[
            'pendingOffers' => $pendingOffers,
        ]);
    }

}
