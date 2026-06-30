# PROMPT BAB 2 — TINJAUAN PUSTAKA
## Tema: Aplikasi Kasir/POS Berbasis Web | Metode: Waterfall

---

## CARA PAKAI
1. Lengkapi dulu bagian **[ISI DI SINI]** di bawah dengan info skripsimu (judul, nama toko/objek penelitian, tools yang dipakai, dll).
2. Copy-paste seluruh blok **"MASTER PROMPT"** ke Claude (atau AI lain).
3. Bisa dikerjakan per subbab (lebih bagus hasilnya, lebih mudah dikontrol) — tinggal sebut subbab mana yang mau dikerjakan dulu.
4. Setelah dapat draft, **baca ulang dan edit dengan kalimatmu sendiri** — ini bukan untuk di-copy mentah, AI hanya bantu kerangka & referensi awal. Ingat aturan etika di Bab 2 Panduan Skripsi: bebas plagiarisme, parafrase, dan tetap tanggung jawabmu sebagai penulis.

---

## A. ISI DULU DATA SKRIPSIMU

```
Judul Skripsi      : [ISI DI SINI — mis. "Pembuatan Aplikasi Kasir Berbasis Website pada Toko ___ Menggunakan Metode Waterfall"]
Objek Penelitian    : [ISI DI SINI — nama toko/UMKM, jenis usaha, lokasi]
Masalah yang diangkat: [ISI DI SINI — mis. pencatatan manual, kasir lambat, stok tidak terkontrol]
Tools/Bahasa        : [ISI DI SINI — mis. PHP, Laravel, MySQL, Bootstrap, dll]
Fitur utama sistem   : [ISI DI SINI — mis. transaksi penjualan, manajemen stok, laporan, multi-cabang, payment gateway/QRIS]
Metode pengujian     : [ISI DI SINI — mis. Black-box Testing]
Jumlah sumber pustaka yang sudah dikumpulkan: [ISI DI SINI — minimal 15, 70% jurnal]
```

---

## B. STRUKTUR SUBBAB BAB 2 YANG DISARANKAN (untuk tema POS + Waterfall)

Sesuaikan urutan sesuai kebutuhan, tapi pola umum yang lazim dipakai untuk skripsi POS:

1. **2.1 Penelitian Terkait / Tinjauan Studi Sejenis** — perbandingan 5+ penelitian sebelumnya (boleh pakai tabel perbandingan)
2. **2.2 Sistem Informasi** — definisi & konsep dasar
3. **2.3 Point of Sale (POS) / Kasir** — definisi, fungsi, komponen
4. **2.4 Website** — definisi, karakteristik, jenis (statis/dinamis)
5. **2.5 Metode Waterfall** — tahapan (analisis, desain, coding, testing, maintenance) + gambar/diagram tahapan
6. **2.6 Basis Data (Database)** — definisi, DBMS yang dipakai (mis. MySQL)
7. **2.7 Struktur Navigasi** — linier, hierarki, non-linier, campuran (pilih yang dipakai di sistem)
8. **2.8 UML (Unified Modeling Language)** — Use Case Diagram, Class Diagram (jika dipakai)
9. **2.9 Tools Pendukung** — bahasa pemrograman, framework, code editor (mis. PHP, Laravel, VS Code, XAMPP)
10. **2.10 Black-box Testing** — definisi dan teknik pengujian yang dipakai

---

## C. MASTER PROMPT (COPY-PASTE INI KE AI)

```
Kamu adalah asisten penulisan akademik yang membantu saya menulis Bab 2 (Tinjauan Pustaka)
skripsi Sistem Informasi saya, sesuai Buku Pedoman Skripsi SI Universitas Gunadarma Edisi 3-2026.

KONTEKS SKRIPSI SAYA:
- Judul: [ISI JUDUL]
- Objek penelitian: [ISI OBJEK]
- Masalah yang diangkat: [ISI MASALAH]
- Metode pengembangan: Waterfall
- Tools/bahasa: [ISI TOOLS]
- Fitur utama sistem: [ISI FITUR]

TUGAS:
Tuliskan subbab [SEBUTKAN SUBBAB, mis. "2.5 Metode Waterfall"] untuk Bab 2 Tinjauan Pustaka.

ATURAN WAJIB DARI PANDUAN SKRIPSI (ikuti persis):
1. Setiap subbab minimal 3 paragraf, ditulis dalam bentuk narasi/esai mengalir — BUKAN poin-poin atau bullet list.
2. Gunakan sitasi format Harvard. Kemunculan pertama tulis semua nama penulis, mis. (Andriasari, Nizam, Nurhasanah & Wulandari, 2024). Untuk penulis 3+ pada kemunculan berikutnya boleh disingkat (Andriasari et al., 2024). Untuk 2 penulis selalu sebutkan keduanya, tidak pernah disingkat.
3. Sumber harus relevan, maksimal 10 tahun terakhir, dan diutamakan dari jurnal ilmiah (bukan blog pribadi).
4. Jangan mengarang sitasi atau data — kalau saya belum kasih sumber spesifik untuk suatu klaim, tandai dengan [BUTUH SUMBER] supaya saya bisa cari sendiri, jangan asal isi.
5. Definisi/konsep dijelaskan dengan kalimat sendiri (parafrase), bukan menyalin kalimat dari sumber.
6. Istilah asing dicetak miring (contoh: *website*, *database*).
7. Jika ada gambar/diagram konsep (mis. tahapan Waterfall), beri keterangan dan nomor gambar sesuai bab (Gambar 2.X).

ATURAN GAYA PENULISAN (PENTING — supaya tidak terbaca seperti tulisan AI):
1. Variasikan panjang kalimat — jangan semua kalimat panjang/medium rata-rata sama, selipkan kalimat pendek sesekali.
2. JANGAN pakai pola transisi berulang yang khas AI seperti "Selain itu,", "Dengan demikian,", "Hal ini menunjukkan bahwa," di hampir setiap paragraf. Variasikan atau hilangkan saja kalau tidak perlu — orang Indonesia menulis akademik biasanya langsung ke poin tanpa basa-basi transisi di setiap kalimat.
3. Hindari struktur "definisi → ciri-ciri → manfaat → kesimpulan" yang terlalu rapi dan simetris di SETIAP subbab — boleh sedikit berantakan/alami, seperti orang benar-benar berpikir sambil menulis.
4. Jangan terlalu banyak generalisasi besar di awal kalimat (mis. "Di era digital saat ini, teknologi berkembang pesat..." — basa-basi semacam ini terasa template AI, langsung saja ke konsep yang dibahas).
5. Sisipkan opini/analisis ringan penulis sendiri (boleh kalimat seperti "Dalam konteks penelitian ini, ...", "Berdasarkan beberapa sumber yang dirujuk, dapat dipahami bahwa...") supaya tidak cuma rangkaian definisi dari berbagai sumber yang ditempel.
6. Saat membandingkan beberapa sumber, jangan format "Menurut A (tahun)... Menurut B (tahun)... Menurut C (tahun)..." berulang persis sama tiap kalimat — variasikan pembukaan kalimat.
7. Gunakan bahasa akademik formal Bahasa Indonesia yang wajar dipakai mahasiswa S1 SI Gunadarma — tidak kaku seperti terjemahan, tidak terlalu santai juga.

FORMAT OUTPUT YANG SAYA MAU:
- Paragraf naratif penuh (tidak ada bullet/numbering kecuali memang saya minta tabel perbandingan).
- Panjang sekitar [3-5] paragraf per subbab kecuali saya minta lebih.
- Sertakan placeholder [BUTUH SUMBER] di tiap klaim yang perlu saya verifikasi/lengkapi sitasinya sendiri.

Sekarang, tuliskan subbab tersebut.
```

---

## D. PROMPT KHUSUS UNTUK 2.1 PENELITIAN TERKAIT (Tabel Perbandingan)

Subbab ini beda formatnya (boleh pakai tabel). Pakai prompt tambahan ini:

```
Tambahan untuk subbab 2.1 Penelitian Terkait/Tinjauan Studi Sejenis:

Buatkan tabel perbandingan penelitian terdahulu dengan kolom:
No | Penulis & Tahun | Judul Penelitian | Metode | Hasil/Kesimpulan | Perbedaan dengan Penelitian Saya

Gunakan 5 sumber berikut (Harvard style untuk sitasi di teks naratif sebelum/sesudah tabel):
1. Andriasari, Nizam, Nurhasanah & Wulandari (2024) — Aplikasi POS untuk profitabilitas & digitalisasi UMKM
2. Andy & Widiono (2024) — Inovasi teknologi manajemen penjualan: POS berbasis web untuk UMKM
3. Halawa & Kurniawan (2025) — Perancangan SI penjualan (POS) berbasis website metode Agile
4. Handayani, Gunawan & Taufiq (2020) — SI pemesanan menu makanan berbasis web
5. Sihaloho, Ramadani & Rahmayanti (2020) — Implementasi QRIS bagi UMKM di Medan

Setelah tabel, tulis 1-2 paragraf narasi yang menjelaskan benang merah dari kelima penelitian
tersebut dan secara spesifik menyebutkan apa yang membedakan penelitian saya (mis. objek
penelitian beda, metode beda, fitur tambahan seperti multi-cabang/QRIS/dsb — sesuaikan dengan
fitur sistem saya yang sudah saya isi di bagian A).

Ikuti aturan gaya penulisan yang sama (poin D di atas: variasi kalimat, hindari pola AI).
```

---

## E. TIPS TAMBAHAN SUPAYA HASIL AI TERASA "KAMU BANGET" (BUKAN AI BANGET)

Setelah dapat draft dari AI, lakukan ini sebelum dipakai final:

1. **Baca keras-keras** — kalau ada kalimat yang terasa "kaku saat diucapkan", itu biasanya ciri kalimat AI. Ganti dengan cara kamu sendiri menjelaskan.
2. **Ganti 2-3 kata di tiap kalimat** dengan sinonim yang lebih natural buat kamu — jangan cuma copy paste utuh.
3. **Hapus kalimat pembuka generik** kalau ada (mis. "Di era digital saat ini...", "Perkembangan teknologi yang pesat...") — langsung ke inti pembahasan.
4. **Pecah/gabung paragraf** sesuai cara kamu biasa nulis — jangan biarkan semua paragraf punya panjang yang sama persis.
5. **Tambahkan 1 kalimat opini/insight kecil dari kamu** di tiap subbab — ini paling efektif bikin tulisan terasa "milik kamu", bukan cuma rangkuman AI.
6. Cek similarity/plagiarism checker kampus (kalau ada) sebelum submit ke pembimbing — jangan asal percaya hasil AI 100% aman.

---

## F. CHECKLIST SEBELUM SUBMIT BAB 2

- [ ] Minimal 15 sumber pustaka, ≤10 tahun, 70% jurnal/30% lainnya
- [ ] Semua subbab minimal 3 paragraf, bentuk narasi (bukan poin)
- [ ] Semua sitasi format Harvard, konsisten
- [ ] Tidak ada [BUTUH SUMBER] yang masih tertinggal
- [ ] Sudah diedit ulang dengan kata-kata sendiri (bukan hasil mentah AI)
- [ ] Istilah asing dicetak miring
- [ ] Gambar/tabel (jika ada) diberi nomor sesuai bab dan dirujuk dalam teks