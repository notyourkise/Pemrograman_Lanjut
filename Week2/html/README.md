# Week 2 — Komponen HTML Lanjutan & Persiapan Portofolio (Tahap 2)

Minggu 2 memperdalam berbagai komponen HTML di luar input: **tabel, media, struktur semantik, metadata, tautan & navigasi**, sekaligus merapikan form kontak yang didemokan pada Week 1 agar siap dipakai di portofolio.

## Tujuan Pembelajaran
- Menyusun halaman dengan struktur semantik yang konsisten (`header`, `nav`, `main`, `section`, `article`, `aside`, `footer`).
- Menambahkan tabel data tabular dengan elemen yang tepat (`caption`, `thead`, `tbody`, `th`).
- Menyematkan media (`img` lanjut, `audio`, `video`) dan memberikan fallback teks.
- Mengelola tautan internal/eksternal, fragment `#id`, dan navigasi berbasis list.
- Mengisi metadata halaman: `title` unik dan `meta description` deskriptif.
- Merapikan form kontak: grouping `fieldset/legend`, validasi built-in, `autocomplete`, `pattern`, `min/max/step`, `accept` pada file input.

## Struktur Folder (Week 2)
- `index.html` — Beranda (navigasi lengkap, ringkasan konten).
- `about.html` — Profil + tabel skill.
- `projects.html` — Daftar ≥2 proyek (gunakan `<article>` per proyek, boleh pakai `<figure>` untuk gambar).
- `contact.html` — Form kontak rapi & siap pakai (menyempurnakan demo Week 1).
- `assets/img/` — Gambar pendukung.

## Materi Inti (Ringkas)
1. Semantik & Navigasi (ul/li dalam `nav`, `aria-label` bila perlu)
2. Tabel: `caption`, `thead`, `tbody`, `th`, aksesibilitas header
3. Media: `audio`/`video` + `source`, atribut `controls`, teks fallback
4. Metadata: `title` unik, `meta description` ringkas, `lang` yang tepat
5. Form kontak: `fieldset/legend`, `label/for`, `required`, `pattern` (contoh tel), `autocomplete`, `accept` (PDF), tombol submit/reset
6. Link dan fragment: buat daftar isi dengan tautan `#id`

## Tugas Mingguan (Week 2)
1. Buat `projects.html` berisi minimal 2 proyek: gambar (boleh placeholder), judul, deskripsi singkat, tautan ke `project-detail.html` (boleh dummy).
2. Rapi-kan `contact.html`: gunakan `fieldset/legend`, `label/for`, `required` pada field utama, `type="email"`, `pattern` untuk tel, `accept` untuk file (mis. PDF).
3. Tambahkan tabel skills pada `about.html` lengkap dengan `caption`, `thead`, `tbody`.
4. Lengkapi `title` dan `meta description` yang unik di setiap halaman.
5. Tambahkan minimal 1 elemen media: `audio` atau `video` (dengan `controls` dan fallback teks).

## Checklist Validasi
- [ ] Struktur semantik konsisten di semua halaman.
- [ ] Navigasi berbasis list, tautan antar halaman berfungsi.
- [ ] Tabel memiliki header (`th`) yang sesuai dan caption.
- [ ] Form menggunakan label yang benar, `required`, dan atribut validasi sesuai.
- [ ] Metadata (`title`, `meta description`) unik per halaman.

## Arah Minggu Berikutnya
Week 3 akan menyatukan semua halaman menjadi portofolio final: menambahkan skip link, optimasi performa (gambar `loading="lazy"`), aksesibilitas dasar, dan validasi HTML akhir.
