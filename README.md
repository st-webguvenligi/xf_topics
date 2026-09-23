# ST Topic Tabs — XenForo 2.3

XenForo 2.3 için dört sekmeli konu widget'i:

- Duyurular
- Son Konular
- Popüler Konular
- Makale Paylaşımları

Her satırda konu başlığı, son cevap yazan kullanıcının avatarı ve kullanıcı adı gösterilir.

## Kurulum

1. `src/addons/ST/TopicTabs` klasörünü XenForo kurulumunuzdaki `src/addons/ST/TopicTabs` konumuna kopyalayın.
2. Admin Panel > Geliştirme > Add-ons bölümünden **ST: Topic Tabs** eklentisini yükleyin veya geliştirici modunda yeniden yapılandırın.
3. Appearance > Widgets > Add widget üzerinden **ST Topic Tabs** widget'ini istediğiniz widget konumuna ekleyin.
4. Widget seçeneklerinde her sekme için ilgili forum/node ID'lerini virgülle ayırarak girin.

## Yapılandırma

- **Duyurular forum ID'leri:** Duyuru konularının bulunduğu forumlar.
- **Son Konular forum ID'leri:** Boş bırakılırsa tüm erişilebilir forumlar.
- **Popüler Konular forum ID'leri:** Popüler konuların aranacağı forumlar.
- **Makale Paylaşımları forum ID'leri:** Makale forumları.
- **Konu sayısı:** Her sekmede gösterilecek konu sayısı.
- **Popülerlik dönemi:** Popüler konular için gün sayısı.

Forum ID'leri örneğin `3,8,12` biçiminde girilebilir. Kullanıcının görme yetkisi olmayan konular XenForo Finder tarafından otomatik olarak filtrelenir.

## Not

Widget sekmeleri, yapılandırmadaki forum ID'lerine göre çalışır. Bu nedenle farklı forum yapılarında kod değişikliği gerekmez; yalnızca widget seçeneklerini güncellemek yeterlidir.
