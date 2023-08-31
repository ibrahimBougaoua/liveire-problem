<div>
    <div class="container-fluid">
        <div class="row">
          <div class="col-lg-4">
            <h4>Pendding offers {{ $nbrPendingOffer }}  </h4>
          </div>
          <div class="col-lg-4"></div>
          <div class="col-lg-4">
            <div class="alert alert-success py-2 px-4 border  align-items-center shadow  " role="alert" id="alertOffer" >
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 12c0-4.969-4.031-9-9-9s-9 4.031-9 9 4.031 9 9 9 9-4.031 9-9Z"></path>
                    <path d="m16.5 8.25-6.3 7.5-2.7-3"></path>
                  </svg>
                <span class="text-2xl font-weight-bold text-dark ml-2">Offer published successfully</span>
            </div>
          </div>
        </div>
      </div>
      
    @foreach($pendingOffers as $index => $offer)
        @livewire('single-offer', ['offer' => $offer], key($offer->id))
    @endforeach

    {{ $pendingOffers->links() }}
    <script>
        var alert = document.getElementById("alertOffer");
        alert.style.display = "none";
        window.addEventListener('offerPublished', event => {
            alert.style.display = "block";
            setTimeout(function() {
                alert.style.display = "none";
            }, 1000);
        })
        window.addEventListener('hideOofferPublished', event => {
            alert.style.display = "none";
        })
    </script>
</div>
