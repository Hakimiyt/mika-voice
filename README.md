# Mika Voice

Chatbot AI dengan avatar 3D (VRM) yang boleh **bercakap, gerak mulut, berkelip dan mengikut kursor**. Mika faham BM, English dan Jepun, dan membalas dalam bahasa yang sama dengan mesej anda.

## Ciri-ciri

- Avatar 3D format `.vrm` (Three.js + `@pixiv/three-vrm`)
- Otak AI menggunakan **Groq** (model `openai/gpt-oss-120b`)
- Balas automatik dalam bahasa yang sama (BM / EN / JP)
- Suara Mika menggunakan suara terbina dalam browser (`speechSynthesis`), percuma dan tanpa pemasangan tambahan
- Input suara melalui mikrofon: klik 🎙️ untuk mula, cakap selama mana anda mahu, klik ⏹ untuk hantar
- Ekspresi muka (neutral, happy, think, surprised) dan gerak mulut semasa bercakap
- API key kekal di server (`.env`), tidak pernah sampai ke browser

## Cara kerja

```
Taip / 🎙️ mikrofon  →  public/api/chat.php  →  Groq API
                                   ↓
                        jawapan + kod bahasa
                                   ↓
              speechSynthesis (suara browser) + gerak mulut avatar
```

## Keperluan

- [Laragon](https://laragon.org) (Apache + PHP 8+) atau mana-mana pelayan PHP tempatan
- Akaun percuma Groq untuk API key: https://console.groq.com
- **Google Chrome** atau **Microsoft Edge** (untuk mikrofon dan suara)
- Sambungan internet (Three.js dimuat dari CDN)
- Fail model `.vrm` anda sendiri (lihat bahagian [Model avatar](#model-avatar))

## Pemasangan

1. Salin folder projek ke `C:\laragon\www\mika-voice`.
2. Salin `.env.example` menjadi `.env`, kemudian isi `GROQ_API_KEY`:

   ```
   GROQ_API_KEY=isi_key_anda_di_sini
   GROQ_MODEL=openai/gpt-oss-120b
   ```

3. Letak model VRM di `public/models/mika.vrm`.
4. Start Laragon (Apache + PHP).
5. Buka **http://localhost/mika-voice/public/** dalam Chrome atau Edge.

> Guna `localhost`. Mikrofon hanya berfungsi pada `localhost` atau HTTPS. Jangan buka `index.html` terus (`file://`), kerana API tidak akan berfungsi.

## Cara guna

- **Menaip:** tulis mesej, tekan Enter atau butang ➤.
- **Suara:** klik 🎙️, mula bercakap, klik ⏹ bila selesai. Bahasa pengecaman suara mengikut dropdown **BM / EN / JP** di sebelah kotak input.
- **Ekspresi:** klik ikon emoji di penjuru kanan atas untuk tukar ekspresi Mika sementara.
- Mika membalas dalam bahasa yang anda guna, tidak kira apa yang dipilih pada dropdown.

## Model avatar

Projek ini **tidak menyertakan fail model** kerana isu lesen.

- Buat model sendiri dengan [VRoid Studio](https://vroid.com/en/studio) (percuma), eksport sebagai `.vrm`, atau guna model yang lesennya membenarkan penggunaan anda.
- Letak fail di `public/models/mika.vrm`.
- Semak syarat lesen model sebelum menyiarkan projek secara awam atau komersial.
- Jangan guna model watak berhak cipta.

## Tetapan `.env`

| Pemboleh ubah | Penerangan | Lalai |
|---|---|---|
| `GROQ_API_KEY` | API key Groq | (wajib) |
| `GROQ_MODEL` | Model Groq yang digunakan | `openai/gpt-oss-120b` |

## Penyelesaian masalah

**`Groq API Reject ... model does not exist`**
Model yang anda guna dah dimansuhkan oleh Groq. Semak senarai model semasa di https://console.groq.com/docs/models dan kemas kini `GROQ_MODEL`.

**`AI tidak memberikan jawapan`**
Model reasoning boleh memulangkan `content` kosong. Pastikan `chat.php` menghantar `include_reasoning: false` dan `max_completion_tokens` yang cukup.

**Mika tidak bersuara / suara pelik**
Suara datang daripada browser. Guna **Microsoft Edge** untuk suara Melayu semula jadi (Yasmin). Semak suara yang tersedia dalam Console (F12):

```js
speechSynthesis.getVoices().map(v => v.name + " | " + v.lang)
```

**Mikrofon tidak berfungsi**
Benarkan akses mikrofon (ikon kunci di sebelah URL), dan pastikan anda guna Chrome atau Edge pada `localhost`.

**Avatar tidak muncul**
Semak fail `public/models/mika.vrm` wujud dan buka Console (F12) untuk lihat ralat `VRM LOAD ERROR`.

## Keselamatan

- Jangan commit fail `.env`. Ia sudah diabaikan oleh `.gitignore`.
- `.htaccess` menyekat akses terus ke `.env` pada Apache.
- Kalau anda deploy ke server awam, tambah **rate limit atau pengesahan** pada `public/api/chat.php` supaya kuota API anda tidak disalahguna.
- `chat.php` mematikan semakan sijil SSL (`verify_peer => false`) supaya mudah dijalankan di Laragon. **Jangan** gunakan tetapan ini di pelayan sebenar.

## Teknologi

- PHP (backend ringkas)
- [Three.js](https://threejs.org) dan [@pixiv/three-vrm](https://github.com/pixiv/three-vrm)
- [Groq](https://groq.com) API
- Web Speech API (`speechSynthesis` dan `SpeechRecognition`)

## Lesen

Tambah fail `LICENSE` (contoh: MIT) mengikut pilihan anda.
