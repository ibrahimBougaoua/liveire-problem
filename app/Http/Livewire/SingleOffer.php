<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Offre;
use App\Models\Journalar;
use App\Models\Journalfr;
use App\Models\TempOffer;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SingleOffer extends Component
{
    // the event listeners
    protected $listeners = ['keydown.escape' => 'resetSelectedItem'];

    // public properties
    public $dismiss;
    public $offer_id ;

    public $titreCount = 0 ;
    public $titreResults = [];
    public $selectedItem = null;

    public $type_journal;
    public $modalForNewEtab = false ;
    public $saved = false ;
    public $offer ;

    public $titre;
    public $wilaya = 'Alger';
    public $etablissement;
    public $secteur = [];
    public $journalOffre ;
    public $journal;
    public $journalF;
    public $journalA;
    public $status = "Appel d'offres & Consultation";
    public $description;
    public $date_publication;
    public $date_after = 15 ;
    public $date_echeance;
    public $image ;
    public $showList = false; // New property to control the visibility of the list

    

    
    public function mount($offer)
    {
        
        $this->dismiss = false ;

        $this->offer = $offer;
        
        $this->image = $this->offer->titre;

        $this->date_publication = Carbon::now()->format('Y-m-d');
        
        $d = $this->date_after - 1 ;
        $this->date_echeance = date('Y-m-d', strtotime($this->date_publication . ' +' . $d . ' days'));
        
        $journalFrData = Journalfr::all()->toArray();
        $journalArData = Journalar::all()->toArray();
        $mergedData = array_merge($journalFrData, $journalArData);
        $this->journal = collect($mergedData);

        
    }
    
    /* -----------------------------------------BEGINING ----------------------------------------------------------------------- */
    // there functions for showing / hiding list of titre and navigating using keyboard keys UP & DOWN and ENTRE key
    public function selectItem($itemId)
    {
        $this->selectedItem = Offre::find($itemId);
        $this->titre = $this->selectedItem->titre;
        $this->showList = false;
    }

    public function chooseSelectedItem()
    {
        if ($this->selectedItem) {
            $this->titre = $this->selectedItem->titre;
            $this->resetSelectedItem();
            $this->showList = false;
        }

        $this->updatedTitre() ;
        
    }

    public function moveSelection($direction)
    {
        $items = $this->titreResults;

        if ($this->selectedItem === null) {
            $this->selectedItem = $direction === 'up' ? $items->last() : $items->first();
        } else {
            $currentIndex = $items->search(function ($item) {
                return $item->id === $this->selectedItem->id;
            });

            if ($direction === 'up') {
                $previousIndex = $currentIndex - 1;
                $this->selectedItem = $previousIndex >= 0 ? $items[$previousIndex] : $items->last();
            } elseif ($direction === 'down') {
                $nextIndex = $currentIndex + 1;
                $this->selectedItem = $nextIndex < $items->count() ? $items[$nextIndex] : $items->first();
            }
        }
    }

    public function resetSelectedItem()
    {
        $this->selectedItem = null;
    }

    public function hideChoices()
    {
        $this->showList = false;
    }
    /* -------------------------------------------- END ----------------------------------------------------------------------- */

    /* ------------------------------------------- BEGINIG OF MY PROBLEM -------------------------------------------------------*/
    public function updatedTitre(){
        // reset the secteur property when a titre is updated
        $this->reset('secteur');
       
        // select a collections where titre we write is like titre of Offers
        $this->titreResults = Offre::where('titre', 'like', '%' . $this->titre . '%')->get();

        // select how many offers titre is like the titre we write
        $this->titreCount = Offre::where('titre', 'like', '%' . $this->titre . '%')->count();

        // sho the list of similare titres
        $this->showList = true; 

        // select a collections where titre we write is like titre of Offers with there secteur
        // here we are intreseted with the "secteur" of "similare titre" 
        $similaireOffers = Offre::where('titre','LIKE','%' . $this->titre . '%')->with('secteur:id')->get(['id','titre']);

        // we test if there is similareoffers with titre LIKE tire we wrote
        if(!empty($similaireOffers) && !empty($this->titre)){

            // here i make sure the secteur array is empty
            $this->secteur = [];

            // added secteur of the similare offers to the list of secteur if not already there
            foreach ($similaireOffers as $offre) {
                foreach ($offre->secteur as $secteur) {
                    if (!in_array($secteur->id, $this->secteur)) {
                        $this->secteur[] = $secteur->id; 
                    }
                }
            }

        }else {
            $this->secteur = [];
        }
    }
    /* --------------------------------------------END OF PROBLEM -------------------------------------------- */

    /* -------------------------------------------- IDON'T THINK THERE REST HAS ANYTHING TO DO WITH MY PROBLEM - FEEL FREE THE CHECK - -------------------------- */
    public function updatedEtablissement(){
        if($this->etablissement == 0){
            $this->emit('openModal', $this->offer->id);
        }
    }

    public function updatedDatePublication(){
        $this->updatedDateAfter();
    }

    public function updatedJournalOffre(){
        [$id, $source] = explode('_', $this->journalOffre);
        if($source == 'journalAr') {
            $this->journalF = NULL ;
            $this->journalA = $id ;
        }
        if($source == 'journalFr') {
            $this->journalF = $id ;
            $this->journalA = NULL ;
        }
    }

    public function updatedDateAfter(){
        $d = $this->date_after - 1 ;
        $this->date_echeance = date('Y-m-d', strtotime($this->date_publication . ' +' . $d .' days'));
    }

    public function submit(){
        $this->validate([
            'journalOffre' => 'required'
        ]);

        
        $offre = Offre::create([
            'user_id' => Auth::id(),
            'titre' => $this->titre,
            'statut' => $this->status,
            'type' => 'national',
            'wilaya' => $this->wilaya,
            'description' => $this->description,
            'date_pub' => $this->date_publication,
            'date_limit' => $this->date_echeance,
            'img_offre' => $this->image,
            'img_offre2' => NULL,
            'adminetab_id' => $this->etablissement,
            'journalar_id' => $this->journalA,
            'journalfr_id' => $this->journalF,
            'etat' => "active",
        ]);

        $offre->secteur()->sync($this->secteur);

        $tempOffer = TempOffer::find($this->offer->id)->delete();

        $this->dispatchBrowserEvent('offerPublished');

        $this->emit('updateNbrPendingOffers');

        $this->dismiss = true ;
    }

    public function render()
    {
        return view('livewire.single-offer');
    }
}
