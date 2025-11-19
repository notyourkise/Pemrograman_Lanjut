# Week 1 — Pengenalan HTML & Seluruh Elemen Input Form (Tahap 1)

Fokus minggu pertama beralih dari hanya struktur dasar menjadi pengenalan **lengkap elemen HTML utama** dan ragam **tipe input form HTML5**. Minggu ini mahasiswa memahami pondasi untuk membangun form kontak dan halaman portofolio di minggu berikutnya.

## Tujuan Pembelajaran
- Memahami anatomy dokumen HTML (`<!DOCTYPE>`, `<html>`, `<head>`, `<body>`).
- Mengenali perbedaan elemen blok & inline.
- Menggunakan heading terstruktur (h1–h6) dan teks (p, strong, em, a, span).
- Menyisipkan gambar dengan `alt` yang bermakna & figure/figcaption.
- Mengenal daftar: ordered (`ol`), unordered (`ul`), definition list (`dl`).
- Memahami struktur semantik dasar: `header`, `nav`, `main`, `section`, `article`, `footer` (pengenalan awal).
- Menguasai berbagai input HTML5 (text, email, password, number, url, tel, search, date, time, datetime-local, week, month, checkbox, radio, range, color, file, hidden, textarea, select, datalist, button, submit, reset).
- Menerapkan atribut form penting: `name`, `id`, `for`, `placeholder`, `required`, `min`, `max`, `step`, `pattern`, `value`, `autocomplete`.
	- Bonus: `multiple` (select/file), `accept` (file), `minlength`/`maxlength` (text/password), `readonly` vs `disabled`.

## Struktur Folder (Week 1)
- `index.html` — Beranda/hero sederhana.
- `about.html` — Perkenalan singkat (profil & list minat).
- `contact.html` — Area demonstrasi berbagai elemen input (bukan final form; akan dirapikan Week 2).
- `assets/img/` — Gambar (gunakan milik sendiri atau placeholder).

## Materi Inti (Ringkas)
1. Anatomy Dokumen & Meta Minimal
2. Heading & Hierarki
3. Teks Inline vs Block
4. Gambar, Figure, Alt yang Deskriptif
5. List (ul/ol/dl)
6. Link Internal & Eksternal, Fragment (#id)
7. Elemen Semantik Dasar
8. Form: Struktur `<form>` & submit
9. Macam Input HTML5 (dengan contoh lengkap: text, email, password, number, url, tel, search, date/time/datetime-local/week/month, range, color, file, checkbox, radio, select, datalist, textarea, button/submit/reset, hidden)
10. Atribut Validasi Built-in
11. Best Practice: 1 `<h1>` / halaman, gunakan label, hindari `<br>` untuk layout

## Macam Input & Contoh (Ringkasan)
| Jenis | Tag/Type | Kegunaan | Catatan |
|-------|----------|----------|---------|
| Text | `<input type="text">` | Input umum | Gunakan `placeholder` seperlunya |
| Email | `type="email"` | Alamat email | Validasi format otomatis |
| Password | `type="password"` | Kata sandi | Karakter disamarkan |
| Number | `type="number"` | Angka | `min`, `max`, `step` |
| Date/Time | `date`, `time`, `datetime-local` | Tanggal/waktu | UI native browser |
| Week/Month | `week`, `month` | Minggu/Bulan | UI native, dukungan tergantung browser |
| Search | `type="search"` | Kotak pencarian | Biasanya tampil dengan X (clear) |
| Range | `type="range"` | Slider nilai | Sertakan label nilai |
| Color | `type="color"` | Pilih warna | Menghasilkan hex |
| File | `type="file"` | Upload berkas | Atribut `accept` |
| Checkbox | `type="checkbox"` | Multi pilihan | Nama sama + array di backend |
| Radio | `type="radio"` | Pilihan tunggal | Sama `name` beda `value` |
| URL | `type="url"` | Alamat URL | Validasi pola URL |
| Tel | `type="tel"` | Nomor telepon | Gunakan `pattern` jika perlu |
| Hidden | `type="hidden"` | Data tersembunyi | Jangan untuk data sensitif |
| Submit/Reset | `type="submit"`/`reset` | Tombol kirim/reset | Reset hati-hati |
| Textarea | `<textarea>` | Teks panjang | Gunakan `rows` |
| Select | `<select><option>` | Pilih satu / multiple | `multiple` opsional |
| Datalist | `<datalist>` | Saran pilihan bebas | Hubung dengan `list` |

## Tugas Mingguan (Week 1)
1. Lengkapi konten `index.html`, `about.html` (≥2 paragraf, ≥1 list).
2. Tambahkan minimal 1 gambar dengan `alt` deskriptif di `index.html` atau `about.html`.
3. Di `contact.html`, buat blok DEMO berisi minimal 15 tipe input berbeda (termasuk checkbox, radio, search, week, month, datetime-local) + label yang benar.
4. Terapkan atribut `required` pada minimal 3 input relevan.
5. Demonstrasikan grouping:
	- Radio: beberapa opsi dengan `name` yang sama (pilih salah satu).
	- Checkbox: beberapa opsi dengan `name="minat[]"` (pilih banyak).
6. Tampilkan contoh `select multiple` dan `file` dengan `accept` (mis. PDF/JPG) dan `multiple` (opsional).
7. Gunakan satu `<h1>` per halaman; heading turun bertahap.
8. Validasi struktur (W3C): tidak ada error fatal.

## Checklist Validasi
- [ ] `<!DOCTYPE html>` & `<html lang="id">` ada.
- [ ] `<meta charset>` & `<meta name="viewport">` ada.
- [ ] Satu `<h1>` per halaman.
- [ ] Semua gambar punya `alt` bermakna.
- [ ] Semua input memiliki `<label for="...">` (kecuali tipe dekoratif/hidden).
- [ ] Navigasi antar halaman berfungsi.
- [ ] Minimal 10 tipe input didemonstrasikan.

## Tips Praktik Baik
- Jangan gunakan `<br><br>` untuk spasi vertikal — gunakan CSS nanti (Week 3 intro CSS).
- `placeholder` bukan pengganti label.
- Gunakan nama file lowercase-tanpa-spasi (`profile.jpg`).
 - Selalu pasangkan `label` dengan `for` ke `id` input yang unik.
 - Untuk aksesibilitas, susun urutan fokus logis dan beri teks link/label yang deskriptif.

## Preview & Pengembangan
Jalankan via browser langsung atau gunakan Live Server VS Code.

## Arah Minggu Berikutnya
Week 2 akan fokus mengorganisasi ulang form menjadi form kontak nyata, menambah tabel, media (audio/video), semantic struktur yang lebih rapi, dan validasi atribut lebih lanjut.

