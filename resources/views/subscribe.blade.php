<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Souscrire
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                    <form action="{{ route('subscribe') }}" method="post" id="payment-form" class="w-1/2" data-secret="{{ $intent->client_secret }}">
                        @csrf
                        <div class="mb-4">
                            <input type="radio" class="mr-3" name="plan" id="" value="price_1LE9dLA03f71qpqckFiYGow7"><span>10€ / mois - Basic</span>
                            <input type="radio" class="mr-3 ml-3" name="plan" id="" value="price_1LE9dLA03f71qpqckzp2KKWO"><span>20€ / mois - Plus</span>
                        </div>          
                        <div class="w-1/2 mb-4 my-3">
                            <label for="cardholder-name" class="mr-3">Porteur de la carte</label>
                            <input type="text" name="cardholder-name" id="cardholder-name">
                        </div>       
                        <div class="w-1/2 mb-4 my-3">
                            <label for="card-element">Carte de crédit</label>
                            <div id="card-element" class="ml-3 my-4"></div>
                            <div id="card-errors" role="alert"></div>
                        </div>
                        <x-button id="card-button" class="ml-3 my-4">
                            Souscrire
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            // Création d'un client Stripe.
            // Mettre sa clé API : Se rappeler de la changer quand on passe en production
            // Pour voir sa clé API : https://dashboard.stripe.com/apikeys
            const stripe = Stripe('pk_test_51LBJaQA03f71qpqcOSHUCWNObRJSliTi4CgUIRLltwgvCMLg73DWqUVov5HUhuz8UQbV5iEaOBtGko1Lzkya5iBX005EfwTauI');

            // Créer une instance d'Element.
            var elements = stripe.elements();
            // Style personnalisé passé pour les éléments.
            var style = {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
            };
            // Création d'une instance pour l'élement "card"
            var card = elements.create('card', {style: style});
            // Ajout d'une instance de l'élément card dans la balise <div> 'card-element'
            card.mount('#card-element');
            // Gérez les erreurs de validation en temps réel à partir de l'élément de la carte.
            card.on('change', function(event) {
            var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
            });
            // Gérer la soumission du formulaire.
            var form = document.getElementById('payment-form');
            var clientSecret = form.dataset.secret;
            const cardHolderName = document.getElementById('cardholder-name');
            
            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                const { setupIntent, error } = await stripe.confirmCardSetup(
                    clientSecret, {
                        payment_method: {
                            card,
                            billing_details: { name: cardHolderName.value }
                        }
                    }
                );
                if (error) {
                    // Informez l'utilisateur en cas d'erreur.
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = error.message;
                } else {
                    // Envoyé le token au serveur.
                    stripeTokenHandler(setupIntent);
                }
            });
            // Soumettez le formulaire avec l'ID de jeton.
            function stripeTokenHandler(setupIntent) {
                // Insérez l'ID du token dans le formulaire afin qu'il soit soumis au serveur
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'paymentMethod');
                hiddenInput.setAttribute('value', setupIntent.payment_method);
                form.appendChild(hiddenInput);
                // Soumission du formulaire
                form.submit();
            }
        </script>
    @endpush
</x-app-layout>
