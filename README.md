# YouTube Video Fetcher

YouTube Video Fetcher, belirli bir YouTube kanalından videoları çekip bir yazıya gömmenizi sağlayan bir WordPress eklentisidir. Eklenti, her 30 dakikada bir yeni videoları kontrol eder ve yeni bir yazı olarak yayınlar.

## Özellikler

- Belirli bir YouTube kanalından videoları çeker.
- Her 30 dakikada bir yeni videoları kontrol eder.
- Videoları yeni bir yazı olarak yayınlar.
- Video thumbnail'larını öne çıkarılmış görsel olarak ayarlar.

## Kurulum

1. Bu repository'i indirin veya klonlayın:
    ```sh
    git clone https://github.com/username/repo.git](https://github.com/cinarci/youtube-fetcher-extension
    ```

2. Dosyaları WordPress eklentiler klasörünüze yükleyin:
    ```sh
    wp-content/plugins/youtube-video-fetcher
    ```

3. Eklentiyi etkinleştirin:
    - WordPress yönetici paneline gidin.
    - `Eklentiler > Yeni Ekle` sekmesine tıklayın.
    - `Yüklenen` sekmesine tıklayın ve `YouTube Video Fetcher` eklentisini etkinleştirin.

## Kullanım

1. `yvf_fetch_all_videos` fonksiyonu ile tüm videoları hemen çekmek için eklentiyi etkinleştirin.
2. Eklenti, her 30 dakikada bir yeni videoları kontrol edecektir.

## Yapılandırma

Eklentiyi yapılandırmak için `yvf_fetch_videos` fonksiyonunda `APIKEY` ve `CHANNELID` değişkenlerini kendi YouTube API anahtarınız ve kanal kimliğiniz ile değiştirin.


```
php
$api_key = 'APIKEY';
$channel_id = 'CHANNELID';
```

Bu eklenti ile BlackVideo wordpress teması birlikte kullanıldığında belirli bir youtube sayfasındaki videolar, youtube'a oldukça benzer şekilde web siteye dönüşmektedir. 
Kategori numarasına göre istenilen kategorilerde yükleme yapılabilir.
