@component('mail::message')
# Réinitialisation de mot de passe

Bonjour {{ $user->email }},<br>
Vous avez demandé une réinitialisation de mot de passe.<br>
Voici votre nouveau mot de passe&nbsp;:&nbsp;<strong>{{ $plainPassword }}</strong>
@endcomponent
