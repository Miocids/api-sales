<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
El sistema deberá tener funcionalidad de:<br>

- Login / Logout<br>

- Dashboard/Home con un texto de prueba<br>

- Recuperar contraseña<br>

Los módulos anteriores se pueden hacer en base a la funcionalidad default de Laravel. Solo habrá que cambiar los templates que trae por defecto.<br>

Crear un CRUD con su modelos y migraciones que administre una nota de venta:<br>

Considerar 3 modelos<br>

- Customer (id, name, email, address)<br>

- Items (id, name, sku, price)<br>

- Note (id, customer_id, date, total)<br>

- Note Items (id, note_id, item_id, quantity, total, attach)<br>

Llenar datos de prueba para los modelos: Customer, Items (no es necesario hacer cruds)<br>

Crear un CRUD completo de notas de venta en donde al crear o editar una nota de venta puedas ir agregando, modificando o eliminando Items  y los totales se van a ir modificando automáticamente, los precios y totales deben ser convertibles a  dólares / euros, para esto debe consumir una API de terceros usar esta o alguna similar, se debe implementar como un servicio dentro de la arquitectura de su aplicación<br>

- Las notas de venta se listarán mostrando Cliente, Fecha, Total, Attach<br>
</p>

<h2>Para correr el proyecto son necesario los siguientes pasos:</h2>

<p align="center">

    1.- composer install
    2.- configurar el archivo .env
    3.- php artisan migrate --seed
    4.- php artisan l5-swagger:generate
    5.- ingrear a la url de la app laravel; http://url/api-documentation
    6.- registrarse en la api en el endpoint auths/sign-up
    7.- loguearse en la api con el usuario registrado consumiendo el endpoint auths/sign-in
    8.- insertar el token en el apartado Authorize que se encuentra en la parte superior derecha tal como el ejemplo: Bearer token-generado-al-haberse-logueado-en-la-api
    9.- puede realizar acciones dentro de la api ya autenticado
</p>

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
