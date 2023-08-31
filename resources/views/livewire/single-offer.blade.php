<div>
    @if(!$dismiss)
        <form wire:submit.prevent="submit">
            <div class="card" style="font-size: 0.8rem">
                <div class="card-body" style="padding: 0px 1rem">
                    <div class="row">
                        <div class="col-md-6"></div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <span class="mr-2" style="font-size: 0.7rem;">{{ $image }}</span>
                            <img src="{{ asset('img/1.png') }}" alt="Logo" style="height: 40px">
                        </div>
                    </div>

                    <div class="row my-1">
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white" style="width: 140px;">Titre</span>
                                </div>
                                <div class="position-relative flex-grow-1"> <!-- Added flex-grow-1 class to make the input field take up remaining space -->
                                    <input type="text"
                                        class="form-control titre"
                                        id=""
                                        wire:model.debounce.500ms="titre"
                                        wire:keydown.arrow-up="moveSelection('up')"
                                        wire:keydown.arrow-down="moveSelection('down')"
                                        wire:keydown.enter.prevent="chooseSelectedItem"
                                        wire:keydown.escape="resetSelectedItem"
                                        style="font-size: 0.8rem;"
                                        required
                                    >
                                    @if($showList && !empty($titre) && $titreCount > 0)
                                        <ul class="list-group position-absolute w-100" style="z-index: 9999; top: 100%; font-size: 0.7rem; background-color:#ff9">
                                            @foreach ($titreResults as $titreData)
                                                <li style="cursor: pointer;" class="list-group-item{{ $selectedItem === $titreData ? ' active' : '' }}" wire:click="selectItem('{{ $titreData->id }}')">
                                                    {{ $titreData->titre }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="row my-1">
                        <div class="col-md-6" wire:ignore>
                            <div class="input-group d-flex">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white" style="width: 140px">Annonceur</span>
                                </div>
                                <select required wire:model="etablissement" class="form-control selectpicker"
                                    data-live-search="true" data-size="5" id="" title="etablissement" style="font-size: 0.6rem;" >
                                    @foreach (App\Models\Adminetab::All() as $etab)
                                        <option value="{{ $etab->id }}">
                                            {{ \Illuminate\Support\Str::limit($etab->nom_etablissement, 50, $end = '...') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal fade" id="{{ 'fullscreenModal' . $offer->id }}" tabindex="-1"
                            role="dialog" aria-labelledby="fullscreenModalLabel" aria-hidden="true" wire:ignore>
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <img src="{{ asset('storage/' . $offer->titre) }}" alt=""
                                            style="max-width: 100%; max-height: 100%;">
                                    </div>
                                </div>
                            </div>
                        </div>
                       
                            <div class="col-md-3" wire:ignore>
                                <select required wire:model="wilaya" class="form-control selectpicker" id="wilaya_offre"
                                    data-live-search="true" data-size="5" style="font-size: 0.6rem;">
                                    <option data-id="0" selected>Wilaya d'etablissement</option>
                                    @foreach (App\Models\Wilaya::where('codeWilaya','!=',NULL)->orderBy('codeWilaya', 'ASC')->select('wilaya', 'codeWilaya')->distinct()->get() as $wilaya)
                                        <option value="{{ $wilaya['wilaya'] }}" > 
                                            
                                            {{ $wilaya['codeWilaya'] . '-' .$wilaya['wilaya'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3" wire:ignore>
                                <select required wire:model="journalOffre" class="form-control selectpicker" id="wilaya_offre"
                                    data-live-search="true" data-size="5" style="font-size: 0.6rem;">
                                    <option data-id="0" value="" selected>Journal</option>
                                    @foreach ($journal as $j)
                                        <option value="{{ $j['id'] . '_' . $j['source'] }}">{{ $j['nom'] }}</option>
                                    @endforeach
                                </select>
                                @error('journalOffre') <span class="error">{{ $message }}</span> @enderror

                            </div>                
                        
                    </div>

                    <div class="row my-1">
                        <div class="col-md-6" wire:ignore>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white" style="width: 140px">Status</span>
                                </div>
                                <select required wire:model="status" class="form-control mb-2 selectpicker" title="statut" data-live-search="true" data-size="5">
                                    <option value="Appel d'offres & Consultation" selected>Appel d'offres & Consultation</option>
                                    <option value="Attribution de marché"> Attribution de marché</option>
                                    <option value="Sous-traitance" >Sous-traitance </option>
                                    <option value="Prorogation de délai" > Prorogation de délai</option>
                                    <option value="Annulation" >Annulation</option>
                                    <option value="Infructuosité" >Infructuosité</option>
                                    <option value="Adjudication" >Adjudication</option>
                                    <option value="Vente aux enchères" >Vente aux enchères</option>
                                    <option value="Mise en demeure et résiliation" >Mise en demeure et résiliation</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6" >
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white" style="width: 140px">Secteur</span>
                                </div>
                                <select required wire:model="secteur" class="form-control mb-2 selectpicker" multiple
                                    title="Secteur" data-live-search="true" data-size="5">
                                    @foreach (App\Models\Secteur::All() as $sect)
                                        <option value="{{ $sect->id }}" data-tokens="{{ $sect->secteur }}">
                                            {{ \Illuminate\Support\Str::limit($sect->secteur, 50, $end = '...') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="row my-1">
                        <div class="col-md-6" wire:ignore>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white" style="width: 140px">Date
                                        publication</span>
                                </div>
                                <input required type="date" wire:model="date_publication" class="form-control"
                                    id="datePublication">
                            </div>


                        </div>
                        <div class="col-md-5">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white" style="width: 140px">Date
                                        d'échéance</span>
                                </div>
                                <input required type="date" class="form-control" id="dateEcheance"
                                    wire:model="date_echeance">
                            </div>
                        </div>
                        <div class="col-md-1" wire:ignore>
                            <div class="input-group">
                                <input required type="text" wire:model="date_after" class="form-control">
                            </div>
                        </div>
                    </div>

                   

                    <div class="row my-1">
                        <div class="col-md-12">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-white"
                                        style="width: 140px">Description</span>
                                </div>
                                <textarea type="text" wire:model="description" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row my-1">
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-success" >Publié</button>
                            <button type="button" class="btn btn-secondary" data-toggle="modal"
                                data-target="{{ '#fullscreenModal' . $offer->id }}" wire:ignore>Voir</button>
                            <div class="modal fade" id="{{ 'fullscreenModal' . $offer->id }}" tabindex="-1"
                                role="dialog" aria-labelledby="fullscreenModalLabel" aria-hidden="true"
                                wire:ignore>
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <img src="{{ asset('storage/' . $offer->titre) }}" alt=""
                                                style="max-width: 100%; max-height: 100%;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

    <script type="text/javascript">
    
    document.addEventListener('livewire:load', function () {
        $('select').selectpicker();
    });
    document.addEventListener('livewire:update', function () {
        $('select').selectpicker();
    });

    document.addEventListener('click', function (event) {
        const clickedElement = event.target;
        const inputElement = document.querySelector('.titre');

        if (inputElement && !inputElement.contains(clickedElement)) {
            Livewire.emit('keydown.escape');
        }
    });

    </script>

</div>

