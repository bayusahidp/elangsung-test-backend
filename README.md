## Instalasi backend elangsung test

- Buka terminal / cmd
- Tentukan lokasi folder
- git clone {{ url repo ini }}
- cd {{ nama folder repo }}
- composer install
- Kemudian buat database menggunakan MySQL (nama database bebas)
- buka .env pada project ini
- kemudian sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD dengan yang sudah anda buat
- kemudian buka terminal / cmd pada project ini
- jalankan perintah berikut "php artisan migrate:refresh --seed"
- pastikan port 8000 tidak digunaan, kemudian jalankan perintah berikut "php artisan serve"

Aplikasi sudah berjalan pada http://localhost:8000

API

baseURL = "http://localhost:8000/api"

Akun admin
- username : admin
- password : password

Akun user
- username : user
- password : password
