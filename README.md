# Dijital Ürün Satış Yönetim Paneli

Bu proje, cPanel paylaşımlı hosting üzerinde çalışacak şekilde tasarlanmış, saf PHP (PDO), Bootstrap 5 ve vanilla JavaScript kullanılarak geliştirilmiş dijital ürün satış yönetim panelidir. İlk sprintte yönetim paneli ve arka uç bileşenleri tamamlanmış olup müşteri arayüzü için yalnızca iskelet dosyaları hazırlanmıştır.

## Özellikler
- Rol tabanlı kimlik doğrulama, oturum yönetimi ve CSRF koruması
- Kategori, ürün, varyant ve lisans anahtarı havuzu yönetimi
- Sipariş oluşturma, durum güncelleme ve dijital teslimat kayıtları
- Müşteri yönetimi, kupon/indirim kuralları, ayarlar ve SMTP testi
- Sağlayıcı entegrasyon altyapısı ve Stub provider örneği
- Audit ve hata loglama, raporlama özet kartları

## Dizin Yapısı
```
public/          # Web kök dizini, index.php front controller
app/Core/        # Çekirdek sınıflar (DB, Auth, Router, Mailer, Security vb.)
app/Controllers/ # Admin controller sınıfları
app/Models/      # PDO tabanlı model/repository sınıfları
app/Services/    # İş mantığı servisleri
app/Integrations/# Sağlayıcı adapter arayüzleri ve stub
app/Views/       # Bootstrap tabanlı admin arayüzü + client iskeletleri
config/          # Uygulama, veritabanı, posta, para birimi ayarları
database/        # migrations.sql ve seeds.sql dosyaları
storage/         # Log ve dışa aktarım dizinleri
```

## Kurulum
1. `config/database.php` dosyasını sunucunuzun veritabanı bilgilerine göre düzenleyin.
2. `config/mail.php` dosyasını SMTP/posta ayarlarınıza göre güncelleyin (veya admin panelinden ayarlar ekranını kullanın).
3. Veritabanında yeni bir şema oluşturun ve `database/migrations.sql` ardından `database/seeds.sql` dosyalarını phpMyAdmin veya MySQL CLI üzerinden içe aktarın.
4. `public/` klasörünü hosting üzerinde `public_html/` dizinine, proje kökünü ise bir üst dizine yükleyin.
5. Yönetici paneline `admin@example.com / Admin!123` bilgileri ile giriş yapın ve gerekli ayarlamaları tamamlayın.

## Manuel Test Planı (Özet)
- Giriş yapın, dashboard özet kartlarını ve log kutucuklarını kontrol edin.
- Yeni kategori/ürün ekleyin, varyant oluşturup lisans anahtarı içe aktarın.
- Manuel sipariş oluşturun, otomatik lisans teslimini doğrulayın.
- Müşteri ve kupon ekleme işlemlerini test edin.
- Ayarlar ekranından SMTP ayarı girip test e-postası gönderin.
- Sağlayıcı ekranında Stub sağlayıcı bakiyesini görüntüleyin.

## Notlar
- Müşteri arayüzü bir sonraki sprintte geliştirilecektir; `/app/Views/client/` dizinindeki dosyalar yalnızca yer tutucudur.
- Tüm sorgular PDO prepared statement kullanır, oturum cookie ayarları güvenli şekilde yapılandırılmıştır.
- Harici bağımlılık bulunmamaktadır.
