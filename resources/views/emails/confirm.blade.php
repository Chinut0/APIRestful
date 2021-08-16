Hola {{$user->name}},
Has cambiado tu correo electronico. Por favor verificala usando el siguiente enlace:
{{route('verify', $user->verification_token)}}